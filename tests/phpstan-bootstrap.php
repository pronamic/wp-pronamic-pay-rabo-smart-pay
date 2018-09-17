<?php

putenv( sprintf( 'WP_PHPUNIT__TESTS_CONFIG=%s', 'tests/wp-config.php' ) );

require_once __DIR__ . '/../vendor/autoload.php';

require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
