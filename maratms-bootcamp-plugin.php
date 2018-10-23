<?php

/**
 * @package MaratMSBootcampPlugin
 */
/*
Plugin name: MaratMS Bootcamp Plugin
Plugin URI: http://maratms.com/
Description: This is a test Bootcamp Plugin
Version: 1.0.0
Author: Marat MaratMS Minnullin
Author URI: http://maratms.com/
Licence: GPLv2 or later
Text Domain: maratms-bootcamp-plugin
 */

use MaratMSBootcampPlugin\Plugin;

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    die('Hi there!  I\'m just a plugin, not much I can do when called directly.');
}

require_once (__DIR__ . "/autoload.php");


$mbpPlugin = new Plugin(__DIR__ . "/");
$mbpPlugin->init();


register_activation_hook(__FILE__, [$mbpPlugin, 'activate']);
register_deactivation_hook(__FILE__, [$mbpPlugin, 'deactivate']);
// register_uninstall_hook(__FILE__, [$mbpPlugin, 'uninstall']);
