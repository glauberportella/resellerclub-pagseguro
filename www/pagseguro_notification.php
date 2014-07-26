<?php

// ResellerClub-PagSeguro bootstrap
require_once(__DIR__."/bootstrap.php");

$notification = new \ResellerClub\Pagseguro\Notification($pagseguro_config);
$notification->listen();