<?php
	 session_start();
	 session_save_path("./"); //path on your server where you are storing session


	//file which has required functions
	require("functions.php");

	// ResellerClub-PagSeguro bootstrap
	require_once(__DIR__."/bootstrap.php");
 ?>
<html>
<head><title>Desenvolve4Web - Pagamento Concluído</title></head>
<body bgcolor="white">
<font size=4>

<?php
		$key = \ResellerClubPagseguro\Config::RESELLERCLUB_KEY; //replace ur 32 bit secure key , Get your secure key from your Reseller Control panel

		$redirectUrl = $_SESSION['redirecturl'];  // redirectUrl received from foundation
		$transId = $_SESSION['transid'];		 //Pass the same transid which was passsed to your Gateway URL at the beginning of the transaction.
		$sellingCurrencyAmount = $_SESSION['sellingcurrencyamount'];
		$accountingCurrencyAmount = $_SESSION['accountingcurrencyamount'];


		$status = \ResellerClubPagseguro\Payment::isSuccessTransaction() ? 'Y' : 'N';	 // Transaction status received from your Payment Gateway
		//This can be either 'Y' or 'N'. A 'Y' signifies that the Transaction went through SUCCESSFULLY and that the amount has been collected.
		//An 'N' on the other hand, signifies that the Transaction FAILED.

		// Glauber Portella <glauberportella@gmail.com>
		// 1. se status = Y salvar \ResellerClubPagseguro\Payment::getTransactionId() no banco para futuras referencias na conta pagseguro

		/**HERE YOU HAVE TO VERIFY THAT THE STATUS PASSED FROM YOUR PAYMENT GATEWAY IS VALID.
		* And it has not been tampered with. The data has not been changed since it can * easily be done with HTTP request.
		*
		**/

		srand((double)microtime()*1000000);
		$rkey = rand();


		$checksum =generateChecksum($transId,$sellingCurrencyAmount,$accountingCurrencyAmount,$status, $rkey,$key);

		// POST to ResellerClub Server
		/*$curl = curl_init();

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
				'accountingcurencyamount' => $accountingCurrencyAmount
			) ) );

		$response = curl_exec( $curl );
		$error = curl_error( $curl );
		$errno = curl_errno( $curl );

		curl_close($curl);

		if ( empty( $error ) && $errno == 0 ) {
			echo $response;
		} else {
			die(sprintf('Post error: [%d] %s', $errno, $error));
		}
*/
			echo "File: postpayment.php<br>";
			echo "redirecturl: ".$redirectUrl."<br>";
			echo "List of Variables to send back<br>";
			echo "transid : ".$transId."<br>";
			echo "status : ".$status."<br>";
			echo "rkey : ".$rkey."<br>";
			echo "checksum : ".$checksum."<br><br>";

?>
		<form name="f1" action="<?php echo $redirectUrl;?>">
			<input type="hidden" name="transid" value="<?php echo $transId;?>">
			<input type="hidden" name="status" value="<?php echo $status;?>">
			<input type="hidden" name="rkey" value="<?php echo $rkey;?>">
			<input type="hidden" name="checksum" value="<?php echo $checksum;?>">
			<input type="hidden" name="sellingamount" value="<?php echo $sellingCurrencyAmount;?>">
			<input type="hidden" name="accountingamount" value="<?php echo $accountingCurrencyAmount;?>">

			<input type="submit" value="Click here to Continue"><BR>
		</form>
</font>
</body>
</html>