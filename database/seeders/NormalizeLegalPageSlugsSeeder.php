<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class NormalizeLegalPageSlugsSeeder extends Seeder
{
  public function run()
  {
    $targets = [
      39 => [
        'title' => 'Política de privacidad',
        'slug' => 'politica-de-privacidad',
      ],
      30 => [
        'title' => 'Términos y condiciones',
        'slug' => 'terminos-y-condiciones',
      ],
      40 => [
        'title' => 'Eliminación de datos',
        'slug' => 'eliminacion-de-datos',
      ],
      41 => [
        'title' => 'Política de reembolsos',
        'slug' => 'politica-de-reembolsos',
      ],
      42 => [
        'title' => 'Política de cookies',
        'slug' => 'politica-de-cookies',
      ],
    ];

    DB::transaction(function () use ($targets) {
      foreach ($targets as $pageContentId => $target) {
        $pageContent = DB::table('page_contents')->where('id', $pageContentId)->first();

        if (!$pageContent) {
          throw new RuntimeException("Legal page content {$pageContentId} was not found.");
        }

        $conflict = DB::table('page_contents')
          ->whereRaw('BINARY slug = ?', [$target['slug']])
          ->where('id', '<>', $pageContentId)
          ->first();

        if ($conflict) {
          throw new RuntimeException("Slug {$target['slug']} already exists on page_content {$conflict->id}.");
        }

        DB::table('page_contents')
          ->where('id', $pageContentId)
          ->update([
            'title' => $target['title'],
            'slug' => $target['slug'],
            'updated_at' => now(),
          ]);
      }
    });
  }
}
