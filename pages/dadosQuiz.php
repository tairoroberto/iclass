<?php 
	require_once "../inc/config.inc.php";
	require_once "../inc/class/quiz.php";
	
	if( count($_SESSION['perguntas']) >= $_SESSION['numQuestao'] )
	{
		$quiz = new quiz();
		$objPergunta = $quiz->getPergunta($_SESSION['perguntas'][$_SESSION['numQuestao'] - 1]);
		
		//devolvo o número da questão atual, o tempo da questão e o total de questões
		print $_SESSION['numQuestao'] . "|" . $objPergunta->tempo . "|" . count($_SESSION['perguntas']) . "|" . $objPergunta->idQuizPergunta;
	}
	else
		print "0";
?>