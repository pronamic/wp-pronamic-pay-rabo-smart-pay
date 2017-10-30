<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 client
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Client {
	/**
	 * Action URL to start a payment request in the test environment,
	 * the POST data is sent to.
	 *
	 * @see page 14 - http://pronamic.nl/wp-content/uploads/2013/10/integratiehandleiding_rabo_omnikassa_en_versie_5_0_juni_2013_10_29451215.pdf
	 * @var string
	 */
	const ACTION_URL_TEST = 'https://payment-webinit.simu.omnikassa.rabobank.nl/paymentServlet';

	/**
	 * Action URL For a payment request in the production environment,
	 * the POST data is sent to
	 *
	 * @see page 14 - http://pronamic.nl/wp-content/uploads/2013/10/integratiehandleiding_rabo_omnikassa_en_versie_5_0_juni_2013_10_29451215.pdf
	 * @var string
	 */
	const ACTION_URL_PRUDCTION = 'https://payment-webinit.omnikassa.rabobank.nl/paymentServlet';

	//////////////////////////////////////////////////

	const ISO_639_1_ENGLISH = 'en';

	const ISO_639_1_FRENCH = 'fr';

	const ISO_639_1_GERMAN = 'de';

	const ISO_639_1_ITALIAN = 'it';

	const ISO_639_1_SPANISH = 'es';

	const ISO_639_1_DUTCH = 'nl';

	//////////////////////////////////////////////////

	public static function get_supported_language_codes() {
		return array(
			self::ISO_639_1_ENGLISH,
			self::ISO_639_1_FRENCH,
			self::ISO_639_1_GERMAN,
			self::ISO_639_1_ITALIAN,
			self::ISO_639_1_SPANISH,
			self::ISO_639_1_DUTCH,
		);
	}

	public static function is_supported_language( $language ) {
		$languages = self::get_supported_language_codes();

		return in_array( $language, $languages, true );
	}

	//////////////////////////////////////////////////

	/**
	 * Interface version HP 1.0
	 *
	 * @var string
	 */
	const INTERFACE_VERSION_HP_1_0 = 'HP_1.0';

	/**
	 * Interface version HP 2.12
	 *
	 * @var string
	 */
	const INTERFACE_VERSION_HP_2_12 = 'HP_2.12';

	/**
	 * Hash algorithm SHA256 indicator
	 *
	 * @var string
	 */
	const HASH_ALGORITHM_SHA256 = 'sha256';

	//////////////////////////////////////////////////

	/**
	 * The action URL
	 *
	 * @var string
	 */
	private $action_url;

	//////////////////////////////////////////////////

	/**
	 * The interface version
	 *
	 * @var string
	 */
	private $interface_version;

	//////////////////////////////////////////////////

	/**
	 * Currency code in ISO 4217-Numeric codification
	 *
	 * @doc http://en.wikipedia.org/wiki/ISO_4217
	 * @doc http://www.iso.org/iso/support/faqs/faqs_widely_used_standards/widely_used_standards_other/currency_codes/currency_codes_list-1.htm
	 *
	 * @var string N3
	 */
	private $currency_numeric_code;

	/**
	 * Merchant ID
	 *
	 * @var string N15
	 */
	private $merchant_id;

	/**
	 * Normal return URL
	 *
	 * @var string ANS512 url
	 */
	private $normal_return_url;

	/**
	 * Amount
	 *
	 * @var string N12
	 */
	private $amount;

	/**
	 * Transaction reference
	 *
	 * @var string AN35
	 */
	private $transaction_reference;

	/**
	 * Key version
	 *
	 * @var string N10
	 */
	private $key_version;

	//////////////////////////////////////////////////

	/**
	 * Automatic response URL
	 *
	 * @var string ANS512 url
	 */
	private $automatic_response_url;

	/**
	 * Customer language in ISO 639‐1 Alpha2
	 *
	 * @doc http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
	 * @var string A2
	 */
	private $customer_language;

	/**
	 * Payment mean brand list
	 *
	 * @var array
	 */
	private $payment_mean_brand_list;

	/**
	 * Order ID
	 *
	 * @var string AN32
	 */
	private $order_id;

	/**
	 * Expiration date
	 *
	 * @var DateTime
	 */
	private $expiration_date;

	//////////////////////////////////////////////////

	/**
	 * Secret key
	 *
	 * @var string
	 */
	private $secret_key;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initalize an OmniKassa object
	 */
	public function __construct() {
		$this->payment_mean_brand_list = array();

		$this->set_interface_version( self::INTERFACE_VERSION_HP_1_0 );
	}

	//////////////////////////////////////////////////

	/**
	 * Get the action URL
	 *
	 * @return the action URL
	 */
	public function get_action_url() {
		return $this->action_url;
	}

	/**
	 * Set the action URL
	 *
	 * @param string $url an URL
	 */
	public function set_action_url( $url ) {
		$this->action_url = $url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get interface version
	 *
	 * @return string
	 */
	public function get_interface_version() {
		return $this->interface_version;
	}

	/**
	 * Set interface version
	 *
	 * @param string $interface_version
	 */
	public function set_interface_version( $interface_version ) {
		$this->interface_version = $interface_version;
	}

	//////////////////////////////////////////////////

	/**
	 * Get the currency numeric code
	 *
	 * @return string currency numeric code
	 */
	public function get_currency_numeric_code() {
		return $this->currency_numeric_code;
	}

	/**
	 * Set the currency code
	 *
	 * @param string $currencyCode
	 */
	public function set_currency_numeric_code( $currency_numeric_code ) {
		$this->currency_numeric_code = $currency_numeric_code;
	}

	//////////////////////////////////////////////////

	/**
	 * Get merchant ID
	 *
	 * @return string
	 */
	public function get_merchant_id() {
		return $this->merchant_id;
	}

	/**
	 * Set the merchant ID
	 *
	 * @param string $merchant_id
	 */
	public function set_merchant_id( $merchant_id ) {
		$this->merchant_id = $merchant_id;
	}

	//////////////////////////////////////////////////

	/**
	 * Get normal return URL
	 *
	 * @return string
	 */
	public function get_normal_return_url() {
		return $this->normal_return_url;
	}

	/**
	 * Set the normal return URL
	 *
	 * LET OP! De URL mag geen parameters bevatten.
	 *
	 * @param string $normal_return_url
	 */
	public function set_normal_return_url( $normal_return_url ) {
		$this->normal_return_url = $normal_return_url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get amount
	 *
	 * @return float
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Get formmated amount
	 *
	 * @return int
	 */
	public function get_formatted_amount() {
		return Pronamic_WP_Pay_Util::amount_to_cents( $this->amount );
	}

	/**
	 * Set amount
	 *
	 * @param float $amount
	 */
	public function set_amount( $amount ) {
		$this->amount = $amount;
	}

	//////////////////////////////////////////////////

	/**
	 * Get transaction reference
	 *
	 * @return string
	 */
	public function get_transaction_reference() {
		return $this->transaction_reference;
	}

	/**
	 * Set transaction reference
	 * AN..max35 (AN = Alphanumeric, free text)
	 *
	 * @param string $transactionReference
	 */
	public function set_transaction_reference( $transaction_reference ) {
		$this->transaction_reference = Pronamic_WP_Pay_Gateways_OmniKassa_DataHelper::filter_an( $transaction_reference, 35 );
	}

	//////////////////////////////////////////////////

	/**
	 * Get key version
	 *
	 * @return string
	 */
	public function get_key_version() {
		return $this->key_version;
	}

	/**
	 * Set key version
	 *
	 * @param string $key_version
	 */
	public function set_key_version( $key_version ) {
		$this->key_version = $key_version;
	}

	//////////////////////////////////////////////////

	/**
	 * Get automatic response URL
	 *
	 * @return string
	 */
	public function get_automatic_response_url() {
		return $this->automatic_response_url;
	}

	/**
	 * Set automatic response URL
	 *
	 * LET OP! De URL mag geen parameters bevatten.
	 *
	 * @param string $automatic_response_url
	 */
	public function set_automatic_response_url( $automatic_response_url ) {
		$this->automatic_response_url = $automatic_response_url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get customer language
	 *
	 * @return string
	 */
	public function get_customer_language() {
		return $this->customer_language;
	}

	/**
	 * Set customer language
	 *
	 * @param string $customerLanguage
	 */
	public function set_customer_language( $customer_language ) {
		$this->customer_language = $customer_language;
	}

	//////////////////////////////////////////////////

	/**
	 * Add the specified payment mean brand to the payment mean brand list
	 *
	 * @param string $payment_mean_brand
	 */
	public function add_payment_mean_brand( $payment_mean_brand ) {
		$this->payment_mean_brand_list[] = $payment_mean_brand;
	}

	/**
	 * Get payment mean brand list
	 *
	 * @return string ANS128 listString comma separated list
	 */
	public function get_payment_mean_brand_list() {
		return apply_filters( 'pronamic_pay_omnikassa_payment_mean_brand_list', implode( ', ', $this->payment_mean_brand_list ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Get order ID
	 *
	 * @return string
	 */
	public function get_order_id() {
		return $this->order_id;
	}

	/**
	 * Set order ID
	 *
	 * @param string $orderId
	 */
	public function set_order_id( $order_id ) {
		$this->order_id = $order_id;
	}

	//////////////////////////////////////////////////

	/**
	 * Get expiration date
	 *
	 * @return DateTime
	 */
	public function get_expiration_date() {
		return $this->expiration_date;
	}

	/**
	 * Get expiration date
	 *
	 * @return string
	 */
	public function get_formatted_expiration_date() {
		$result = null;

		if ( null !== $this->expiration_date ) {
			$result = $this->expiration_date->format( DATE_ISO8601 );
		}

		return $result;
	}

	/**
	 * Set expiration date
	 *
	 * @param DateTime $expirationDate
	 */
	public function set_expiration_date( DateTime $date = null ) {
		$this->expiration_date = $date;
	}

	//////////////////////////////////////////////////

	public function get_data_array() {
		// Payment Request - required fields
		$required_fields = array(
			'amount'               => $this->get_formatted_amount(),
			'currencyCode'         => $this->get_currency_numeric_code(),
			'merchantId'           => $this->get_merchant_id(),
			'normalReturnUrl'      => $this->get_normal_return_url(),
			'transactionReference' => $this->get_transaction_reference(),
			'keyVersion'           => $this->get_key_version(),
		);

		// Payment request - optional fields
		$optional_fields = array(
			'automaticResponseUrl' => $this->get_automatic_response_url(),
			'customerLanguage'     => $this->get_customer_language(),
			'paymentMeanBrandList' => $this->get_payment_mean_brand_list(),
			'orderId'              => $this->get_order_id(),
			'expirationDate'       => $this->get_formatted_expiration_date(),
		);

		// @see http://briancray.com/2009/04/25/remove-null-values-php-arrays/
		$optional_fields = array_filter( $optional_fields );

		// Data
		$data = $required_fields + $optional_fields;

		return $data;
	}

	//////////////////////////////////////////////////

	/**
	 * Get data
	 *
	 * @return string
	 */
	public function get_data() {
		$data = $this->get_data_array();

		return self::create_piped_string( $data );
	}

	//////////////////////////////////////////////////

	/**
	 * Get secret key
	 *
	 * @return string
	 */
	public function get_secret_key() {
		return $this->secret_key;
	}

	/**
	 * Set secret key
	 *
	 * @return string
	 */
	public function set_secret_key( $secret_key ) {
		$this->secret_key = $secret_key;
	}

	//////////////////////////////////////////////////

	/**
	 * Get seal
	 *
	 * @return string
	 */
	public function get_seal() {
		$data       = $this->get_data();
		$secret_key = $this->get_secret_key();

		return self::compute_seal( $data, $secret_key );
	}

	/**
	 * Compute seal
	 *
	 * @param string $data
	 * @param string $secretKey
	 */
	public static function compute_seal( $data, $secret_key ) {
		$value = $data . $secret_key;
		$value = utf8_encode( $value );

		return hash( self::HASH_ALGORITHM_SHA256, $value );
	}

	//////////////////////////////////////////////////

	/**
	 * Get fields
	 *
	 * @since 1.1.2
	 * @return array
	 */
	public function get_fields() {
		return array(
			'Data'             => $this->get_data(),
			'InterfaceVersion' => $this->get_interface_version(),
			'Seal'             => $this->get_seal(),
		);
	}

	//////////////////////////////////////////////////

	/**
	 * Create an piped string for the specified data array
	 *
	 * @param array $data
	 * @return string
	 */
	public static function create_piped_string( array $data ) {
		// @see http://core.trac.wordpress.org/browser/tags/3.3.1/wp-includes/functions.php#L1385
		return _http_build_query( $data, null, '|', '', false );
	}

	/**
	 * Parse piped string
	 *
	 * @param string $string
	 * @return array
	 */
	public static function parse_piped_string( $string ) {
		$data = array();

		$pairs = explode( '|', $string );
		foreach ( $pairs as $pair ) {
			list( $key, $value ) = explode( '=', $pair );

			$data[ $key ] = $value;
		}

		return $data;
	}

	//////////////////////////////////////////////////

	public function get_response_code_description() {
		return array(
			'00' => 'Transaction success, authorization accepted',
			'02' => 'Please call the bank because the authorization limit on the card has been exceeded',
			'03' => 'Invalid merchant contract',
			'05' => 'Do not honor, authorization refused',
			'12' => 'Invalid transaction, check the parameters sent in the request',
			'14' => 'Invalid card number or invalid Card Security Code or Card (for MasterCard) or invalid Card Verification Value (for Visa/MAESTRO)',
			'17' => 'Cancellation of payment by the end user',
			'24' => 'Invalid status',
			'25' => 'Transaction not found in database',
			'30' => 'Invalid format',
			'34' => 'Fraud suspicion',
			'40' => 'Operation not allowed to this Merchant',
			'60' => 'Pending transaction',
			'63' => 'Security breach detected, transaction stopped',
			'75' => 'The number of attempts to enter the card number has been exceeded (three tries exhausted)',
			'90' => 'Acquirer server temporarily unavailable',
			'94' => 'Duplicate transaction',
			'97' => 'Request time-out; transaction refused',
			'99' => 'Payment page temporarily unavailable',
		);
	}
}
