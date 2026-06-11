#!/bin/sh

if (git diff --cached --name-only --diff-filter=ACM HEAD | grep -q '\.php$') then
    git diff --cached --name-only --diff-filter=ACM HEAD | grep '\.php$' | xargs -n1 php -l
else
    echo "No PHP file to check"
fi