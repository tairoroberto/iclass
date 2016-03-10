<?
	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/fabricante.php";
	require_once "../../inc/class/categoria.php";
	require_once "../../inc/class/imagem.php";
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
	
	$fabricante = new fabricante();
	$categoria = new categoria();
	
	if( $_GET['subaction'] == "delImg" )
		$fabricante->delImgFabricante($_GET['idFabricanteDel'], $_GET['numImg']);


	if( $_GET['action'] == "incluir" || !$_GET['action'] )
	{
		$btLabel 		= "Incluir";
		$head 			= "I n c l u i r&nbsp;&nbsp;&nbsp;&nbsp;F a b r i c a n t e";
		$arrayRedes		= $categoria->getRedes();
	}
	elseif( $_GET['action'] == "alterar" )
	{
		$btLabel 		= "Alterar";
		$head 			= "A l t e r a r&nbsp;&nbsp;&nbsp;&nbsp;F a b r i c a n t e";
		$linhaReg		= $fabricante->getOneFabricante($_REQUEST['idFabricante']);
		$arrayRedes		= $categoria->getRedes($_REQUEST['idFabricante']);
	}
	
	//monta html das redes
	$checkRedes = "";
	for( $i=0; $i<count($arrayRedes["idCategoria"]); $i++ )
	{
		if( $arrayRedes["idCategoria"][$i] != "" )
		{
				$combo = "<input type='checkbox' name='idCategoria[]' value='".$arrayRedes["idCategoria"][$i]."' ".$arrayRedes["checked"][$i]." />&nbsp;".$arrayRedes["nome"][$i]."</b>";
			
			$checkRedes .= "<div style='width: 400px; padding-bottom: 2px;'>".$espaco.$combo."</div>";			
		}
	}

	//incluindo
	if( $_POST['btEnviar'] == $btLabel )
	{
		if( $_GET['action'] == "incluir" )
		{
			//se não conseguir incluir, volta pra mesma página
			if( $fabricante->insertFabricante() )
			{	
				header("Location: listar.php?action=listar&begin=1");
				die();
			}
		}
		elseif( $_GET['action'] == "alterar" )
		{
			if( $fabricante->alterFabricante() )
				header("Location: listar.php?action=listar&begin=1");
			else
				header("Location: incluir.php?action=alterar&idFabricante=".$_POST['idFabricante']);
			die();
		}
	}
	
	function loadImage( $img )
	{
		if( file_exists(PATH_IMG_FABRICANTE .$img) && ($img != ".") )
			print PATH_IMG_FABRICANTE . $img;
		else
			print "../img/default.gif";
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
	
	if( !trim(form.nome.value) )
		erro.push("Perfil");
	
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
	location.href = "incluir.php?action=alterar&p=<?=$_GET['p']?>&idFabricante=<?=$_GET['idFabricante']?>&idFabricanteDel=" + id + "&numImg=" + numImg + "&subaction=delImg";
}
</script>
<script src="../js/functions.inc.js" type="text/javascript"></script>
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
				<input type='hidden' name='idFabricante' value='<?=$_GET['idFabricante']?>' />
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
                        <td style="width: 30%;" id="txtNome">Descri&ccedil;&atildeo:</td>
                        <td style="width: 70%;"><textarea id="descricao" name="descricao" cols="40" rows="5" class="inputText"><?=htmlentities($linhaReg->descricao, ENT_QUOTES)?><?=htmlentities($_POST['descricao'], ENT_QUOTES)?></textarea></td>
                    </tr>
					<tr>
                        <td style="width: 30%;" id="txtNome">* Link do Banner:</td>
                        <td style="width: 70%;"><input type="text" style='text-align: left;' name="linkBanner" id="linkBanner" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->linkBanner, ENT_QUOTES)?><?=htmlentities($_POST['linkBanner'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td>Ativo:</td>
                        <td><input type="checkbox" name="ativo" value="1" <?php if($_POST['ativo'] || $linhaReg->ativo) print "checked"; ?> /></td>
                    </tr>
					<tr>
                        <td>Redes:</td>
                        <td><?php print $checkRedes; ?></td>
                    </tr>
                    <tr>
						<td style="width: 400px; text-align: center; font-weight: bold; padding-top: 10px;" id="txtNome" colspan="2">Imagens do Fabricante (.gif ou .jpg):</td>
					</tr>
                    <tr>
						<td align="center" colspan="2">
							<table cellpadding="2" cellspacing="2">
                            	<tr>
                                	<td>
                                        <table cellpadding='3' cellspacing='0' border='1' align="center" style='width: 100px; border-collapse: collapse;' bordercolor="#CCCCCC">
                                            <tr>
                                                <td style='text-align: center; background-color: #EFEFEF;'><b>Principal </b> <?=((file_exists(PATH_IMG_FABRICANTE .$_GET['idFabricante'].".".$linhaReg->extImg1) && ($_GET['idFabricante'].".".$linhaReg->extImg1 != "."))  ? "<input type='button' onclick='delImg(\"".$_GET['idFabricante']."\", \"\")' name='btExcluiImg1'  class='inputButton' value='Excluir Imagem' />" : "")?></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <div align="center" style='width: 100px; height: 100px; border: 1px solid #CCCCCC;'>
                                                        <img id="imagem1" src="<?=loadImage($_GET['idFabricante'].".".$linhaReg->extImg1)?>" border='0' style='width: 100px; height: 100px;' />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center"><input type="file" id="imgFabricante1" name='imgFabricante1' class="inputText" /></td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td>216 x 208 pixels</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table cellpadding='3' cellspacing='0' border='1' align="center" style='width: 100px; border-collapse: collapse;' bordercolor="#CCCCCC">
                                            <tr>
                                                <td style='text-align: center; background-color: #EFEFEF;'><b>Thumbnail </b><?=(file_exists(PATH_IMG_FABRICANTE .$_GET['idFabricante']."_thumb.".$linhaReg->extImg2) ? "<input type='button' onclick='delImg(\"".$_GET['idFabricante']."\", \"_thumb\")' name='btExcluiImg3' class='inputButton' value='Excluir Imagem' />" : "")?></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <div align="center" style='width: 100px; height: 100px; border: 1px solid #CCCCCC;'>
                                                        <img id="imagem2" src="<?=loadImage($_GET['idFabricante']."_thumb.".$linhaReg->extImg2)?>" border='0' style='width: 100px; height: 100px;' />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center"><input type="file" id="imgFabricante2" name='imgFabricante2' class="inputText" /></td>
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
