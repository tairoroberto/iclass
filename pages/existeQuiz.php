<?php 
	require_once "../inc/config.inc.php";
	require_once "../inc/class/treinamento.php"; 
	
	$treinamento = new treinamento();
	$countQuiz = $treinamento->existeQuiz($_REQUEST['idTreinamento']);

	if( $countQuiz > 0 )
		print "1";
	else
		print "0";
?>