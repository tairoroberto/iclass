<?php
	require_once "../inc/config.inc.php";
	require_once "../inc/class/usuario_site.php";
	require_once "../inc/class/imagem.php";
	
	$usuario_site = new usuario_site();
	
	/*---- /UPLOAD DE IMAGEM ----*/
	$idImagem = "";
	$flagFotoGravada = "";
	//salva foto no banco
	if( $_POST['salvarFoto'] )
	{
		if( $usuario_site->salvarFoto() )
		{
			$msg = "Nova Foto gravada com sucesso.";
			$flagFotoGravada = "1";
		}
		else
			$msg = "N&atilde;o foi poss&iacute;vel salvar a Nova Foto. Tente novamente.";
	}
	
	if( $_GET['delFotoAtual'] )
	{
		if( $usuario_site->delFotoAtual($_SESSION['sess_idUsuarioSite']) )
			$msg = "Foto Atual apagada com sucesso.";
		else
			$msg = "N&atilde;o foi poss&iacute;vel apagar a Foto Atual. Tente novamente.";
	}

	if( !trim($_SESSION['sess_idImagem']) )
	{
		$idImagem = uniqid();
		$_SESSION['sess_idImagem'] = $idImagem;
	}
	else
		$idImagem = $_SESSION['sess_idImagem'];
		
	//Constants
	//You can alter these options
	$upload_dir = "../img_usuarios_site_temp"; 				// The directory for the images to be saved in
	$upload_path = "../img_usuarios_site/";				// The path to where the image will be saved
	$large_image_name = $idImagem.".jpg"; 		// New name of the large image
	$thumb_image_name = $idImagem."_thumb.jpg"; 	// New name of the thumbnail image
	$max_file = "250000"; 						// Approx 250KB
	$max_width = "150";							// Max width allowed for the large image
	$thumb_width = "150";						// Width of thumbnail image
	$thumb_height = "150";						// Height of thumbnail image
	$js = "";
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
		imagedestroy($newImage);
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
		imagedestroy($newImage);
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
		/*if(file_exists($thumb_image_location)){
			$thumb_photo_exists = "<img border='1' src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail\"/>";
			$_SESSION['pathNovaFoto'] = $upload_path.$thumb_image_name;
		}else{
			$thumb_photo_exists = "";
			$_SESSION['pathNovaFoto'] = "";
		}*/
		$_SESSION['pathNovaFoto'] = $upload_path.$large_image_name;
		$large_photo_exists = "<img border='1' src=\"".$upload_path.$large_image_name."\" alt=\"Imagem\"/>";
		
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
			$js =  "jQuery(document).ready(function(){ 
						jQuery('#formCadastroFoto').attr('action', 'cadastro_foto.php');
						jQuery('#formCadastroFoto').submit();
					});";
		}
		else
			$js = "alert('".$error."');";
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
		$js = "	jQuery(document).ready(function(){ 
					jQuery('#formCadastroFoto').attr('action', 'cadastro_foto.php');
					jQuery('#formCadastroFoto').submit();
				});";
		
	}

	if ($_GET['a']=="delete"){
		if (file_exists($large_image_location)) {
			unlink($large_image_location);
		}
		if (file_exists($thumb_image_location)) {
			unlink($thumb_image_location);
		}
		$js =  "jQuery(document).ready(function(){ 
					jQuery('#formCadastroFoto').attr('action', 'cadastro_foto.php');
					jQuery('#formCadastroFoto').submit();
				});";
	}
	/*---- /UPLOAD DE IMAGEM ----*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript" src="../Util/js/jquery.imgareaselect-0.3.min.js"></script>
<script type="text/javascript" language="javascript">
<?php print $js; ?>
/*Funções para seleção dinâmica da imagem*/
function preview(img, selection) { 
	var scaleX = 100 / selection.width; 
	var scaleY = 100 / selection.height; 
	
	jQuery('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * 500) + 'px', 
		height: Math.round(scaleY * 375) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	jQuery('#x1').val(selection.x1);
	jQuery('#y1').val(selection.y1);
	jQuery('#x2').val(selection.x2);
	jQuery('#y2').val(selection.y2);
	jQuery('#w').val(selection.width);
	jQuery('#h').val(selection.height);
} 
 
jQuery(document).ready(function () { 
	jQuery('#save_thumb').click(function() {
		var x1 = jQuery('#x1').val();
		var y1 = jQuery('#y1').val();
		var x2 = jQuery('#x2').val();
		var y2 = jQuery('#y2').val();
		var w = jQuery('#w').val();
		var h = jQuery('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}else{
			return true;
		}
	});
}); 
 
jQuery(window).load(function () { 
	jQuery('#thumbnail').imgAreaSelect({ aspectRatio: '1:1', onSelectChange: preview }); 
});

function deleteImage()
{
	$('#formCadastroFoto').attr('action', 'cadastro_foto.php?&uploadImage=1&a=delete');
	$('#formCadastroFoto').submit();
}

function deleteFotoAtual()
{
	$('#formCadastroFoto').attr('action', 'cadastro_foto.php?&uploadImage=1&delFotoAtual=1');
	$('#formCadastroFoto').submit();
}


</script>
</head>

<body style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color: #333333;">
<form method="post" enctype="multipart/form-data" id="formCadastroFoto">
<div style="text-align: left; padding: 10px 0 10px 5px;"><img src="../util/img/LogoIclassKaspersky.png" /></div>
<div style="padding-left: 20px;">
    <div style="width: 530px; border: 1px solid #CCCCCC; padding: 5px;">
		<div style="padding: 10px; color: #C00; font-weight: bold;"><?php print $msg; ?></div>
        <?php //print (file_exists("../".PATH_IMG_USUARIO_SITE_SITE.$_SESSION['sess_idUsuarioSite']."_1.".$_SESSION['sess_extImg']) ? "<label><span style='color: #265B9B; font-size: 14px;'><b>Imagem Atual:</b></span></label><br /><br /><img src='../".PATH_IMG_USUARIO_SITE_SITE.$_SESSION['sess_idUsuarioSite']."_1.".$_SESSION['sess_extImg']."' border='1'  /><br /><input type='button' value='Excluir Foto Atual' onclick='deleteFotoAtual()' /><br /><br />" : "");
			if( !$flagFotoGravada )
			{
		?>
        
        <label><span style='color: #265B9B; font-size: 14px;'><b>Nova foto:</b></span></label><br /><br />
        <?php
				//Mostra mensagem de erro, caso haja alguma
				if(strlen($error)>0){
					echo "<label>Erro ao fazer upload da foto: ".$error."<label>";
				}
				//if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0)
				/*if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0)
				{
					//echo $large_photo_exists."&nbsp;".$thumb_photo_exists;
					echo "	<div style='padding: 3px;'>".$thumb_photo_exists."</div>
							<div><p><input type='button' onclick='deleteImage();' value='Selecionar Outra Foto' />&nbsp;&nbsp;<form action='cadastro_foto.php'><input type='hidden' value='1' name='salvarFoto' /><input type='submit' value='Salvar Foto' /></form></p></div>";
				}*/
				if(strlen($large_photo_exists) > 0)
				{
					//echo $large_photo_exists."&nbsp;".$thumb_photo_exists;
					echo "	<div style='padding: 3px;'>".$large_photo_exists."</div>
							<div><p><input type='button' onclick='deleteImage();' value='Selecionar Outra Foto' />&nbsp;&nbsp;<form action='cadastro_foto.php'><input type='hidden' value='1' name='salvarFoto' /><input type='submit' value='Salvar Foto' /></form></p></div>";
				}
				else
				{
						if(strlen($large_photo_exists)>0)
						{
						?>
							<label>Clique sobre a foto, arraste o mouse e selecione a por&ccedil;&atilde;o da foto que deseja:<br /><br /></label>
							<div style="text-align: left;">
								<img border='1' src="<?php echo $upload_path.$large_image_name;?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
								<div style="float:left; display: none; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
									<img src="<?php echo $upload_path.$large_image_name;?>" style="position: relative;" alt="Thumbnail Preview" />
								</div>
								<br style="clear:both;"/>
								<form name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
									<input type="hidden" name="uploadImage" value="1" />
									<input type="hidden" name="x1" value="" id="x1" />
									<input type="hidden" name="y1" value="" id="y1" />
									<input type="hidden" name="x2" value="" id="x2" />
									<input type="hidden" name="y2" value="" id="y2" />
									<input type="hidden" name="w" value="" id="w" />
									<input type="hidden" name="h" value="" id="h" />
									<br /><br />
									<input type="submit" name="upload_thumbnail" value="Cortar Foto" id="save_thumb" />&nbsp;&nbsp;<input type="button" onclick="deleteImage();" value="Selecionar Outra Foto" />
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
			}
			else
			{
				print "<input type='button' value='Fechar' onclick='parent.fechaLightBox(\"windowCadastroFoto\"); parent.location.reload();' />";
			}
        ?>
    </div>
</div>
</form>
</body>
</html>