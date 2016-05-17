<?php

	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	session_start();
	require("functions.php");	//file which has required functions
	// ResellerClub-PagSeguro bootstrap
	require_once(__DIR__."/bootstrap.php");
?>

<html>
<head><title>Desenvolve4Web - Pagamento</title>

</head>
<body bgcolor="white">

<?php

		$key = \ResellerClubPagseguro\Config::RESELLERCLUB_KEY;

		//Below are the  parameters which will be passed from foundation as http GET request

		$paymentTypeId = $_GET["paymenttypeid"];  //payment type id
		$transId = $_GET["transid"];			   //This refers to a unique transaction ID which we generate for each transaction
		$userId = $_GET["userid"];               //userid of the user who is trying to make the payment
		$userType = $_GET["usertype"];  		   //This refers to the type of user perofrming this transaction. The possible values are "Customer" or "Reseller"
		$transactionType = $_GET["transactiontype"];  //Type of transaction (ResellerAddFund/CustomerAddFund/ResellerPayment/CustomerPayment)

		$invoiceIds = $_GET["invoiceids"];		   //comma separated Invoice Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
		$debitNoteIds = $_GET["debitnoteids"];	   //comma separated DebitNotes Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"

		$description = $_GET["description"];

		$sellingCurrencyAmount = $_GET["sellingcurrencyamount"]; //This refers to the amount of transaction in your Selling Currency
		$accountingCurrencyAmount = $_GET["accountingcurrencyamount"]; //This refers to the amount of transaction in your Accounting Currency

		$redirectUrl = $_GET["redirecturl"];  //This is the URL on our server, to which you need to send the user once you have finished charging him


		$checksum = $_GET["checksum"];	 //checksum for validation

		if(verifyChecksum($paymentTypeId, $transId, $userId, $userType, $transactionType, $invoiceIds, $debitNoteIds, $description, $sellingCurrencyAmount, $accountingCurrencyAmount, $key, $checksum))
		{
			// Parameters which will be passed from foundation as http GET request
			$resellerPaymentData = array(
					'key'						=> $key,
					'paymenttypeid' 			=> $paymentTypeId,
					'transid' 					=> $transId,
					'userid'					=> $userId,
					'usertype'					=> $userType,
					'transactiontype'			=> $transactionType,
					'invoiceids'				=> $invoiceIds,
					'debitnoteids'				=> $debitNoteIds,
					'description'				=> $description,
					'sellingcurrencyamount' 	=> $sellingCurrencyAmount,
					'accountingcurrencyamount' 	=> $accountingCurrencyAmount,
					'redirecturl'				=> $redirectUrl,
					'checksum'					=> $checksum
				);

			// aditional variables passed by ResellerClub and that will
			// be used to create PagSeguro payment
			$customerData = array(
					'name' 					=> $_GET['name'],
					'company' 				=> $_GET['company'],
					'emailAddr' 			=> $_GET['emailAddr'],
					'address1' 				=> $_GET['address1'],
					'address2' 				=> $_GET['address2'],
					'address3' 				=> $_GET['address3'],
					'city' 					=> $_GET['city'],
					'state' 				=> $_GET['state'],
					'country' 				=> $_GET['country'],
					'zip' 					=> $_GET['zip'],
					'telNoCC' 				=> $_GET['telNoCc'],
					'telNo'					=> $_GET['telNo'],
					'faxNoCC' 				=> $_GET['faxNoCc'],
					'faxNo'					=> $_GET['faxNo'],
					'resellerEmail' 		=> $_GET['resellerEmail'],
					'resellerURL' 			=> $_GET['resellerURL'],
					'resellerCompanyName' 	=> $_GET['resellerCompanyName']
				);

			/**
			* since all these data has to be passed back to foundation after making the payment you need to save these data
			*
			* You can make a database entry with all the required details which has been passed from foundation.
			*
			*							OR
			*
			* keep the data to the session which will be available in postpayment.php as we have done here.
			*
			* It is recommended that you make database entry.
			**/
			foreach ($resellerPaymentData as $key => $value) {
				$_SESSION[$key] = $value;
			}
			foreach ($customerData as $key => $value) {
				$_SESSION[$key] = $value;
			}

			// transId on session
			$_SESSION['transid'] = $transId;

			$payment = new \ResellerClubPagseguro\Payment();
			$paymentUrl = $payment->createRequestUrl($resellerPaymentData, $customerData);
			if ($paymentUrl) {
?>
				<script type="text/javascript">window.location.href = "<?php echo $paymentUrl; ?>";</script>
<?php
			}
?>
<?php

		}
		else
		{
			/**This message will be dispayed in any of the following case
			*
			* 1. You are not using a valid 32 bit secure key from your Reseller Control panel
			* 2. The data passed from foundation has been tampered.
			*
			* In both these cases the customer has to be shown error message and shound not
			* be allowed to proceed  and do the payment.
			*
			**/

			echo "Erro de checksum. Algum erro ocorreu no processamento. Nada foi debitado de sua conta. Tente novamente mais tarde, se o error persistir entre em contato com o suporte.";

		}
?>
</body>
</html>
