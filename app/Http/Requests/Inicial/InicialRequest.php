<?php

namespace App\Http\Requests\Inicial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Base de las peticiones del módulo de Educación Inicial.
 *
 * ─────────────────────────────────────────────────────────────────
 * POR QUÉ EXISTE (y no se usa `FormRequest::createFrom()`)
 * ─────────────────────────────────────────────────────────────────
 * `Illuminate\Http\Request::createFrom()` está firmado como
 * `createFrom(self $from, ...)`, es decir, exige OTRO `Request`. Un componente
 * Livewire no lo es: `EiplanningwkRequest::createFrom($this)` compila pero
 * revienta en tiempo de ejecución con `TypeError: Argument #1 must be of type
 * Illuminate\Http\Request, App\Livewire\Inicial\EiplanningwkComponent given`.
 *
 * `fromInput()` construye la petición a mano: coloca los datos del formulario
 * en la bolsa `request` (la que `getInputSource()` devuelve para POST), conecta
 * el contenedor —imprescindible para `validateResolved()`, que hace
 * `$container->make(ValidationFactory::class)`— y el resolutor de usuario.
 *
 * Sin `setUserResolver()` el `authorize()` recibiría `null` y devolvería 403
 * siempre: `Request::user()` por defecto es un resolver vacío.
 *
 * ─────────────────────────────────────────────────────────────────
 * POLÍTICA DE ACCESO
 * ─────────────────────────────────────────────────────────────────
 * Idéntica a la del middleware `isInicial` (app/Http/Kernel.php) y a la del
 * `authorizeInicial()` de los componentes: docente de Inicial **o**
 * administrador. Se replica en cada nivel a propósito (defensa en profundidad),
 * no por descuido.
 */
abstract class InicialRequest extends FormRequest
{
    /**
     * Propiedad del componente Livewire que contiene el formulario
     * (`eiplanningwk`, `eiplanningwsummary`, …).
     *
     * La declaran las subclases; `fromInput()` la usa para anidar la entrada
     * y `field()` para leer de `$this->validated()`. Debe coincidir con el
     * prefijo de las claves de `rules()`.
     */
    public const PREFIX = '';

    /**
     * Construye la petición a partir del array de un formulario Livewire.
     *
     * El array se ANIDA bajo `{@see PREFIX}`: así las claves de validación y
     * los mensajes llegan al ErrorBag como `eiplanningwk.ffinal`, que es la
     * ruta que leen los `@error(...)` de la vista. Pasar el array plano
     * produciría errores con la clave `ffinal` que existirían pero no se
     * mostrarían.
     *
     * @param  array<string, mixed>  $input  Array plano del formulario.
     */
    public static function fromInput(array $input): static
    {
        $request = new static(
            [],                              // query
            [static::PREFIX => $input],      // request → fuente de `input()`/`all()` en POST
            [],                              // attributes
            [],                              // cookies
            [],                              // files
            ['REQUEST_METHOD' => 'POST']
        );

        $request->setContainer(app());
        $request->setUserResolver(fn () => Auth::user());

        // Sin redirector: en Livewire la excepción de validación la captura el
        // propio componente y los errores se vuelcan en su ErrorBag. Fijar el
        // Redirector obligaría a `previous()` con sesión en un contexto donde
        // no hace falta.
        return $request;
    }

    /**
     * Lee un valor de `$this->validated()` usando el prefijo de la subclase.
     */
    protected function field(string $key): mixed
    {
        return data_get($this->validated(), static::PREFIX.'.'.$key);
    }

    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user && ($user->isInicial() || $user->is_admin));
    }

    /**
     * URL a la que "redirigir" cuando la validación falla.
     *
     * El `getRedirectUrl()` de Laravel es incondicional:
     * `$this->redirector->getUrlGenerator()` — si `setRedirector()` no se ha
     * llamado, revienta con "Call to a member function getUrlGenerator() on
     * null" ANTES de que la ValidationException llegue a Livewire, que es
     * quien de verdad gestiona el fallo.
     *
     * Fijar el Redirector tampoco sirve: caería en `previous()`, que lee la
     * sesión. Aquí la URL no se usa nunca —Livewire captura la excepción y
     * vuelca los mensajes en su ErrorBag—, así que devolvemos algo inofensivo
     * y sin dependencias.
     */
    protected function getRedirectUrl(): string
    {
        return $this->redirect ?: url('/');
    }
}
