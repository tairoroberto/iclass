<div class="logo_fabricante" align="center">
	<?php
		$tamanhoFoto = @getimagesize(PATH_IMG_USUARIO_SITE_SITE.$_SESSION['sess_idUsuarioSite']."_1.".$_SESSION['sess_extImg']);
	?>
	<div style="overflow: hidden; width: 200px; height: <?php print ($tamanhoFoto[1] + 10); ?>px; "><?php print (file_exists(PATH_IMG_USUARIO_SITE_SITE.$_SESSION['sess_idUsuarioSite']."_1.".$_SESSION['sess_extImg']) ? "<img src='".PATH_IMG_USUARIO_SITE_SITE.$_SESSION['sess_idUsuarioSite']."_1.".$_SESSION['sess_extImg']."' border='1'  />" : "") ?></div>
</div>
<p align="center"><a href="#" onclick="cadastroFoto();">Trocar minha Foto</a></p>
<p><strong><?php print $_SESSION['sess_nomeUsuarioSite']; ?></strong></p>
<ul>
    <li><a href="index.php?land=cadastro"><img src="Util/img/user-32.png" width="24" height="24" border="0"  align="absmiddle"/> Alterar Cadastro</a></li>
    <li><a href="index.php?land=treinamentos_usuario"><img src="Util/img/Tests-32.png" width="24" height="24" border="0" align="absmiddle" /> Meu Boletim</a></li>
</ul>