<?
	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/treinamento.php";
	require_once "../../inc/class/fabricante.php";
	require_once "../../inc/class/imagem.php";
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
	
	$treinamento = new treinamento();
	$fabricante = new fabricante();

	if( $_GET['subaction'] == "delImg" )
		$treinamento->delImgTreinamento($_GET['idTreinamentoDel'], $_GET['numImg']);

	if( $_GET['action'] == "incluir" || !$_GET['action'] )
	{
		$btLabel 		= "Incluir";
		$head 			= "I n c l u i r&nbsp;&nbsp;&nbsp;&nbsp;T r e i n a m e n t o";
	}
	elseif( $_GET['action'] == "alterar" )
	{
		$btLabel 		= "Alterar";
		$head 			= "A l t e r a r&nbsp;&nbsp;&nbsp;&nbsp;T r e i n a m e n t o";
		$linhaReg		= $treinamento->getOneTreinamento($_REQUEST['idTreinamento']);
		
		//monta slides
		$slides = explode("|", $linhaReg->slides);
		$nSlides = count($slides) + 1;
		$camposSlides = "";
		for($i=0; $i<$nSlides; $i++)
		{
			if( trim($slides[$i]) )
				$camposSlides .= '<div><div class="floatLeft">Link Slide '. ($i + 1) .':&nbsp;</div><div class="floatLeft"><input type="text" value="'.$slides[$i].'" name="slides[]" class="inputText" size="25" /></div><div class="clear"></div></div><div style="height: 10px;"></div>';
		}
	}
	
	//montando combo de fabricantes
	$allFabricantes = $fabricante->allFabricantes();
	$comboFabricantes = "<select name='idFabricante' class='combo'>";	
	while( $linha = $db->fetchObject($allFabricantes) )
	{
		if( $linhaReg->idFabricante == $linha->idFabricante )
			$selected = "selected";
		else
			$selected = "";			
		$comboFabricantes .= "<option value='". $linha->idFabricante ."' ". $selected .">". $linha->nome  ."</option>";
	}	
	$comboFabricantes .= "</select>";

	//incluindo
	if( $_POST['btEnviar'] == $btLabel )
	{
		if( $_GET['action'] == "incluir" )
		{
			//se não conseguir incluir, volta pra mesma página
			if( $treinamento->insertTreinamento() )
			{	
				header("Location: listar.php?action=listar&begin=1");
				die();
			}
		}
		elseif( $_GET['action'] == "alterar" )
		{
			if( $treinamento->alterTreinamento() )
				header("Location: listar.php?action=listar&begin=1");
			else
				header("Location: incluir.php?action=alterar&idTreinamento=".$_POST['idTreinamento']);
			die();
		}
	}
	
	function loadImage( $img )
	{
		if( file_exists(PATH_IMG_TREINAMENTO .$img) && ($img != ".") )
			print PATH_IMG_TREINAMENTO . $img;
		else
			print "../img/default.gif";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" language="javascript">
var numSlide = <?php if(!$nSlides) print "1"; else print $nSlides; ?>;
//verifica se os dados do formulário estão ok
function submitForm()
{
	var form = document.forms['form'];
	var erro = new Array();
	
	if( !trim(form.nome.value) )
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
	location.href = "incluir.php?action=alterar&p=<?=$_GET['p']?>&idTreinamento=<?=$_GET['idTreinamento']?>&idTreinamentoDel=" + id + "&numImg=" + numImg + "&subaction=delImg";
}

function adicionarSlide()
{	
	$('#divSlides').append('<div><div class="floatLeft">Link Slide '+ numSlide +':&nbsp;</div><div class="floatLeft"><input type="text" name="slides[]" class="inputText" size="25" /></div><div class="clear"></div></div><div style="height: 10px;"></div>');
	numSlide++;
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
				<input type='hidden' name='idTreinamento' value='<?=$_GET['idTreinamento']?>' />
				<table cellpadding="5" cellspacing="0" border="0" align="center">
					<tr>
						<td style="text-align: center;" colspan="2" class="msgError"><? print $_SESSION['msg']; $_SESSION['msg'] = ""; ?></td>
					</tr>
					<tr>
						<td colspan="2"><b>* Campos obrigatórios</b></td>
					</tr>
                    <tr>
                        <td style="width: 30%;" id="txtNome">* Nome:</td>
                        <td style="width: 70%;"><input type="text" style='text-align: left;' name="nome" id="nome" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->nome, ENT_QUOTES)?><?=htmlentities($_POST['nome'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td style="width: 30%;" id="txtNome">Descri&ccedil;&atilde;o:</td>
                        <td style="width: 70%;"><textarea id="descricao" name="descricao" cols="40" rows="5" class="inputText"><?=htmlentities($linhaReg->descricao, ENT_QUOTES)?><?=htmlentities($_POST['descricao'], ENT_QUOTES)?></textarea></td>
                    </tr>
					<tr>
                        <td style="width: 30%;" id="txtNome">* Fabricante:</td>
                        <td style="width: 70%;"><?php print $comboFabricantes; ?></td>
                    </tr>
                    <tr>
                        <td>Ativo:</td>
                        <td><input type="checkbox" name="ativo" value="1" <?php if($_POST['ativo'] || $linhaReg->ativo) print "checked"; ?> /></td>
                    </tr>
					<tr>
						<td style="vertical-align: top;">Slides:</td>
						<td>
							<div><input type="button" name="btAdicionarSlide" id="btAdicionarSlite" value="Adicionar" class="inputButton" onclick="adicionarSlide()" /></div>
							<div id="divSlides" style="padding-top: 5px;">
								<?php print $camposSlides; ?>
							</div>
						</td>
					</tr>
                    <tr>
						<td style="width: 400px; text-align: center; font-weight: bold; padding-top: 10px;" id="txtNome" colspan="2">Imagens do Treinamento (.gif ou .jpg):</td>
					</tr>
                    <tr>
						<td align="center" colspan="2">
							<table cellpadding="2" cellspacing="2">
                            	<tr>
                                	<td>
                                        <table cellpadding='3' cellspacing='0' border='1' align="center" style='width: 100px; border-collapse: collapse;' bordercolor="#CCCCCC">
                                            <tr>
                                                <td style='text-align: center; background-color: #EFEFEF;'><b>Principal </b> <?=((file_exists(PATH_IMG_TREINAMENTO .$_GET['idTreinamento'].".".$linhaReg->extImg1) && ($_GET['idTreinamento'].".".$linhaReg->extImg1 != "."))  ? "<input type='button' onclick='delImg(\"".$_GET['idTreinamento']."\", \"\")' name='btExcluiImg1'  class='inputButton' value='Excluir Imagem' />" : "")?></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <div align="center" style='width: 100px; height: 100px; border: 1px solid #CCCCCC;'>
                                                        <img id="imagem1" src="<?=loadImage($_GET['idTreinamento'].".".$linhaReg->extImg1)?>" border='0' style='width: 100px; height: 100px;' />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center"><input type="file" id="imgTreinamento1" name='imgTreinamento1' class="inputText" /></td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td>191 x 184 pixels</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table cellpadding='3' cellspacing='0' border='1' align="center" style='width: 100px; border-collapse: collapse;' bordercolor="#CCCCCC">
                                            <tr>
                                                <td style='text-align: center; background-color: #EFEFEF;'><b>Thumbnail </b><?=(file_exists(PATH_IMG_TREINAMENTO .$_GET['idTreinamento']."_thumb.".$linhaReg->extImg2) ? "<input type='button' onclick='delImg(\"".$_GET['idTreinamento']."\", \"_thumb\")' name='btExcluiImg3' class='inputButton' value='Excluir Imagem' />" : "")?></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <div align="center" style='width: 100px; height: 100px; border: 1px solid #CCCCCC;'>
                                                        <img id="imagem2" src="<?=loadImage($_GET['idTreinamento']."_thumb.".$linhaReg->extImg2)?>" border='0' style='width: 100px; height: 100px;' />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center"><input type="file" id="imgTreinamento2" name='imgTreinamento2' class="inputText" /></td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td>108 x 104 pixels</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align='center' style="padding-top: 5px;"><input type="submit" name="btEnviar" class='inputButton' value="<?=$btLabel?>" maxlength="100" size="30" /></td>
                    </tr>
				</table>
			</form>
		</td>
	</tr>
</table>
</body>
</html>
