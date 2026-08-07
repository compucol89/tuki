<?php

namespace Tests\Unit\Services\EventAi;

use App\Services\EventAi\EventAiDraftPostProcessor;
use Tests\TestCase;

class EventAiDraftPostProcessorTest extends TestCase
{
    private EventAiDraftPostProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new EventAiDraftPostProcessor();
    }

    public function test_filters_faq_with_price_not_available(): void
    {
        $faq = [
            ['question' => '¿Cuál es el precio?', 'answer' => 'El precio no está disponible en los datos del evento.'],
            ['question' => '¿Dónde se realiza?', 'answer' => 'En La Troja, Niceto Vega 5646, Buenos Aires.'],
        ];

        $result = $this->processor->filterFaq($faq);

        $this->assertCount(1, $result);
        $this->assertSame('¿Dónde se realiza?', $result[0]['question']);
    }

    public function test_filters_faq_with_no_fue_informado(): void
    {
        $faq = [
            ['question' => '¿Cuál es el precio?', 'answer' => 'El precio no fue informado por el organizador.'],
        ];

        $this->assertSame([], $this->processor->filterFaq($faq));
    }

    public function test_filters_faq_with_internal_editorial_notes(): void
    {
        foreach (['debe confirmarse antes de publicar', 'pendiente de confirmación', 'sujeto a confirmación', 'se desconoce el horario', 'la edad mínima no informada', 'no contamos con información'] as $answer) {
            $result = $this->processor->filterFaq([
                ['question' => '¿Hay condiciones?', 'answer' => $answer],
            ]);

            $this->assertSame([], $result, "debería filtrar: {$answer}");
        }
    }

    public function test_filters_faq_when_question_exposes_missing_data(): void
    {
        $faq = [
            ['question' => '¿Cuál es la edad mínima? (no informada)', 'answer' => 'Consultá con el organizador.'],
            ['question' => '¿Qué música hay?', 'answer' => 'Salsa, merengue y reggaetón.'],
        ];

        $result = $this->processor->filterFaq($faq);

        $this->assertCount(1, $result);
        $this->assertSame('¿Qué música hay?', $result[0]['question']);
    }

    public function test_drops_empty_faq_items(): void
    {
        $faq = [
            ['question' => '', 'answer' => 'algo'],
            ['question' => '¿Pregunta?', 'answer' => ''],
            ['question' => '¿Válida?', 'answer' => 'Respuesta real y verificada.'],
        ];

        $result = $this->processor->filterFaq($faq);

        $this->assertCount(1, $result);
    }

    public function test_keeps_valid_faq(): void
    {
        $faq = [
            ['question' => '¿Cuándo es?', 'answer' => 'El sábado 8 de agosto desde las 23:50.'],
            ['question' => '¿Dónde?', 'answer' => 'En La Troja, Niceto Vega 5646.'],
        ];

        $this->assertCount(2, $this->processor->filterFaq($faq));
    }

    public function test_sanitize_fills_missing_fallbacks(): void
    {
        $generated = [
            'content' => [
                'public_title' => 'MINITK Dosmilera',
                'short_description' => 'Noche dosmilera en Buenos Aires.',
            ],
            'faq' => [
                ['question' => '¿Precio?', 'answer' => 'El precio no está disponible.'],
            ],
        ];

        $result = $this->processor->sanitize($generated, []);

        $this->assertSame([], $result['faq']);
        $this->assertStringContainsString('MINITK Dosmilera', $result['seo']['ai_search_summary']);
        $this->assertSame('MINITK Dosmilera', $result['social']['open_graph_title']);
        $this->assertGreaterThanOrEqual(6, count($result['review_checklist']));
    }

    public function test_contains_internal_note_detects_banned_answers(): void
    {
        $generated = [
            'faq' => [
                ['question' => '¿Condiciones?', 'answer' => 'Sujeto a confirmación del organizador.'],
            ],
        ];

        $this->assertTrue($this->processor->containsInternalNote($generated));
        $this->assertFalse($this->processor->containsInternalNote(['faq' => [['question' => '¿Dónde?', 'answer' => 'En La Troja.']]]));
    }
}
