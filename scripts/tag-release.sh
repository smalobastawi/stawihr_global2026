#!/usr/bin/env bash
# Tag a new HR release locally and push to GitHub.
# Usage: ./scripts/tag-release.sh 2.5.0
set -euo pipefail

VERSION="${1:-}"
if [[ -z "$VERSION" ]]; then
  echo "Usage: $0 <semver-version>   e.g. 2.5.0"
  exit 1
fi

VERSION="${VERSION#v}"
TAG="v${VERSION}"

cd "$(dirname "$0")/.."

php artisan app:stamp-version "$VERSION" --ref="$TAG"

git add VERSION.json
git commit -m "chore: stamp version ${TAG}" || true
git tag -a "$TAG" -m "Release ${TAG}"
git push origin HEAD
git push origin "$TAG"

echo "Tagged and pushed ${TAG}. Portal will pick it up via webhook or Sync from GitHub."
