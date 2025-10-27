<?php
/**
 * Remote price provider implementation.
 *
 * Fetches cryptocurrency price data from a remote HTTP API.
 *
 * @package CryptoPriceTicker\Http
 */

namespace CryptoPriceTicker\Http;

use CryptoPriceTicker\Contracts\PriceProviderInterface;
use WP_Error;
use WP_Http;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RemotePriceProvider
 *
 * Retrieves price data from a configured upstream endpoint.
 */
final class RemotePriceProvider implements PriceProviderInterface {

	/**
	 * Base URL of the upstream API (without trailing slash).
	 *
	 * @var string
	 */
	private string $base_url;

	/**
	 * RemotePriceProvider constructor.
	 *
	 * @param string $base_url Base URL for the upstream API (e.g. "https://example.com").
	 */
	public function __construct( string $base_url ) {
		$this->base_url = rtrim( $base_url, '/' );
	}

	/**
	 * Get price data from the upstream API.
	 *
	 * Builds a request to `{base_url}/price/{id}` and normalizes the response.
	 * Returns an error payload array if the request fails or the response is malformed.
	 *
	 * @param string $id       Asset id (e.g. "bitcoin").
	 * @param string $currency Requested currency code (e.g. "USD").
	 * @return array<string,mixed> Normalized price payload or error payload.
	 */
	public function get_price( string $id, string $currency ): array {
		if ( empty( $this->base_url ) ) {
			return $this->error_payload( 'Missing API base URL in settings.' );
		}

		$url = esc_url_raw( $this->base_url . '/price/' . rawurlencode( $id ) );

		$args = array(
			'timeout' => 8,
			'headers' => array(
				'Accept' => 'application/json',
			),
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $this->error_payload( $response->get_error_message() );
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( 200 !== $code || empty( $body ) ) {
			return $this->error_payload( 'Upstream API error.' );
		}

		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || empty( $data['id'] ) || ! isset( $data['price'] ) ) {
			return $this->error_payload( 'Malformed upstream response.' );
		}

		// Normalize to expected shape. Respect requested currency if present upstream, otherwise override.
		return array(
			'id'       => (string) $data['id'],
			'name'     => isset( $data['name'] ) ? (string) $data['name'] : '',
			'symbol'   => isset( $data['symbol'] ) ? strtoupper( (string) $data['symbol'] ) : '',
			'price'    => (float) $data['price'],
			'currency' => strtoupper( (string) ( $data['currency'] ?? $currency ) ),
			'cachedAt' => isset( $data['cachedAt'] ) ? (string) $data['cachedAt'] : gmdate( 'c' ),
		);
	}

	/**
	 * Build an error payload.
	 *
	 * @param string $message Error.
	 * @return array<string,mixed>
	 */
	private function error_payload( string $message ): array {
		return array(
			'error'   => true,
			'message' => $message,
		);
	}
}
