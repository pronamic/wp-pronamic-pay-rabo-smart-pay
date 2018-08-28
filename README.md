# WordPress Pay Gateway: OmniKassa 2.0

**OmniKassa 2.0 driver for the WordPress payment processing library.**

[![Build Status](https://travis-ci.org/wp-pay-gateways/omnikassa-2.svg?branch=develop)](https://travis-ci.org/wp-pay-gateways/omnikassa-2)
[![Coverage Status](https://coveralls.io/repos/wp-pay-gateways/omnikassa-2/badge.svg?branch=master&service=github)](https://coveralls.io/github/wp-pay-gateways/omnikassa-2?branch=master)
[![Latest Stable Version](https://poser.pugx.org/wp-pay-gateways/omnikassa-2/v/stable.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Total Downloads](https://poser.pugx.org/wp-pay-gateways/omnikassa-2/downloads.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Latest Unstable Version](https://poser.pugx.org/wp-pay-gateways/omnikassa-2/v/unstable.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![License](https://poser.pugx.org/wp-pay-gateways/omnikassa-2/license.svg)](https://packagist.org/packages/wp-pay-gateways/omnikassa-2)
[![Built with Grunt](https://cdn.gruntjs.com/builtwith.svg)](http://gruntjs.com/)

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
