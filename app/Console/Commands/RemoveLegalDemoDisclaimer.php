<?php

namespace App\Console\Commands;

use App\Support\LegalPageFooter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveLegalDemoDisclaimer extends Command
{
  protected $signature = 'legal:remove-demo-disclaimer {--dry-run : Solo mostrar páginas afectadas}';

  protected $description = 'Quita el pie demo de asesoría legal en páginas CMS y aplica el disclaimer operativo de Tukipass';

  public function handle(): int
  {
    $rows = DB::table('page_contents')
      ->where(function ($query) {
        $query
          ->where('content', 'like', '%asesoría legal%')
          ->orWhere('content', 'like', '%asesoria legal%')
          ->orWhere('content', 'like', '%publicación definitiva%');
      })
      ->get(['id', 'slug', 'title', 'content']);

    if ($rows->isEmpty()) {
      $this->info('No hay páginas con el texto demo de asesoría legal.');

      return self::SUCCESS;
    }

    $this->table(
      ['id', 'slug', 'title'],
      $rows->map(fn ($row) => [$row->id, $row->slug, $row->title])->all()
    );

    if ($this->option('dry-run')) {
      $this->info('Dry-run: no se modificó la base de datos.');

      return self::SUCCESS;
    }

    $footer = LegalPageFooter::publishedFooterHtml();
    $updated = 0;

    foreach ($rows as $row) {
      $content = LegalPageFooter::stripDemoDisclaimer((string) $row->content);

      if (stripos($content, 'Importante:</strong> Tukipass no organiza') === false) {
        $content = rtrim($content) . "\n\n" . $footer;
      }

      DB::table('page_contents')
        ->where('id', $row->id)
        ->update([
          'content' => $content,
          'updated_at' => now(),
        ]);

      $updated++;
    }

    $this->info("Páginas actualizadas: {$updated}");

    return self::SUCCESS;
  }
}
