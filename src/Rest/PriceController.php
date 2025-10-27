<?php
/**
 * REST controller for cryptocurrency prices.
 *
 * This file contains the PriceController class responsible for registering
 * the REST endpoint and returning normalized price data.
 *
 * @package CryptoPriceTicker\Rest
 */

namespace CryptoPriceTicker\Rest;

use CryptoPriceTicker\Domain\PriceService;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PriceController
 *
 * Registers the /crypto-ticker/v1/price REST endpoint and handles requests.
 */
final class PriceController {

	/**
	 * Price service instance.
	 *
	 * @var PriceService
	 */
	private PriceService $service;

	/**
	 * Default currency code (ISO 4217, uppercase).
	 *
	 * @var string
	 */
	private string $default_ccy;


	/**
	 * PriceController constructor.
	 *
	 * @param PriceService $service     Service used to fetch price data.
	 * @param string       $default_ccy Default currency code (e.g. "USD").
	 */
	public function __construct( PriceService $service, string $default_ccy ) {
		$this->service     = $service;
		$this->default_ccy = strtoupper( ! empty( $default_ccy ) ? $default_ccy : 'USD' );
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			'crypto-ticker/v1',
			'/price',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_price' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'id'       => array(
							'type'     => 'string',
							'required' => true,
						),
						'currency' => array(
							'type'     => 'string',
							'required' => false,
						),
					),
				),
			)
		);
	}

	/**
	 * REST callback.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_price( WP_REST_Request $request ): WP_REST_Response {
		$id             = (string) $request->get_param( 'id' );
		$param_currency = (string) $request->get_param( 'currency' );
		$currency       = strtoupper( ! empty( $param_currency ) ? $param_currency : $this->default_ccy );
		$data           = $this->service->get_price( $id, $currency );

		return new WP_REST_Response( $data, 200 );
	}
}
