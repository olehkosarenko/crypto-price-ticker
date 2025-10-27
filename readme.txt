=== Crypto Price Ticker ===
Contributors: you
Requires at least: 6.5
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later

This plugin provides a hook, a shortcode, and a REST API endpoint to display real-time cryptocurrency prices.
Data comes from your external API base URL settings (e.g. https://site.com/price/{id}).

Shortcode:
[crypto_ticker id="bitcoin" currency="USD"]

Action for theme:
do_action( 'crypto_ticker/render', array( 'id' => 'bitcoin', 'currency' => 'USD' ) );

Template tag:
\CryptoPriceTicker\Helpers\crypto_ticker( array( 'id' => 'bitcoin', 'currency' => 'USD' ) );
