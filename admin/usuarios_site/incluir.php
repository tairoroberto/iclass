<?
	require_once "../../inc/config.inc.php";
	require_once "../../inc/class/usuario_site.php";
	require_once "../../inc/class/fabricante.php";
	require_once "../../inc/class/loja.php";
	require_once "../../inc/class/categoria.php";
	require_once "../../inc/class/imagem.php";
	require_once "../../inc/class/chaveacesso.php";
	
	//vê se está logado mesmo.
	if( !validaLogin() )
	{
		header("Location: index.php");
		die();
	}
	
	$usuario_site = new usuario_site();
	$fabricante = new fabricante();
	$loja = new loja();
	$categoria = new categoria();
	$chaves = new chaveacesso();
	
	if( $_GET['subaction'] == "delImg" )
		$usuario_site->delImgUsuarioSite($_GET['idUsuarioSiteDel'], $_GET['numImg']);


	if( $_GET['action'] == "incluir" || !$_GET['action'] )
	{
		$btLabel 		= "Incluir";
		$head 			= "I n c l u i r&nbsp;&nbsp;&nbsp;&nbsp;U s u &aacute; r i o&nbsp;&nbsp;&nbsp;d o&nbsp;&nbsp;&nbsp;S i t e";
	}
	elseif( $_GET['action'] == "alterar" )
	{
		$btLabel 		= "Alterar";
		$head 			= "A l t e r a r&nbsp;&nbsp;&nbsp;&nbsp;U s u &aacute; r i o&nbsp;&nbsp;&nbsp;d o&nbsp;&nbsp;&nbsp;S i t e";
		$linhaReg		= $usuario_site->getOneUsuarioSite($_REQUEST['idUsuarioSite']);
		$objEstado 		= $usuario_site->getEstadoPeloMunicipio($linhaReg->idCidade);
		$jsComboCidade 	= " changeEstado('".$objEstado->id."','".$linhaReg->idCidade."'); ";
		$objCategoria	= $loja->getCategoriaPelaLoja($linhaReg->idLoja);
		$jsComboCategoria 	= " changeCategoria('".$objCategoria->idCategoria."','".$linhaReg->idLoja."'); ";
	}

	if( $_POST['idEstado'] )
	{
		$objEstado 		= $usuario_site->getEstadoPeloMunicipio($_POST['idCidade']);
		$jsComboCidade 	= "window.onload = function() { changeEstado('".$_POST['idEstado']."','".$_POST['idCidade']."') };";
	}
	
	if( $_POST['idCategoria'] )
	{
		$objCategoria	= $loja->getLojas($_POST["idLoja"]);
		$jsComboCategoria 	= "window.onload = function() { changeCategoria('".$_POST['idCategoria']."','".$_POST['idLoja']."') };";
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
			if( $usuario_site->insertUsuarioSite() )
			{	
				header("Location: listar.php?action=listar&begin=1");
				die();
			}
		}
		elseif( $_GET['action'] == "alterar" )
		{
			if( $usuario_site->alterUsuarioSite() )
				header("Location: listar.php?action=listar&begin=1");
			else
				header("Location: incluir.php?action=alterar&idUsuarioSite=".$_POST['idUsuarioSite']);
			die();
		}
	}
	
	function loadImage( $img )
	{
		if( file_exists(PATH_IMG_USUARIO_SITE .$img) && ($img != ".") )
			print PATH_IMG_USUARIO_SITE . $img;
		else
			print "../img/default.gif";
	}

	//montando combo de estados
	$estados = $usuario_site->allEstados();
	$comboEstados = "<select name='idEstado' class='combo' onchange='changeEstado(this.value, \"".$_REQUEST['idCidade']."\")'>";
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
	$categorias = $categoria->allCategorias();
	while($linha = $db->fetchObject($categorias))
		$arrCategorias[] = $linha;
		
	$comboCategorias = "<select name='idCategoria' class='combo' onchange='changeCategoria(this.value, \"".$_REQUEST['idLoja']."\")'>";
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
		
	$comboLojas = "<select name='idLoja' class='combo'>";
	for( $i=0; $i<count($arrLojas); $i++ )
	{
		if( $linhaReg->idLoja == $arrLojas[$i]->idLoja )
			$selected = "selected";
		else
			$selected = "";
			
		$comboLojas .= "<option value='". $arrLojas[$i]->idLoja ."' ". $selected .">". $arrLojas[$i]->nome ."</option>";
	}
	$comboLojas .= "</select>";

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
		erro.push("Nome");
	if( !trim(form.email.value) )
		erro.push("E-mail");
	if( !trim(form.dtNascimento.value) )
		erro.push("Data de Nascimento");


	if( erro.length > 0 )
	{
		alert("Preencha os seguintes campos corretamente: " + erro.join(', '));
		return false;
	}
	else
		return true;
}

function changeEstado( p_idEstado, p_idCidade )
{
	$("#divMunicipio").html("Carregando Cidades...");
	
	$.post('comboCidades.php', { idCidade: p_idCidade, idEstado: p_idEstado }, function(data) {
	  $('#divMunicipio').html(data);
	});

}

function changeCategoria( p_idCategoria, p_idLoja )
{
	$("#divLojas").html("Carregando Lojas...");
	
	$.post('comboLojas.php', { idLoja: p_idLoja, idCategoria: p_idCategoria }, function(data) {
	  $('#divLojas').html(data);
	});

}

window.onload = function() {
	
<?php
print $jsComboCidade;
print $jsComboCategoria;
?>
}

function delImg(id, numImg)
{
	location.href = "incluir.php?action=alterar&p=<?=$_GET['p']?>&idUsuarioSite=<?=$_GET['idUsuarioSite']?>&idUsuarioSiteDel=" + id + "&numImg=" + numImg + "&subaction=delImg";
}

function validarChaveAcesso(){
	var chave = $('#chave_acesso').val();
	var action = $('#action_chave').val();
	var label = $('#labeChave');

	if(chave != ''){
		$.ajax({
			url: '../chaves_acesso/validar_chaves.php',
			data: "valor_valor=" + chave + "&action=" + action,
			type: "POST",
			success: function(json) {
				if(json != 'inexistente'){
					json = JSON.parse(json);
					if(json.ativa == 1){
						label.css('display', 'block');
						label.css('color', 'red');
						label.html('Chave de acesso já está sendo usada por outro usuário');
					}else {
						label.css('display', 'block');
						label.css('color', '#006400');
						label.html('Chave de acesso permitida');
					}
				}else {
					label.css('display', 'block');
					label.css('color', 'red');
					label.html('Chave de acesso não existe');
				}
			}
		});
	}
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
				<input type='hidden' name='idUsuarioSite' value='<?=$_GET['idUsuarioSite']?>' />
				<table cellpadding="5" cellspacing="0" border="0" align="center">
					<tr>
						<td style="text-align: center;" colspan="2" class="msgError"><? print $_SESSION['msg']; $_SESSION['msg'] = ""; ?></td>
					</tr>
					<tr>
						<td colspan="2"><b>* Campos obrigatórios</b></td>
					</tr>
                    <tr>
                        <td style="width: 45%;" id="txtNome">* Nome:</td>
                        <td style="width: 55%;"><input type="text" style='text-align: left;' name="nome" id="nome" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->nome, ENT_QUOTES)?><?=htmlentities($_POST['nome'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td id="txtNome">Rede:</td>
                        <td><?php print $comboCategorias; ?></td>
                    </tr>
                    <tr>
                        <td id="txtNome">Loja:</td>
                        <td><div id="divLojas" name="divLojas"></div></td>
                    </tr>
					<tr>
                        <td id="txtNome">Endere&ccedil;o:</td>
                        <td><input type="text" style='text-align: left;' name="endereco" id="endereco" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->endereco, ENT_QUOTES)?><?=htmlentities($_POST['endereco'], ENT_QUOTES)?>" /></td>
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
                        <td>CPF:</td>
                        <td><input type="text" class="inputText" name="cpf" MaxLength="14" onKeyDown="return noLetters(event);" onKeyUp="formataCPF(this);" value="<?=htmlentities($linhaReg->cpf, ENT_QUOTES)?><?=htmlentities($_POST['cpf'], ENT_QUOTES)?>" /></td>
                    </tr>
					<tr>
                        <td>CEP:</td>
                        <td><input type="text" name="cep" id="cep" onKeyDown="return noLetters(event);" onKeyUp="formataCEP(this);" style='text-align: left;' maxlength="9" size="30" class="inputText" value="<?=htmlentities($linhaReg->cep, ENT_QUOTES)?><?=htmlentities($_POST['cep'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td>* Data de Nascimento:</td>
                        <td><input type="text" name="dtNascimento" id="dtNascimento" style='text-align: left;' class="inputText" onkeydown="return noLetters(event);" onkeyup="formataData(this);" maxlength="10" size="12" value="<?=(trim($linhaReg->dataNascto) ? formataDataSql(substr($linhaReg->dataNascto, 0, 10)) : "")?><?=$_POST['dtNascimento']?>" /></td>
                    </tr>
                    <tr>
                        <td>Telefone:</td>
                        <td><input type="text" name="telefone" id="telefone" style='text-align: left;' maxlength="40" size="30" class="inputText" value="<?=htmlentities($linhaReg->telefone, ENT_QUOTES)?><?=htmlentities($_POST['telefone'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td>Cargo:</td>
                        <td><input type="radio" name="cargo" value="Vendedor" <?php if($_POST['cargo'] == "Vendedor" || $linhaReg->cargo == "Vendedor") print "checked"; ?> /> Vendedor&nbsp;&nbsp;<input type="radio" name="cargo" value="Gerente" <?php if($_POST['cargo'] == "Gerente" || $linhaReg->cargo == "Gerente") print "checked"; ?> /> Gerente&nbsp;&nbsp;<input type="radio" name="cargo" value="Promotor" <?php if($_POST['cargo'] == "Promotor" || $linhaReg->cargo == "Promotor") print "checked"; ?> /> Promotor</td>
                    </tr>

					<tr>
						<td>* Chave de acesso:</td>
						<td>
							<input type="text" name="chave_acesso" id="chave_acesso"
								   onblur="validarChaveAcesso();" style='text-align: left;' maxlength="10" size="15" class="inputText"
								   <?php if(isset($linhaReg->valor_chave))print 'disabled';?>
							value="<?=htmlentities($linhaReg->valor_chave, ENT_QUOTES)?><?=htmlentities($_POST['valor_chave'], ENT_QUOTES)?>" />
							<label id="labeChave" style="color: red; font-family: bold; font-size: 12px;display: none;">Chave de Acesso Obrigatória</label>
							<input type="hidden" name="action_chave" id="action_chave" value="validar_chave">
						</td>
					</tr>

					<tr>
                        <td>* E-mail:</td>
                        <td><input type="text" name="email" id="email" style='text-align: left;' maxlength="300" size="30" class="inputText" value="<?=htmlentities($linhaReg->email, ENT_QUOTES)?><?=htmlentities($_POST['email'], ENT_QUOTES)?>" /></td>
                    </tr>
                    <tr>
                        <td>* Senha:</td>
                        <td><input type="password" name="senha" id="senha" style='text-align: left;' maxlength="12" size="15" class="inputText" /> (m&iacute;nimo 6 d&iacute;gitos)</td>
                    </tr>    
                    <tr>
                        <td>* Confirmação de Senha:</td>
                        <td><input type="password" name="confirmacaoSenha" id="confirmacaoSenha" style='text-align: left;' maxlength="12" size="15" class="inputText" /></td>
                    </tr>
                    <tr>
                        <td>Ativo:</td>
                        <td><input type="checkbox" name="ativo" value="1" <?php if($_POST['ativo'] || $linhaReg->ativo) print "checked"; ?> /></td>
                    </tr>
                    <tr>
						<td style="width: 400px; text-align: center; font-weight: bold; padding-top: 10px;" id="txtNome" colspan="2">Foto (.gif, .jpg ou .png):</td>
					</tr>
                    <tr>
						<td align="center" colspan="2">
							<table cellpadding="2" cellspacing="2">
                            	<tr>
                                	<td>
                                        <table cellpadding='3' cellspacing='0' border='1' align="center" style='width: 100px; border-collapse: collapse;' bordercolor="#CCCCCC">
                                            <tr>
                                                <td style='text-align: center; background-color: #EFEFEF;'> <?=(file_exists(PATH_IMG_USUARIO_SITE .$_GET['idUsuarioSite']."_1.".$linhaReg->extImg) && ($_GET['idUsuarioSite'].".".$linhaReg->extImg1 != ".")  ? "<input type='button' onclick='delImg(\"".$_GET['idUsuarioSite']."\", \"1\")' name='btExcluiImg1'  class='inputButton' value='Excluir Imagem' />" : "")?></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <div align="center" style='width: 100px; height: 100px; border: 1px solid #CCCCCC;'>
                                                        <img id="imagem1" src="<?=loadImage($_GET['idUsuarioSite']."_1.".$linhaReg->extImg)?>" border='0' style='width: 100px; height: 100px;' />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center"><input type="file" id="imgUsuarioSite1" name='imgUsuarioSite1' class="inputText" /></td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td>191 x 184 pixels</td>
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
