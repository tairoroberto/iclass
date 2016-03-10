<?
require_once "../inc/config.inc.php";

//deslogando;
$_SESSION['sess_sessId'] = "";

//deslogando;
unset( $_SESSION['sess_sessId'], $_SESSION['sess_idUsuario'] );

if( $_POST['btEnviar'] == "Entrar" )
{
	if( !trim($_POST['login']) || !trim($_POST['senha']) )
		$msgErro = "Login ou senha incorretos.";
	else
	{
		$sql = "SELECT idUsuario, login, senha, isAdmin, nomeUsuario FROM ".PRE."usuario WHERE login = '" . trataVarSql($_POST['login']) . "' AND senha = '" . md5(trataVarSql($_POST['senha'])) . "'";
		$query = $db->query($sql);
		$linha = $db->fetchObject($query);
		
		if( ( $_POST['login'] == $linha->login ) && ( md5($_POST['senha']) == $linha->senha ) )
		{
			/*
				login e senha: uma vez que o login e a senha foram validados, é gravada uma sessão com md5 na base de dados.
				essa sessão será validada em todas as páginas. uma vez validada, faz-se uma nova query na base, desta vez, 
				cadastrando um novo valor aleatório para a sessão. isso impede que uma mesma sessão vague por todas as páginas.
				a encriptação é um md5() com 3 números.			
			*/
			//gravando a sessão na base.
			$_SESSION['sess_sessId'] = md5(rand(012,978));
			$sql = "UPDATE ".PRE."usuario SET sessId = '" . $_SESSION['sess_sessId'] . "' WHERE login = '" . $linha->login . "' AND senha = '" . $linha->senha . "'";
			$db->query($sql); 
								
			//sessão com o id do usuário logado (usada para conferir se o cara tá logado ou não)
			$_SESSION['sess_idUsuario'] = $linha->idUsuario;
			$_SESSION['sess_login'] = $linha->login;
			$_SESSION['sess_isAdmin'] = $linha->isAdmin;
			$_SESSION['sess_nomeUsuario'] = $linha->nomeUsuario;
			
			header("Location: principal.php");
			die();
		}
		else
			$msgErro = "Login ou senha incorretos.";	
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>iClass</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/lightbox.css" />
<script src="js/menu.js" type="text/javascript"></script>
<script type="text/javascript">
function popRelatorio(pagina)
{	
	//abrirLightBox();	
	var form = document.getElementById("popRelatorio");
	//form.target = "frameRelatorio";
	form.target = "_blank";
	form.action = pagina;
	form.submit();
}
</script>
<style type="text/css">
body
{   background-color: #EFEFEF;
	margin-top: 20px;
}
</style>
</head>

<body>
<table border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td style="width: 100%; height: 100%; vertical-align: top;" align="center">
			<!-- tabela principal -->
			<table style="width: 900px; border: 1px solid #666666;" boder="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td style="height: 86px; width: 900px; background-color: #FFFFFF; text-align: center;"><img src="img/logo.jpg" border="0" /></td>
				</tr>
				<tr>
					<td style="background-color: #CCCCCC; height: 25px;"></td>
				</tr>
				<tr>
					<td style="height: 290px; background-color: #FFFFFF; vertical-align:middle;" align="center">
					<!-- Login e Senha -->
						<form action="index.php" method="post">
							<table border="0" cellpadding="3" cellspacing="0" align="center" style="margin: 0px; padding: 0px;">
								<tr>
									<td  colspan="2" style="font-weight: bold; text-align: center; color: <?php print MSG_WELCOME_COLOR; ?>;">Bem-vindo ao Módulo Administrativo iClass.<br /><br /></td>
								</tr>
								<tr>
									<td style="text-align: right;">Login: </td>
									<td align="left"><input type="text" class="inputText" name="login" id="login" maxlength="50" /></td>
								</tr>
								<tr>
									<td style="text-align: right;">Senha: </td>
									<td align="left"><input type="password" class="inputText" name="senha" id="senha" maxlength="50" /></td>
								</tr>
								<tr>
									<td></td>
									<td align="left"><br /><br /><input type="submit" class="inputButton" name="btEnviar" id="btEnviar" value="Entrar" style="width: 50px;" /></td>
								</tr>
								<tr>
									<td colspan="2" style="color: #FF0000; font-weight: bold;"><? print $msgErro; $msgErro = ""; ?></td>
								</tr>
							</table>
						</form>
					<!-- -->
					</td>
				</tr>
				<tr>
					<td align="left">
						<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" align="left">
							<tr>
								<td style="background-image: url(img/footer_bg.jpg); text-align: left; height: 50px;">
									<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td style="padding-left: 20px; height: 25px; color: #FFFFFF; font-weight: bold;">©<?=date("Y")?> - iClass</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<!-- -->
		</td>
	</tr>
</table>
<form name="popRelatorio" id="popRelatorio"></form>
</body>
</html>