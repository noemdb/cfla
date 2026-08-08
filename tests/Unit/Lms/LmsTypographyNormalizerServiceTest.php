<?php

namespace Tests\Unit\Lms;

use App\Services\Lms\LmsTypographyNormalizerService;
use PHPUnit\Framework\TestCase;

/**
 * Spec "Armonía tipográfica generateSlideHtmlTags" (F3) — normalizador
 * determinista de escala. Conservador: solo baja tamaños, nunca sube.
 */
class LmsTypographyNormalizerServiceTest extends TestCase
{
    private LmsTypographyNormalizerService $normalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->normalizer = new LmsTypographyNormalizerService();
    }

    public function test_clamps_title_sizes_to_text_lg(): void
    {
        $html = '<h3 class="text-3xl font-bold text-emerald-700">Título</h3>';
        $out = $this->normalizer->normalize($html);

        $this->assertStringNotContainsString('text-3xl', $out);
        $this->assertStringContainsString('text-lg font-bold', $out);
    }

    public function test_clamps_all_oversized_text_classes(): void
    {
        $html = '<p class="text-4xl">a</p><p class="text-2xl">b</p><p class="text-xl">c</p>';
        $out = $this->normalizer->normalize($html);

        $this->assertSame(3, substr_count($out, 'text-lg')); // un text-lg por elemento
        // Las tres clases grandes se unifican en text-lg (sin duplicados por clase).
        $this->assertStringNotContainsString('text-4xl', $out);
        $this->assertStringNotContainsString('text-2xl', $out);
        $this->assertStringNotContainsString('text-xl', $out);
    }

    public function test_preserves_stat_card_text_2xl_with_extrabold(): void
    {
        // Patrón stat card: text-2xl + font-extrabold se conserva (token 'stat').
        $html = '<p class="text-2xl font-extrabold text-amber-800">95%</p>';
        $out = $this->normalizer->normalize($html);

        $this->assertStringContainsString('text-2xl font-extrabold', $out);
    }

    public function test_clamps_stat_number_when_without_extrabold(): void
    {
        $html = '<p class="text-2xl text-amber-800">95</p>';
        $out = $this->normalizer->normalize($html);

        $this->assertStringNotContainsString('text-2xl', $out);
        $this->assertStringContainsString('text-lg', $out);
    }

    public function test_clamps_arbitrary_pixel_sizes(): void
    {
        $html = '<p class="text-[22px]">grande</p><p class="text-[17px]">normal</p>';
        $out = $this->normalizer->normalize($html);

        $this->assertStringNotContainsString('text-[22px]', $out);
        $this->assertStringContainsString('text-[17px]', $out); // dentro de escala: intacto
    }

    public function test_clamps_padding_and_shadows(): void
    {
        $html = '<div class="p-6 shadow-lg">x</div><div class="px-5 py-8 shadow-xl">y</div>';
        $out = $this->normalizer->normalize($html);

        $this->assertStringContainsString('p-4', $out);
        $this->assertStringContainsString('px-4', $out);
        $this->assertStringContainsString('py-4', $out);
        $this->assertStringNotContainsString('p-6', $out);
        $this->assertStringNotContainsString('px-5', $out);
        $this->assertStringNotContainsString('shadow-lg', $out);
        $this->assertStringNotContainsString('shadow-xl', $out);
        $this->assertStringContainsString('shadow-sm', $out);
    }

    public function test_preserves_classes_within_scale(): void
    {
        $html = '<div class="rounded-xl p-4 shadow-sm border border-stone-200 text-sm text-gray-700">ok</div>';
        $out = $this->normalizer->normalize($html);

        $this->assertSame($html, $out);
    }

    public function test_preserves_non_size_classes_and_progress_bar(): void
    {
        $html = '<div class="h-1.5 bg-gray-200 rounded-full"><div class="h-1.5 bg-amber-500 rounded-full" style="width:70%"></div></div>';
        $out = $this->normalizer->normalize($html);

        $this->assertStringContainsString('bg-gray-200', $out);
        $this->assertStringContainsString('style="width:70%"', $out);
    }

    public function test_removes_duplicate_classes_after_clamp(): void
    {
        // text-3xl y text-xl en el mismo elemento → ambas se claman a text-lg (una sola).
        $html = '<p class="text-3xl text-xl">x</p>';
        $out = $this->normalizer->normalize($html);

        $this->assertSame(1, substr_count($out, 'text-lg'));
    }
}
