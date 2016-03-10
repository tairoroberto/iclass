<?php 
	require_once "../inc/config.inc.php";
	require_once "../inc/class/treinamento.php"; 
	require_once "../inc/class/quiz.php";
	
	$treinamento = new treinamento();
	$quiz = new quiz();
	
	$perguntaAtual = $quiz->getPergunta($_REQUEST['idQuizPergunta']);
	
	++$_SESSION['numQuestao'];
	
	$_SESSION['respostaUsuario']["idTreinamento"] = $_REQUEST['idTreinamento'];
	
	//computa a resposta do usuário	
	$_SESSION['respostaUsuario']["acertos"] = (($_REQUEST['idQuizResposta'] == $quiz->pegaRespostaCorreta($_REQUEST["idQuizPergunta"])) ? ($_SESSION['respostaUsuario']["acertos"] + 1) : $_SESSION['respostaUsuario']["acertos"]);
	
	//incrementa o contador de questões
	if( count($_SESSION['perguntas']) >= $_SESSION['numQuestao'] )
	{
		$proximaPergunta = $quiz->getPergunta($_SESSION['perguntas'][$_SESSION['numQuestao'] - 1]);
		
		//traz o html da próxima questão
		$htmlProximaQuestao = "<div class='pergunta'>".$_SESSION['numQuestao'].") ".$proximaPergunta->pergunta."</div>";
		$_SESSION['idPrimeiraQuestao'] = $proximaPergunta->idQuizPergunta; //seta o id da primeira questão
		$respostas = $quiz->getRespostas($proximaPergunta->idQuizPergunta, 1, 1);
		for( $j=0; $j<count($respostas); $j++ )
		{
			$htmlProximaQuestao .= "<div class='resposta'><input type='radio' name='radioResposta' value='".$respostas[$j]->idQuizResposta."' />&nbsp;&nbsp;".$_SESSION['arrLetras'][$j].") ".$respostas[$j]->resposta."</div>";
		}
		
		print $htmlProximaQuestao;
	}
	else // fim do quiz. Acabaram as questões.
	{
		//grava resultados no banco.
		$quiz->computaResultado();
		
		print "<div style='text-align: center; font-weight: bold; font-size: 13px; padding-top: 150px;'>Obrigado por participar do nosso Quiz!<br /><br />Clique <a href='index.php?land=treinamentos_usuario'>aqui</a> para visualizar seu Boletim.</div>";
	}
	
	
?>