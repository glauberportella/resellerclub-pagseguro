# About

A PagSeguro (http://www.pagseguro.com.br) brazillian Payment Gateway Integration for Reseller Club resellers.

# Development

Clone the repository `git clone git@github.com:glauberportella/resellerclub-pagseguro.git`

Install dependencies `composer install`

# Setup on your Host

1. Copy 'www/' directory contents to your web public directory (or one of your choice in your public web dir);

2. Copy 'src' and 'vendor' directories to a non-public directory on your server;

3. Edit www/pagseguro_config.php, change params for ResellerClub and PagSeguro to your needs;

4. Edit www/bootstrap.php to point correctly to where you put the vendor dir relative to your 'www' (web public dir);

5. On your ResellerClub Panel go to settings and add a New Payment Gateway and inform the Gateway Url to 'pagseguro_payment.php' on your web public directory. Ex.: http://www.yoursite.com/pagseguro_payment.php;

6. On your PagSeguro account configure the Notification API to point to http://www.yoursite.com/pagseguro_notification.php;

7. Done.