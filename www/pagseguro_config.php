<?php
//
// CONFIGURE
// 
$pagseguro_config = array(

	// Your ResellerClub KEY for Payment Gateway and other parameters, change to fit your needs
	'RESELLERCLUB_KEY' 		=> 'AhBNDrHBeIcHqcZHe3usTBvvnrfXKNKC',
	
	'PAGSEGURO_RETURN_URL' 	=> 'http://site.pagamento.com/pagseguro_return.php',
	'PAGSEGURO_EMAIL' 		=> 'email@pagamento.com',
	'PAGSEGURO_TOKEN' 		=> '8DAEC9E606FC409084D42A125278B756',

	// Database configs, the reselleclub_pagseguro_transaction table will be created for use
	// to save the transactions data on your MySQL server
	'DB_HOST' 				=> 'localhost',	// change to your mysql server host
	'DB_PORT' 				=> '3306',			// mysql port (default = 3306)
	'DB_NAME' 				=> 'test', 		// change to your database name
	'DB_USER' 				=> 'root', 		// change to your database user
	'DB_PASS' 				=> '1q2w3e', 		// change to your database user password

	// Default tablename for transactions in resellerclub with pagseguro	
	'TABLENAME' 			=> 'resellerclub_pagseguro_transaction',

);