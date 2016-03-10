<?php 
	require_once "../inc/config.inc.php";
	require_once "../inc/class/treinamento.php"; 
	require_once "../inc/class/quiz.php";
	
	$treinamento = new treinamento();
	$quiz = new quiz();
	$objTreinamento = $treinamento->getOneTreinamento($_REQUEST['idTreinamento']);
	$objQuiz = $quiz->getOneQuizFromTreinamento($_REQUEST['idTreinamento']);
	$perguntasQuiz = $quiz->getPerguntas($objQuiz->idQuiz, 1);

	//se abriu o quiz agora, então inicia o contador
	$_SESSION['numQuestao'] = 1;
	$_SESSION['respostaUsuario'] = array();		
	$_SESSION['perguntas'] = array();

	//monta as questões e traz o html da primeira questão
	for( $i=0; $i<count($perguntasQuiz); $i++ )
	{
		$_SESSION['perguntas'][] = $perguntasQuiz[$i]->idQuizPergunta;
		if( $i==0 )
		{
			$htmlPrimeiraQuestao = "<div class='pergunta'>".($i + 1).") ".$perguntasQuiz[$i]->pergunta."</div>";
			$_SESSION['idPrimeiraQuestao'] = $perguntasQuiz[$i]->idQuizPergunta; //seta o id da primeira questão
			$respostas = $quiz->getRespostas($perguntasQuiz[$i]->idQuizPergunta, 1, 1);
			for( $j=0; $j<count($respostas); $j++ )
			{
				$htmlPrimeiraQuestao .= "<div class='resposta'><input type='radio' name='radioResposta' value='".$respostas[$j]->idQuizResposta."' />&nbsp;&nbsp;".$_SESSION['arrLetras'][$j].") ".$respostas[$j]->resposta."</div>";
			}
		}
	}

	print $htmlPrimeiraQuestao;
?>