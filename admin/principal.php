<?
	require_once "../inc/config.inc.php";
	
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>iClass</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
<script src="js/menu.js" type="text/javascript"></script>
<script src="js/gradient.js" type="text/javascript"></script>
<style type="text/css">
body
{
	background-color: #EFEFEF;
	margin-top: 20px;
}
</style>

<link rel="stylesheet" type="text/css" href="css/jqueryslidemenu.css" />

<!--[if lte IE 7]>
<style type="text/css">
html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/
</style>
<![endif]-->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript" src="js/jqueryslidemenu.js"></script>
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
</head>



<body>

<table border="0" cellpadding="0" cellspacing="0" align="center">

	<tr>

		<td style="width: 100%; height: 100%; vertical-align: top;" align="center">

			<!-- tabela principal -->

			<table style="width: 900px; height:700px; border: 1px solid #666666;" boder="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td style="height: 86px; width: 900px; background-color: #FFFFFF; text-align: center;"><a href="principal.php" target="_self"><img src="img/logo.jpg" border="0" /></a></td>
				</tr>
				<tr>
					<td style="background-color: #CCCCCC; height: 25px; text-align: right; padding-right: 20px;"><span style='color: #000000; font-weight: bold;'><?=date("d/m/Y")?> - Olá, <?=$_SESSION['sess_nomeUsuario']?>.</span></td>
				</tr>
				<tr>
                	<td>
						<? include "menu.php"; ?>
                    </td>
                </tr>
				<tr>
					<td style="height: 100%; background-color: #FFFFFF; vertical-align: top;">
                        <!-- MIOLO -->
                        <iframe id="iframePrincipal" align="top" style="width: 900px; height:700px;" frameborder="0" scrolling="auto" src="bemvindo.php"></iframe>
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