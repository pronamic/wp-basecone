<?php
/**
 * CLI.
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license GPL-2.0-or-later
 * @package Pronamic\WordPress\Basecone
 */

namespace Pronamic\WordPress\Basecone;

use WP_CLI;

/**
 * CLI.
 */
class CLI {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		WP_CLI::add_command(
			'basecone documents import process-queue',
			function( $args, $assoc_args ) {
				$this->documents_import_process_queue( $args, $assoc_args );
			}
		);
	}

	/**
	 * Documents import process queue.
	 * 
	 * @link https://www.mc4wp.com/kb/ecommerce-wp-cli-commands/
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associated arguments.
	 */
	private function documents_import_process_queue( $args, $assoc_args ) {
		global $wpdb;

		if ( ! \defined( 'BASECONE_CLIENT_IDENTIFIER' ) ) {
			WP_CLI::error( 'The `BASECONE_CLIENT_IDENTIFIER` constant is not defined.' );
		}

		$where = 'import.document_id IS NULL';

		$where .= $wpdb->prepare( ' AND import.status != %s', 'ignore' );

		if ( \array_key_exists( 'gf_entry_id', $args ) ) {
			$where .= $wpdb->prepare( ' AND gf_entry_id = %d', $args['gf_entry_id'] );
		}

		$query = "
			SELECT
				import.*,
				basecone_company.company_id AS basecone_company_uuid,
				basecone_office.api_access_key
			FROM
				wp_lookup_basecone_document_imports AS import
					INNER JOIN
				wp_lookup_basecone_companies AS basecone_company
						ON import.company_id = basecone_company.id
					INNER JOIN
				wp_lookup_basecone_offices AS basecone_office
						ON basecone_company.office_id = basecone_office.id
			WHERE
				$where
			;
		";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$data = $wpdb->get_results( $query );

		$client = new Client( BASECONE_CLIENT_IDENTIFIER );

		foreach ( $data as $import ) {
			WP_CLI::line( \sprintf( 'Import `%s` → "%s"…', $import->id, $import->path ) );

			try {
				$basecone_response = $client->document_import( $import->api_access_key, $import->basecone_company_uuid, $import->path, $import->filename );

				$result = $wpdb->update(
					'wp_lookup_basecone_document_imports',
					array(
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Basecone object property.
						'document_id' => $basecone_response->documentId,
						'status'      => 'imported',
					),
					array(
						'id' => $import->id,
					),
					array(
						'document_id' => '%s',
						'status'      => '%s',
					),
					array(
						'id' => '%d',
					)
				);
			} catch ( \Exception $exception ) {
				WP_CLI::error( $exception );
			}
		}
	}
}
