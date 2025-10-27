<?php
/**
 * Price provider interface.
 *
 * Defines a contract for fetching cryptocurrency price data
 * from an external source.
 *
 * @package CryptoPriceTicker\Contracts
 */

namespace CryptoPriceTicker\Contracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface PriceProviderInterface
 *
 * Describes the method required for any price provider implementation.
 */
interface PriceProviderInterface {
	/**
	 * Fetch price for asset and currency.
	 *
	 * @param string $id Asset id, e.g. "bitcoin".
	 * @param string $currency Currency code, e.g. "USD".
	 * @return array<string,mixed> Normalized price payload.
	 */
	public function get_price( string $id, string $currency ): array;
}
