<?php
namespace ResellerClubPagseguro;

require_once __DIR__.'/../../vendor/pagseguro/source/PagSeguroLibrary/PagSeguroLibrary.php';

class Payment
{
	public function createRequestUrl(array $resellerPaymentData)
	{
		// Instantiate a new payment request
		$paymentRequest = new \PagSeguroPaymentRequest();

		// Sets the currency
		$paymentRequest->setCurrency("BRL");

		// Add an item for this payment request
		$paymentRequest->addItem('0001', 'Notebook prata', 2, 430.00);

		// Sets a reference code for this payment request, it is useful to identify this payment in future notifications.
		$paymentRequest->setReference("REF123");

		// (optional) Sets shipping information for this payment request
		// $CODIGO_SEDEX = \PagSeguroShippingType::getCodeByType('SEDEX');
		// $paymentRequest->setShippingType($CODIGO_SEDEX);
		// $paymentRequest->setShippingAddress('01452002', 'Av. Brig. Faria Lima', '1384', 'apto. 114', 'Jardim Paulistano', 'São Paulo', 'SP', 'BRA');

		// Sets your customer information.
		$paymentRequest->setSender('João Comprador', 'comprador@s2it.com.br', '11', '56273440', 'CPF', '123.456.789-01');

		// Sets the url used by PagSeguro for redirect user after ends checkout process
		$paymentRequest->setRedirectUrl("http://www.lojamodelo.com.br");

		try {
			// PagSeguro credentials
			$credentials = \PagSeguroConfig::getAccountCredentials();
			// or $credentials = new \PagSeguroAccountCredentials("your@email.com", "your_token_here");
			// Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
			$url = $paymentRequest->register($credentials);
			return $url;
		} catch (\PagSeguroServiceException $e) {
			echo "<p><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
			return null;
		}
	}
}
