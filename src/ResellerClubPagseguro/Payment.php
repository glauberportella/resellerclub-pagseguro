<?php
namespace ResellerClubPagseguro;

use PagSeguroConfig;

/**
 * Class to process Pagseguro payments from ResellerClub
 *
 * @package ResellerClubPagseguro
 * @author Glauber Portella <glauberportella@gmail.com>
 * @version 0.1
 */
class Payment
{
	protected $pagSeguroConfig;

	public function __construct()
	{
		// pagseguro config
		// set pagseguro config
		$this->pagSeguroConfig = Config::getPagSeguroConfig();

		PagSeguroConfig::init();
		PagSeguroConfig::setEnvironment($this->pagSeguroConfig['environment']);
		PagSeguroConfig::setApplicationCharset($this->pagSeguroConfig['application']['charset']);
		foreach ($this->pagSeguroConfig as $key1 => $config) {
			if (in_array($key1, array('environment', 'application')))
				continue;

			foreach ($config as $key2 => $value) {
				PagSeguroConfig::setData($key1, $key2, $value);
			}
		}
	}

	/**
	 * Generate a payment request url for PagSeguro
	 *
	 * @param array $resellerPaymentData ResellerClub transaction information data for payment process
	 * @param array $customerData ResellerClub customer information
	 * @return boolean|string False on error or payment URL for PagSeguro
	 */
	public function createRequestUrl(array $resellerPaymentData, array $customerData)
	{
		// Instantiate a new payment request
		$paymentRequest = new \PagSeguroPaymentRequest();

		// Sets the currency (only accepts BRL)
		$paymentRequest->setCurrency("BRL");

		// Add an item for this payment request
		$amount = '0.00';
		if ($resellerPaymentData['sellingcurrencyamount'] == $resellerPaymentData['accountingcurrencyamount'])
		{
			$amount = number_format($resellerPaymentData['sellingcurrencyamount'], 2, '.', '');
		}
		else
		{
			// @TODO calc currency change
			$amount = number_format($resellerPaymentData['sellingcurrencyamount'], 2, '.', '');
		}

		if (in_array($resellerPaymentData['transactiontype'], array('ResellerPayment', 'CustomerPayment')))
		{
			$description = substr($resellerPaymentData['description'], 0, 100);

			if ($resellerPaymentData['invoiceids']) {
				$paymentRequest->addItem($resellerPaymentData['invoiceids'], $description, 1, $amount);
			} elseif ($resellerPaymentData['debitnoteids']) {
				$paymentRequest->addItem($resellerPaymentData['debitnoteids'], $description, 1, $amount);
			} else {
				$paymentRequest->addItem($resellerPaymentData['transid'], $description, 1, $amount);
			}

		}
		else
		{
			$description = substr('Adição de fundo - '.$customerData['name'], 0, 100);
			$paymentRequest->addItem($resellerPaymentData['transid'], $description, 1, $amount);
		}


		// Sets a reference code for this payment request, it is useful to identify this payment in future notifications.
		$paymentRequest->setReference($resellerPaymentData['transid']);

		// Sets your customer information.
		$paymentRequest->setSender($customerData['name'], $customerData['emailAddr']);

		// Sets shipping type (3 = NOT_SPECIFIED)
		$paymentRequest->setShippingType(\PagSeguroShippingType::getCodeByType('NOT_SPECIFIED'));

		// Sets the url used by PagSeguro for redirect user after ends checkout process
		$paymentRequest->setRedirectUrl(Config::PAGSEGURO_RETURN_URL);

		try {
			// PagSeguro credentials
			$credentials = PagSeguroConfig::getAccountCredentials();
			// Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
			$url = $paymentRequest->register($credentials);
			return $url;
		} catch (\PagSeguroServiceException $e) {
			return false;
		}
	}

	public static function isSuccessTransaction()
	{
		return true; // NAO FUNCIONA MAIS isset($_GET['transaction_id']) && !empty($_GET['transaction_id']) ? true : false;
	}

	public static function getTransactionId()
	{
		return isset($_GET['transaction_id']) ? $_GET['transaction_id'] : null;
	}
}
