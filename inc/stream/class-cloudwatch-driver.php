<?php

namespace HM\Platform\Security\Stream;

use Aws\CloudWatch\CloudWatchClient;
use function HM\Platform\Cloud\CloudWatch_Logs\cloudwatch_logs_client;
use function HM\Platform\Cloud\CloudWatch_Logs\send_events_to_stream;
use function HM\Platform\get_environment_name;
use function HM\Platform\get_aws_sdk;
use WP_Stream\DB_Driver as DB_Driver_Interface;

class CloudWatch_Driver implements DB_Driver_Interface {
	/**
	 * Holds Query class
	 *
	 * @var Query
	 */
	protected $query;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		wp_cache_add_global_groups( 'stream_column_values' );
	}

	/**
	 * Insert a record.
	 *
	 * @param array $data Data to insert.
	 *
	 * @return int
	 */
	public function insert_record( $data ) {
		if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) {
			return false;
		}

		$result = send_events_to_stream( [ [ 'timestamp' => time() * 1000, 'message' => json_encode( $data ) ] ], get_environment_name() . '/audit-log', 'items' );

		// Add the values to the column values caches if they exist
		foreach ( $data as $column => $value ) {
			$cache = wp_cache_get( $column, 'stream_column_values' );
			if ( $cache === false || in_array( $value, $cache, true ) ) {
				continue;
			}
			$cache[] = $value;
			wp_cache_set( $column, $cache, 'stream_column_values' );
		}
	}

	/**
	 * Retrieve records
	 *
	 * @param array $args
	 *
	 * @return array
	 */
		/**
	 * Retrieve records
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_records( $args ) {
		$fields = [
			'blog_id',
			'site_id',
			'user_id',
			'user_role',
			'connector',
			'context',
			'action',
			'ip',
			'object_id',
		];

		$field_where = [];
		foreach ( $fields as $field ) {
			if ( $args[ $field ] || $args[ $field ] === '0' ) {
				$field_where[] = sprintf( '%s = "%s" ', $field, $args[ $field ] );
			}
		}

		if ( $args['search'] ) {
			$field_where[] = sprintf( '%s like "%s" ', $args['search_field'], $args['search'] );
		}

		if ( $field_where ) {
			$where = sprintf( '| filter ( %s )', implode( ' and ', $field_where ) );
		}

		$limit = $args['records_per_page'] + ( absint( $args['paged'] - 1 ) * $args['records_per_page'] );
		$offset = absint( $args['paged'] - 1 ) * $args['records_per_page'];
		$order = $args['order'];

		$query = "fields @message $where | sort @timestamp $order";

		$params = [
			'logGroupName'   => get_environment_name() . '/audit-log',
			'limit'          => $limit,
			'endTime'        => time() * 1000,
			'startTime'      => 0,
			'queryString'    => $query,
		];

		$query = cloudwatch_logs_client()->startQuery( $params );

		$results = [ 'status' => 'Running' ];
		while ( ! in_array( $results['status'], [ 'Failed', 'Cancelled', 'Complete' ], true ) ) {
			$results = cloudwatch_logs_client()->getQueryResults([
				'queryId' => $query['queryId'],
			] );
		}

		/**
		 * Future optimization: currently we have to pull all results up until the offset.
		 * There's no way in the CloudWatch Insights API to supply a number offset.
		 */
		$items = [];
		$count = 0;
		foreach ( $results['results'] as $result ) {
			$count++;
			if ( $count < $offset ) {
				continue;
			}
			$object = json_decode( $result[0]['value'] );
			$object->meta = json_decode( json_encode( $object->meta ), true );
			$items[] = $object;
		}

		return [
			'items' => $items,
			'count' => $results['statistics']['recordsMatched'],
		];
	}


	/**
	 * Returns array of existing values for requested column.
	 * Used to fill search filters with only used items, instead of all items.
	 *
	 * @param string $column
	 *
	 * @return array
	 */
	public function get_column_values( $column ) {
		$values = wp_cache_get( $column, 'stream_column_values' );
		if ( $values === false ) {
			$query = "stats distinct( $column ) by $column";

			$params = [
				'logGroupName'   => get_environment_name() . '/audit-log',
				'endTime'        => time() * 1000,
				'startTime'      => 0,
				'queryString'    => $query,
			];

			$query = cloudwatch_logs_client()->startQuery( $params );
			$results = [ 'status' => 'Running' ];
			while ( ! in_array( $results['status'], [ 'Failed', 'Cancelled', 'Complete' ], true ) ) {
				$results = cloudwatch_logs_client()->getQueryResults([
					'queryId' => $query['queryId'],
				] );
			}

			foreach ( $results['results'] as $result ) {
				$values[] = $result[0]['value'];
			}

			wp_cache_set( $column, $values, 'stream_column_values' );
		}

		$values = array_map( function ( $value ) : array {
			return [ 'cell' => $value ];
		}, $values );

		return $values;
	}

	/**
	 * Public getter to return table names
	 *
	 * @return array
	 */
	public function get_table_names() {
		return [];
	}

	/**
	 * Init storage.
	 *
	 * @param \WP_Stream\Plugin $plugin Instance of the plugin.
	 * @return \WP_Stream\Install
	 */
	public function setup_storage( $plugin ) {
		//
	}

	/**
	 * Purge storage.
	 *
	 * @param \WP_Stream\Plugin $plugin Instance of the plugin.
	 * @return \WP_Stream\Uninstall
	 */
	public function purge_storage( $plugin ) {
		// no op.
		return;
	}

	protected function get_cloudwatch_client() : CloudWatchClient {
		$client = get_aws_sdk()->createCloudWatch();
		return $client;
	}
}
