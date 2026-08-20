<?php

namespace Tests\Feature\Lms;

use App\Services\EmailDeliveryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailDeliveryTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Aislar: MAIL_MODE_TESTER=true en .env redirige a un buzón de pruebas.
        config(['mail.mode_tester' => false]);
    }

    /** @test */
    public function uses_sendpulse_as_primary_channel(): void
    {
        Http::fake([
            'https://api.sendpulse.com/oauth/access_token' => Http::response(['access_token' => 'token-de-prueba']),
            'https://api.sendpulse.com/smtp/emails' => Http::response(['result' => 'ok']),
            'https://api.resend.com/*' => Http::response([], 500),
        ]);

        $result = (new EmailDeliveryService)->send('alumno@escuela.com', 'Asunto', '<p>Hola</p>');

        $this->assertTrue($result['success']);
        $this->assertEquals('sendpulse', $result['channel']);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'sendpulse.com/smtp/emails'));
        Http::assertNotSent(fn ($req) => str_contains($req->url(), 'resend.com'));
    }

    /** @test */
    public function falls_back_to_resend_when_sendpulse_fails(): void
    {
        Http::fake([
            'https://api.sendpulse.com/oauth/access_token' => Http::response([], 500),
            'https://api.sendpulse.com/smtp/emails' => Http::response([], 500),
            'https://api.resend.com/emails' => Http::response(['id' => 'email-123'], 200),
        ]);

        $result = (new EmailDeliveryService)->send('alumno@escuela.com', 'Asunto', '<p>Hola</p>');

        $this->assertTrue($result['success']);
        $this->assertEquals('resend', $result['channel']);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'resend.com/emails'));
    }

    /** @test */
    public function returns_failure_without_throwing_when_all_channels_fail(): void
    {
        Http::fake([
            'https://api.sendpulse.com/oauth/access_token' => Http::response([], 500),
            'https://api.sendpulse.com/smtp/emails' => Http::response([], 500),
            'https://api.resend.com/emails' => Http::response([], 500),
        ]);

        $result = (new EmailDeliveryService)->send('alumno@escuela.com', 'Asunto', '<p>Hola</p>');

        $this->assertFalse($result['success']);
        $this->assertNull($result['channel']);
    }

    /** @test */
    public function skips_sendpulse_when_not_configured(): void
    {
        config(['services.sendpulse.client_id' => null, 'services.sendpulse.client_secret' => null]);

        Http::fake([
            'https://api.resend.com/emails' => Http::response(['id' => 'email-123'], 200),
        ]);

        $result = (new EmailDeliveryService)->send('alumno@escuela.com', 'Asunto', '<p>Hola</p>');

        $this->assertTrue($result['success']);
        $this->assertEquals('resend', $result['channel']);

        Http::assertNotSent(fn ($req) => str_contains($req->url(), 'sendpulse.com'));
    }

    /** @test */
    public function tester_mode_redirects_to_tester_mailbox(): void
    {
        config(['mail.mode_tester' => true, 'mail.address_tester' => 'tester@saefl.com']);

        Http::fake([
            'https://api.sendpulse.com/oauth/access_token' => Http::response(['access_token' => 'token-de-prueba']),
            'https://api.sendpulse.com/smtp/emails' => Http::response(['result' => 'ok']),
        ]);

        (new EmailDeliveryService)->send('alumno@escuela.com', 'Asunto', '<p>Hola</p>');

        Http::assertSent(function ($req) {
            if (! str_contains($req->url(), 'sendpulse.com/smtp/emails')) {
                return false;
            }

            return ($req['email']['to'][0]['email'] ?? null) === 'tester@saefl.com';
        });
    }
}
