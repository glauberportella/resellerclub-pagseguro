<?php
namespace ResellerClub\Pagseguro;

/**
 * Class to process Pagseguro payments from ResellerClub
 *
 * @package ResellerClub\Pagseguro
 * @author Glauber Portella <glauberportella@gmail.com>
 * @version 0.1
 */
class Payment
{
	private $config = array();

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * Generate a payment request url for PagSeguro
	 *
	 * @param array $transactionData ResellerClub transaction information data for payment process
	 * @throws \PagSeguroServiceException
	 * @return string
	 */
	public function createRequestUrl(array $transactionData)
	{
		// Instantiate a new payment request
		$paymentRequest = new \PagSeguroPaymentRequest();

		// Sets the currency (only accepts BRL)
		$paymentRequest->setCurrency("BRL");

		// Add an item for this payment request
		$amount = '0.00';

		if ($transactionData['sellingcurrencyamount'] == $transactionData['accountingcurrencyamount'])
		{
			$amount = number_format($transactionData['sellingcurrencyamount'], 2, '.', '');
		}
		else
		{
			// @TODO calc currency change
			$amount = number_format($transactionData['sellingcurrencyamount'], 2, '.', '');
		}

		if (in_array($transactionData['transactiontype'], array('ResellerPayment', 'CustomerPayment')))
		{
			$description = substr($transactionData['description'], 0, 100);

			if ($transactionData['invoiceids']) {
				$paymentRequest->addItem($transactionData['invoiceids'], $description, 1, $amount);
			} elseif ($transactionData['debitnoteids']) {
				$paymentRequest->addItem($transactionData['debitnoteids'], $description, 1, $amount);
			} else {
				$paymentRequest->addItem($transactionData['transid'], $description, 1, $amount);
			}

		}
		else
		{
			$description = substr('Adição de fundo - '.$transactionData['name'], 0, 100);
			$paymentRequest->addItem($transactionData['transid'], $description, 1, $amount);
		}

		// Sets a reference code for this payment request, it is useful to identify this payment in future notifications.
		$paymentRequest->setReference($transactionData['transid']);

		// Sets your customer information.
		$paymentRequest->setSender($transactionData['name'], $transactionData['emailAddr']);

		// Sets shipping type (3 = NOT_SPECIFIED)
		$paymentRequest->setShippingType(\PagSeguroShippingType::getCodeByType('NOT_SPECIFIED'));

		// Sets the url used by PagSeguro for redirect user after ends checkout process
		$paymentRequest->setRedirectUrl($this->config['PAGSEGURO_RETURN_URL']);

		// PagSeguro credentials
		$credentials = new \PagSeguroAccountCredentials($this->config['PAGSEGURO_EMAIL'], $this->config['PAGSEGURO_TOKEN']);

		// Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
		$url = $paymentRequest->register($credentials);

		return $url;
	}

	/**
	 * Post process Resellerclub payment
	 * 
	 * @param array $transaction Transaction from ResellerClub as saved in DB
	 */
	public function post(array $transaction)
	{
		$key = $this->config['RESELLERCLUB_KEY'];

		// redirectUrl received from foundation
		$redirectUrl = $transaction['redirecturl'];
		// Pass the same transid which was passsed to your Gateway URL at the beginning of the transaction.
		$transId = $transaction['transid'];
		$sellingCurrencyAmount = $transaction['sellingcurrencyamount'];
		$accountingCurrencyAmount = $transaction['accountingcurrencyamount'];
		$status = $transaction['pagseguroTransactionStatus'] === \ResellerClub\Pagseguro\Notification::PAID ? 'Y' : 'N';

		// random key
		srand((double)microtime()*1000000);
		$rkey = rand();
		// verify checsum
		$checksum = \ResellerClub\Checksum::generate($transId, $sellingCurrencyAmount, $accountingCurrencyAmount, $status, $rkey, $key);

		// Post to Resellerclub via cURL
		$curl = curl_init();

		curl_setopt( $curl , CURLOPT_URL , $redirectUrl );
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
		curl_setopt( $curl , CURLOPT_POST , 1 );
		curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( array(
				'transid' => $transId,
				'status' => $status,
				'rkey' => $rkey,
				'checksum' => $checksum,
				'sellingcurrencyamount' => $sellingCurrencyAmount,
				'accountingcurrencyamount' => $accountingCurrencyAmount
			) ) );

		$response = curl_exec( $curl );
		$error = curl_error( $curl );
		$errno = curl_errno( $curl );

		curl_close( $curl );

		if ( !empty( $error ) && $errno != 0 ) {
			return false;
		}

		return true;
	}
}
