# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [2.3.4] - 2021-05-28
- Added support for gateway configuration specific webhook URLs.
- Improved webhook error handling.

## [2.3.3] - 2021-05-11
- Improved error and exception handling in webhook controller.
- Introduced the `InvalidSignatureException` class.
- Improved documentation of the filters.

## [2.3.2] - 2021-04-26
- Started using `pronamic/wp-http`.

## [2.3.1] - 2021-01-21
- Updated check for response object in client request.

## [2.3.0] - 2020-11-09
- Switched to REST API for webhook.
- Catch input JSON validation exception in webhook listener.

## [2.2.4] - 2020-07-08
- Switched to new endpoint at `/order/server/api/v2/order`.
- Removed obsolete update of payment transaction ID.

## [2.2.3] - 2020-06-02
- Fix incorrect payments order when handling order status notifications.

## [2.2.2] - 2020-04-20
- Improved webhook handling if multiple gateway configurations exist.

## [2.2.1] - 2020-04-03
- Improved webhook handling if multiple payments exist with same merchant order ID.

## [2.2.0] - 2020-03-19
- Extend from AbstractGatewayIntegration class.

## [2.1.10] - 2019-12-22
- Added URL to manual in gateway settings.
- Added address fields validation.
- Improved error handling with exceptions.

## [2.1.9] - 2019-10-04
- Use line 1 as street if address splitting failed (i.e. no house number given).
- Improved support for merchantOrderId = AN (Strictly)..Max 24 field.

## [2.1.8] - 2019-09-10
- Use 'fully qualified name' for all function calls.
- Fixed `validate_an`, `wp_strip_all_tags` and `trim` issue.

## [2.1.7] - 2019-08-28
- Updated packages.
- Renamed `DataHelper::shorten` to `DataHelper::sanitize_an` which also strip tags.
- Replaced `mb_strimwidth` function with `mb_substr` to shorten strings.

## [2.1.6] - 2019-02-04
- Removed workaround for order item name length, Rabobank has resolved the issue.

## [2.1.5] - 2019-01-24
- Workaround for OmniKassa 2.0 bug in order item name length.

## [2.1.4] - 2019-01-21
- Workaround for OmniKassa 2.0 bug in order item names with special characters.

## [2.1.3] - 2019-01-03
- Improved error handling.

## [2.1.2] - 2018-12-18
- Limit order item name to 50 characters.

## [2.1.1] - 2018-12-11
- Make sure order item name and description are not empty.

## [2.1.0] - 2018-12-10
- Added support for payment lines, shipping, billing and customer data.
- Improved signature handling.

## [2.0.4] - 2018-09-28
- Remove unused `use` statements.

## [2.0.3] - 2018-09-17
- Fixed - Fatal error: Cannot use Pronamic\WordPress\Pay\Core\Gateway as Gateway because the name is already in use.

## [2.0.2] - 2018-08-28
- Improved webhook handler functions and logging.
- Improved return URL request handler functions and logging.
- Store OmniKassa 2.0 merchant order ID in the payment.
- No longer send empty User-Agent string to OmniKassa servers, Rabobank solved the issue. 

## [2.0.1] - 2018-08-15
- Send empty User-Agent string to OmniKassa servers, Rabobank is blocking "WordPress/4.9.8; https://example.com/" User-Agent.

## [2.0.0] - 2018-05-11
- Switched to PHP namespaces.

## 1.0.0 - 2017-12-13
- First release.

[unreleased]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.3.4...HEAD
[2.3.4]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.3.3...2.3.4
[2.3.3]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.3.2...2.3.3
[2.3.2]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.3.1...2.3.2
[2.3.1]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.2.4...2.3.0
[2.2.4]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.10...2.2.0
[2.1.10]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.9...2.1.10
[2.1.9]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.8...2.1.9
[2.1.8]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.7...2.1.8
[2.1.7]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.6...2.1.7
[2.1.6]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.5...2.1.6
[2.1.5]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.4...2.1.5
[2.1.4]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.3...2.1.4
[2.1.3]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.4...2.1.0
[2.0.4]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/wp-pay-gateways/omnikassa-2/compare/1.0.0...2.0.0
