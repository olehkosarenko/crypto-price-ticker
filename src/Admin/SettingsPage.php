<?php
/**
 * Admin settings page for the Crypto Price Ticker plugin.
 *
 * Registers options, fields and renders the settings screen.
 *
 * @package CryptoPriceTicker\Admin
 */

namespace CryptoPriceTicker\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SettingsPage
 *
 * Handles registration of settings and rendering of the admin page.
 */
final class SettingsPage {

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register(): void {
		register_setting(
			'cpticker',
			'cpticker_api_base_url',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);

		register_setting(
			'cpticker',
			'cpticker_default_currency',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_currency' ),
				'default'           => 'USD',
			)
		);

		register_setting(
			'cpticker',
			'cpticker_cache_ttl',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 60,
			)
		);

		add_settings_section(
			'cpticker_section_main',
			__( 'Crypto Ticker Settings', 'crypto-price-ticker' ),
			'__return_false',
			'cpticker'
		);

		add_settings_field(
			'cpticker_api_base_url',
			__( 'API Base URL', 'crypto-price-ticker' ),
			array( $this, 'field_api_base_url' ),
			'cpticker',
			'cpticker_section_main'
		);

		add_settings_field(
			'cpticker_default_currency',
			__( 'Default Currency', 'crypto-price-ticker' ),
			array( $this, 'field_default_currency' ),
			'cpticker',
			'cpticker_section_main'
		);

		add_settings_field(
			'cpticker_cache_ttl',
			__( 'Cache TTL (seconds)', 'crypto-price-ticker' ),
			array( $this, 'field_cache_ttl' ),
			'cpticker',
			'cpticker_section_main'
		);
	}

	/**
	 * Add submenu item under "Settings".
	 *
	 * @return void
	 */
	public function add_menu(): void {
		add_options_page(
			__( 'Crypto Ticker', 'crypto-price-ticker' ),
			__( 'Crypto Ticker', 'crypto-price-ticker' ),
			'manage_options',
			'cpticker',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render input field for API Base URL.
	 *
	 * @return void
	 */
	public function field_api_base_url(): void {
		printf(
			'<input type="url" class="regular-text code" name="cpticker_api_base_url" value="%s" placeholder="https://your-api.example.com">',
			esc_attr( get_option( 'cpticker_api_base_url', '' ) )
		);
		echo '<p class="description">' . esc_html__( 'Example: https://site.com', 'crypto-price-ticker' ) . '</p>';
	}

	/**
	 * Render input field for Default Currency.
	 *
	 * @return void
	 */
	public function field_default_currency(): void {
		printf(
			'<input type="text" class="regular-text" name="cpticker_default_currency" value="%s" maxlength="6">',
			esc_attr( strtoupper( get_option( 'cpticker_default_currency', 'USD' ) ) )
		);
	}

	/**
	 * Render input field for Cache TTL (seconds).
	 *
	 * @return void
	 */
	public function field_cache_ttl(): void {
		printf(
			'<input type="number" class="small-text" name="cpticker_cache_ttl" value="%d" min="1">',
			(int) get_option( 'cpticker_cache_ttl', 60 )
		);
	}

	/**
	 * Render the settings page markup.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Crypto Price Ticker', 'crypto-price-ticker' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'cpticker' );
				do_settings_sections( 'cpticker' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitize currency code.
	 *
	 * @param string $value Input.
	 * @return string
	 */
	public function sanitize_currency( string $value ): string {
		$value = strtoupper( preg_replace( '/[^A-Za-z]/', '', $value ) ?? '' );
		if ( empty( $value ) ) {
			$value = 'USD';
		}
		return $value;
	}
}
