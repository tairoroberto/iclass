<?php
	require_once "inc/class/imagem.php";
	require_once "inc/class/loja.php";
	require_once "inc/class/categoria.php";
	
	$idImagem = "";

	if( !trim($_SESSION['sess_idImagem']) )
		$idImagem = uniqid();
	else
		$idImagem = $_SESSION['sess_idImagem'];

	$loja = new loja();
	$categoria = new categoria();

	/*---- /UPLOAD DE IMAGEM ----*/

	//Constants
	//You can alter these options
	$upload_dir = "img_usuarios_site_temp"; 				// The directory for the images to be saved in
	$upload_path = "img_usuarios_site/";				// The path to where the image will be saved
	$large_image_name = $idImagem.".jpg"; 		// New name of the large image
	$thumb_image_name = $idImagem."_thumb.jpg"; 	// New name of the thumbnail image
	$max_file = "250000"; 						// Approx 250KB
	$max_width = "500";							// Max width allowed for the large image
	$thumb_width = "100";						// Width of thumbnail image
	$thumb_height = "150";						// Height of thumbnail image

	//Image functions
	//You do not need to alter these functions
	function resizeImage($image,$width,$height,$scale) {
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		$source = imagecreatefromjpeg($image);
		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
		imagejpeg($newImage,$image,90);
		chmod($image, 0777);
		return $image;
	}
	//You do not need to alter these functions
	function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		$source = imagecreatefromjpeg($image);
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		imagejpeg($newImage,$thumb_image_name,90);
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}
	//You do not need to alter these functions
	function getHeight($image) {
		$sizes = getimagesize($image);
		$height = $sizes[1];
		return $height;
	}
	//You do not need to alter these functions
	function getWidth($image) {
		$sizes = getimagesize($image);
		$width = $sizes[0];
		return $width;
	}

	//Image Locations
	$large_image_location = $upload_path.$large_image_name;
	$thumb_image_location = $upload_path.$thumb_image_name;

	//Create the upload directory with the right permissions if it doesn't exist
	if(!is_dir($upload_dir)){
		mkdir($upload_dir, 0777);
		chmod($upload_dir, 0777);
	}

	//Check to see if any images with the same names already exist
	if (file_exists($large_image_location)){
		if(file_exists($thumb_image_location)){
			$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail\"/>";
		}else{
			$thumb_photo_exists = "";
		}
		$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Imagem\"/>";
	} else {
		$large_photo_exists = "";
		$thumb_photo_exists = "";
	}

	if (isset($_POST["upload"])) { 
		//Get the file information
		$userfile_name = $_FILES['image']['name'];
		$userfile_tmp = $_FILES['image']['tmp_name'];
		$userfile_size = $_FILES['image']['size'];
		$filename = basename($_FILES['image']['name']);
		$file_ext = substr($filename, strrpos($filename, '.') + 1);
		
		//Only process if the file is a JPG and below the allowed limit
		if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
			if (($file_ext!="jpg") && ($userfile_size > $max_file)) {
				$error= "Apenas imagens no formato JPG e com peso inferior a 250KB são permitidas.";
			}
		}else{
			$error= "Você precisa selecionar uma imagem.";
		}
		//Everything is ok, so we can upload the image.
		if (strlen($error)==0){
			
			if (isset($_FILES['image']['name'])){
				
				move_uploaded_file($userfile_tmp, $large_image_location);
				chmod($large_image_location, 0777);
				
				$width = getWidth($large_image_location);
				$height = getHeight($large_image_location);
				//Scale the image if it is greater than the width set above
				if ($width > $max_width){
					$scale = $max_width/$width;
					$uploaded = resizeImage($large_image_location,$width,$height,$scale);
				}else{
					$scale = 1;
					$uploaded = resizeImage($large_image_location,$width,$height,$scale);
				}
				//Delete the thumbnail file so the user can create a new one
				if (file_exists($thumb_image_location)) {
					unlink($thumb_image_location);
				}
			}
			//Refresh the page to show the new uploaded image
			print "	<script type='text/javascript'>
					$(document).ready(function(){ 
						$('#formCadastro').submit();
					});</script>";
		}
	}

	if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists)>0) {
		//Get the new coordinates to crop the image.
		$x1 = $_POST["x1"];
		$y1 = $_POST["y1"];
		$x2 = $_POST["x2"];
		$y2 = $_POST["y2"];
		$w = $_POST["w"];
		$h = $_POST["h"];
		//Scale the image to the thumb_width set above
		$scale = $thumb_width/$w;
		$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
		//Reload the page again to view the thumbnail
		print "	<script type='text/javascript'>
				$(document).ready(function(){ 
					$('#formCadastro').submit();
				});</script>";
		
	}

	if ($_GET['a']=="delete"){
		if (file_exists($large_image_location)) {
			unlink($large_image_location);
		}
		if (file_exists($thumb_image_location)) {
			unlink($thumb_image_location);
		}
		print "	<script type='text/javascript'>
				$(document).ready(function(){ 
					$('#formCadastro').submit();
				});</script>";
	}
	/*---- /UPLOAD DE IMAGEM ----*/


	if( $_REQUEST['subaction'] == "delImg" )
		$usuario_site->delImgUsuarioSite($_SESSION['sess_idUsuarioSite'], 1);

	$js = "";
	//incluindo
	if( $_SERVER['REQUEST_METHOD'] == "POST" )
	{
		if( !$_SESSION['sess_idUsuarioSite'] )
		{
			//se não conseguir incluir, volta pra mesma página
			if( $usuario_site->insertUsuarioSite(1) )
			{
				$js = "alert('".$_SESSION['msg']."'); location.href = 'index.php?land=cadastro_ok';";
			}			
		}
		else
		{
			if( $usuario_site->alterUsuarioSite(1) )
			{
				$js = "alert('".$_SESSION['msg']."');  location.href = 'index.php?land=cadastro_ok';";
			}
			else
			{
				$js = "alert('".$_SESSION['msg']."'); location.href = 'index.php?land=cadastro';";	
			}
		}
		$_SESSION['msg'] = "";
	}

	if( $_SESSION['sess_idUsuarioSite'] )
	{
		$linhaReg		= $usuario_site->getOneUsuarioSite($_SESSION['sess_idUsuarioSite']);
		$objEstado 		= $usuario_site->getEstadoPeloMunicipio($linhaReg->idCidade);
		$jsComboCidade 	= "window.onload = function() { changeEstado('".$objEstado->id."','".$linhaReg->idCidade."') };";
		$objCategoria	= $loja->getLojaPelaCategoria($linhaReg->idLoja);
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
	
	function loadImage( $img )
	{
		if( file_exists(PATH_IMG_USUARIO_SITE_SITE .$img) && ($img != ".") )
			print PATH_IMG_USUARIO_SITE_SITE . $img;
		else
			print "Util/img/default.gif";
	}

	//montando combo de estados
	$estados = $usuario_site->allEstados();
	$comboEstados = "<select name='idEstado' class='combo' onchange='changeEstado(this.value, \"".$_REQUEST['idCidade']."\")'>
						<option value=''>-- Selecione --</option>";
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

?>
<script type="text/javascript" language="javascript">
//verifica se os dados do formulário estão ok
function submitForm()
{
	/*var form = document.forms['formCadastro'];
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
		return true;*/
	return true;
}

function changeEstado( p_idEstado, p_idCidade )
{
	$("#lblMunicipio").html("Carregando Cidades...");
	
	$.post('pages/comboCidades.php', { idCidade: p_idCidade, idEstado: p_idEstado }, function(data) {
	  $('#lblMunicipio').html(data);
	});

}

function changeCategoria( p_idCategoria, p_idLoja )
{
	$("#lblLojas").html("Carregando Lojas...");
	
	$.post('pages/comboLojas.php', { idLoja: p_idLoja, idCategoria: p_idCategoria }, function(data) {
	  $('#lblLojas').html(data);
	});

}

<?php
print $jsComboCidade;
print $jsComboCategoria;
print $js;
?>

function delImg(id, numImg)
{
	location.href = "index.php?land=cadastro&numImg=" + numImg + "&subaction=delImg";
}
</script>
<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
	$current_large_image_width = getWidth($large_image_location);
	$current_large_image_height = getHeight($large_image_location);?>
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}else{
			return true;
		}
	});
}); 

$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:1', onSelectChange: preview }); 
});

</script>
<script type="text/javascript" src="Util/js/jquery.imgareaselect-0.3.min.js"></script>
<?php }?>
<form id="formCadastro"  action="index.php?land=cadastro" name="formCadastro" method="POST" onsubmit="return submitForm();" enctype="multipart/form-data">
	<input type="hidden" value="<?php print $_SESSION['sess_idUsuarioSite']; ?>" name="idUsuarioSite" />
	
	<div class="formulario">
    	<?php 
		if( !$_SESSION['sess_idUsuarioSite'] )
		{
		?>
        <strong>Seja bem vindo ao iClass</strong><br />
		Preencha os campos abaixo e clique em <b>Registrar</b> para realizar seu cadastro.<br />Você poderá utilizar seu cadastro no iClass assim que o mesmo for liberado por nossa equipe.<br /><br />Os campos com * são de preenchimento obrigatório.
		<?php
		}
		else
		{
		?>
        <strong>Atualize seu cadastro</strong><br />
		Preencha os campos abaixo para atualizar seu cadastro.
        <?php 
		}
		?>
<p><span class="destaque_fonte">DADOS PESSOAIS</span></p>
		
		<p><label>* Nome Completo:&nbsp;&nbsp;</label>
		<input type="text" style='text-align: left;' size="50" name="nome" id="nome" maxlength="100" class="inputText" value="<?=htmlentities($linhaReg->nome, ENT_QUOTES)?><?=htmlentities($_POST['nome'], ENT_QUOTES)?>" /><br />
        
		<label class="cpf">* CPF:&nbsp;&nbsp;</label>
		<input type="text" class="inputText" <?php print ($_SESSION['sess_idUsuarioSite'] ? "disabled" : ""); ?> name="cpf" MaxLength="14" size="20" onKeyDown="return noLetters(event);" onKeyUp="formataCPF(this);" value="<?=htmlentities($linhaReg->cpf, ENT_QUOTES)?><?=htmlentities($_POST['cpf'], ENT_QUOTES)?>" /><br />

        <label>Rede:&nbsp;&nbsp;</label>
		<?php print $comboCategorias; ?>&nbsp;&nbsp;<br />

	    <label>* Loja/Distribuidor:&nbsp;&nbsp;</label>
		<label id="lblLojas" name="divLojas" style></label><br />
        		
		<label>Endereço:&nbsp;&nbsp;</label>
		<input type="text" style='text-align: left;' name="endereco" id="endereco" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->endereco, ENT_QUOTES)?><?=htmlentities($_POST['endereco'], ENT_QUOTES)?>" /><br />
		
		<label>CEP:&nbsp;&nbsp;</label>
		<input type="text" name="cep" id="cep" onKeyDown="return noLetters(event);" onKeyUp="formataCEP(this);" style='text-align: left;' maxlength="9" size="20" class="inputText" value="<?=htmlentities($linhaReg->cep, ENT_QUOTES)?><?=htmlentities($_POST['cep'], ENT_QUOTES)?>" /><br />
		
		<label>UF:&nbsp;&nbsp;</label>
		<?php print $comboEstados; ?>&nbsp;&nbsp;

		<label>* Cidade:&nbsp;&nbsp;</label>
		<label id="lblMunicipio" name="lblMunicipio"></label><br />
		
		<label>* Data de nasc.:</label>
		<input type="text" name="dtNascimento" id="dtNascimento" style='text-align: left;' class="inputText" onkeydown="return noLetters(event);" onkeyup="formataData(this);" maxlength="10" size="12" value="<?=(trim($linhaReg->dataNascto) ? formataDataSql(substr($linhaReg->dataNascto, 0, 10)) : "")?><?=$_POST['dtNascimento']?>" />
		
		<label>Telefone:&nbsp;&nbsp;</label>
		<input type="text" name="telefone" id="telefone" style='text-align: left;' maxlength="40" size="30" class="inputText" value="<?=htmlentities($linhaReg->telefone, ENT_QUOTES)?><?=htmlentities($_POST['telefone'], ENT_QUOTES)?>" /><br />
		
		<label>Cargo:&nbsp;&nbsp;</label>
		<input type="radio" name="cargo" value="Vendedor" <?php if($_POST['cargo'] == "Vendedor" || $linhaReg->cargo == "Vendedor" || (!$_POST['cargo'] && !$linhaReg->cargo)) print "checked"; ?> />
        <label>Vendedor&nbsp;&nbsp;&nbsp;&nbsp;</label>
		
		<input type="radio" name="cargo" value="Gerente" <?php if($_POST['cargo'] == "Gerente" || $linhaReg->cargo == "Gerente") print "checked"; ?> />
        <label>Gerente&nbsp;&nbsp;&nbsp;&nbsp;</label>
		
		<input type="radio" name="cargo" value="Promotor" <?php if($_POST['cargo'] == "Promotor" || $linhaReg->cargo == "Promotor") print "checked"; ?> />
        <label>Promotor&nbsp;&nbsp;&nbsp;&nbsp;</label><br />
		
		<label>* E-mail:&nbsp;&nbsp;</label>
		<input type="text" name="email" id="email" style='text-align: left;' <?php print ($_SESSION['sess_idUsuarioSite'] ? "disabled" : ""); ?> maxlength="300" size="30" class="inputText" value="<?=htmlentities($linhaReg->email, ENT_QUOTES)?><?=htmlentities($_POST['email'], ENT_QUOTES)?>" /><br />
		
		<label>* Senha:&nbsp;&nbsp;</label>
		<input type="password" name="senha" id="senha" style='text-align: left;' maxlength="12" size="15" class="inputText" /> <b><span style="color: #666;">(m&iacute;nimo 6 d&iacute;gitos)</span></b>&nbsp;&nbsp;&nbsp;
		
		<label>* Repita sua senha:&nbsp;&nbsp;</label>
		<input type="password" name="confirmacaoSenha" id="confirmacaoSenha" style='text-align: left;' maxlength="12" size="15" class="inputText" /><br />
		
		<div style="padding-left: 20px;">
			<div style="width: 580px; border: 1px solid #CCCCCC; padding: 5px;">
				<label>Sua foto:</label><br />
				<?php
					//Mostra mensagem de erro, caso haja alguma
					if(strlen($error)>0){
						echo "<label>Erro ao fazer upload da foto: ".$error."<label>";
					}
					if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0)
					{
						//echo $large_photo_exists."&nbsp;".$thumb_photo_exists;
						echo "	<div style='padding: 3px;'>".$thumb_photo_exists."</div>
								<div><p><a href=\"".$_SERVER["PHP_SELF"]."?land=cadastro&a=delete\">Excluir Foto</a></p></div>";
					}
					else
					{
							if(strlen($large_photo_exists)>0)
							{
							?>
								<label>Clique sobre a foto, arraste o mouse e selecione a porção da foto que deseja:<label>
								<div style="text-align: left;">
									<img src="<?php echo $upload_path.$large_image_name;?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
									<div style="float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
										<img src="<?php echo $upload_path.$large_image_name;?>" style="position: relative;" alt="Thumbnail Preview" />
									</div>
									<br style="clear:both;"/>
									<form name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
										<input type="hidden" name="x1" value="" id="x1" />
										<input type="hidden" name="y1" value="" id="y1" />
										<input type="hidden" name="x2" value="" id="x2" />
										<input type="hidden" name="y2" value="" id="y2" />
										<input type="hidden" name="w" value="" id="w" />
										<input type="hidden" name="h" value="" id="h" />
										<input type="submit" name="upload_thumbnail" value="Salvar" id="save_thumb" />
									</form>
								</div>
								<hr />
						<?php 	
							}
							else
							{
						?>
							Selecione a foto: <input type="file" name="image" size="30" /> <input type="submit" name="upload" value="Enviar" />
						
				<?php 
							}
					} 
				?>
			</div>
		</div>
        <!--<br />
        <label><img id="imagem1" src="<?=loadImage($_SESSION['sess_idUsuarioSite']."_1.".$linhaReg->extImg)?>" border='0' style='width: 100px; height: 100px;' /><br /></label>
        <?=(file_exists(PATH_IMG_USUARIO_SITE_SITE .$_SESSION['sess_idUsuarioSite']."_1.".$linhaReg->extImg) && ($_SESSION['sess_idUsuarioSite'].".".$linhaReg->extImg1 != ".")  ? "<label><div style='height: 6px;'></div><img src='Util/img/bt_excluir_imagem.jpg' onclick='delImg(\"".$_SESSION['sess_idUsuarioSite']."\", \"1\")' name='btExcluiImg1' alt='Excluir Imagem de Perfil' style='cursor: pointer;' /><div style='height: 6px;'></div></label>" : "")?>
		<br />
        <label><input type="file" id="imgUsuarioSite1" name='imgUsuarioSite1' class="inputText" /></label> -->
        <br />
        <textarea class="textAreaCadastro inputText"></textarea><br />
		
		<div class="termos_de_uso"><p><input name="chkTermos" type="checkbox" value="1" <?php print ($_SESSION['sess_idUsuarioSite'] ? "checked" : ""); ?> />* Li e estou de acordo com os termos de uso e regras do programa.</p></div>
		<div class="botao_cadastre-se"><input style='margin-bottom: 0px;' class="inputButton" type="submit" value="<?php print (!$_SESSION['sess_idUsuarioSite'] ? "Registrar" : "Atualizar"); ?>" /><input type="button" class="inputButton"  name="btCancelar" onclick='location.href = "index.php";' alt="Cancelar"  /></div>
		
		</p>
  </div>
		
		<p align="center">Em caso de dúvida ou problema de acessos ao site, favor contratar o suporte técnico por e-mail<br />
	<div class="destaque_fonte" align="center"><a href="#">suporteiclass@itailers.com.br</a></div></p>
</form>
<div class="clear"></div>