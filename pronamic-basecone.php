<?php
/**
 * Plugin Name: Pronamic Basecone
 * Plugin URI: https://www.pronamic.eu/plugins/pronamic-basecone/
 * Description: The Pronamic WordPress Basecone plugin allows you to connect your WordPress installation to Basecone.
 *
 * Version: 1.0.0
 * Requires at least: 4.7
 *
 * Author: Pronamic
 * Author URI: https://www.pronamic.eu/
 *
 * Text Domain: pronamic-basecone
 * Domain Path: /languages/
 *
 * License: GPL-3.0-or-later
 *
 * GitHub URI: https://github.com/pronamic/wp-basecone
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\pronamic
 */

/**
 * Autoload.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Bootstrap.
 */
$pronamic_basecone_plugin = new \Pronamic\WordPress\Basecone\Plugin();

$pronamic_basecone_plugin->setup();
