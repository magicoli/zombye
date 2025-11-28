<?php
/**
 * Plugin Name:     Zombye
 * Plugin URI:      https://github.com/magicoli/zombye
 * Description:     Say goodbye to zombie registrations
 * Author:          Magiiic
 * Author URI:      https://magiiic.com
 * Text Domain:     zombye
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Zombye
 */

// Your code starts here.
if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path(__FILE__) . 'classes/class-zombye.php';

$zombye = new Zombye();
