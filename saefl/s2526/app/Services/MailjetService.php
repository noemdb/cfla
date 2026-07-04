<?php

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class MailjetService
{
    protected $client;
    protected $defaultFrom;

    public function __construct()
    {
        // Cambio en nombre de variable para mayor claridad
        $this->client = new Client(
            config('services.mailjet.public_key'),
            config('services.mailjet.private_key'),
            true,
            ['version' => 'v3.1']
        );

        $this->defaultFrom = [
            'Email' => config('services.mailjet.default_from_email'),
            'Name' => config('services.mailjet.default_from_name')
        ];        
    }

    public function sendEmail(array $data)
    {
        $defaultData = [
            'From' => $this->defaultFrom,
            'Subject' => '',
            'TextPart' => '',
            'HTMLPart' => ''
        ];

        $message = array_merge($defaultData, $data);

        $body = [
            'Messages' => [$message]
        ];

        $response = $this->client->post(Resources::$Email, ['body' => $body]);

        return [
            'success' => $response->success(),
            'data' => $response->getData(),
            'status' => $response->getStatus()
        ];
    }

    public function sendBulkEmails(array $messages)
    {
        $body = [
            'Messages' => $messages
        ];

        $response = $this->client->post(Resources::$Email, ['body' => $body]);

        return [
            'success' => $response->success(),
            'data' => $response->getData(),
            'status' => $response->getStatus()
        ];
    }
}