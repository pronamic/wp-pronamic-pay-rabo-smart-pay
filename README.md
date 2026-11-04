<p align="center">
	<a href="https://www.wp-pay.org/">
		<img src="https://www.wp-pay.org/assets/pronamic-pay.svgo-min.svg" alt="Pronamic Pay → Gateway → Rabo Smart Pay" width="72" height="72">
	</a>
</p>

<h1 align="center">Pronamic Pay → Gateway → Rabo Smart Pay</h3>

<p align="center">
	Rabo Smart Pay driver for the WordPress payment processing library.
</p>

## Table of contents

- [Status](#status)
- [WordPress Filters](#wordpress-filters)
- [Errors](#errors)
- [Simulate Requests](#simulate-requests)
- [Webhook](#webhook)
- [License](#license)

## Status

[![GitHub Stars](https://img.shields.io/github/tag/pronamic/wp-pronamic-pay-rabo-smart-pay.svg?style=social&label=Star)](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay)
[![CI](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/actions/workflows/ci.yaml/badge.svg)](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/actions/workflows/ci.yaml)
[![Latest Stable Version](https://img.shields.io/packagist/v/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Total Downloads](https://img.shields.io/packagist/dt/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![License](https://img.shields.io/packagist/l/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)

## WordPress Filters

### pronamic_pay_omnikassa_2_request_args

```php
add_filter( 'pronamic_pay_omnikassa_2_request_args', function( $args ) {
	$args['timeout'] = 3600;

	return $args;
} );
```

### pronamic_pay_omnikassa_2_merchant_return_url

```php
add_filter( 'pronamic_pay_omnikassa_2_merchant_return_url', function( $url ) {
	$url = 'https://example.com/';

	return $url;
} );
```

## Errors

| Request                  | Response Status Code              | Code   | Property          | Message                                                                                    |
| ------------------------ | --------------------------------- | ------ | ----------------- | ------------------------------------------------------------------------------------------ |
| `gatekeeper/refresh`     | HTTP/1.1 401 Unauthorized         | `5001` | `errorMessage`    | Full authentication is required to access this resource                                    |
| `order/server/api/order` | HTTP/1.1 403 Forbidden            | `5001` | `consumerMessage` | The timestamp of the order announcement is invalid                                         |
| `order/server/api/order` | HTTP/1.1 422 Unprocessable Entity | `5017` | `consumerMessage` | merchantOrderId is required                                                                |
| `order/server/api/order` | HTTP/1.1 422 Unprocessable Entity | `5017` | `consumerMessage` | merchantReturnURL is required                                                              |
| `order/server/api/order` | HTTP/1.1 422 Unprocessable Entity | `5017` | `consumerMessage` | currency required and should be one of: [AUD, CAD, CHF, DKK, EUR, GBP, JPY, NOK, SEK, USD] |
| `order/server/api/order` | HTTP/1.1 422 Unprocessable Entity | `5017` | `consumerMessage` | order amount must be greater than zero                                                     |

## Simulate Requests

### Refresh

```
curl --request GET https://betalen.rabobank.nl/omnikassa-api/gatekeeper/refresh \
	--header "Authorization: Bearer __refresh_token__" \
	--connect-timeout 5 \
	--max-time 5 \
	--user-agent "WordPress/4.9.8; https://example.com/" \
	--verbose
```

### Order

```
curl --request POST https://betalen.rabobank.nl/omnikassa-api-sandbox/order/server/api/order \
	--header "Authorization: Bearer __refresh_token__" \
	--connect-timeout 5 \
	--max-time 5 \
	--user-agent "WordPress/4.9.8; https://example.com/" \
	--verbose
```

### Notification

```
curl --request POST "https://example.com/wp-json/pronamic-pay/omnikassa-2/v1/webhook" \
	--data-binary "@tests/json/notification.json" \
	--user-agent "Java/1.8.0" \
	--verbose
```

```
http POST https://example.com/wp-json/pronamic-pay/omnikassa-2/v1/webhook @tests/json/notification.json User-Agent:Java/1.8.0
```

### Event

```
curl --request GET https://betalen.rabobank.nl/omnikassa-api-sandbox/order/server/api/events/results/merchant.order.status.changed \
	--header "Authorization: Bearer __refresh_token__" \
	--connect-timeout 5 \
	--max-time 5 \
	--user-agent "WordPress/4.9.8; https://example.com/" \
	--verbose
```

```
curl --request GET https://betalen.rabobank.nl/omnikassa-api/order/server/api/events/results/merchant.order.status.changed \
	--header "Authorization: Bearer __refresh_token__" \
	--connect-timeout 5 \
	--max-time 5 \
	--user-agent "WordPress/4.9.8; https://example.com/" \
	--verbose
```

## Webhook

The Pronamic Pay OmniKassa 2.0 gateway can handle OmniKassa 2.0 notifications via the WordPress REST API.

**Route:** `/wp-json/pronamic-pay/omnikassa-2/v1/webhook`

The WordPress REST API OmniKassa 2.0 webhook can be tested with for example cURL, see for an example the [Simulate Requests](#simulate-requests) section.

In principle it is not possible to view this REST API endpoint via your web browser because it is a HTTP `POST` method only endpoint.
However, the WordPress REST API has the option to override the HTTP method via the `_method` parameter.

`/wp-json/pronamic-pay/omnikassa-2/v1/webhook?_method=POST`

https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_method-or-x-http-method-override-header

## License

GPL-3.0-or-later
