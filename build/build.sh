#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 生成不能处理的文件的报告
report="reports/phpcs.txt"
command="php phpcs.phar --report-file=${report} ."
${command}
code=$?

# 4. 移除最后两行空白,并附加命令到报告中
head -n -2 "${report}" > temp.txt ; mv temp.txt "${report}"
append_report "${report}" "${command}"

# 5. 如果检测到问题,仍然认为是运行成功
if [[ code -eq 1 ]]; then
  exit 0
else
  exit ${code}
fi
