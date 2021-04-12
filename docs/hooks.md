# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

### pronamic_pay_webhook_log_payment

*Webhook log payment.*



Argument | Type | Description
-------- | ---- | -----------
`$payment` | `\Pronamic\WordPress\Pay\Payments\Payment` | Payment to log.

Source: [src/Gateway.php](../src/Gateway.php), [line 329](../src/Gateway.php#L329-L334)

## Filters

### pronamic_pay_omnikassa_2_merchant_return_url

*Filters the OmniKassa 2.0 merchant return URL.*



Argument | Type | Description
-------- | ---- | -----------
`$merchant_return_url` | `string` | Merchant return URL.

Source: [src/Gateway.php](../src/Gateway.php), [line 96](../src/Gateway.php#L96-L101)

### pronamic_pay_omnikassa_2_request_args

*Filters the OmniKassa 2.0 remote request arguments.*



Argument | Type | Description
-------- | ---- | -----------
`$args` | `array` | WordPress remote request arguments.

Source: [src/Client.php](../src/Client.php), [line 170](../src/Client.php#L170-L175)


