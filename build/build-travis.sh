#!/bin/bash

# 1. 执行各类检查
mkdir -p reports
#bash "${BASH_SOURCE[0]%/*}/phpcs.sh"
bash "${BASH_SOURCE[0]%/*}/phpmd.sh"
bash "${BASH_SOURCE[0]%/*}/csslint.sh"
bash "${BASH_SOURCE[0]%/*}/eslint.sh"
bash "${BASH_SOURCE[0]%/*}/htmllint.sh"

# 2. 合并错误报告
error_file="error.txt"
count=`ls -1 reports/*.txt 2>/dev/null | wc -l`
if [ ${count} != 0 ]; then
  cat reports/*.txt > ${error_file}
fi

# 3. 创建issues

# Check if error file not empty
if [ -e "$error_file" ]; then
    detail=$(cat "$error_file")
else
    echo "error file \"$error_file\" not found"
    exit 0
fi

# Build issue title
message=$(git log -1 --pretty=%B "$TRAVIS_COMMIT")
title="【$(date +%y-%m-%d)】Build failed: $message"
assignee=$(git log -1 --pretty=%cn "$TRAVIS_COMMIT")
body="Status: failed

Author: $assignee
Message: $message

"

if [ "$detail" != "" ]; then
    body+="Detail:
\`\`\`
$detail
\`\`\`
"
fi

body+="

View the changeset: $TRAVIS_REPO_SLUG@$TRAVIS_COMMIT

View the full build log and details: https://travis-ci.org/$TRAVIS_REPO_SLUG/jobs/$TRAVIS_JOB_ID"
body=${body//\"/\\\"}
body=${body//\	/\\\t}
body=${body//
/\\\n}

data="{\"title\":\"$title\",\"body\":\"$body\",\"assignees\":[\"$assignee\"],\"labels\":[\"task\"]}"
echo "$data"

curl -i -H "Authorization: token $GITHUB_ISSUE_NOTIFY_TOKEN" -d "$data" \
"https://api.github.com/repos/twinh/test/issues"