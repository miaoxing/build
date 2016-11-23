#!/bin/bash

mkdir -p reports

source "${BASH_SOURCE[0]%/*}/phpcs.sh"
source "${BASH_SOURCE[0]%/*}/phpmd.sh"
source "${BASH_SOURCE[0]%/*}/csslint.sh"
source "${BASH_SOURCE[0]%/*}/eslint.sh"
