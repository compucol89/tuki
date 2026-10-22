#!/usr/bin/env bash
# Verifica que skills-lock.json coincida con el contenido real y que los
# symlinks de .claude/skills apunten a archivos existentes.
#
# Por defecto, las skills ausentes se informan pero no fallan (un checkout de CI
# no tiene el store local .agents/skills). Usar STRICT=1 para exigir que las 37
# skills estén presentes, por ejemplo en la máquina de un operador.
set -euo pipefail
cd "$(dirname "$0")/.."

php -r '
$strict = getenv("STRICT") === "1";
$lock = json_decode(file_get_contents("skills-lock.json"), true);
$bad = [];
$missing = [];
$ok = 0;
foreach ($lock["skills"] as $name => $meta) {
    $root = ".agents/skills/" . ($name === "performance" ? "addyosmani-performance" : $name);
    if (!is_dir($root)) {
        $root = ".claude/skills/" . $name;
    }
    if (!is_dir($root)) {
        $missing[] = $name;
        continue;
    }
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isFile()) {
            $files[] = substr($file->getPathname(), strlen($root) + 1);
        }
    }
    sort($files);
    $ctx = hash_init("sha256");
    foreach ($files as $rel) {
        hash_update($ctx, $rel);
        hash_update_file($ctx, $root . "/" . $rel);
    }
    $hash = hash_final($ctx);
    if (!hash_equals($meta["computedHash"], $hash)) {
        $bad[] = "$name: hash distinto";
    } else {
        $ok++;
    }
}
foreach (glob(".claude/skills/*") as $path) {
    if (is_link($path) && !file_exists($path)) {
        $bad[] = "symlink roto: $path";
    }
}
echo "Skills OK: $ok\n";
if ($missing) {
    echo "Skills ausentes en este checkout: " . count($missing) . "\n";
    if ($strict) {
        fwrite(STDERR, "Faltan skills requeridas por el lock:\n - " . implode("\n - ", $missing) . "\n");
        exit(1);
    }
}
if ($bad) {
    fwrite(STDERR, "Skills con problemas:\n - " . implode("\n - ", $bad) . "\n");
    exit(1);
}
'
