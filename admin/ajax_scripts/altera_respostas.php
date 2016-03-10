<?	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/quiz.php";
	
	$quiz = new quiz();
	$quiz->cadastraRespostas($_REQUEST['idQuizPergunta']);
	
	print "<script type='text/javascript'>parent.document.getElementById('loader_respostas".$_REQUEST['idQuizPergunta']."').style.display = 'none'; parent.document.getElementById('msgErrorPergunta".$_REQUEST['idQuizPergunta']."').innerHTML = 'Respostas atualizadas com sucesso.'; </script>";
?>
	