<?php
/**
 * Error.
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license GPL-2.0-or-later
 * @package Pronamic\WordPress\Basecone
 */

namespace Pronamic\WordPress\Basecone;

/**
 * Error.
 * 
 * @link https://developers.basecone.com/ApiReference/ErrorHandling
 */
class Error extends \Exception {
	/**
	 * Error code.
	 * 
	 * @var string
	 */
	private $error_code;

	/**
	 * Metadata
	 * 
	 * @var array|null
	 */
	private $metadata;

	/**
	 * More info URL.
	 * 
	 * @var string|null
	 */
	private $more_info_url;

	/**
	 * Construct error.
	 * 
	 * @param string     $message      Message.
	 * @param string     $error_code   Error code.
	 * @param \Throwable $previous The previous exception used for the exception chaining.
	 */
	public function __construct( $message, $error_code, $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->error_code = $error_code;
	}

	/**
	 * Set metadata.
	 * 
	 * @param array $metadata Metadata.
	 */
	public function set_metadata( $metadata ) {
		$this->metadata = $metadata;
	}

	/**
	 * Set more info URL.
	 * 
	 * @param string $url More info URL.
	 */
	public function set_more_info_url( $url ) {
		$this->more_info_url = $url;
	}
}
