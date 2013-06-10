<?php
	session_start();
	//@session_save_path("./");  //specify path where you want to save the session.
	require("functions.php");	//file which has required functions
	// ResellerClub-PagSeguro bootstrap
	require_once(__DIR__."/../bootstrap.php");
?>

<html>
<head><title>Payment Page </title>
<script language="JavaScript">
		function successClicked()
		{
			document.paymentpage.submit();
		}
		function failClicked()
		{
			document.paymentpage.status.value = "N";
			document.paymentpage.submit();
		}
		function pendingClicked()
		{
			document.paymentpage.status.value = "P";
			document.paymentpage.submit();
		}
</script>
</head>
<body bgcolor="white">

<?php

		//$key = "44q9dn7WCUrLHgi8bPsdiBIlLi6WaHI0"; //replace ur 32 bit secure key , Get your secure key from your Reseller Control panel
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

		// echo "File paymentpage.php<br>";
		// echo "Checksum Verification..............";

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

			$payment = new \ResellerClubPagseguro\Payment();
			$paymentUrl = $payment->createRequestUrl($resellerPaymentData, $customerData);
			if ($paymentUrl) {
?>
				<script type="text/javascript">window.location.href = "<?php echo $paymentUrl; ?>";</script>
<?php
			}

			/*echo "Verified<br>";
			echo "List of Variables Received as follows<br>";
			echo "Paymenttypeid : ".$paymentTypeId."<br>";
			echo "transid : ".$transId."<br>";
			echo "userid : ".$userId."<br>";
			echo "usertype : ".$userType."<br>";
			echo "transactiontype : ".$transactionType."<br>";
			echo "invoiceids : ".$invoiceIds."<br>";
			echo "debitnoteids : ".$debitNoteIds."<br>";
			echo "description : ".$description."<br>";
			echo "sellingcurrencyamount : ".$sellingCurrencyAmount."<br>";
			echo "accountingcurrencyamount : ".$accountingCurrencyAmount."<br>";
			echo "redirecturl : ".$redirectUrl."<br>";
			echo "checksum : ".$checksum."<br><br>";*/
?>
<?php /*
<form name="paymentpage" action="postpayment.php">
	<input type="hidden" name="status" value="Y">
	<input type="button" name="btnSuccess" onClick="successClicked();" value="Continue Test of a Successful Transaction"><br>
	<input type="button" name="btnPending" onClick="pendingClicked();" value="Continue Test of a Pending Transaction"><br>
	<input type="button" name="btnFailed" onClick="failClicked();" value="Continue Test of a Failed Transaction"><br>
</form>
*/
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

			echo "Checksum mismatch !";

		}
?>
</body>
</html>
