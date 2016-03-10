<?	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/usuario_site.php";
	
	$usuario_site = new usuario_site();
	
	if( $_GET['action'] == 'excluir' )
	{
		$usuario_site->delUsuarioSite( trataVarSql($_GET['idUsuarioSite']) );
		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";
			
		header("Location: listar.php?action=listar&".$p);
		die();
	}
	//setando as sessões pro filtro
	if( $_SERVER['REQUEST_METHOD'] == 'POST' )
	{
		$_SESSION['fNomeUsuarioSite'] 	= $_POST['fNomeUsuarioSite'];
	}
	
	//esvaziando os filtros se for o primeiro acesso. 
	if( $_GET['begin'] )
	{
		$_SESSION['fNomeUsuarioSite'] 	= "";
	}
	
	if( $_REQUEST['ativaUsuario'] )
	{
		$usuario_site->ativaUsuario($_REQUEST['flagAtivo'], $_REQUEST['idUsuarioSite']);
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
<script src="../js/menu.js" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>

<body style="background-color: #FFFFFF;">
<table cellpadding="0" cellspacing="0" border="0" style="width: 90%;" align="center">
	<tr>
		<td style="border-bottom: 1px solid  #CCCCCC; text-align: center; padding-top: 5px; padding-bottom: 5px; color: #FFFFFF; font-weight: bold; background-color: <?=TITULO_INTERNAS_BGCOLOR?>;">L I S T A G E M&nbsp;&nbsp;&nbsp;D E&nbsp;&nbsp;&nbsp;U S U &Aacute; R I O S&nbsp;&nbsp;&nbsp;D O&nbsp;&nbsp;&nbsp;S I T E</td>
	</tr>
	<tr>
		<td style="padding-top: 5px;">
			<!-- filtros -->
			<table style="width: 100%;" cellpadding="3" cellspacing="0">
				<tr>
					<td style="text-align: center;" class="topTextFiltros">F i l t r o s</td>
				</tr>
				<tr>
					<td style="height: 60px; padding-top: 10px; border: 1px solid #CCCCCC; background-color: #EFEFEF;">
						<form action="listar.php?action=listar" method="POST">
							<table border="0" cellpadding="4" cellspacing="0" style="width: 100%;">
								<tr>
									<td class="msgError" style="text-align: center;"><? print $_SESSION['msg']; $_SESSION['msg'] = "";?></td>
								</tr>
							</table>
							<table border="0" cellpadding="2" cellspacing="0">
								<tr>
									<td>Nome: </td>
									<td style='padding-left: 10px;'><input type="text" class="inputText" name="fNomeUsuarioSite" id="fNomeUsuarioSite" value="<?=$_SESSION['fNomeUsuarioSite']?>" style='width: 200px;' /></td>
								</tr>
								<tr>
									<td></td>
									<td style='padding-left: 100px;'><input type="submit" class="inputButton" value="Filtrar" name="btFiltrar" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 10px;">
						<table align="center" cellpadding="3" cellspacing="0" style="width: 98%; border-collapse: collapse; color: #666666;" border="1" bordercolor="#999999">
							<tr>
                                <td class="topTextTitleGrid" style="width: 40%;">Nome</td>
                                <td class="topTextTitleGrid" style="width: 20%;">Ativo</td>
								<td class="topTextTitleGrid" style="width: 10%;" colspan='2'>Ações</td>
							</tr>
							<!-- registros -->
							<? print $usuario_site->listaUsuarioSite( 10 ); ?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>