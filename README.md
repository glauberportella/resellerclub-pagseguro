SETUP
=====

		1. Edit src/ResellerClubPagseguro/Config.php, change params for ResellerClub and PagSeguro

		    const RESELLERCLUB_KEY = 'YOUR RESELLER CLUB SECURE KEY';  
		    const PAGSEGURO_RETURN_URL = 'YOUR HOST OR DOMAIN/resellerclub/postpayment.php';  
		    const PAGSEGURO_EMAIL = 'PAGSEGURO ACCOUNT EMAIL';  
		    const PAGSEGURO_TOKEN = 'PAGSEGURO TOKEN';  

		2. Upload www content to your server public html directory

		3. Upload src and vendor dir to non web accessible directory (i.e. if in a linux cpanel host the www content to public_html and src and vendor to account root dir)

		4. Edit www/bootstrap.php to point correctly to where you put the vendor dir

		5. Edit www/resellerclub/paymentpage.php to your site needs

		6. Edit www/resellerclub/postpayment.php to your site needs

		7. Done