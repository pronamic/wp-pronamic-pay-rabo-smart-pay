<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 payment brands
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class PaymentBrands {
	const IDEAL = 'IDEAL';

	const PAYPAL = 'PAYPAL';

	const MASTERCARD = 'MASTERCARD';

	const VISA = 'VISA';

	const BANCONTACT = 'BANCONTACT';

	const MAESTRO = 'MAESTRO';

	const V_PAY = 'V_PAY';

	/**
	 * Dutch: De waarde CARDS zorgt ervoor dat de consument
	 * kan kiezen uit de betaalmethoden: MASTERCARD, VISA,
	 * BANCONTACT, MAESTRO en V_PAY.
	 */
	const CARDS = 'CARDS';
}
