<?php
// ResellerClub-PagSeguro bootstrap
require_once(__DIR__."/bootstrap.php");


// Reseller club key (configured on pagseguro_config.php)
$key = $pagseguro_config['RESELLERCLUB_KEY'];

//Below are the  parameters which will be passed from foundation as http GET request
$paymentTypeId 				= $_GET["paymenttypeid"];  	//payment type id
$transId 					= $_GET["transid"];		   	//This refers to a unique transaction ID which we generate for each transaction
$userId 					= $_GET["userid"];			//userid of the user who is trying to make the payment
$userType 					= $_GET["usertype"];		//This refers to the type of user perofrming this transaction. The possible values are "Customer" or "Reseller"
$transactionType 			= $_GET["transactiontype"]; //Type of transaction (ResellerAddFund/CustomerAddFund/ResellerPayment/CustomerPayment)
$invoiceIds 				= $_GET["invoiceids"];		//comma separated Invoice Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
$debitNoteIds 				= $_GET["debitnoteids"];	//comma separated DebitNotes Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
$description 				= $_GET["description"];
$sellingCurrencyAmount 		= $_GET["sellingcurrencyamount"];		//This refers to the amount of transaction in your Selling Currency
$accountingCurrencyAmount 	= $_GET["accountingcurrencyamount"];	//This refers to the amount of transaction in your Accounting Currency
$redirectUrl				= $_GET["redirecturl"];  //This is the URL on our server, to which you need to send the user once you have finished charging him
$checksum 					= $_GET["checksum"];	 //checksum for validation

if (true === \ResellerClub\Checksum::verify($paymentTypeId, $transId, $userId, $userType, $transactionType, $invoiceIds, $debitNoteIds, $description, $sellingCurrencyAmount, $accountingCurrencyAmount, $key, $checksum))
{
	// Parameters which will be passed from foundation as http GET request
	// IMPORTANT the transactionData array must keep the order of keys as the database table columns order
	$transactionData = array(
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
			'checksum'					=> $checksum,
			// aditional variables passed by ResellerClub and that will
			// be used to create PagSeguro payment
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
			'resellerCompanyName' 	=> $_GET['resellerCompanyName'],
		);

	
	// Keep the data which will be available in postpayment.php as we have done here.
	$db = \ResellerClub\Pagseguro\Database::instance($pagseguro_config);
	$db->saveResellerClubTransaction($transactionData);
	
	// create the url to PagSeguro Payment
	$payment = new \ResellerClub\Pagseguro\Payment($pagseguro_config);
	$paymentUrl = $payment->createRequestUrl($transactionData);

	header('Location: '.$paymentUrl);
	exit(); // garantee paymentUrl access only
}
else
{
?>
		
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Pagamento</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body style="margin-top: 100px">

	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2>Erro de checksum</h2>
			</div>
			<div class="panel-body">
				<p class="lead">Algum erro ocorreu no processamento. Nada foi debitado de sua conta. Tente novamente mais tarde, se o error persistir entre em contato com o suporte.</p>
			</div>
			<div class="panel-footer text-center">
				<a href="#" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span> Retornar ao Site</a>
			</div>
		</div>
	</div>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>

<?php
} // end if (checksum ok)
?>