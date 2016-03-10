<?	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/quiz.php";
	require_once "../../inc/class/treinamento.php";
	
	$quiz = new quiz();
	$objQuiz = $quiz->getOneQuiz($_GET['idQuiz']);
	
	if( $_GET['action'] == 'excluir' )
	{
		$quiz->delPergunta( trataVarSql($_GET['idQuizPergunta']) );
		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";
			
		header("Location: questoes.php?action=listar&p=".$p."&idQuiz=".$_REQUEST['idQuiz']);
		die();
	}
	
	if( $_GET['action'] == 'incluir' )
	{
		$quiz->cadastraPerguntaAvulsa( trataVarSql($_POST['idQuiz']) );
		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";
			
		header("Location: questoes.php?action=listar&p=".$p."&idQuiz=".$_REQUEST['idQuiz']);
		die();
	}
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="../css/main.css" rel="stylesheet" type="text/css">
<script src="../js/listagem.js" type="text/javascript"></script>
<script src="../js/functions.inc.js" type="text/javascript"></script>
<script src="../js/menu.js" type="text/javascript"></script>
<script src="../js/jquery-1.3.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
	jQuery.noConflict();
	function abreRespostas( idTrForm, idQuizPergunta )
	{
		var trForm = document.getElementById( idTrForm );
		if( trForm.style.display == "" )
			trForm.style.display = "none";
		else
		{
			trForm.style.display = "";
			document.getElementById('msgErrorPergunta'+idQuizPergunta).innerHTML = "";
		}
	}
	
	function alteraPergunta(p_idQuizPergunta)
	{
		jQuery("#loader"+p_idQuizPergunta).show("fast");
		jQuery("#msgError").html("");
		var erros = new Array();
		var p_txtTempo = jQuery("input[name=txtTempo"+ p_idQuizPergunta +"]").val();
		var p_txtPergunta = jQuery("input[name=txtPergunta"+ p_idQuizPergunta +"]").val();

		if( p_txtTempo == "" )
			erros.push("Nome da Pergunta");

		if( erros.length > 0 )
		{
			if( erros.length == 1 )
				msgCustom = "O campo "+erros.join(", ")+" não foi preenchido corretamente.";
			else
				msgCustom = "Os campos "+erros.join(", ")+" não foram preenchidos corretamente.";
			jQuery("#msgError").html(msgCustom);
			jQuery("#loader"+p_idQuizPergunta).hide("fast");
		}
		else
		{
			jQuery.post('../ajax_scripts/altera_pergunta.php', { txtTempo: p_txtTempo, txtPergunta: p_txtPergunta, idQuizPergunta: p_idQuizPergunta }, function(data) {
				jQuery("#msgError").html(data);
				jQuery("#loader"+p_idQuizPergunta).hide("fast");
			});	
		}
	}
	
	function atualizaRespostas( p_idQuizPergunta )
	{
		jQuery("#loader_respostas"+p_idQuizPergunta).show("fast");
		document.getElementById('formRespostas'+p_idQuizPergunta).action = '../ajax_scripts/altera_respostas.php';
		document.getElementById('formRespostas'+p_idQuizPergunta).target = 'iframe_' + p_idQuizPergunta;
		document.getElementById('formRespostas'+p_idQuizPergunta).submit();	
	}
	
	function adicionaNovaResposta( idTableRespostas, idQuizPergunta )
	{
		var numRadios = jQuery('#formRespostas'+idQuizPergunta+' :input[name=respostaCerta]').length;		
		var randId = Math.floor(Math.random()*9999)
		jQuery('#'+idTableRespostas+' tbody>tr:last').after('<tr name="trResposta'+ randId +'"><td><div class="floatLeft" style="padding: 2px; color: #666666; width: 535px;"><input type="text" name="txtResposta[]" class="inputText" size="95" /></div><div class="floatLeft" style="width: 80px; text-align: center; margin-left: 3px;  pading-top: 1px; padding-bottom: 2px;"><input type="radio" name="respostaCerta" value="'+ (numRadios+1) +'" /></div><div class="floatLeft" style="width: 100px; text-align: center;"><input type="button" name="btRemover" value="Remover" class="inputButton" onclick="removeAlternativa(this)" /></div><div class="clear"></div></div><div style="height: 5px;"></td></tr>');
	}
	
	function removeAlternativa( obj )
	{
		//obj é o botão. vamos remover a linha a partir dele.
		jQuery(obj).parent().parent().parent().remove();
	}
	
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>

<body style="background-color: #FFFFFF;">
<table cellpadding="0" cellspacing="0" border="0" style="width: 90%;" align="center">
	<tr>
		<td style="border-bottom: 1px solid  #CCCCCC; text-align: center; padding-top: 5px; padding-bottom: 5px; color: #FFFFFF; font-weight: bold; background-color: <?=TITULO_INTERNAS_BGCOLOR?>;">Perguntas do Quiz "<?php print $objQuiz->nome; ?>"</td>
	</tr>
    <tr>
		<td style="height: 30px; border: 1px solid #EFEFEF; text-align: center;"><a href="listar.php?p=<?=$_REQUEST['p']?>" class="linkVoltar">&bull; Voltar para a listagem &bull;</a></td>
	</tr>
	<tr>
		<td style="padding-top: 5px;">
			<!-- filtros -->
			<table style="width: 100%;" cellpadding="3" cellspacing="0">
            	<tr>
					<td style="text-align: center;" class="topTextFiltros">I n s e r i r&nbsp;&nbsp;&nbsp;N o v a&nbsp;&nbsp;&nbsp;P e r g u n t a</td>
				</tr>
				<tr>
					<td style="height: 60px; padding-top: 10px; border: 1px solid #CCCCCC; background-color: #EFEFEF;">
						<form action="questoes.php?action=incluir&p=<?=$_REQUEST['p']?>" name='formCadastroPergunta' method="POST">
                        	<input type='hidden' name='idQuiz' value='<?php print $objQuiz->idQuiz; ?>' />
							<table border="0" cellpadding="4" cellspacing="0" style="width: 100%;">
								<tr>
									<td class="msgError" id='msgError' style="text-align: center;"><? print $_SESSION['msg']; $_SESSION['msg'] = "";?></td>
								</tr>
							</table>
							<table border="0" cellpadding="2" cellspacing="0">
								<tr>
									<td>Pergunta: </td>
									<td style='padding-left: 10px;'><input type="text" class="inputText" name="txtPergunta" id="txtPergunta" style='width: 250px;' maxlength="250" /></td>
								</tr>
                                <tr>
									<td>Tempo (segundos): </td>
									<td style='padding-left: 10px;'><input type="text" class="inputText" name="txtTempo" id="txtTempo" style='width: 100px;' maxlength="10" onkeydown="return noLetters(event);" /></td>
								</tr>
								<tr>
									<td></td>
									<td style='padding-left: 100px;'><input type="submit" class="inputButton" value="Incluir Nova Pergunta" name="btIncluir" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 10px;">
						<table align="center" cellpadding="3" cellspacing="0" style="width: 98%; border-collapse: collapse; color: #666666;" border="1" bordercolor="#999999">
							<tr>
                                <td class="topTextTitleGrid" style="width: 80%;">Pergunta</td>
								<td class="topTextTitleGrid" style="width: 20%;" colspan='3'>Ações</td>
							</tr>
							<? print $quiz->listaPerguntas( $objQuiz->idQuiz ); ?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>