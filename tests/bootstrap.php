<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

if ( ! file_exists( "$_tests_dir/includes/functions.php" ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "$_tests_dir/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require_once dirname( __DIR__, 2 ) . '/glotpress/tests/phpunit/includes/loader.php';
	require_once dirname( __DIR__ ) . '/wporg-gp-translation-events.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require "$_tests_dir/includes/bootstrap.php";

// Require GlotPress test code.
require_once dirname( __DIR__, 2 ) . '/glotpress/tests/phpunit/lib/testcase.php';
require_once dirname( __DIR__, 2 ) . '/glotpress/tests/phpunit/lib/testcase-route.php';
require_once dirname( __DIR__, 2 ) . '/glotpress/tests/phpunit/lib/testcase-request.php';

function _install_glotpress() {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	require_once dirname( __DIR__, 2 ) . '/glotpress/gp-includes/schema.php';
	require_once dirname( __DIR__, 2 ) . '/glotpress/gp-includes/install-upgrade.php';
	gp_upgrade_db();
}
_install_glotpress();
