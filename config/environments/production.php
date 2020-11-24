<?php
/**
 * Platform.sh Configuration overrides for WP_ENV === 'production'
 */

use Roots\WPConfig\Config;
use Platformsh\ConfigReader\Config as PlatformshConfig;

require __DIR__.'/../vendor/autoload.php';

// Create a new config object to ease reading the Platform.sh environment variables.
// You can alternatively use getenv() yourself.
$config = new PlatformshConfig();

if ($config->isValidPlatform()) {
	if ($config->hasRelationship('database')) {
		// This is where we get the relationships of our application dynamically
		// from Platform.sh.

		// // Avoid PHP notices on CLI requests.
		// if (php_sapi_name() === 'cli') {
		// 	session_save_path("/tmp");
		// }

		// Get the database credentials
		$credentials = $config->credentials('database');

		// We are using the first relationship called "database" found in your
		// relationships. Note that you can call this relationship as you wish
		// in your `.platform.app.yaml` file, but 'database' is a good name.
		// define( 'DB_NAME', $credentials['path']);
		// define( 'DB_USER', $credentials['username']);
		// define( 'DB_PASSWORD', $credentials['password']);
		// define( 'DB_HOST', $credentials['host']);
		// define( 'DB_CHARSET', 'utf8' );
        // define( 'DB_COLLATE', '' );
        
        /**
         * DB settings
         */
        Config::define('DB_NAME', $credentials['path']);
        Config::define('DB_USER', $credentials['username']);
        Config::define('DB_PASSWORD', $credentials['password']);
        Config::define('DB_HOST', $credentials['host'] ?: 'localhost');
        Config::define('DB_CHARSET', 'utf8');
        Config::define('DB_COLLATE', '');
        $table_prefix = env('DB_PREFIX') ?: 'wp_';

		// Check whether a route is defined for this application in the Platform.sh
		// routes. Use it as the site hostname if so (it is not ideal to trust HTTP_HOST).
		// if ($config->routes()) {

		// 	$routes = $config->routes();

		// 	foreach ($routes as $url => $route) {
		// 		if ($route['type'] === 'upstream' && $route['upstream'] === $config->applicationName) {

		// 			// Pick the first hostname, or the first HTTPS hostname if one exists.
		// 			$host = parse_url($url, PHP_URL_HOST);
		// 			$scheme = parse_url($url, PHP_URL_SCHEME);
		// 			if ($host !== false && (!isset($site_host) || ($site_scheme === 'http' && $scheme === 'https'))) {
		// 				$site_host = $host;
		// 				$site_scheme = $scheme ?: 'http';
		// 			}
		// 		}
		// 	}
		// }

		// Debug mode should be disabled on Platform.sh. Set this constant to true
		// in a wp-config-local.php file to skip this setting on local development.
		// if (!defined( 'WP_DEBUG' )) {
		// 	define( 'WP_DEBUG', false );
		// }

		// Set all of the necessary keys to unique values, based on the Platform.sh
		// entropy value.
		if ($config->projectEntropy) {
			$keys = [
				'AUTH_KEY',
				'SECURE_AUTH_KEY',
				'LOGGED_IN_KEY',
				'NONCE_KEY',
				'AUTH_SALT',
				'SECURE_AUTH_SALT',
				'LOGGED_IN_SALT',
				'NONCE_SALT',
			];
			$entropy = $config->projectEntropy;
			foreach ($keys as $key) {
				if (!Config::defined($key)) {
                    // define( $key, $entropy . $key );
                    Config::define($key, $entropy . $key );
// Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
// Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
// Config::define('NONCE_KEY', env('NONCE_KEY'));
// Config::define('AUTH_SALT', env('AUTH_SALT'));
// Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
// Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
// Config::define('NONCE_SALT', env('NONCE_SALT'));
				}
			}
		}
	}
}
else {
  // Local configuration file should be in project root.
  if (file_exists(dirname(__FILE__, 2) . '/wp-config-local.php')) {
    include(dirname(__FILE__, 2) . '/wp-config-local.php');
  }
}