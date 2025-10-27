// Comments in English (as requested).
import { store, getContext } from '@wordpress/interactivity';

// Use global apiFetch provided by WordPress.
const apiFetch = window.wp && window.wp.apiFetch ? window.wp.apiFetch : null;

// Keep all per-element data in context. Global state stays empty.
const api = store( 'cpticker', {
	state: {},
	actions: {
		init() {
			const ctx = getContext() || {};

			// One-time defaults.
			if ( typeof ctx.display === 'undefined' ) {
				ctx.display = 'Loading price…';
			}
			if ( typeof ctx.id !== 'string' ) {
				ctx.id = '';
			}
			if ( typeof ctx.currency !== 'string' ) {
				ctx.currency = 'USD';
			}

			if ( ctx.id.length === 0 ) {
				ctx.display = 'Missing "id" attribute';
				return;
			}

			// First fetch.
			api.actions.refresh();

			// Per-element timer (clear previous if re-initialized).
			if ( ctx._timer ) {
				clearInterval( ctx._timer );
				ctx._timer = null;
			}
			ctx._timer = setInterval( function () {
				api.actions.refresh();
			}, 60000 );
		},

		async refresh() {
			const ctx = getContext() || {};

			if ( ! apiFetch ) {
				ctx.display = 'apiFetch is not available';
				return;
			}
			if ( ctx.id.length === 0 ) {
				ctx.display = 'Missing "id" attribute';
				return;
			}

			try {
				const qs =
					'id=' +
					encodeURIComponent( String( ctx.id ) ) +
					'&currency=' +
					encodeURIComponent( String( ctx.currency || 'USD' ) );

				// apiFetch path is relative to /wp-json/
				const data = await apiFetch( {
					path: 'crypto-ticker/v1/price?' + qs,
					method: 'GET',
				} );

				if ( data && ! data.error ) {
					const name = data.name || data.id || '';
					const symbol = data.symbol
						? String( data.symbol ).toUpperCase()
						: '';
					const price =
						typeof data.price !== 'undefined' ? data.price : '';
					const ccy = data.currency || ctx.currency || 'USD';
					ctx.display =
						name + ' (' + symbol + ') — ' + price + ' ' + ccy;
				} else {
					const message =
						data && ( data.message || data.error )
							? data.message || data.error
							: 'Unknown';
					ctx.display = 'Error: ' + message;
				}
			} catch ( e ) {
				// eslint-disable-next-line no-console
				console.error( '[cpticker] refresh failed', e );
				ctx.display = 'Network error';
			}
		},
	},
} );

// Optional: ensure processing if the element is inserted late.
if (
	window.wpInteractivity &&
	typeof window.wpInteractivity.process === 'function'
) {
	window.wpInteractivity.process( document );
} else {
	document.addEventListener( 'DOMContentLoaded', function () {
		if (
			window.wpInteractivity &&
			typeof window.wpInteractivity.process === 'function'
		) {
			window.wpInteractivity.process( document );
		}
	} );
}
