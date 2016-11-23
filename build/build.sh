#!/bin/bash

mkdir -p reports

bash "${BASH_SOURCE[0]%/*}/phpcs.sh"
bash "${BASH_SOURCE[0]%/*}/phpmd.sh"
bash "${BASH_SOURCE[0]%/*}/csslint.sh"
bash "${BASH_SOURCE[0]%/*}/eslint.sh"
bash "${BASH_SOURCE[0]%/*}/htmllint.sh"
