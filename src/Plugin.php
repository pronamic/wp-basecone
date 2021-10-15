<?php
/**
 * Plugin.
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license GPL-2.0-or-later
 * @package Pronamic\WordPress\Basecone
 */

namespace Pronamic\WordPress\Basecone;

/**
 * Plugin.
 */
class Plugin {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		/**
		 * CLI.
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$cli = new CLI();

			$cli->setup();
		}
	}
}
