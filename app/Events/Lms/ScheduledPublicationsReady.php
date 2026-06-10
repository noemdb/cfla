<?php

namespace App\Events\Lms;

use Illuminate\Foundation\Events\Dispatchable;

class ScheduledPublicationsReady
{
    use Dispatchable;

    public function __construct(
        public int $publicationsActivated,
    ) {}
}
