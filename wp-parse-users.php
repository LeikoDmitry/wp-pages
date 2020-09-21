<?php
/**
* Plugin Name: Type Code
* Description: The plugin is parse the JSON response and display an HTML table
* Version: 1.0.0
* Author: Crock-dev
* License: GPLv2 or later
*/

require_once 'vendor/autoload.php';

use Wordpress\Type\Code\FactoryInterface;
use Wordpress\Type\Code\Base\Factory;

define('WORDPRESS_TYPE_CODE_VERSION', '1.0.0');
define('WORDPRESS_TYPE_CODE_FILE', __FILE__);
define('WORDPRESS_TYPE_CODE_ROOT_DIR', plugin_dir_path(WORDPRESS_TYPE_CODE_FILE));
define('WORDPRESS_TYPE_CODE_BASE_URL', 'https://jsonplaceholder.typicode.com');
define('WORDPRESS_TYPE_CODE_PAGE_STATUS', 'publish');
define('WORDPRESS_TYPE_CODE_SHORT_PAGE_TITLE', 'Typicode users');
define('WORDPRESS_TYPE_CODE_SHORT_PAGE_CREATOR_ID', 1);
define('WORDPRESS_TYPE_CODE_SHORT_PAGE_TYPE', 'page');
define('WORDPRESS_TYPE_CODE_SHORT_CODE', 'wordpress_type_code');
define('WORDPRESS_TYPE_CODE_LOG', sprintf('%s/%s', __DIR__, 'logs/app.log'));
define('WORDPRESS_TYPE_CODE_QUERY_VAR', 'users');
define('WORDPRESS_TYPE_CODE_AJAX_ENDPOINT', 'type_code');
define('WORDPRESS_TYPE_ADMIN_NOTICE_MESSAGE', 'Thank you for using this plugin! Check please the page');
define('WORDPRESS_TYPE_ADMIN_NOTICE_MESSAGE_KEY', 'wordpress-type-code-admin-notice');

function wordpress_type_code(FactoryInterface $factory): void {
    $factory->getPlugin()->run();
}
wordpress_type_code(Factory::getInstance());