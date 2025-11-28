<?php
/**
 * Plugin Name:     Zombye
 * Plugin URI:      https://github.com/magicoli/zombye
 * Description:     Say goodbye to zombie registrations
 * Author:          Magiiic
 * Author URI:      https://magiiic.com
 * Text Domain:     zombye
 * Domain Path:     /languages
 * Version:         0.9.0
 *
 * @package         Zombye
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load the autoloader
require_once plugin_dir_path(__FILE__) . 'autoload.php';

use Zombye\Zombye;

// Initialize Zombye
$zombye = new Zombye();
