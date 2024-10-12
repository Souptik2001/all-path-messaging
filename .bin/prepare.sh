#!/bin/bash
set -x
set -euo pipefail

# Git Config
git config --global user.email "automation@souptik.dev"
git config --global user.name "GitHub Deploy"

echo $BUILT_BRANCH

git checkout $BUILT_BRANCH || git checkout -b $BUILT_BRANCH

# Cleanup
rm README.md
rm -rf .tests
rm -rf .github
rm -rf .bin
rm .nvmrc
rm .gitignore
rm .wp-env.json
rm composer.json
rm composer.lock
rm LICENSE
rm package.json
rm package-lock.json
rm phpstan.neon

# Check if we have changes
if [[ -z $(git status -s) ]]; then
	# No changes bail out
	echo "No changes to push"
else
	# Push the changes!
	git add .
	git status
	git commit -m "Automated Build"
	git push origin $BUILT_BRANCH
fi

set +x
