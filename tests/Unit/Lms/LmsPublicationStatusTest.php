<?php

namespace Tests\Unit\Lms;

use App\Services\Lms\LmsPublicationStatus;
use PHPUnit\Framework\TestCase;

/**
 * LmsPublicationStatus — P5: etiquetas/clases del estado de publicación LMS
 * (antes duplicado en los 3 controllers de impresión).
 */
class LmsPublicationStatusTest extends TestCase
{
    public function test_labels_are_localized(): void
    {
        $this->assertSame('Publicado', LmsPublicationStatus::label('PUBLISHED'));
        $this->assertSame('Programado', LmsPublicationStatus::label('SCHEDULED'));
        $this->assertSame('Archivado', LmsPublicationStatus::label('ARCHIVED'));
        $this->assertSame('N.PUB', LmsPublicationStatus::label(null));
        $this->assertSame('Borrador', LmsPublicationStatus::label('DRAFT'));
        $this->assertSame('Borrador', LmsPublicationStatus::label('cualquier_cosa'));
    }

    public function test_css_classes_match_the_print_views(): void
    {
        $this->assertSame('estado-pub', LmsPublicationStatus::cssClass('PUBLISHED'));
        $this->assertSame('estado-prog', LmsPublicationStatus::cssClass('SCHEDULED'));
        $this->assertSame('estado-arc', LmsPublicationStatus::cssClass('ARCHIVED'));
        $this->assertSame('estado-npub', LmsPublicationStatus::cssClass(null));
        $this->assertSame('estado-draft', LmsPublicationStatus::cssClass('DRAFT'));
    }
}
