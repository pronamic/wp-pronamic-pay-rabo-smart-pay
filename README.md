# WordPress Pay Gateway: OmniKassa 2.0

**OmniKassa 2.0 driver for the WordPress payment processing library.**

[![GitHub Stars](https://img.shields.io/github/tag/wp-pay-gateways/omnikassa-2.svg?style=social&label=Star)](https://github.com/wp-pay-gateways/omnikassa-2)
[![Build Status](https://travis-ci.org/wp-pay-gateways/omnikassa-2.svg?branch=master)](https://travis-ci.org/wp-pay-gateways/omnikassa-2)
[![Coverage Status](https://coveralls.io/repos/wp-pay-gateways/omnikassa-2/badge.svg?branch=master&service=github)](https://coveralls.io/github/wp-pay-gateways/omnikassa-2?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Total Downloads](https://img.shields.io/packagist/dt/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![License](https://img.shields.io/packagist/l/wp-pay-gateways/omnikassa-2.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Built with Grunt](https://gruntjs.com/cdn/builtwith.svg)](http://gruntjs.com/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wp-pay-gateways/omnikassa-2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wp-pay-gateways/omnikassa-2/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/wp-pay-gateways/omnikassa-2/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/wp-pay-gateways/omnikassa-2/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/wp-pay-gateways/omnikassa-2/badges/build.png?b=master)](https://scrutinizer-ci.com/g/wp-pay-gateways/omnikassa-2/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/wp-pay-gateways/omnikassa-2/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Maintainability](https://api.codeclimate.com/v1/badges/d115fb4e5c1ae697a0cf/maintainability)](https://codeclimate.com/github/wp-pay-gateways/omnikassa-2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d115fb4e5c1ae697a0cf/test_coverage)](https://codeclimate.com/github/wp-pay-gateways/omnikassa-2/test_coverage)
[![CircleCI](https://circleci.com/gh/wp-pay-gateways/omnikassa-2/tree/master.svg)](https://circleci.com/gh/wp-pay-gateways/omnikassa-2/tree/master)
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fomnikassa-2.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fomnikassa-2?ref=badge_shield)

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

## Links

*	https://betalen-acpt3.rabobank.nl/omnikassa-api/
*	https://betalen.rabobank.nl/omnikassa-api/

## Documentation

*	https://www.ideal-checkout.nl/payment-providers/rabobank/TB6jSGOVPr$HhqTg*rS$c9ThjxpTSPXbCifkWG0yrT-URES==v7e:A:mdmpjXdDFPayFkIfsvMgBFKKYRPyF1ScJVP

## Errors

| Request                  | Response Status Code              | Code   | Propery           | Message                                                                                    |
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
curl --request POST "https://example.com/?omnikassa2_webhook" \
	--data-binary "@tests/json/notification.json" \
	--user-agent "Java/1.8.0" \
	--verbose
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

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fomnikassa-2.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fomnikassa-2?ref=badge_large)
