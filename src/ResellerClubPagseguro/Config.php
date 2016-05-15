<?php
namespace ResellerClubPagseguro;

class Config
{
	// Your ResellerClub KEY for Payment Gateway
	const RESELLERCLUB_KEY = 'AhBNDrHBeIcHqcZHe3usTBvvnrfXKNKC';
	const PAGSEGURO_RETURN_URL = 'http://pagamento.desenvolve4web.com/postpayment.php';

	public static function getPagSeguroConfig()
	{
		return array(
			'environment' => 'sandbox', // sandbox or production
			'credentials' => array(
				'email' => 'glauberportella@gmail.com',
				'token' => array(
					'sandbox' => 'E67C7B3036764CE695250776DAB54AC0',
					'production' => 'FD362C3423F94F0E97F5906E483D3423',
				),
				'appId' => array(
					'sandbox' => '',
					'production' => '',
				),
				'appKey' => array(
					'sandbox' => '',
					'production' => '',
				),
			),
			'application' => array(
				'charset' => 'UTF-8',
			),
			'log' => array(
				'active' => false,
				'fileLocation' => '',
			),
		);
	}
}
