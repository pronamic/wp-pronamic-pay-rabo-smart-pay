# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

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

[unreleased]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.1.8...HEAD
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
