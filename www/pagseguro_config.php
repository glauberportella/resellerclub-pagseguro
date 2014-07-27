<?php
//
// CONFIGURE
// 1. RESELLERCLUB PARAMS
// 2. PAGSEGURO PARAMS
// 3. DATABASE PARAMS (FOR RESELLER CLUB TRANSACTION PERSISTENCY)
// 4. YOUR PARAMS
// 
$pagseguro_config = array(
	/********************************************************************************************
	 * 1. RESELLER CLUB PARAMS
	 ********************************************************************************************/

	// Your ResellerClub KEY for Payment Gateway and other parameters, change to fit your needs
	'RESELLERCLUB_KEY' 			=> 'AhBNDrHBeIcHqcZHe3usTBvvnrfXKNKC',
	// Your ResellerClub ID (Access your account and go to Manage Profile, this must be the number in Reseller ID field)
	'RESELLERCLUB_RESELLER_ID'	=> '',
	//  ResellerClub API Key (Access your account, click Settings > API, and then View API key)
	//  if, on future, you generate a new fresh API Key, you must specify it again here
	'RESELLERCLUB_API_KEY'		=> '',
	// Your ResellerClub user access and password
	'RESELLERCLUB_USERNAME'		=> 'host@desenvolve4web.com',
	'RESELLERCLUB_PASSWORD'		=> 'devgp120182',

	// REMEMBER TO WHITELIST YOUR SERVER IP WITH RESELLERCLUB
	// 1. Login to your Reseller Control Panel.
	// 2. In the Menu, point to Settings and then click API.
	// 3. Under the Whitelist your IP Addresses section, specify the 
	//    IP Addresses from where you will be making API requests and 
	//    then click the Save whitelisted IP addresses button.
	'RESELLERCLUB_TEST_API_URL' => 'https://test.httpapi.com/',

	/********************************************************************************************
	 * 2. PAGSEGURO PARAMS
	 ********************************************************************************************/

	'PAGSEGURO_RETURN_URL' 	=> 'http://pagamento.desenvolve4web.com/pagseguro_return.php',
	'PAGSEGURO_EMAIL' 		=> 'host@desenvolve4web.com',
	'PAGSEGURO_TOKEN' 		=> '8DAEC9E606FC409084D42A125278B756',

	/********************************************************************************************
	 * 3. DATABASE PARAMS
	 ********************************************************************************************/

	// Database configs, the reselleclub_pagseguro_transaction table will be created for use
	// to save the transactions data on your MySQL server
	'DB_HOST' 				=> 'localhost',		// change to your mysql server host
	'DB_PORT' 				=> '3306',			// mysql port (default = 3306)
	'DB_NAME' 				=> 'desen3go_test', // change to your database name
	'DB_USER' 				=> 'desen3go_test', // change to your database user
	'DB_PASS' 				=> '1q2w3e',		// change to your database user password
	// Default tablename for transactions in resellerclub with pagseguro	
	'TABLENAME' 			=> 'resellerclub_pagseguro_transaction',

	/********************************************************************************************
	 * 4. YOUR PARAMS
	 ********************************************************************************************/

	// URL to return to site, edit to your need
	'WEBSITE_URL'			=> 'http://www.desenvolve4web.com',
);