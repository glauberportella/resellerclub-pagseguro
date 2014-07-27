<?php
session_save_path('./');
session_start();

// ResellerClub-PagSeguro bootstrap
require_once(__DIR__."/bootstrap.php");

$notification = new \ResellerClub\Pagseguro\Notification($pagseguro_config);
$notification->listen();