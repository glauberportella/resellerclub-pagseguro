<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Pagamento</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body style="margin-top: 100px">

	<div class="container">

		<?php
		// ResellerClub-PagSeguro bootstrap
		require_once(__DIR__."/bootstrap.php");

		// saves transaction id on reseller transaction table
		$credentials = new \PagSeguroAccountCredentials($pagseguro_config['PAGSEGURO_EMAIL'], $pagseguro_config['PAGSEGURO_TOKEN']);
		  
		/* Código identificador da transação  */    
		$transaction_id = $_GET['transaction_id'];
		  
		// gets pagseguro transaction so we can get reseller club transid from reference code
		$transaction = \PagSeguroTransactionSearchService::searchByCode(
		    $credentials,
		    $transaction_id  
		);

		if ($transaction)
		{
			// updates reseller transaction o db to save the pagseguro transaction id
			$db = \ResellerClub\Pagseguro\Database::instance($pagseguro_config);

			$update = 'UPDATE '.$pagseguro_config['TABLENAME'].' SET pagseguroTransactionId = ? WHERE transid = ?';
						
			$stmt = $db->getConnection()->prepare($update);
			$success = $stmt->execute(array($transaction->getCode(), $transaction->getReference()));

			if (true === $success)
			{
		?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2>Pagamento Efetuado com Sucesso</h2>
					</div>
					<div class="panel-body">
						<p class="lead">Seu pedido será processado e liberado assim que o PagSeguro nos notificar sobre aprovação do pagamento.</p>
						<p class="lead">Agradecemos pela compra.</p>
					</div>
					<div class="panel-footer text-center">
						<a href="<?php echo $pagseguro_config['WEBSITE_URL'] ?>" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span> Retornar ao Site</a>
					</div>
				</div>
		<?php
			}
			else
			{
		?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2>Erro ao salvar Pagamento</h2>
					</div>
					<div class="panel-body">
						<p class="lead">Seu pedido se encontra em nosso sistema porém não foi possível atualizá-lo com o dado da transação PagSeguro.</p>
						<p class="lead">Entre em contato com o suporte e informe o ocorrido com o <b>ID da Transação PagSeguro <?php transaction_id ?></b>.</p>
					</div>
					<div class="panel-footer text-center">
						<a href="<?php echo $pagseguro_config['WEBSITE_URL'] ?>" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span> Retornar ao Site</a>
					</div>
				</div>
		<?php
			}
		?>
		<?php
		}
		else
		{
		?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2>Erro Fatal</h2>
					</div>
					<div class="panel-body">
						<p class="lead">Erro fatal. Não existe transação PagSeguro associada a esse pedido. Entre em contato com o suporte para identificar e verificar o pedido caso o mesmo realmente foi efetuado.</p>
					</div>
					<div class="panel-footer text-center">
						<a href="<?php echo $pagseguro_config['WEBSITE_URL'] ?>" class="btn btn-lg btn-danger"><span class="glyphicon glyphicon-ok"></span> Retornar ao Site</a>
					</div>
				</div>
		<?php
		} // end if
		?>

	</div>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>