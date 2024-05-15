# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [4.7.1] - 2024-05-15

### Commits

- Updated .gitattributes ([d916e14](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/d916e14f9df033f77c23bceb75bf14cb9ab8634d))
- Updated .gitattributes ([ad3e737](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/ad3e73777db9880b58ccea008c74d72948801aea))

Full set of changes: [`4.7.0...4.7.1`][4.7.1]

[4.7.1]: https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/compare/v4.7.0...v4.7.1

## [4.7.0] - 2024-05-15

### Commits

- Updated hooks.md ([6671e02](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/6671e028331a9e44f919976ffc30b2134f709707))
- Removed `Statuses` class, see #24. ([68970bf](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/68970bffd50b58b0839fd21f261e778d3d23c8e5))
- Simplify client. ([9534e65](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/9534e65e53285e39388c98a125ec6f84a9c8d982))
- Removed the 'pronamic_pay_omnikassa_2_merchant_return_url' filter, see #26. ([ae58afd](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/ae58afdb5997fa68cabadec4fe8026ddcd98941a))
- Simplify the 'rest_api_init' callback signature. ([70871c5](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/70871c5ebc2722ec05b30bc0a4d44745aed59a52))
- Make all classes final. ([05a4911](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/05a4911c3890ad064d43ad385dadf44d26c60a78))
- Fixed #33. ([a8a4480](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/a8a4480f94c4bb92215f997afee496e5370457f8))

### Composer

- Changed `php` from `>=8.0` to `>=8.1`.

Full set of changes: [`4.6.0...4.7.0`][4.7.0]

[4.7.0]: https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/compare/v4.6.0...v4.7.0

## [4.6.0] - 2024-03-26

### Changed

- Added `X-Api-User-Agent` header. ([b3f685d](https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/commit/b3f685db7b79be8876b3c2e309fe38144d7ecefb))

### Composer

- Changed `php` from `>=7.4` to `>=8.0`.
- Changed `wp-pay/core` from `^4.15` to `v4.16.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.16.0

Full set of changes: [`4.5.4...4.6.0`][4.6.0]

[4.6.0]: https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/compare/v4.5.4...v4.6.0

## [4.5.4] - 2024-02-07

### Composer

- Changed `automattic/jetpack-autoloader` from `^2.12` to `v3.0.2`.
	Release notes: https://github.com/Automattic/jetpack-autoloader/releases/tag/v3.0.2
- Changed `wp-pay/core` from `^4.9` to `v4.15.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.15.0

Full set of changes: [`4.5.3...4.5.4`][4.5.4]

[4.5.4]: https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/compare/v4.5.3...v4.5.4

## [4.5.3] - 2023-12-18

### Commits

- AfterPay specific code for description is no longer required. ([084c6dc](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/084c6dc9b946a56a3de68b1fd8871ed6c8e9d45f))
- Removed Riverty (formerly AfterPay) because Rabobank no longer supports it ([eda5176](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/eda51760336ac3d162d973a191e7a678076bd775))

Full set of changes: [`4.5.2...4.5.3`][4.5.3]

[4.5.3]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.5.2...v4.5.3

## [4.5.2] - 2023-11-08

### Fixed

- Fixed problem with Rabo Smart Pay notification (webhook) and status pull call requests, due to incorrect documentation from Rabobank. ([27](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/issues/27))

Full set of changes: [`4.5.1...4.5.2`][4.5.2]

[4.5.2]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.5.1...v4.5.2

## [4.5.1] - 2023-11-06

### Fixed

- Fixed redirect to return redirect URL after updated payment status (#23). ([e09fa36](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/e09fa368998198c71b1b1e8e3cfe91c1b929a536)) ([4afc90d](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/4afc90d4b2789c137acf6f36239891ff06cf6465))

Full set of changes: [`4.5.0...4.5.1`][4.5.1]

[4.5.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.5.0...v4.5.1

## [4.5.0] - 2023-10-30

### Changed

- Improvements in differences between order and transaction IDs. ([21](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/issues/21))

### Composer

- Added `automattic/jetpack-autoloader` `^2.12`.

Full set of changes: [`4.4.5...4.5.0`][4.5.0]

[4.5.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.4.5...v4.5.0

## [4.4.5] - 2023-07-12

### Commits

- Updated for removed payment ID fallback in formatted payment string (pronamic/wp-pronamic-pay-adyen#23). ([6fe8f00](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/6fe8f00c6c27e5cd29fbf309fd24fb8908ede03f))
- Updated dashboard URL to new URL, closes #20. ([e8dcb7c](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/e8dcb7c99f74fe7ec9f2b60c6534e6c9dedacbf1))

Full set of changes: [`4.4.4...4.4.5`][4.4.5]

[4.4.5]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.4.4...v4.4.5

## [4.4.4] - 2023-06-01

### Commits

- Switch from `pronamic/wp-deployer` to `pronamic/pronamic-cli`. ([c187c0b](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/c187c0b39979779f1ffbdd94529c835124c51061))
- Updated .gitattributes ([714f64c](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/714f64cf5e3e4579984387056ce0eb6a9355cb3c))

Full set of changes: [`4.4.3...4.4.4`][4.4.4]

[4.4.4]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.4.3...v4.4.4

## [4.4.3] - 2023-03-29

### Commits

- Set Composer type to `wordpress-plugin`. ([b343ff6](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/b343ff62137eb21f023a3d8dbdc4cc4b5e82d176))
- Added support for refunds. ([e7e8e98](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/e7e8e9801918e6cf84b8fd45f1be6d658204795f))

### Composer

- Changed `wp-pay/core` from `^4.6` to `v4.9.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.9.0
Full set of changes: [`4.4.2...4.4.3`][4.4.3]

[4.4.3]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.4.2...v4.4.3

## [4.4.2] - 2023-01-31
### Composer

- Changed `php` from `>=8.0` to `>=7.4`.
Full set of changes: [`4.4.1...4.4.2`][4.4.2]

[4.4.2]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.4.1...v4.4.2

## [4.4.1] - 2023-01-18

### Commits

- Ignore `documentation` folder in archive files. ([95ed400](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/95ed40087c0f426ef3d88f6839e22bb0e271f042))
- Happy 2023. ([05a0f18](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/05a0f1834172eeae9b6432a9e01ebefbf800e932))

Full set of changes: [`4.4.0...4.4.1`][4.4.1]

[4.4.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.4.0...v4.4.1

## [4.4.0] - 2022-12-23

### Commits

- Updated payment method status the other way around. ([6453eeb](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/6453eebed92faa7c8d9c2b6769f5b94313c4830b))
- Register payment method Riverty. ([61435d4](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/61435d44879fa1cfb905e4a1b8e8441d4039d485))
- Added support for Riverty payment method. ([ea91ccc](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/ea91ccccff3c840373ddf56cc3c6bda8f046c634))
- Removed usage of deprecated `\FILTER_SANITIZE_STRING` in gateway settings fields. ([1003d7c](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/1003d7c5f85428f71a4c35ad65b9515dda4f1d30))
- Fixed text domain. ([1986728](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/198672876fcb78426b42142103a0ab1c094a29ce))
- Updated manual URL to pronamicpay.com (pronamic/pronamic-pay#15). ([0514c6e](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/commit/0514c6edef6dee8d3615215ec3c29365db1297f1))

### Composer

- Changed `php` from `>=7.4` to `>=8.0`.
- Changed `pronamic/wp-http` from `^1.0` to `v1.2.0`.
	Release notes: https://github.com/pronamic/wp-http/releases/tag/v4.3.0
- Changed `wp-pay/core` from `^4.5` to `v4.6.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.3.0
Full set of changes: [`4.3.0...4.4.0`][4.4.0]

[4.4.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/v4.3.0...v4.4.0

## [4.3.0] - 2022-11-07
- Changed name from "OmniKassa" to "Rabo Smart Pay". [#13](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/issues/13)
- Enrich payments methods from new `order/server/api/payment-brands` endpoint. [#15](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/issues/15)
- Added support for SOFORT payment method. [#16](https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/issues/16)

## [4.2.0] - 2022-09-26
- Updated payment methods registration.

## [4.1.1] - 2022-04-13
- Fixed using core develop library.

## [4.1.0] - 2022-04-11
- No longer use core mode.
- Added support for iDEAL issuers.

## [4.0.0] - 2022-01-11
### Changed
- Updated to https://github.com/pronamic/wp-pay-core/releases/tag/4.0.0.

## [3.0.1] - 2021-08-16
- Added support for Mastercard, V PAY and Visa.

## [3.0.0] - 2021-08-05
- Updated to `pronamic/wp-pay-core` version `3.0.0`.
- Updated to `pronamic/wp-money` version `2.0.0`.
- Switched to `pronamic/wp-coding-standards`.

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

[unreleased]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/4.3.0...HEAD
[4.3.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/4.2.0...4.3.0
[4.2.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/4.1.1...4.2.0
[4.1.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/4.1.0...4.1.1
[4.1.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/3.0.1...4.0.0
[3.0.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.3.4...3.0.0
[2.3.4]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.3.3...2.3.4
[2.3.3]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.3.2...2.3.3
[2.3.2]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.3.1...2.3.2
[2.3.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.2.4...2.3.0
[2.2.4]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.10...2.2.0
[2.1.10]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.9...2.1.10
[2.1.9]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.8...2.1.9
[2.1.8]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.7...2.1.8
[2.1.7]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.6...2.1.7
[2.1.6]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.5...2.1.6
[2.1.5]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.4...2.1.5
[2.1.4]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.3...2.1.4
[2.1.3]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.0.4...2.1.0
[2.0.4]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/compare/1.0.0...2.0.0
