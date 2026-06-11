#!/bin/sh

if (git diff --cached --name-only --diff-filter=ACM HEAD | grep -q '\.php$') then
    git diff --cached --name-only --diff-filter=ACM HEAD | grep '\.php$' | XDEBUG_MODE=off xargs -n1 ./vendor/bin/php-cs-fixer fix --dry-run --diff
else
    echo "No PHP file to check"
fi