#!/bin/bash

set -e

if [ "$TRAVIS_PHP_VERSION" == "7.4" ]; then
  # Install yarn
  curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version 1.3.2
  export PATH=$HOME/.yarn/bin:$PATH

  # Install dependencies
  yarn
fi

bash "${BASH_SOURCE[0]%/*}/install.sh"