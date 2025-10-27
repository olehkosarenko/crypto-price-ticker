<?php
/**
 * Domain service for cryptocurrency price retrieval and caching.
 *
 * Orchestrates the price provider and cache layer to return normalized data.
 *
 * @package CryptoPriceTicker\Domain
 */

namespace CryptoPriceTicker\Domain;

use CryptoPriceTicker\Contracts\CacheInterface;
use CryptoPriceTicker\Contracts\PriceProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Application service orchestrating cache and provider.
 */
final class PriceService {
	/**
	 * Price data provider implementation.
	 *
	 * @var PriceProviderInterface
	 */
	private PriceProviderInterface $provider;

	/**
	 * Cache implementation used to store fetched prices.
	 *
	 * @var CacheInterface
	 */
	private CacheInterface $cache;


	/**
	 * PriceService constructor.
	 *
	 * @param PriceProviderInterface $provider Provider used to fetch price data.
	 * @param CacheInterface         $cache    Cache storage used for price data.
	 */
	public function __construct( PriceProviderInterface $provider, CacheInterface $cache ) {
		$this->provider = $provider;
		$this->cache    = $cache;
	}

	/**
	 * Get price (cached for configured TTL).
	 *
	 * @param string $id Asset id.
	 * @param string $currency Currency code.
	 * @return array<string,mixed>
	 */
	public function get_price( string $id, string $currency ): array {
		$key = $this->cache_key( $id, $currency );

		$cached = $this->cache->get( $key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$data = $this->provider->get_price( $id, $currency );

		// Cache only successful responses with a price.
		if ( is_array( $data ) && empty( $data['error'] ) && isset( $data['price'] ) ) {
			$this->cache->set( $key, $data );
		}

		return $data;
	}

	/**
	 * Build cache key.
	 *
	 * @param string $id Asset id.
	 * @param string $currency Currency code.
	 * @return string
	 */
	private function cache_key( string $id, string $currency ): string {
		return 'cpticker_' . md5( strtolower( $id . '|' . $currency ) );
	}
}
