<?php

namespace ResellerClub\Pagseguro;

class Database
{
	static private $config = array();
	
	static private $instance = null;
	static private $con = null;

	static public function instance(array $config)
	{
		if (null === static::$instance) {
			static::$config = $config;
			static::$con = new \PDO(sprintf('mysql:dbname=%s;host=%s;port=%d', static::$config['DB_NAME'], static::$config['DB_HOST'], static::$config['DB_PORT']), static::$config['DB_USER'], static::$config['DB_PASS']);
			static::createTable();
			static::$instance = new Database();
		}

		return static::$instance;
	}

	/**
	 * @return \PDO connection object
	 */
	public function getConnection()
	{
		if (null === static::$con) {
			static::$con = new \PDO(sprintf('mysql:dbname=%s;host=%s;port=%d', static::$config['DB_NAME'], static::$config['DB_HOST'], static::$config['DB_PORT']), static::$config['DB_USER'], static::$config['DB_PASS']);
		}

		return static::$con;
	}

	/**
	 * Saves a reseller club transaction on database
	 * 
	 * @param  array   $transactionData IMPORTANT the transactionData array must keep the order of keys as the database table columns order
	 * @return boolean
	 */
	public function saveResellerClubTransaction(array $transactionData)
	{
		$sql = 'INSERT INTO '.static::$config['TABLENAME'].' VALUES(NULL, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, 1);';
		$stmt = static::getConnection()->prepare($sql);

		$values = array_values($transactionData);
		
		return $stmt->execute($values);
	}

	/**
	 * Gets a reseller club transaction by its (resellerclub) transid
	 * 
	 * @param  string  $transid
	 * @return array   Associative array with transaction column names as array keys
	 */
	public function getResellerClubTransactionById($transid)
	{
		$sql = 'SELECT * FROM '.static::$config['TABLENAME'].' WHERE transid = ?';
		
		$stmt = static::getConnection()->prepare($sql);
		$stmt->execute(array($transid));
		$transaction = $stmt->fetch(\PDO::FETCH_ASSOC);

		return $transaction;
	}

	/**
	 * Gets a reseller club transaction by its (pagseguro) transaction id
	 * 
	 * @param  string $pagseguroTransactionId
	 * @return array   Associative array with transaction column names as array keys
	 */
	public function getResellerClubTransactionByPagSeguroTransactionId($pagseguroTransactionId)
	{
		$sql = 'SELECT * FROM '.static::$config['TABLENAME'].' WHERE pagseguroTransactionId = ?';

		$stmt = static::getConnection()->prepare($sql);
		$stmt->execute(array($pagseguroTransactionId));
		$transaction = $stmt->fetch(\PDO::FETCH_ASSOC);

		return $transaction;
	}

	/**
	 * Creates the table to store transactions
	 * @param  \PDO $con
	 * @return void
	 */
	static private function createTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'.static::$config['TABLENAME'].'` ('
			.'id 						INT UNSIGNED AUTO_INCREMENT NOT NULL,'
			.'createdat 				TIMESTAMP DEFAULT CURRENT_TIMESTAMP,'
			.'`key`						VARCHAR(64) NOT NULL,'
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
			.'city						VARCHAR(50),'
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
			.'pagseguroTransactionStatus TINYINT DEFAULT 1,'
			.'PRIMARY KEY(id),'
			.'INDEX(transid),'
			.'INDEX(pagseguroTransactionId)'
		.') Engine=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci';

		static::$con->exec($sql);
	}

}
