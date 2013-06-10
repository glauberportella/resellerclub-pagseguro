<?php
namespace ResellerClubPagseguro;

require_once __DIR__.'/../../vendor/PagSeguroLibrary/PagSeguroLibrary.php';

/**
 * Class to process Pagseguro payments from ResellerClub
 *
 * @package ResellerClubPagseguro
 * @author Glauber Portella <glauberportella@gmail.com>
 * @version 0.1
 */
class Payment
{
	/**
	 * Generate a payment request url for PagSeguro
	 *
	 * @param array $resellerPaymentData ResellerClub transaction information data for payment process
	 * @return null|string Null on error or payment URL for PagSeguro
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

		$paymentRequest->addItem($resellerPaymentData['invoiceids'], $resellerPaymentData['description'], 1, $amount);

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
			$credentials = new \PagSeguroAccountCredentials(Config::PAGSEGURO_EMAIL, Config::PAGSEGURO_TOKEN);
			// Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
			$url = $paymentRequest->register($credentials);
			return $url;
		} catch (\PagSeguroServiceException $e) {
			echo "<p><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
			return null;
		}
	}
}
