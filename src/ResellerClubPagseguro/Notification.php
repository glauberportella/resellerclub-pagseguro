<?php

namespace ResellerClubPagseguro;

class Notification
{
	const WAITING_PAYMENT	= 1;
	const IN_ANALYSIS		= 2;
	const PAID				= 3;
	const AVAILABLE			= 4;
	const IN_DISPUTE 		= 5;
	const REFUNDED			= 6;
	const CANCELLED			= 7;
	/**
	 * Listen to PagSeguro notifications
	 */
	static public function listen()
	{
		// PagSeguro credentials
		$credentials = new \PagSeguroAccountCredentials(\ResellerClubPagseguro\Config::PAGSEGURO_EMAIL, \ResellerClubPagseguro\Config::PAGSEGURO_TOKEN);

		// Notification type
		$type = $_POST['notificationType'];  
  
		// Notification code
		$code = $_POST['notificationCode'];  		

		// received a transaction notification
		if ($type === 'transaction') {
			// Get the PagSeguroTransaction object
			$transaction = \PagSeguroNotificationService::checkTransaction(  
				$credentials,
				$code // notification code
			);

			$status = $transaction->getStatus();

			// If status = PAID we process the payment on Resellerclub.
			// The PagSeguro PAID status can be refunded or cancelled,
			// as reseller club also allows refund it is ok to
			// process on a PagSeguro PAID status
			switch ($status->getValue()) {
				case static::WAITING_PAYMENT:
					self::waitingPayment();
					break;
				case static::IN_ANALYSIS:
					self::inAnalysis();
					break;
				case static::PAID:
					self::paid();
					break;
				case static::AVAILABLE:
					self::available();
					break;
				case static::IN_DISPUTE:
					self::inDispute();
					break;
				case static::REFUNDED:
					self::refunded();
					break;
				case static::CANCELLED:
					self::cancelled();
					break;
			}

		}
	}

	static protected function paid()
	{

	}

	// here you can implement if needed to care about these statuses
	static protected function waitingPayment()	{ }
	static protected function inAnalysis()		{ }
	static protected function available()		{ }
	static protected function inDispute()		{ }
	static protected function refunded()		{ }
	static protected function cancelled()		{ }
}