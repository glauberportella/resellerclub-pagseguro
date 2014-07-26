<?php
namespace ResellerClubPagseguro;

class Config
{
	// Your ResellerClub KEY for Payment Gateway and other parameters, change to
	// fit your needs
	const RESELLERCLUB_KEY 		= 'AhBNDrHBeIcHqcZHe3usTBvvnrfXKNKC';
	const PAGSEGURO_RETURN_URL 	= 'http://site.pagamento.com/postpayment.php';
	const PAGSEGURO_EMAIL 		= 'email@pagamento.com';
	const PAGSEGURO_TOKEN 		= '8DAEC9E606FC409084D42A125278B756';

	// Database configs, the reselleclub_pagseguro_transaction table will be created for use
	// to save the transactions data on your MySQL server
	const DB_HOST = 'localhost';	// change to your mysql server host
	const DB_PORT = '3306';			// mysql port (default = 3306)
	const DB_NAME = 'test'; 		// change to your database name
	const DB_USER = 'root'; 		// change to your database user
	const DB_PASS = '1q2w3e'; 		// change to your database user password

	// Default tablename for transactions in resellerclub with pagseguro	
	const TABLENAME = 'resellerclub_pagseguro_transaction';

	static private $con = null;

	static public function prepareDatabase()
	{
		static::databaseConnection();
		static::createTable();
	}

	/**
	 * Creates a PDO connection to the database
	 * @return \PDO
	 */
	static public function databaseConnection()
	{
		if (self::$con === null) {
			self::$con = new \PDO(sprintf('mysql:dbname=%s;host=%s;port=%d', static::DB_NAME, static::DB_HOST, static::DB_PORT), static::DB_USER, static::DB_PASS);
		}

		return self::$con;
	}

	/**
	 * Creates the table to store transactions
	 * @param  \PDO $con
	 * @return void
	 */
	static private function createTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS '.static::TABLENAME.'('
			.'id 						INT UNSIGNED AUTO_INCREMENT NOT NULL,'
			.'createdat 				DATETIME DEFAULT NOW(),'
			.'key 						VARCHAR(64) NOT NULL,'
			.'paymenttypeid 			INT UNSIGNED NOT NULL,'
			.'transid 					VARCHAR(64) NOT NULL,'
			.'userid 					INT UNSIGNED NOT NULL,'
			.'usertype 					VARCHAR(30) NOT NULL,'
			.'transactiontype 			VARCHAR(50) NOT NULL,'
			.'invoiceids 				TINYTEXT,'
			.'debitnoteids 				TINYTEXT,'
			.'description 				TINYTEXT,'
			.'sellingcurrencyamount 	DECIMAL(18,3),'
			.'accountingcurrencyamount 	DECIMAL(18,3),'
			.'redirecturl 				VARCHAR(255),'
			.'checksum 					VARCHAR(255),'
			.'name						VARCHAR(255),'
			.'company					VARCHAR(255),'
			.'emailAddr					VARCHAR(255),'
			.'address1					VARCHAR(255),'
			.'address2					VARCHAR(100),'
			.'address3					VARCHAR(100),'
			.'city,						VARCHAR(50),'
			.'state						VARCHAR(50),'
			.'country					VARCHAR(50),'
			.'zip						VARCHAR(20),'
			.'telNoCC					VARCHAR(10),'
			.'telNo						VARCHAR(20),'
			.'faxNoCC					VARCHAR(10),'
			.'faxNo						VARCHAR(20),'
			.'resellerEmail				VARCHAR(255),'
			.'resellerURL				VARCHAR(255),'
			.'resellerCompanyName		VARCHAR(255),'
			.'pagseguroTransactionId	VARCHAR(128),'
			.'PRIMARY KEY(id),'
			.'INDEX(transid)'
		.')';

		self::$con->exec($sql);
	}

}
