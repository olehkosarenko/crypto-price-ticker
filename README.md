# 🪙 Crypto Price Ticker

A modern WordPress plugin that displays real-time cryptocurrency prices using the **WordPress Interactivity API**.  
The ticker updates every minute and can display any crypto symbol retrieved from your custom external API endpoint.

---

## 🚀 Features

- Built with **WordPress Interactivity API**
- OOP architecture following **SOLID** principles
- **Composer autoloading** (PSR-4)
- **Admin settings page** for API base URL, default currency, and cache TTL
- Cached REST API integration (1-minute default)
- Can display tickers via:
    - Action hook
    - Shortcode
    - Template tag
- **PHPCS / WPCS** and **wp-scripts** for linting and build
- Safe for production (handles missing API or Interactivity API fallback)

---

## 🧩 Requirements

- WordPress **6.5+**
- PHP **8.0+**
- Node.js **18+**
- Composer **2+**

---

## ⚙️ Installation

1. Clone or download the plugin.

2. Install PHP dependencies:

---

## 🧠 Configuration

In the WordPress admin panel go to
Settings → Crypto Ticker and configure:

Setting	Description	Example
API Base URL	Base endpoint of your Crypto Price API	https://site.com
Default Currency	Default fallback currency	USD
Cache TTL (seconds)	Duration for transient cache	60

Your API must return a response like:

```json
{
  "id": "bitcoin",
  "name": "Bitcoin",
  "symbol": "BTC",
  "price": 100000.00,
  "currency": "USD",
  "cachedAt": "2025-10-10T10:00:00.760Z"
}
```

---

## 💻 Usage
1. Action Hook
 
```php
do_action( 'crypto_ticker/render', array(
'id'       => 'bitcoin',
'currency' => 'USD',
'class'    => 'header-ticker',
) );
```

2. Shortcode
```html
[crypto_ticker id="bitcoin" currency="USD"]
```

3. Template Tag
```html
\CryptoPriceTicker\Helpers\crypto_ticker( array(
'id'       => 'ethereum',
'currency' => 'EUR',
) );
```

---

## ⚙️ Development


### Customisation
For customisation you can use this filters: cpticker_wrapper_open, cpticker_fallback_content, cpticker_wrapper_close.

### 🧰 Composer Commands

Update PHP dependencies (add phpcs/wpcs):
```sh
composer update
```

Run PHPCS using phpcs.xml
```sh
composer lint
```

Auto-fix coding style issues
```sh
composer lint:fix
```

### 🧪 NPM Commands

Install Node dependencies:

```sh
npm install
```

Lint JavaScript
```sh
npm run lint:js
```
Format JavaScript
```sh
npm run format
```

---

## 🔍 Code Quality

This plugin follows:

WordPress Coding Standards (WPCS)

PHPCS configuration: phpcs.xml

No abbreviations or inline short-returns (e.g. always use {} in if)

To manually lint (or use `composer lint`):
```
vendor/bin/phpcs --standard=phpcs.xml
```

---

## 📄 License

Licensed under the GPL-2.0-or-later license.

