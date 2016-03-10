<?
	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/quiz.php";
	require_once "../../inc/class/treinamento.php";
	require_once "../../inc/class/imagem.php";
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
	
	$quiz = new quiz();
	$treinamento = new treinamento();

	if( $_GET['action'] == "incluir" || !$_GET['action'] )
	{
		$btLabel 		= "Incluir";
		$head 			= "I n c l u i r&nbsp;&nbsp;&nbsp;&nbsp;Q u i z";
	}
	elseif( $_GET['action'] == "alterar" )
	{
		$btLabel 		= "Alterar";
		$head 			= "A l t e r a r&nbsp;&nbsp;&nbsp;&nbsp;Q u i z";
		$linhaReg		= $quiz->getOneQuiz($_REQUEST['idQuiz']);
		$perguntas		= $quiz->getPerguntas($_REQUEST['idQuiz']);
		
		/*//monta perguntas
		$camposPerguntas = "";
		for($i=0; $i<count($perguntas); $i++)
		{
			$respostas = $quiz->getRespostas($perguntas[$i]->idQuizPergunta);
			$camposPerguntas .= '<div><div><div class="floatLeft" style="width: 70px; padding: 3px;">Pergunta '. ($i + 1) .':&nbsp;</div><div  class="floatLeft"><input type="text" value="'.$perguntas[$i]->pergunta.'" name="pergunta[]" class="inputText" size="25" /></div><div class="clear"></div></div><div><div class="floatLeft" style="width: 70px; padding: 3px;">Tempo:&nbsp;</div><div class="floatLeft"><input type="text" style="text-align: left;" onkeydown="return noLetters(event);" name="tempo[]" value="'.$perguntas[$i]->tempo.'" maxlength="10" size="3" class="inputText" value="" /> <span style="color: #666666;">(em segundos)&nbsp;&nbsp;&nbsp;</span></div><div class="floatLeft" style="padding-left: 10px;"><input type="button" name="btAdicionarResposta" onclick="adicionarResposta('.($i + 1).')" value="Adicionar Resposta" class="inputButton" /></div><div class="clear"></div></div></div><div style="height: 5px;"></div><div id="divRespostas'. ($i + 1) .'" name="divRespostas" style="padding-bottom: 10px;">';
			for( $j=0; $j<count($respostas); $j++ )
				$camposPerguntas .= '<div><div class="floatLeft" style="padding: 2px; padding-left: 35px; color: #666666; font-size: 11px;">Alternativa:&nbsp;</div><div class="floatLeft"><input type="text" name="resposta'. ($i + 1) .'[]" value="'.$respostas[$j]->resposta.'" class="inputText" size="25" /></div><div class="floatLeft"><input type="radio" name="respostaCerta'. ($i + 1) .'" value="'. ($j + 1) .'" '.(($respostas[$j]->certa == "1") ? "checked" : "").' /></div><div class="clear"></div></div><div style="height: 5px;"></div>';
		}*/
	}
	
	//montando combo de treinamentos
	$allTreinamentos = $treinamento->allTreinamentosSemQuiz($_GET['idQuiz']);
	$comboTreinamentos = "<select name='idTreinamento' class='combo'>";	
	while( $linha = $db->fetchObject($allTreinamentos) )
	{
		if( $linhaReg->idTreinamento == $linha->idTreinamento )
			$selected = "selected";
		else
			$selected = "";			
		$comboTreinamentos .= "<option value='". $linha->idTreinamento ."' ". $selected .">". $linha->nome  ."</option>";
	}	
	$comboTreinamentos .= "</select>";

	//incluindo
	if( $_POST['btEnviar'] == $btLabel )
	{
		if( $_GET['action'] == "incluir" )
		{
			//se não conseguir incluir, volta pra mesma página
			if( $quiz->insertQuiz() )
			{	
				//header("Location: listar.php?action=listar&begin=1");
				header("Location: incluir.php?action=alterar&idQuiz=".$_POST['idQuiz']);
				die();
			}
		}
		elseif( $_GET['action'] == "alterar" )
		{
			if( $quiz->alterQuiz() )
				header("Location: listar.php?action=listar&begin=1");
			else
				header("Location: incluir.php?action=alterar&idQuiz=".$_POST['idQuiz']);
			die();
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" language="javascript">
//verifica se os dados do formulário estão ok
function submitForm()
{
	var form = document.forms['form'];
	var erro = new Array();
	
	if( !trim(form.textoNome.value) )
		erro.push("Nome");
	
	if( erro.length > 0 )
	{
		alert("Preencha os seguintes campos corretamente: " + erro.join(', '));
		return false;
	}
	else
		return true;
}

function delImg(id, numImg)
{
	location.href = "incluir.php?action=alterar&p=<?=$_GET['p']?>&idQuiz=<?=$_GET['idQuiz']?>&idQuizDel=" + id + "&numImg=" + numImg + "&subaction=delImg";
}

function adicionarPergunta()
{	
	$('#divPerguntas').append('<div><div><div class="floatLeft" style="padding: 3px; width: 70px;">Pergunta '+ ($("div[name=divRespostas]").length + 1) +':&nbsp;</div><div class="floatLeft"><input type="text" name="pergunta[]" class="inputText" size="25" /></div><div class="clear"></div></div><div><div class="floatLeft" style="width: 70px; padding: 3px;">Tempo:&nbsp;</div><div class="floatLeft"><input type="text" style="text-align: left;" onkeydown="return noLetters(event);" name="tempo[]" maxlength="10" size="3" class="inputText" value="" /> <span style="color: #666666;">(em segundos)&nbsp;&nbsp;&nbsp;</span></div><div class="floatLeft" style="padding-left: 10px;"><input type="button" name="btAdicionarResposta" onclick="adicionarResposta('+ ($("div[name=divRespostas]").length + 1) +')" class="inputButton" value="Adicionar Resposta" /></div><div class="clear"></div></div></div><div style="height: 5px;"></div><div id="divRespostas'+ ($("div[name=divRespostas]").length + 1) +'" name="divRespostas" style="padding-bottom: 10px;"></div>');
}

function adicionarResposta( numPergunta )
{
	$('#divRespostas' + numPergunta).append('<div><div class="floatLeft" style="padding: 2px; padding-left: 35px; color: #666666; font-size: 11px;">Alternativa:&nbsp;</div><div class="floatLeft"><input type="text" name="resposta'+ numPergunta +'[]" class="inputText" size="25" /></div><div class="floatLeft"><input type="radio" name="respostaCerta'+numPergunta+'" value="'+($("input[name=respostaCerta"+numPergunta+"]").length + 1)+'" /></div><div class="clear"></div></div><div style="height: 5px;"></div>');
}

function verQuestoes()
{
	location.href = "questoes.php?begin=1&p=<?=$_GET['p']?>&idQuiz=<?=$_GET['idQuiz']?>";
}
</script>
<script src="../js/functions.inc.js" type="text/javascript"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<link href="../css/main.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>

<body style="background-color: #FFFFFF;">
<table cellpadding="0" cellspacing="0" border="0" style="width: 90%;" align="center">
	<tr>
		<td style="border-bottom: 1px solid  #CCCCCC; text-align: center; padding-top: 5px; padding-bottom: 5px; color: #FFFFFF; font-weight: bold; background-color: <?=TITULO_INTERNAS_BGCOLOR?>;"><?=$head?></td>
	</tr>
	<? 	if( $_GET['action'] == "alterar" )
		{
	?>
	<tr>
		<td style="height: 30px; border: 1px solid #EFEFEF; text-align: center;"><a href="listar.php?p=<?=$_REQUEST['p']?>" class="linkVoltar">&bull; Voltar para a listagem &bull;</a></td>
	</tr>
	<?
		}
	?>
	<tr>
		<td style="padding-top: 10px;">
			<form name="form" id="form" action="incluir.php?action=<?=($_GET['action'] ? $_GET['action'] : "incluir")?>" method="POST" onsubmit="return submitForm();" enctype="multipart/form-data">
				<input type='hidden' name='idQuiz' value='<?=$_GET['idQuiz']?>' />
				<table cellpadding="5" cellspacing="0" border="0" align="center">
					<tr>
						<td style="text-align: center;" colspan="2" class="msgError"><? print $_SESSION['msg']; $_SESSION['msg'] = ""; ?></td>
					</tr>
                    <? 	if( $_GET['action'] == "alterar" )
						{
					?>
                    <tr>
                    	<td colspan="2" style="text-align: center;"><input type="button" name="btQuestoes" onclick='verQuestoes();' class='inputButton' value="CLIQUE AQUI PARA VER AS PERGUNTAS DESTE QUIZ" size="50" /></td>
                    </tr>
                    <?
						}
					?>
					<tr>
						<td colspan="2"><b>* Campos obrigatórios</b></td>
					</tr>
                    <tr>
                        <td style="width: 35%;" id="txtNome">* Nome:</td>
                        <td style="width: 65%;"><input type="text" style='text-align: left;' name="textoNome" id="textoNome" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->nome, ENT_QUOTES)?><?=htmlentities($_POST['textoNome'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td id="txtNome">Descri&ccedil;&atilde;o:</td>
                        <td><textarea id="descricao" name="descricao" cols="40" rows="5" class="inputText"><?=htmlentities($linhaReg->descricao, ENT_QUOTES)?><?=htmlentities($_POST['descricao'], ENT_QUOTES)?></textarea></td>
                    </tr>
					<tr>
                        <td id="txtNome">* Treinamento:</td>
                        <td><?php print $comboTreinamentos; ?></td>
                    </tr>
                    <tr>
                        <td>Ativo:</td>
                        <td><input type="checkbox" name="ativo" value="1" <?php if($_POST['ativo'] || $linhaReg->ativo) print "checked"; ?> /></td>
                    </tr>
					<!--<tr>
						<td colspan="2" style="vertical-align: top; text-align: center; font-size: 14px; padding-top: 10px; font-weight: bold;">P E R G U N T A S</td>
                    </tr>
                    <tr>
						<td colspan="2">
							<div style="text-align: center;"><input type="button" name="btAdicionarPergunta" id="btAdicionarPergunta"  class="inputButton" value="Adicionar Pergunta" onclick="adicionarPergunta()" /></div>
							<div id="divPerguntas" style="padding-top: 5px;">
								<?php print $camposPerguntas; ?>
							</div>
						</td>
					</tr> -->
                    <tr>
                        <td colspan="2" align='center' style="padding-top: 15px;"><input type="submit" name="btEnviar" class='inputButton' value="<?=$btLabel?>" /></td>
                    </tr>
				</table>
			</form>
		</td>
	</tr>
</table>
</body>
</html>
