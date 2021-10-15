<?php
/**
 * Client.
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license GPL-2.0-or-later
 * @package Pronamic\WordPress\Basecone
 */

namespace Pronamic\WordPress\Basecone;

use Pronamic\WordPress\Http\Facades\Http;

/**
 * Client.
 */
class Client {
	/**
	 * Client identifier.
	 * 
	 * @var string
	 */
	private $client_identifier;

	/**
	 * Construct client object.
	 * 
	 * @param string $client_identifier Client identifier.
	 */
	public function __construct( $client_identifier ) {
		$this->client_identifier = $client_identifier;
	}

	/**
	 * Get API URL.
	 * 
	 * @param string                $endpoint Endpoint.
	 * @param array<string, string> $parts    Parts.
	 * @param array<string, string> $parameters Parameters.
	 * @return string
	 */
	private function get_api_url( $endpoint, $parts = array(), $parameters = array() ) {
		$endpoint = \strtr( $endpoint, $parts );

		$url = 'https://api.basecone.com/v1/' . $endpoint;

		$url = \add_query_arg( $parameters, $url );

		return $url;
	}

	/**
	 * Get authorization header.
	 * 
	 * @param string $api_access_key API Access Key.
	 * @return string
	 */
	private function get_authorization_header( $api_access_key ) {
		return 'Basic ' . \base64_encode( $this->client_identifier . ':' . $api_access_key );
	}

	/**
	 * Boundary.
	 * 
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type
	 * @return string
	 */
	private function get_boundary() {
		return \hash( 'sha256', \uniqid( '', true ) );
	}

	/**
	 * Sanitize filename.
	 *
	 * @link https://github.com/wp-pay-gateways/ideal-basic/blob/master/src/DataHelper.php
	 * @link https://tools.ietf.org/html/rfc6266
	 * @param string $string String.
	 * @return string
	 */
	private function sanitize_filename( $string ) {
		$characters = array( 'A-Z', 'a-z', '0-9', ' ', '_', '.' );

		$pattern = '#[^' . \implode( $characters ) . ']#';

		$string = \preg_replace( $pattern, '', $string );

		return $string;
	}

	/**
	 * Post.
	 * 
	 * @param string $url  URL.
	 * @param array  $args Arguments.
	 * @return object
	 * @throws \Exception Throws exceptions when the HTTP status is in the Basecone error status list.
	 */
	private function post( $url, $args ) {
		$response = Http::post( $url, $args );

		$data = $response->json();

		if ( ! \is_object( $data ) ) {
			throw new \Exception( 'Unknown response from Basecone.' );
		}

		/**
		 * Error handling.
		 * 
		 * @link https://developers.basecone.com/ApiReference/ErrorHandling
		 */
		$status = (int) $response->status();

		if ( \in_array( $status, array( 400, 401, 403, 404, 405, 500 ), true ) ) {
			$exception = new \Exception( $response->message(), $status );

			if ( \property_exists( $data, 'code' ) && \property_exists( $data, 'message' ) ) {
				$exception = new Error( $data->message, (int) $response->status(), $data->code, $exception );

				if ( \property_exists( $data, '_metadata' ) ) {
					$exception->set_metadata( $data->_metadata );
				}

				if ( \property_exists( $data, '_moreInfo' ) ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Basecone object property.
					$exception->set_more_info_url( $data->_moreInfo );
				}
			}

			throw $exception;
		}

		return $data;
	}

	/**
	 * Document import.
	 * 
	 * @link https://developers.basecone.com/ApiReference/DocumentImport
	 * @param string $api_access_key API Access Key.
	 * @param string $company_id     Basecone company ID.
	 * @param string $file           File.
	 * @param string $name           Name.
	 * @return object
	 * @throws \Exception Throws exception when document import failed.
	 */
	public function document_import( $api_access_key, $company_id, $file, $name ) {
		if ( ! \is_readable( $file ) ) {
			throw new \Exception( \sprintf( 'Cannot read file "%s".', $file ) );
		}

		$filetype = \wp_check_filetype( $file );

		if ( false === $filetype['type'] ) {
			throw new \Exception( 'File error.' );
		}

		$type = $filetype['type'];

		$filename = $name . '.' . $filetype['ext'];

		$body = '';

		$boundary = $this->get_boundary();

		$body .= '--' . $boundary . "\r\n";

		$body .= \sprintf(
			'Content-Disposition: form-data; name="%s"; filename="%s"',
			$this->sanitize_filename( $name ),
			$this->sanitize_filename( $filename )
		) . "\r\n";

		$body .= \sprintf(
			'Content-Type: %s',
			$type
		) . "\r\n";

		$body .= "\r\n";

		$body .= \file_get_contents( $file ) . "\r\n";

		$body .= '--' . $boundary . '--';

		// Remote.
		$args = array(
			'headers' => array(
				'Authorization' => $this->get_authorization_header( $api_access_key ),
				'Content-Type'  => 'multipart/form-data; boundary=' . $boundary,
			),
			'body'    => $body,
		);

		$url = $this->get_api_url(
			'documents/import',
			array(),
			array(
				'companyId' => $company_id,
			) 
		);

		return $this->post( $url, $args );
	}
}
