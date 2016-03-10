<?	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/quiz.php";
	
	$quiz = new quiz();
	
	print $quiz->alteraPerguntaAvulsa($_REQUEST['idQuizPergunta']);
?>
	