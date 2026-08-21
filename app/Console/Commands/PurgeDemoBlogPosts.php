<?php

namespace App\Console\Commands;

use App\Models\Journal\Blog;
use App\Models\Journal\BlogInformation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * F-004 — purga reversible de los posts demo del seed original
 * (slugs: vivamus-vestibulum, vestibulum-commodo, nam-dui-mi,
 * phasellus-ultrices, donec-nec-justo, morbi-in-sem).
 *
 * Genera un backup JSON en storage/app/backups/ antes de borrar.
 * Uso:
 *   php artisan blog:purge-demo --dry-run   # reporta sin borrar
 *   php artisan blog:purge-demo             # backup + borra
 *   php artisan blog:purge-demo --restore=<archivo.json>  # restaura
 */
class PurgeDemoBlogPosts extends Command
{
  protected $signature = 'blog:purge-demo
    {--dry-run : Reportar sin borrar}
    {--restore= : Restaurar desde un backup JSON en storage/app/backups}';

  protected $description = 'Purga (o restaura) los posts demo del blog con backup JSON reversible';

  private const DEMO_SLUG_PREFIXES = [
    'vivamus-vestibulum', 'vestibulum-commodo', 'nam-dui-mi',
    'phasellus-ultrices', 'donec-nec-justo', 'morbi-in-sem',
  ];

  public function handle(): int
  {
    if ($restore = $this->option('restore')) {
      return $this->restore($restore);
    }

    $demoInfos = BlogInformation::where(function ($q) {
      foreach (self::DEMO_SLUG_PREFIXES as $prefix) {
        $q->orWhere('slug', 'like', $prefix . '%');
      }
    })->get();

    if ($demoInfos->isEmpty()) {
      $this->info('No hay posts demo para purgar.');

      return self::SUCCESS;
    }

    $blogIds = $demoInfos->pluck('blog_id')->unique();
    $backup = [
      'generated_at' => now()->toDateTimeString(),
      'command' => 'blog:purge-demo',
      'blog_informations' => $demoInfos->toArray(),
      'blogs' => Blog::whereIn('id', $blogIds)->get()->toArray(),
    ];

    $filename = 'blog-demo-backup-' . now()->format('Ymd-His') . '.json';
    Storage::disk('local')->put('backups/' . $filename, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $this->info(sprintf('Backup creado: storage/app/backups/%s', $filename));
    $this->table(
      ['blog_id', 'slug', 'language_id', 'title'],
      $demoInfos->map(fn ($i) => [$i->blog_id, $i->slug, $i->language_id, \Illuminate\Support\Str::limit((string) $i->title, 40)])
    );

    if ($this->option('dry-run')) {
      $this->warn('Modo dry-run: no se borró nada.');

      return self::SUCCESS;
    }

    BlogInformation::whereIn('id', $demoInfos->pluck('id'))->delete();
    Blog::whereIn('id', $blogIds)->delete();

    $this->info(sprintf('Purgados %d posts demo (blog_informations) y %d blogs.', $demoInfos->count(), $blogIds->count()));

    return self::SUCCESS;
  }

  private function restore(string $file): int
  {
    $path = str_starts_with($file, 'backups/') ? $file : 'backups/' . $file;

    if (!Storage::disk('local')->exists($path)) {
      $this->error("Backup no encontrado: {$path}");

      return self::FAILURE;
    }

    $backup = json_decode(Storage::disk('local')->get($path), true);

    if (!is_array($backup)) {
      $this->error('Backup inválido.');

      return self::FAILURE;
    }

    foreach ($backup['blogs'] ?? [] as $row) {
      $model = Blog::find($row['id']) ?? new Blog();
      $model->forceFill($row)->save();
    }
    foreach ($backup['blog_informations'] ?? [] as $row) {
      $model = BlogInformation::find($row['id']) ?? new BlogInformation();
      $model->forceFill($row)->save();
    }

    $this->info(sprintf('Restaurados %d posts demo desde %s.', count($backup['blog_informations'] ?? []), $path));

    return self::SUCCESS;
  }
}
