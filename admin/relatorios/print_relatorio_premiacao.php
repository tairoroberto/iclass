<?php

	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/usuario_site.php";
	
	//v� se est� logado mesmo.
	/*if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}*/
	$usuario_site = new usuario_site();
		
	$timestamp = mktime(date("H")-HORAS_GMT, date("i"), date("s"), date("m"), date("d"), date("Y"));
	$dataAtual = gmdate("d/m/Y, �\s H:m:s", $timestamp);
	//$sqlData = "SELECT DATE_FORMAT((NOW() +  INTERVAL ".HORAS_GMT." HOUR) , '%d/%m/%Y  %H:%i:%s') AS dataAtual";
	//$queryData = $db->query($sqlData);
	//$objData = $db->fetchObject($queryData);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Relat&oacute;rio iClass</title>
<style tyle="text/css">
@media all
{
  .page-break  { display:none; }
}

@media print
{
  .page-break  { display:block; page-break-before:always; }
}
body
{
	 font-family:Arial, Helvetica, sans-serif;
	 font-size: 12px;
}
</style>
</head>

<body>
<div style="text-align: left; width: 100%; padding-top: 4px; padding-bottom: 8px;"><img src="../img/logo.jpg" border="0" /></div>
<div>
<span style="font-weight: bold; font-size: 16px; color: #006;">RELAT&Oacute;RIO DE PREMIA&Ccedil;&Atilde;O</span><br /><br />
<span style="font-weight: bold; color: #006;">Relat&oacute;rio gerado em: </span><?php print $dataAtual; ?>
</div>
<br />
<table align="center" cellpadding="1" cellspacing="0" style="width: 100%; border-collapse: collapse; color: #333333; font-family:Arial, Helvetica, sans-serif;"  border="1" bordercolor="#999999" >
    <tr>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 15%;">Nome do Usu�rio</td>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 10%;">Cargo</td>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 15%;">Rede/Loja</td>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 10%;">Cidade</td>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 10%;">UF</td>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 16%;">Treinamento</td>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 12%;">M�dia do Quiz (0 a 10)</td>
        <td style="color: #006; font-weight: bold; text-align: left;	padding: 2px; width: 12%;">M�dia Geral (0 a 10)</td>
    </tr>
    <!-- registros -->
    <? print $usuario_site->printRelatorioPremiacao(); ?>
</table>
</body>
</html>
