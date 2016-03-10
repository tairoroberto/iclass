<?php
	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/usuario_site.php";
	require_once "../../inc/class/fabricante.php";
	require_once "../../inc/class/loja.php";
	require_once "../../inc/class/categoria.php";
	require_once "../../inc/class/treinamento.php";
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
	
	$usuario_site = new usuario_site();
	$treinamento = new treinamento();
	$loja = new loja();
	$categoria = new categoria();
	$fabricante = new fabricante();
	
	//montando combo de treinamentos
	$allTreinamentos = $treinamento->allTreinamentosSemQuiz($_GET['idQuiz']);
	$comboTreinamentos = "<select name='idTreinamento' class='combo'><option value=''>-- Selecione --</option>";	
	while( $linha = $db->fetchObject($allTreinamentos) )
	{
		if( $linhaReg->idTreinamento == $linha->idTreinamento )
			$selected = "selected";
		else
			$selected = "";			
		$comboTreinamentos .= "<option value='". $linha->idTreinamento ."' ". $selected .">". $linha->nome  ."</option>";
	}	
	$comboTreinamentos .= "</select>";
	
	//montando combo de estados
	$estados = $usuario_site->allEstados();
	$comboEstados = "<select name='idEstado' class='combo' onchange='changeEstado(this.value, \"".$_REQUEST['idCidade']."\")'><option value=''>-- Selecione --</option>";
	for( $i=0; $i<count($estados); $i++ )
	{
		if( $objEstado->id == $estados[$i]->id )
			$selected = "selected";
		else
			$selected = "";
			
		$comboEstados .= "<option value='". $estados[$i]->id ."' ". $selected .">". $estados[$i]->nome ."</option>";
	}
	$comboEstados .= "</select>";
	
	//montando combo de categorias
	//se o usuário não for administrador, e não tiver redes associadas, não vê nada. 
	if( $_SESSION['sess_isAdmin'] == "1" )
		$categorias = $categoria->allCategorias();	
	else
		$categorias = $categoria->allCategoriasUsuario();
	
	while($linha = $db->fetchObject($categorias))
		$arrCategorias[] = $linha;
		
	$comboCategorias = "<select name='idCategoria' class='combo' onchange='changeCategoria(this.value, \"".$_REQUEST['idLoja']."\")'><option value=''>-- Selecione --</option>";
	for( $i=0; $i<count($arrCategorias); $i++ )
	{
		if( $objCategoria->idCategoria == $arrCategorias[$i]->idCategoria )
			$selected = "selected";
		else
			$selected = "";
			
		$comboCategorias .= "<option value='". $arrCategorias[$i]->idCategoria ."' ". $selected .">". $arrCategorias[$i]->nome ."</option>";
	}
	$comboCategorias .= "</select>";
	
	//montando combo de lojas
	$lojas = $loja->allLojas();
	while($linha = $db->fetchObject($lojas))
		$arrLojas[] = $linha;
		
	$comboLojas = "<select name='idLoja' class='combo'><option value=''>-- Selecione --</option>";
	for( $i=0; $i<count($arrLojas); $i++ )
	{
		if( $linhaReg->idLoja == $arrLojas[$i]->idLoja )
			$selected = "selected";
		else
			$selected = "";
			
		$comboLojas .= "<option value='". $arrLojas[$i]->idLoja ."' ". $selected .">". $arrLojas[$i]->nome ."</option>";
	}
	$comboLojas .= "</select>";
	
	//montando combo de fabricantes
	$allFabricantes = $fabricante->allFabricantes();
	$comboFabricantes = "<select name='idFabricante' class='combo'><option value=''>-- Selecione --</option>";	
	while( $linha = $db->fetchObject($allFabricantes) )
	{
		if( $linhaReg->idFabricante == $linha->idFabricante )
			$selected = "selected";
		else
			$selected = "";			
		$comboFabricantes .= "<option value='". $linha->idFabricante ."' ". $selected .">". $linha->nome  ."</option>";
	}	
	$comboFabricantes .= "</select>";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="../css/main.css" rel="stylesheet" type="text/css">
<script src="../js/listagem.js" type="text/javascript"></script>
<script src="../js/menu.js" type="text/javascript"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript">
function changeEstado( p_idEstado, p_idCidade )
{
	$("#divMunicipio").html("Carregando Cidades...");
	
	$.post('../usuarios_site/comboCidades.php', { idCidade: p_idCidade, idEstado: p_idEstado }, function(data) {
	  $('#divMunicipio').html(data);

	});

}

function changeCategoria( p_idCategoria, p_idLoja )
{
	$("#divLojas").html("Carregando Lojas...");
	
	$.post('../usuarios_site/comboLojas.php', { idLoja: p_idLoja, idCategoria: p_idCategoria }, function(data) {
	  $('#divLojas').html(data);
	});

}

window.onload = function() {
	
<?php
print $jsComboCidade;
print $jsComboCategoria;
?>
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>

<body style="background-color: #FFFFFF;">
<table cellpadding="0" cellspacing="0" border="0" style="width: 90%;" align="center">
	<tr>
		<td style="border-bottom: 1px solid  #CCCCCC; text-align: center; padding-top: 5px; padding-bottom: 5px; color: #FFFFFF; font-weight: bold; background-color: <?=TITULO_INTERNAS_BGCOLOR?>;">R E L A T Ó R I O</td>
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
						<form action="print_relatorio.php" target="_blank" method="POST">
							<table border="0" cellpadding="4" cellspacing="0" style="width: 100%;">
								<tr>
									<td class="msgError" style="text-align: center;"><? print $_SESSION['msg']; $_SESSION['msg'] = "";?></td>
								</tr>
							</table>
							<table border="0" cellpadding="2" cellspacing="0">
								<tr>
									<td>Nome do Usuário: </td>
									<td style='padding-left: 10px;'><input type="text" class="inputText" name="fNomeUsuarioSite" id="fNomeUsuarioSite" style='width: 200px;' /></td>
								</tr>
								<tr>
                                    <td>Cargo:</td>
                                    <td><input type="checkbox" name="cargo[]" value="Vendedor" /> Vendedor&nbsp;&nbsp;<input type="checkbox" name="cargo[]" value="Gerente" /> Gerente&nbsp;&nbsp;<input type="checkbox" name="cargo[]" value="Promotor" /> Promotor</td>
                                </tr>
                                <tr>
                                    <td id="txtNome">Estado:</td>
                                    <td><?php print $comboEstados; ?></td>
                                </tr>
                                <tr>
                                    <td id="txtNome">Cidade:</td>
                                    <td><div id="divMunicipio" name="divMunicipio"></div></td>
                                </tr>
                                <tr>
                                    <td id="txtNome">Rede:</td>
                                    <td><?php print $comboCategorias; ?></td>
                                </tr>
                                <tr>
                                    <td id="txtNome">Lojas:</td>
                                    <td><div id="divLojas" name="divLojas"></div></td>
                                </tr>
                                <tr>
                                    <td id="txtNome">Fabricante:</td>
                                    <td><?php print $comboFabricantes; ?></td>
                                </tr>
                                <tr>
                                    <td id="txtNome">Treinamento:</td>
                                    <td><?php print $comboTreinamentos; ?></td>
                                </tr>
								<tr>
									<td></td>
									<td style='padding-left: 100px;'><input type="submit" class="inputButton" value="Filtrar" name="btFiltrar" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>