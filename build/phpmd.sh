#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
report="reports/phpmd.txt"
command="phpmd . text phpmd.xml \
--reportfile-text ${report} \
--ignore-violations-on-exit"
${command}
code=$?

# 3. 附加命令到报告中
append_report "${report}" "${command}"

exit ${code}