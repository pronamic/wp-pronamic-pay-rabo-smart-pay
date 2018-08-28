# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

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

[unreleased]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.2...HEAD
[2.0.2]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.0...2.0.2
[2.0.1]: https://github.com/wp-pay-gateways/omnikassa-2/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/wp-pay-gateways/omnikassa-2/compare/1.0.0...2.0.0
