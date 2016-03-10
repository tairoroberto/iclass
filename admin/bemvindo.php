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
<title></title>
</head>
<body>
    <table cellpadding="0" cellspacing="0" border="0" style="width: 90%;" align="center">
        <tr>
            <td><p align="center"><img src="img/fundo-principal.jpg" /></p></td>
        </tr>
    </table>
</body>
</html>