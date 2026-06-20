#!/bin/sh

if (git diff --cached --name-only --diff-filter=ACM HEAD | grep '\.php$' | grep -vq '^template.*\.php$') then
    git diff --cached --name-only --diff-filter=ACM HEAD | grep '\.php$' | grep -v '^template.*\.php$' | XDEBUG_MODE=off  xargs -n1 ./vendor/bin/phpcs --standard=phpcs.xml.dist
else
    echo "No PHP file to check"
fi