#!/bin/sh
# Move a major version tag (v1) to a published release tag (v1.2.3).
set -eu

tag="${1:?usage: move-major-tag.sh <release-tag>}"

if ! printf '%s' "$tag" | grep -Eqx 'v[0-9]+\.[0-9]+\.[0-9]+'; then
	echo "::notice::$tag is not a semantic version tag; no major tag to move."
	exit 0
fi

major="${tag%%.*}"

if git rev-parse -q --verify "refs/tags/$major" >/dev/null; then
	if ! git merge-base --is-ancestor "$major" "$tag"; then
		echo "::error::Refusing to move $major backwards: $tag is not a descendant of the current $major."
		exit 1
	fi
fi

git tag -f "$major" "$tag^{}"
git push origin "refs/tags/$major" --force
