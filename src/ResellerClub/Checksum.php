<?php

namespace ResellerClub;

class Checksum
{
	static public function generate($transId, $sellingCurrencyAmount, $accountingCurrencyAmount, $status, $rkey, $key)
	{
		$str = "$transId|$sellingCurrencyAmount|$accountingCurrencyAmount|$status|$rkey|$key";
        
        $generatedCheckSum = md5($str);
		
		return $generatedCheckSum;
	}

	static public function verify($paymentTypeId, $transId, $userId, $userType, $transactionType, $invoiceIds, $debitNoteIds, $description, $sellingCurrencyAmount, $accountingCurrencyAmount, $key, $checksum)
	{
		$str = "$paymentTypeId|$transId|$userId|$userType|$transactionType|$invoiceIds|$debitNoteIds|$description|$sellingCurrencyAmount|$accountingCurrencyAmount|$key";
        
        $generatedCheckSum = md5($str);

		if($generatedCheckSum == $checksum)
			return true;
		else
			return false;
	}
}