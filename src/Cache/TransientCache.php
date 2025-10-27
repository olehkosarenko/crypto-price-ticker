<?php
/**
 * Transient-based cache implementation.
 *
 * Provides a simple cache layer using WordPress transients.
 *
 * @package CryptoPriceTicker\Cache
 */

namespace CryptoPriceTicker\Cache;

use CryptoPriceTicker\Contracts\CacheInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TransientCache
 *
 * Implements the CacheInterface using the WordPress Transients API.
 */
final class TransientCache implements CacheInterface {
	/**
	 * Cache Time To Live (in seconds).
	 *
	 * @var int
	 */
	private int $ttl;

	/**
	 * TransientCache constructor.
	 *
	 * @param int $ttl Cache time-to-live in seconds.
	 */
	public function __construct( int $ttl ) {
		$this->ttl = max( 1, $ttl );
	}

	/**
	 * Retrieve cached value by key.
	 *
	 * @param string $key Cache key.
	 * @return mixed|null Cached value or null if not found.
	 */
	public function get( string $key ) {
		return get_transient( $key );
	}

	/**
	 * Store a value in the cache.
	 *
	 * @param string $key   Cache key.
	 * @param mixed  $value Value to store.
	 * @return void
	 */
	public function set( string $key, $value ): void {
		set_transient( $key, $value, $this->ttl );
	}
}
