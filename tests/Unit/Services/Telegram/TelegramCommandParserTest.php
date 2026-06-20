<?php

namespace Tests\Unit\Services\Telegram;

use App\Services\Telegram\TelegramCommandParser;
use Tests\TestCase;

class TelegramCommandParserTest extends TestCase
{
  public function test_it_parses_plain_commands_and_arguments(): void
  {
    $parser = new TelegramCommandParser();

    $this->assertSame(['resumen', null], $parser->parse('/resumen'));
    $this->assertSame(['evento', '123'], $parser->parse('/evento 123'));
    $this->assertSame(['evento', '123'], $parser->parse('/evento_123'));
    $this->assertSame(['start', 'abc-123'], $parser->parse('/start abc-123'));
    $this->assertSame(['ayuda', null], $parser->parse('hola'));
  }
}
