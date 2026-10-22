# CI quality gates — TukiPass

## Workflows

| Workflow | Disparo | Qué bloquea |
|---|---|---|
| `tests.yml` → `phpunit` | push/PR a `master`, `remediation/*` | PHPUnit, skills lock, exposición de repo |
| `tests.yml` → `dependency-audit` | push/PR | Composer high/critical, npm runtime high |
| `tests.yml` → `codeql` | push/PR del mismo repo | Análisis estático PHP |
| `organizer-theme.yml` → `static-audit` | push/PR | Deuda de tema |
| `organizer-theme.yml` → Playwright | solo si existe `tukipass.sql` | E2E, a11y, aria, SEO, legal, tema |

## Estado

- `master` es la rama por defecto; el workflow de tema ya no escucha `main`.
- PHPUnit corre con SQLite in-memory; no necesita MySQL.
- `composer audit` quedó en 0 advisories tras actualizar Laravel 12.70.1, Symfony 7.4.21, dompdf 3.1.6, guzzle 7.16, commonmark 2.10.4 y phpspreadsheet 1.30.7.
- `npm audit --omit=dev` quedó en 0. Las vulnerabilidades restantes son de build (`devDependencies`).

## Limitación conocida: Playwright en CI

Los jobs de Playwright dependen de `tukipass.sql`, un dump de producción que **no está versionado**. Para que corran en CI hace falta un seed reproducible:

1. Crear `database/seeders/CiPublicPagesSeeder.php` con idiomas, settings, páginas legales, menú, un evento real y un organizador.
2. Reemplazar el mount de `tukipass.sql` en `docker-compose.yml` por `php artisan migrate --seed`.
3. Quitar la condición `hashFiles('tukipass.sql')` de los jobs.
4. Validar que `npm run test:full` pase contra ese seed.

Mientras tanto, los jobs se omiten en un checkout limpio para no dar una falsa sensación de cobertura.

## Branch protection recomendada

- Require pull request before merging.
- Require status checks: `PHPUnit`, `Dependency audit`, `Static audit`.
- Require branches to be up to date.
- Restrict force pushes.
- Require signed commits (opcional).
- Secret scanning y Dependabot alerts activos.
