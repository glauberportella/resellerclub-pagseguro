<?php
session_save_path('./');
session_start();

require_once __DIR__.'/../bootstrap.php';

$key = $pagseguro_config['RESELLERCLUB_KEY'];

// redirectUrl received from foundation
$redirectUrl = 'http://manage.br.resellerclub.com/servlet/TestCustomPaymentAuthCompletedServlet';
// Pass the same transid which was passsed to your Gateway URL at the beginning of the transaction.
$transId = 'Payment-Test-1';
$sellingCurrencyAmount = '100.0';
$accountingCurrencyAmount = '100.0';
$status = 'Y';

// random key
$rkey = '1653913958';
// verify checsum
$checksum = '8c72a23605b583fca3f7f52db7958494';

// Post to Resellerclub via cURL
$curl = curl_init();

// AUTHENTICATE on foundation
$authUrl = 'https://www.foundationapi.com/servlet/AuthenticationServlet';
$authPostParams = array(
	'username' 			=> $pagseguro_config['RESELLERCLUB_USERNAME'],
	'password' 			=> $pagseguro_config['RESELLERCLUB_PASSWORD'],
	'productCategory' 	=> '',
	'domain' 			=> '',
	'redirectpage' 		=> 'null',
	'currenturl' 		=> 'http://manage.br.resellerclub.com',
	'pid' 				=> '331653',
	'role' 				=> 'reseller',
);

curl_setopt( $curl , CURLOPT_URL , $authUrl );
curl_setopt( $curl , CURLOPT_USERAGENT , 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
curl_setopt( $curl , CURLOPT_FOLLOWLOCATION , true );
curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true );
curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
curl_setopt( $curl , CURLOPT_POST , true );
curl_setopt( $curl , CURLOPT_COOKIEFILE , 'cookie.txt' );
curl_setopt( $curl , CURLOPT_COOKIEJAR  , 'cookie.txt' );
curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query($authPostParams));

$login_response = curl_exec( $curl );
curl_close( $curl );

// It gets redirect to auth on manager.br.resellerclub.com, do it with curl
$doc = new DOMDocument();
$doc->loadHTML($login_response);
$inputs = $doc->getElementsByTagName('input');
// get and create new CURL login from the login form in login_response
$postParams = array();
for ($i = $inputs->length; --$i >= 0;) {
	$input = $inputs->item($i);
	$postParams[$input->getAttribute('name')] = $input->getAttribute('value');
}

$postUrl = 'http://manage.br.resellerclub.com/servlet/AuthenticationPassServlet';
$curl = curl_init();
curl_setopt( $curl , CURLOPT_URL , $postUrl );
curl_setopt( $curl , CURLOPT_USERAGENT , 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
curl_setopt( $curl , CURLOPT_FOLLOWLOCATION , true );
curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true );
curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
curl_setopt( $curl , CURLOPT_POST , true );
curl_setopt( $curl , CURLOPT_COOKIEFILE , 'cookie.txt' );
curl_setopt( $curl , CURLOPT_COOKIEJAR  , 'cookie.txt' );
curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query($postParams));

$response = curl_exec( $curl );
curl_close( $curl );

// NOW DO THE POST PAYMENT REQUEST
$curl = curl_init();
curl_setopt( $curl , CURLOPT_URL , $redirectUrl );
curl_setopt( $curl , CURLOPT_USERAGENT , 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
curl_setopt( $curl , CURLOPT_FOLLOWLOCATION , true );
curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true );
curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
curl_setopt( $curl , CURLOPT_POST , true );
curl_setopt( $curl , CURLOPT_COOKIEFILE , 'cookie.txt' );
curl_setopt( $curl , CURLOPT_COOKIEJAR  , 'cookie.txt' );
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

if (1 === preg_match('/Login Required/i', $response)) {
	$errno = 'LOGIN-00001';
	$error = 'Error. Need to be logged in.';
}

if ( !empty( $error ) && $errno != 0 ) {
	echo "<br>Err no. $errno";
	print_r($error);
} else {
	echo $response;
}