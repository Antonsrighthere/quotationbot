<?php
/*
Plugin Name: TutBot
Description: Custom logic for Teplobot
Version: 1.0
Author: Teplitsa
Author URI: https://te-st.ru/
Text Domain: tut
Domain Path: /lang
*/

if(!defined('ABSPATH')) die; // Die if accessed directly

// Plugin version:
if( !defined('TUT_VERSION') )
    define('TUT_VERSION', '1.0');
	
// Plugin DIR, with trailing slash:
if( !defined('TUT_PLUGIN_DIR') )
    define('TUT_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

// Plugin URL:
if( !defined('TUT_PLUGIN_BASE_URL') )
    define('TUT_PLUGIN_BASE_URL', plugin_dir_url(__FILE__));
	
// Plugin ID:
if( !defined('TUT_PLUGIN_BASE_NAME') )
    define('TUT_PLUGIN_BASE_NAME', plugin_basename(__FILE__));

// Environment checks. If some failed, deactivate the plugin to save WP from possible crushes:
if( !defined('PHP_VERSION') || version_compare(PHP_VERSION, '5.3.0', '<') ) {

    echo '<div id="message" class="error"><p><strong>Внимание:</strong> версия PHP ниже <strong>5.3.0</strong>. Лейка нуждается в PHP хотя бы <strong>версии 5.3.0</strong>, чтобы работать корректно. Плагин будет деактивирован.<br /><br />Пожалуйста, направьте вашему хостинг-провайдеру запрос на повышение версии PHP для этого сайта.</p> <p><strong>Warning:</strong> your PHP version is below <strong>5.3.0</strong>. Leyka needs PHP <strong>v5.3.0</strong> or later to work. Plugin will be deactivated.<br /><br />Please contact your hosting provider to upgrade your PHP version.</p></div>';

    die();
}


/** Init **/
require_once(plugin_dir_path(__FILE__).'inc/core.php');
require_once(plugin_dir_path(__FILE__).'inc/functions.php');
require_once(plugin_dir_path(__FILE__).'inc/botan_io.php');
require_once(plugin_dir_path(__FILE__).'inc/utils.php');


Tutbot_Core::get_instance();

