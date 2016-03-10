<?
	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/usuario.php";
	require_once "../../inc/class/categoria.php";
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
	
	$usuario = new usuario();
	$categoria = new categoria();

	if( $_GET['action'] == "incluir" || !$_GET['action'] )
	{
		$btLabel 		= "Incluir";
		$head 			= "I n c l u i r&nbsp;&nbsp;&nbsp;&nbsp;U s u &aacute; r i o";
		$arrayPerfis	= $usuario->getPerfis();
		$arrayRedes		= $categoria->getRedes();
	}
	elseif( $_GET['action'] == "alterar" )
	{
		$btLabel 			= "Alterar";
		$head 				= "A l t e r a r&nbsp;&nbsp;&nbsp;&nbsp;U s u &aacute; r i o";
		$linhaReg			= $usuario->getOneUsuario($_REQUEST['idUsuario']);
		$arrayPerfis		= $usuario->getPerfis($_REQUEST['idUsuario']);
		$arrayRedes			= $categoria->getRedesUsuario($_REQUEST['idUsuario']);
	}
	
	//monta html dos perfis
	$perfis = "";
	for( $i=0; $i<count($arrayPerfis["idPerfil"]); $i++ )
	{
		if( trim($arrayPerfis["idPerfil"][$i]) )
			$perfis .= "<input type='checkbox' name='perfil[]' value='".$arrayPerfis["idPerfil"][$i]."' ".$arrayPerfis["checked"][$i]." />&nbsp;".$arrayPerfis["nome"][$i]."<br />";
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
			if( $usuario->insertUsuario() )
			{	
				header("Location: listar.php?action=listar&begin=1");
				die();
			}
		}
		elseif( $_GET['action'] == "alterar" )
		{
			if( $usuario->alterUsuario() )
				header("Location: listar.php?action=listar&begin=1");
			else
				header("Location: incluir.php?action=alterar&idUsuario=".$_POST['idUsuario']);
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
	
	if( !trim(form.nomeUsuario.value) )
		erro.push("Usuario");
	if( !trim(form.login.value) )
		erro.push("Login");
	<? if( $_GET['action'] == "incluir" ) {?>
		if( !trim(form.senha.value) )
			erro.push("Senha");
	<? } ?>
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
				<input type='hidden' name='idUsuario' value='<?=$_GET['idUsuario']?>' />
				<table cellpadding="5" cellspacing="0" border="0" align="center">
					<tr>
						<td style="text-align: center;" colspan="2" class="msgError"><? print $_SESSION['msg']; $_SESSION['msg'] = ""; ?></td>
					</tr>
					<tr>
						<td colspan="2"><b>* Campos obrigatórios</b></td>
					</tr>
                    <tr>
                        <td style="width: 30%;" id="txtNome">Nome:</td>
                        <td style="width: 70%;"><input type="text" style='text-align: left;' name="nomeUsuario" id="nomeUsuario" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->nomeUsuario, ENT_QUOTES)?><?=htmlentities($_POST['nomeUsuario'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td>Login:</td>
                        <td><input type="text" name="login" id="login" style='text-align: left;' maxlength="100" size="30" class="inputText" value="<?=htmlentities($linhaReg->login, ENT_QUOTES)?><?=htmlentities($_POST['login'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td>Senha:</td>
                        <td><input type="password" name="senha" id="senha" style='text-align: left;' maxlength="12" size="15" class="inputText" /> (mínimo 6 carac.)</td>
                    </tr>
                    <tr>
                        <td>É Admin?</td>
                        <td><input type="checkbox" name="isAdmin" value="1" <?php if($_POST['isAdmin'] || $linhaReg->isAdmin) print "checked"; ?> /></td>
                    </tr>
					<tr>
						<td style="vertical-align: top;">Perfis</td>
						<td><?php print $perfis ?></td>
					</tr>
                    <tr>
                        <td style='padding-top: 20px; vertical-align: top;'>Redes:</td>
                        <td style='padding-top: 20px;'><?php print $checkRedes; ?></td>
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
