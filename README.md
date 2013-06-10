SETUP
=====

1. Edit src/ResellerClubPagseguro/Config.php, change params for ResellerClub and PagSeguro

	$ const RESELLERCLUB_KEY = 'YOUR RESELLER CLUB SECURE KEY';
	$ const PAGSEGURO_RETURN_URL = 'YOUR HOST OR DOMAIN/resellerclub/postpayment.php';
	$ const PAGSEGURO_EMAIL = 'PAGSEGURO ACCOUNT EMAIL';
	$ const PAGSEGURO_TOKEN = 'PAGSEGURO TOKEN';

2. Edit www/resellerclub/paymentpage.php to your site needs

3. Edit www/resellerclub/postpayment.php to your site needs

4. Done