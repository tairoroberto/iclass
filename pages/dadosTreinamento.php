<?php 
	require_once "../inc/config.inc.php";
	require_once "../inc/class/treinamento.php"; 
	
	$treinamento = new treinamento();
	$objTreinamento = $treinamento->getOneTreinamento($_REQUEST['idTreinamento']);

	print $objTreinamento->slides;
?>