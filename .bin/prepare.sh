#!/bin/bash
set -x
set -euo pipefail

# Git Config
git config --global user.email "automation@souptik.dev"
git config --global user.name "GitHub Deploy"

echo $BUILT_BRANCH

# Make the backup of the parent branch.
cp -r $GITHUB_WORKSPACE $HOME/parent-branch

# Checkout to the built branch.
git checkout $BUILT_BRANCH || git checkout -b $BUILT_BRANCH

# Remove everything from built branch.
rm -rf *

# Copy the main branch content to built branch.
cp -r $HOME/parent-branch/* .

# Cleanup.
rm -f README.md
rm -rf .tests
rm -rf .github
rm -rf .bin
rm -f .nvmrc
rm -f .gitignore
rm -f .wp-env.json
rm -f composer.json
rm -f composer.lock
rm -f LICENSE
rm -f package.json
rm -f package-lock.json
rm -f phpcs.xml
rm -f phpstan.neon
rm -rf node_modules

# Check if we have changes.
if [[ -z $(git status -s) ]]; then
	# No changes bail out.
	echo "No changes to push"
else
	# Push the changes!
	git add .
	git status
	git commit -m "Automated Build"
	git push origin $BUILT_BRANCH
fi

set +x
