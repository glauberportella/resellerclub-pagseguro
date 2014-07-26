<?php

class DatabaseTest extends \PHPUnit_Framework_TestCase
{

	private $pagseguro_config = array(

		// Your ResellerClub KEY for Payment Gateway and other parameters, change to fit your needs
		'RESELLERCLUB_KEY' 		=> 'AhBNDrHBeIcHqcZHe3usTBvvnrfXKNKC',
		
		'PAGSEGURO_RETURN_URL' 	=> 'http://localhost:8000/pagseguro_return.php',
		'PAGSEGURO_EMAIL' 		=> 'host@desenvolve4web.com',
		'PAGSEGURO_TOKEN' 		=> '8DAEC9E606FC409084D42A125278B756',

		// Database configs, the reselleclub_pagseguro_transaction table will be created for use
		// to save the transactions data on your MySQL server
		'DB_HOST' 				=> 'localhost',	// change to your mysql server host
		'DB_PORT' 				=> '3306',		// mysql port (default = 3306)
		'DB_NAME' 				=> 'test', 		// change to your database name
		'DB_USER' 				=> 'test', 		// change to your database user
		'DB_PASS' 				=> '1q2w3e',	// change to your database user password

		// Default tablename for transactions in resellerclub with pagseguro	
		'TABLENAME' 			=> 'resellerclub_pagseguro_transaction',

	);

	public function testInstance()
	{
		$instance = \ResellerClub\Pagseguro\Database::instance($this->pagseguro_config);
		$this->assertInstanceOf('\ResellerClub\Pagseguro\Database', $instance);
	}

	public function testGetConnection()
	{
		$instance = \ResellerClub\Pagseguro\Database::instance($this->pagseguro_config);
		$this->assertInstanceOf('\ResellerClub\Pagseguro\Database', $instance);

		$con = $instance->getConnection();
		$this->assertInstanceOf('\PDO', $con);
	}

	public function testSaveResellerClubTransaction()
	{
		$instance = \ResellerClub\Pagseguro\Database::instance($this->pagseguro_config);
		$this->assertInstanceOf('\ResellerClub\Pagseguro\Database', $instance);

		$con = $instance->getConnection();
		$this->assertInstanceOf('\PDO', $con);

		// truncate table data
		$con->exec('TRUNCATE TABLE '.$this->pagseguro_config['TABLENAME']);

		$sample_transaction = array(
			'key'						=> $this->pagseguro_config['RESELLERCLUB_KEY'],
			'paymenttypeid' 			=> 1000,
			'transid' 					=> 'Asaoiuoifaosiyduaisu',
			'userid'					=> 2000,
			'usertype'					=> 'Customer',
			'transactiontype'			=> 'CustomerPayment',
			'invoiceids'				=> '12345',
			'debitnoteids'				=> null,
			'description'				=> 'Teste',
			'sellingcurrencyamount' 	=> 100.000,
			'accountingcurrencyamount' 	=> 120.000,
			'redirecturl'				=> 'http://localhost:8000/return.php',
			'checksum'					=> '123456789000000000',
			// aditional variables passed by ResellerClub and that will
			// be used to create PagSeguro payment
			'name' 					=> 'Glauber Portella',
			'company' 				=> 'Desenvolve4Web',
			'emailAddr' 			=> 'glauberportella@gmail.com',
			'address1' 				=> 'Rua A, 123',
			'address2' 				=> 'Bairro B',
			'address3' 				=> 'null',
			'city' 					=> 'Belo Horizonte',
			'state' 				=> 'Minas Gerais',
			'country' 				=> 'Brazil',
			'zip' 					=> '31111111',
			'telNoCC' 				=> '31',
			'telNo'					=> '12345678',
			'faxNoCC' 				=> null,
			'faxNo'					=> null,
			'resellerEmail' 		=> 'host@desenvolve4web.com',
			'resellerURL' 			=> 'http://www.desenvolve4web.com',
			'resellerCompanyName' 	=> 'Desenvolve4Web',
		);

		$success = $instance->saveResellerClubTransaction($sample_transaction);

		$this->assertTrue($success);
	}

}