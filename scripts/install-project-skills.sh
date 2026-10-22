#!/usr/bin/env bash
# Instala las skills propias versionadas en docs/skills dentro del store local
# .agents/skills y crea los symlinks de .claude/skills.
set -euo pipefail
cd "$(dirname "$0")/.."

for dir in docs/skills/*/; do
  [ -f "$dir/SKILL.md" ] || continue
  name="$(basename "$dir")"
  mkdir -p ".agents/skills/$name"
  rsync -a --delete "$dir" ".agents/skills/$name/"
  mkdir -p .claude/skills
  ln -sfn "../../.agents/skills/$name" ".claude/skills/$name"
  echo "Instalada: $name"
done
