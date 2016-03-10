<?php 
	require_once "../inc/config.inc.php";
	require_once "../inc/class/quiz.php";
	
	if( count($_SESSION['perguntas']) >= $_SESSION['numQuestao'] )
	{
		$quiz = new quiz();
		$objPergunta = $quiz->getPergunta($_SESSION['perguntas'][$_SESSION['numQuestao'] - 1]);
		
		//devolvo o n�mero da quest�o atual, o tempo da quest�o e o total de quest�es
		print $_SESSION['numQuestao'] . "|" . $objPergunta->tempo . "|" . count($_SESSION['perguntas']) . "|" . $objPergunta->idQuizPergunta;
	}
	else
		print "0";
?>