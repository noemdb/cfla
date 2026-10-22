<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // TimetablePdfController eleva set_time_limit(300) para generar
        // reportes; en una suite larga ese límite persiste y termina matando
        // el proceso de PHPUnit. La suite completa no debe tener ese tope.
        @set_time_limit(0);
    }
}
