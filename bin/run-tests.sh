#!/usr/bin/env bash

# This is only for use in development environments, it's not used in CI.
# You can call this with: composer dev:test

set -ex

wp-env run tests-cli --env-cwd=wp-content/plugins/wporg-gp-translation-events sh -c 'wp db query < schema.sql'
wp-env run tests-cli --env-cwd=wp-content/plugins/wporg-gp-translation-events ./vendor/bin/phpunit
