#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
standard=""
if [ -e ".php_cs" ]; then
  config=".php_cs"
else
  config="plugins/plugin/.php_cs"
fi

IFS='
'
CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB origin/master HEAD)
if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php_cs(\\.dist)?|composer\\.lock)$"; then EXTRA_ARGS=$(printf -- '--path-mode=intersection\n--\n%s' "${CHANGED_FILES}"); else EXTRA_ARGS=''; fi
vendor/bin/php-cs-fixer fix --config=${config} -v --dry-run --stop-on-violation --using-cache=no ${EXTRA_ARGS}
