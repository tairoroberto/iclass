<?
	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/perfil.php";
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
	
	$perfil = new perfil();

	if( $_GET['action'] == "incluir" || !$_GET['action'] )
	{
		$btLabel 		= "Incluir";
		$head 			= "I n c l u i r&nbsp;&nbsp;&nbsp;&nbsp;P e r f i l";
		$arrayAreas		= $perfil->getAreas();
	}
	elseif( $_GET['action'] == "alterar" )
	{
		$btLabel 		= "Alterar";
		$head 			= "A l t e r a r&nbsp;&nbsp;&nbsp;&nbsp;P e r f i l";
		$linhaReg		= $perfil->getOnePerfil($_REQUEST['idPerfil']);
		$arrayAreas		= $perfil->getAreas($_REQUEST['idPerfil']);
	}
	
	//monta html dos perfis
	$areas = "";
	for( $i=0; $i<count($arrayAreas["idPagina"]); $i++ )
	{
		if( $arrayAreas["idPagina"][$i] != "" )
		{
			if( $arrayAreas["idPagina_Pai"][$i] != "0" ) //nível 0
				$espaco = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			else
				$espaco = "";
				
			if( $arrayAreas["havePai"][$i] == "1" ) //nível 1, é pai de sub sub pagina
				$espacoSubSub = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			else
				$espacoSubSub = "";
			
			if( $arrayAreas["idPagina_Pai"][$i] != "0" && $arrayAreas["isPai"][$i] == "" )
				$combo = "<input type='checkbox' name='pagina[]' value='".$arrayAreas["idPagina"][$i]."' ".$arrayAreas["checked"][$i]." />&nbsp;".$arrayAreas["nome"][$i]."</b>";
			else
				$combo = "&nbsp;<b>".$arrayAreas["nome"][$i]."</b>";
			
			$areas .= "<div style='width: 400px; padding-bottom: 2px;'>".$espaco.$espacoSubSub.$combo."</div>";			
		}
	}

	//incluindo
	if( $_POST['btEnviar'] == $btLabel )
	{
		if( $_GET['action'] == "incluir" )
		{
			//se não conseguir incluir, volta pra mesma página
			if( $perfil->insertPerfil() )
			{	
				header("Location: listar.php?action=listar&begin=1");
				die();
			}
		}
		elseif( $_GET['action'] == "alterar" )
		{
			if( $perfil->alterPerfil() )
				header("Location: listar.php?action=listar&begin=1");
			else
				header("Location: incluir.php?action=alterar&idPerfil=".$_POST['idPerfil']);
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
			<form name="form" id="form" action="incluir.php?action=<?=($_GET['action'] ? $_GET['action'] : "incluir")?>" method="POST" onsubmit="return submitForm();">
				<input type='hidden' name='idPerfil' value='<?=$_GET['idPerfil']?>' />
				<table cellpadding="5" cellspacing="0" border="0" align="center">
					<tr>
						<td style="text-align: center;" colspan="2" class="msgError"><? print $_SESSION['msg']; $_SESSION['msg'] = ""; ?></td>
					</tr>
					<tr>
						<td colspan="2"><b>* Campos obrigatórios</b></td>
					</tr>
                    <tr>
                        <td style="width: 30%;" id="txtNome">Nome:</td>
                        <td style="width: 70%;"><input type="text" style='text-align: left;' name="nome" id="nome" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->nome, ENT_QUOTES)?><?=htmlentities($_POST['nome'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td>Ativo:</td>
                        <td><input type="checkbox" name="ativo" value="1" <?php if($_POST['ativo'] || $linhaReg->ativo) print "checked"; ?> /></td>
                    </tr>
					<tr>
						<td style="vertical-align: top;">Áreas do Site</td>
						<td><?php print $areas ?></td>
					</tr>
                    <!-- <tr>
                    	<td>Admin?</td>
                        <td><input type="checkbox" name="admin" value="1" <?php print($linhaReg->admin ? "checked" : ""); ?> /></td>
                    </tr> -->
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
