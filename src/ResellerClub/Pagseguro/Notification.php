<?php

namespace ResellerClub\Pagseguro;

class Notification
{
	const WAITING_PAYMENT	= 1;
	const IN_ANALYSIS		= 2;
	const PAID				= 3;
	const AVAILABLE			= 4;
	const IN_DISPUTE 		= 5;
	const REFUNDED			= 6;
	const CANCELLED			= 7;

	private $config = array();

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * Listen to PagSeguro notifications
	 *
	 * @param array $config Pagseguro and Resellerclub configurations
	 */
	public function listen()
	{
		// PagSeguro credentials
		$credentials = new \PagSeguroAccountCredentials($this->config['PAGSEGURO_EMAIL'], $this->config['PAGSEGURO_TOKEN']);

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
					$this->waitingPayment($transaction);
					break;
				case static::IN_ANALYSIS:
					$this->inAnalysis($transaction);
					break;
				case static::PAID:
					$this->paid($transaction);
					break;
				case static::AVAILABLE:
					$this->available($transaction);
					break;
				case static::IN_DISPUTE:
					$this->inDispute($transaction);
					break;
				case static::REFUNDED:
					$this->refunded($transaction);
					break;
				case static::CANCELLED:
					$this->cancelled($transaction);
					break;
			}

		}
	}

	protected function paid(\PagSeguroTransaction $transaction)
	{
		$db = \ResellerClub\Pagseguro\Database::instance($this->config);
		$con = $db->getConnection();
		
		$sql = 'SELECT * FROM '.\ResellerClub\Pagseguro\Config::TABLENAME.' WHERE pagseguroTransactionId = ?';
		
		$stmt = $con->prepare($sql);
		$stmt->execute(array($transaction->getCode()));
		$resellerClubTransaction = $stmt->fetch(\PDO::FETCH_ASSOC);

		if ($resellerClubTransaction) {
			$payment = new \ResellerClub\Pagseguro\Payment($this->config);
			// Send POST to Resellerclub with the paid transaction
			if (true === $payment->post($resellerClubTransaction)) {
				// Updates pagseguro transaction status on DB for reference
				$resellerClubTransaction['pagseguroTransactionStatus'] = static::PAID;
				$update = 'UPDATE '.$this->config['TABLENAME'].' SET pagseguroTransactionStatus = ? WHERE id = ?';
				$stmt2 = $con->prepare($update);
				$stmt2->execute(array(static::PAID, $resellerClubTransaction['id']));
			}
		}
	}

	// here you can implement if needed to care about these statuses
	protected function waitingPayment(\PagSeguroTransaction $transaction)	{ }
	protected function inAnalysis(\PagSeguroTransaction $transaction)		{ }
	protected function available(\PagSeguroTransaction $transaction)		{ }
	protected function inDispute(\PagSeguroTransaction $transaction)		{ }
	protected function refunded(\PagSeguroTransaction $transaction)			{ }
	protected function cancelled(\PagSeguroTransaction $transaction)		{ }
}