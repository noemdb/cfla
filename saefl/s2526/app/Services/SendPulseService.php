<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;
use Carbon\Carbon;
use InvalidArgumentException;
use App\Models\ResendEmail;
use App\Jobs\Email\SendPulse\ProcessSendPulseEmail;

class SendPulseService
{
    protected $apiClient;
    protected $userId;
    protected $secret;
    protected $tokenStorage;
    protected $verifiedSender;
    protected $from;

    public function __construct()
    {
        $this->userId = config('services.sendpulse.client_id');
        $this->secret = config('services.sendpulse.client_secret');
        $this->tokenStorage = config('services.sendpulse.token_storage', 'file');
        $this->verifiedSender = config('services.sendpulse.verified_sender');
        $this->from = config('services.sendpulse.from');

        // Verificar la configuración
        $this->verifyConfiguration();

        $this->initializeApiClient();
    }

    /**
     * Verifica que todas las variables de configuración estén correctamente asignadas
     * @throws InvalidArgumentException
     */
    protected function verifyConfiguration(): void
    {
        $configValues = [
            'userId' => $this->userId,
            'secret' => $this->secret,
            'tokenStorage' => $this->tokenStorage,
            'verifiedSender' => $this->verifiedSender,
            'from' => $this->from
        ];

        $missing = [];
        foreach ($configValues as $key => $value) {
            if (empty($value)) {
                $missing[] = strtoupper("SENDPULSE_" . str_replace(
                    ['userId', 'tokenStorage', 'verifiedSender'],
                    ['CLIENT_ID', 'TOKEN_STORAGE', 'VERIFIED_SENDER'],
                    $key
                ));
            }
        }

        if (!empty($missing)) {
            $this->logError('Configuración incompleta', [
                'missing' => $missing,
                'config_values' => array_map(function ($value) {
                    return $value ? '***' : null;
                }, $configValues)
            ]);

            throw new InvalidArgumentException(
                'Configuración de SendPulse incompleta. Por favor verifica en tu archivo .env: ' . implode(', ', $missing)
            );
        }

        // Verificar que los valores no sean solo espacios en blanco
        foreach ($configValues as $key => $value) {
            if (is_string($value) && trim($value) === '') {
                $missing[] = strtoupper("SENDPULSE_" . str_replace(
                    ['userId', 'tokenStorage', 'verifiedSender'],
                    ['CLIENT_ID', 'TOKEN_STORAGE', 'VERIFIED_SENDER'],
                    $key
                ));
            }
        }

        if (!empty($missing)) {
            $this->logError('Configuración con valores vacíos', [
                'missing' => $missing,
                'config_values' => array_map(function ($value) {
                    return $value ? '***' : null;
                }, $configValues)
            ]);

            throw new InvalidArgumentException(
                'Configuración de SendPulse con valores vacíos. Por favor verifica en tu archivo .env: ' . implode(', ', $missing)
            );
        }
    }

    /**
     * Registra errores en el log de manera consistente
     */
    public function logError(string $action, array $data = []): void
    {
        Log::error('SendPulseService: ' . $action, $data);
    }

    /**
     * Initialize the SendPulse API client
     */
    protected function initializeApiClient()
    {
        try {
            if (empty($this->userId) || empty($this->secret)) {
                throw new \Exception('Credenciales de SendPulse no configuradas');
            }

            // Crear el directorio de almacenamiento si no existe
            $storagePath = storage_path('app/sendpulse');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $this->apiClient = new ApiClient($this->userId, $this->secret, new FileStorage($storagePath));

            // Verificar que el cliente se inicializó correctamente
            if (!$this->apiClient) {
                throw new \Exception('No se pudo inicializar el cliente API de SendPulse');
            }
        } catch (\Exception $e) {
            $this->logError('Error al inicializar el cliente API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Envía un correo electrónico usando la API de SendPulse
     *
     * @param string $to
     * @param string $subject
     * @param string $htmlContent
     * @param Carbon|null $delayTime
     * @param bool $queue Si es true, usa la queue
     * @param string|array|null $cc Dirección o array de direcciones de correo para copia
     * @param string|array|null $bcc Dirección o array de direcciones de correo para copia oculta
     * @return array
     */
    public function send(string $to, string $subject, string $htmlContent, ?Carbon $delayTime = null, bool $queue = false, string|array|null $cc = null, string|array|null $bcc = null): array
    {
        try {
            // Validar el email del destinatario
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $this->logError('Email inválido', ['to' => $to]);
                return [
                    'success' => false,
                    'message' => 'La dirección de correo electrónico del destinatario no es válida'
                ];
            }

            // Validar el email del remitente
            if (!filter_var($this->from, FILTER_VALIDATE_EMAIL)) {
                $this->logError('Email remitente inválido', ['from' => $this->from]);
                return [
                    'success' => false,
                    'message' => 'La dirección de correo electrónico del remitente no es válida'
                ];
            }

            // Mejorar el contenido HTML para reducir spam
            $htmlContent = $this->improveEmailContent($htmlContent);

            if ($queue) {
                // Si se solicita queue, despachar el job
                $dataEmail = [
                    'html' => $htmlContent,
                    'subject' => $subject,
                    'address' => $to,
                    'cc' => $cc,
                    'bcc' => $bcc,
                ];
                $job = ProcessSendPulseEmail::dispatch($dataEmail);
                if ($delayTime) {
                    $job->delay($delayTime);
                }
                return [
                    'success' => true,
                    'message' => 'Correo programado en la queue',
                    'queue' => true,
                    'scheduled_time' => $delayTime ? $delayTime->format('Y-m-d H:i:s') : null
                ];
            }

            // Asegurarnos de que el cliente API esté inicializado
            if (!$this->apiClient) {
                $this->initializeApiClient();
            }

            // Generar un ID único para el mensaje
            $messageId = '<' . uniqid() . '@' . parse_url(config('app.url'), PHP_URL_HOST) . '>';

            $email = [
                'html' => $htmlContent,
                'text' => strip_tags($htmlContent),
                'subject' => $subject,
                'from' => [
                    'name' => 'Notificaciones SAEF',
                    'email' => $this->from
                ],
                'to' => [
                    [
                        'name' => $to,
                        'email' => $to
                    ]
                ],
                'headers' => [
                    'Message-ID' => $messageId,
                    'Date' => date('r'),
                    'X-Mailer' => 'SendPulse API',
                    'X-Priority' => '1',
                    'X-MSMail-Priority' => 'High',
                    'Importance' => 'High',
                    'List-Unsubscribe' => '<mailto:unsubscribe@uefrayluisamigosf.com>',
                    'Precedence' => 'bulk',
                    'X-Auto-Response-Suppress' => 'OOF, AutoReply',
                    'X-Sender' => $this->from,
                    'X-Entity-Ref-ID' => uniqid(),
                    'Feedback-ID' => 'notification:uefrayluisamigosf.com',
                    'X-Campaign-ID' => 'notification',
                    'X-Report-Abuse' => 'Please report abuse to abuse@uefrayluisamigosf.com',
                    'X-Report-Spam' => 'Please report spam to spam@uefrayluisamigosf.com',
                    'Reply-To' => $this->from,
                    'Return-Path' => $this->from,
                    'DKIM-Signature' => 'v=1; a=rsa-sha256; c=relaxed/relaxed; d=uefrayluisamigosf.com; s=default; t=' . time() . ';',
                    'SPF' => 'v=spf1 include:_spf.sendpulse.com ~all',
                    'DMARC' => 'v=DMARC1; p=reject; rua=mailto:dmarc@uefrayluisamigosf.com',
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'SAMEORIGIN',
                    'X-XSS-Protection' => '1; mode=block'
                ]
            ];

            if ($cc) {
                $email['cc'] = is_array($cc) ? array_map(function ($email) {
                    return [
                        'name' => $email,
                        'email' => $email
                    ];
                }, $cc) : [
                    [
                        'name' => $cc,
                        'email' => $cc
                    ]
                ];
            }

            if ($bcc) {
                $email['bcc'] = is_array($bcc) ? array_map(function ($email) {
                    return [
                        'name' => $email,
                        'email' => $email
                    ];
                }, $bcc) : [
                    [
                        'name' => $bcc,
                        'email' => $bcc
                    ]
                ];
            }

            // Filtrar valores nulos o vacíos de CC y BCC
            if (isset($email['cc'])) {
                $email['cc'] = array_filter($email['cc'], function ($item) {
                    return !empty($item['email']) && filter_var($item['email'], FILTER_VALIDATE_EMAIL);
                });
                if (empty($email['cc'])) {
                    unset($email['cc']);
                }
            }

            if (isset($email['bcc'])) {
                $email['bcc'] = array_filter($email['bcc'], function ($item) {
                    return !empty($item['email']) && filter_var($item['email'], FILTER_VALIDATE_EMAIL);
                });
                if (empty($email['bcc'])) {
                    unset($email['bcc']);
                }
            }

            // Intentar el envío con reintentos
            $maxRetries = 3;
            $retryCount = 0;
            $lastError = null;

            while ($retryCount < $maxRetries) {
                try {
                    $result = $this->apiClient->smtpSendMail($email);
                    if ($result) {
                        return $this->handleSuccessfulResponse($result, $to, $subject, $htmlContent, $cc, $bcc, $delayTime);
                    }
                    break;
                } catch (\Exception $e) {
                    $lastError = $e;
                    $retryCount++;
                    if ($retryCount < $maxRetries) {
                        sleep(2); // Esperar 2 segundos antes de reintentar
                    }
                }
            }

            if ($lastError) {
                throw $lastError;
            }

            return [
                'success' => false,
                'message' => 'Error al enviar el correo: No se recibió respuesta del servidor',
                'data' => $result ?? null
            ];
        } catch (\Exception $e) {
            $this->logError('Excepción al enviar email', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'scheduled' => $delayTime ? true : false,
                'scheduled_time' => $delayTime ? $delayTime->format('Y-m-d H:i:s') : null
            ]);
            return [
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Maneja la respuesta exitosa del envío de correo
     */
    protected function handleSuccessfulResponse($response, string $to, string $subject, string $htmlContent, $cc, $bcc, ?Carbon $delayTime): array
    {
        // El ID de SendPulse suele venir en el campo 'id' de la respuesta
        $resendId = null;
        if (is_array($response) && isset($response['id'])) {
            $resendId = $response['id'];
        } elseif (is_object($response) && isset($response->id)) {
            $resendId = $response->id;
        }

        // Si no se encuentra el ID, generamos uno local pero registramos la respuesta para depuración
        $resendId = $resendId ?: uniqid('sp_');

        // Guardar el email en la base de datos
        $email = ResendEmail::create([
            'resend_id' => $resendId,
            'from' => $this->from,
            'to' => $to,
            'subject' => $subject,
            'html' => $htmlContent,
            'cc' => $cc,
            'bcc' => $bcc,
            'status' => $delayTime ? 'scheduled' : 'sent',
            'sent_at' => $delayTime ? null : now()
        ]);

        // Programar 3 jobs para actualizar el estado del email
        if (!$delayTime) {
            // Primer job: 1 minuto después del envío
            dispatch(function () use ($email) {
                $this->getEmailStatus($email->resend_id);
            })->delay(now()->addMinute());

            // Segundo job: 5 minutos después del envío
            dispatch(function () use ($email) {
                $this->getEmailStatus($email->resend_id);
            })->delay(now()->addMinutes(5));

            // Tercer job: 15 minutos después del envío
            dispatch(function () use ($email) {
                $this->getEmailStatus($email->resend_id);
            })->delay(now()->addMinutes(15));
        }

        return [
            'success' => true,
            'message' => $delayTime
                ? 'Correo programado para envío exitosamente'
                : 'Correo enviado exitosamente',
            'data' => $response,
            'scheduled' => $delayTime ? true : false,
            'scheduled_time' => $delayTime ? $delayTime->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Envía múltiples correos con retraso incremental
     */
    public function sendBulkWithDelay(array $emails, int $initialDelay = 60, int $incrementDelay = 40): array
    {
        $results = [];
        $currentDelay = $initialDelay;

        foreach ($emails as $email) {
            $delayTime = Carbon::now()->addSeconds($currentDelay);
            $result = $this->send(
                $email['to'],
                $email['subject'],
                $email['html'],
                $delayTime
            );

            $results[] = [
                'email' => $email['to'],
                'scheduled_time' => $delayTime->format('Y-m-d H:i:s'),
                'result' => $result
            ];

            $currentDelay += $incrementDelay;
        }

        return [
            'success' => true,
            'message' => 'Proceso de envío masivo iniciado',
            'data' => $results
        ];
    }

    /**
     * Add subscriber to address book
     *
     * @param string $email
     * @param int $addressBookId
     * @param array $variables
     * @return array
     */
    public function addSubscriber(string $email, int $addressBookId, array $variables = []): array
    {
        try {
            $result = $this->apiClient->addEmails($addressBookId, [
                [
                    'email' => $email,
                    'variables' => $variables
                ]
            ]);

            return [
                'success' => true,
                'message' => 'Suscriptor agregado exitosamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            Log::error('SendPulse Add Subscriber Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al agregar suscriptor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get list of address books
     *
     * @return array
     */
    public function getAddressBooks(): array
    {
        try {
            $result = $this->apiClient->listAddressBooks();
            return [
                'success' => true,
                'data' => $result
            ];
        } catch (\Exception $e) {
            Log::error('SendPulse Get Address Books Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener libros de direcciones: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a new address book
     *
     * @param string $bookName
     * @return array
     */
    public function createAddressBook(string $bookName): array
    {
        try {
            $result = $this->apiClient->createAddressBook($bookName);
            return [
                'success' => true,
                'message' => 'Libro de direcciones creado exitosamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            Log::error('SendPulse Create Address Book Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear libro de direcciones: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mejora el contenido del email para reducir la probabilidad de spam
     */
    protected function improveEmailContent(string $htmlContent): string
    {
        // Asegurar que el contenido tenga una estructura HTML válida
        if (!str_contains($htmlContent, '<html')) {
            $htmlContent = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email</title>
            </head>
            <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                ' . $htmlContent . '
            </body>
            </html>';
        }

        // Agregar texto plano alternativo
        $textContent = strip_tags($htmlContent);

        // Asegurar que el contenido tenga un balance texto/imagen
        if (substr_count($htmlContent, '<img') > substr_count($htmlContent, '</p>')) {
            $htmlContent .= '<p style="display:none;">' . $textContent . '</p>';
        }

        return $htmlContent;
    }

    /**
     * Obtiene el estado de un email enviado
     *
     * @param string $messageId ID del mensaje
     * @return string|null Estado del email o null si no se puede obtener
     */
    public function getEmailStatus(string $messageId): ?string
    {
        try {
            // Asegurarnos de que el cliente API esté inicializado
            if (!$this->apiClient) {
                $this->initializeApiClient();
            }

            // Intentar obtener el estado con reintentos
            $maxRetries = 3;
            $retryCount = 0;
            $lastError = null;
            $baseDelay = 1; // Delay base en segundos

            while ($retryCount < $maxRetries) {
                try {
                    // Obtener la lista de emails y filtrar por el ID del mensaje
                    $result = $this->apiClient->smtpListEmails(
                        limit: 100,
                        offset: 0,
                        fromDate: date('Y-m-d', strtotime('-7 days')), // Últimos 7 días
                        toDate: date('Y-m-d'),
                        sender: $this->from,
                        recipient: '',
                        country: 'off'
                    );

                    // La respuesta del SDK puede venir envuelta en 'data' o ser el array directo
                    $data = isset($result['data']) ? $result['data'] : $result;

                    if (!is_array($data)) {
                        $this->logError('Respuesta inválida al obtener estado del email (data no es array)', [
                            'message_id' => $messageId,
                            'result' => $result
                        ]);
                        return null;
                    }

                    // Buscar el email específico por su ID
                    $email = collect($data)->first(function ($email) use ($messageId) {
                        return ($email['id'] ?? '') === $messageId;
                    });

                    if (!$email) {
                        return null;
                    }

                    // Mapear el estado de SendPulse a nuestro formato
                    return match ($email['status'] ?? '') {
                        'delivered' => 'delivered',
                        'opened' => 'opened',
                        'clicked' => 'clicked',
                        'bounced' => 'bounced',
                        'complained' => 'complained',
                        'unsubscribed' => 'unsubscribed',
                        'rejected' => 'rejected',
                        'sent' => 'sent',
                        'pending' => 'pending',
                        default => null
                    };
                } catch (\Exception $e) {
                    $lastError = $e;
                    $retryCount++;

                    // Calcular el tiempo de espera exponencial
                    $delay = $baseDelay * pow(2, $retryCount - 1);

                    // Si es un error de rate limit, esperar más tiempo
                    if (strpos($e->getMessage(), 'rate_limit_exceeded') !== false) {
                        $delay = max($delay, 2); // Mínimo 2 segundos para rate limits
                    }

                    if ($retryCount < $maxRetries) {
                        $this->logError('Reintentando obtener estado del email', [
                            'message_id' => $messageId,
                            'attempt' => $retryCount,
                            'delay' => $delay,
                            'error' => $e->getMessage()
                        ]);

                        sleep($delay); // Esperar con delay exponencial
                    }
                }
            }

            if ($lastError) {
                throw $lastError;
            }

            return null;
        } catch (\Exception $e) {
            $this->logError('Error al obtener estado del email', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Obtiene la lista de emails de SendPulse
     */
    public function getEmailsList($fromDate, $toDate, $limit = 100, $offset = 0): array
    {
        try {
            $result = $this->apiClient->smtpListEmails(
                limit: $limit,
                offset: $offset,
                fromDate: $fromDate,
                toDate: $toDate,
                sender: '',
                recipient: '',
                country: 'off'
            );

            if (!isset($result['data'])) {
                $this->logError('Respuesta inválida de SendPulse', [
                    'result' => $result
                ]);
                return ['data' => []];
            }

            return $result;
        } catch (\Exception $e) {
            $this->logError('Error al obtener lista de emails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ]);
            return ['data' => []];
        }
    }
}
