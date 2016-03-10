<?php
	require_once "inc/class/fabricante.php";
	require_once "inc/class/treinamento.php";
	//vê se está logado mesmo.
	if( !validaLoginSite() )
	{
		print("<script type='text/javascript'>location.href  ='index.php?land=login'; </script>");
		die();
	}
	
	$fabricante = new fabricante();
	$treinamento = new treinamento();
	$fab = $fabricante->getOneFabricante($_REQUEST['idFabricante']);
	
	//setando as sessões pro filtro
	if( $_SERVER['REQUEST_METHOD'] == 'POST' )
	{
		$_SESSION['fNomeFabricante'] 	= $_POST['fNomeFabricante'];
	}
	
	//esvaziando os filtros se for o primeiro acesso. 
	if( $_GET['begin'] )
	{
		$_SESSION['fNomeFabricante'] 	= "";
	}
?>
<div class="minha_contata_cot_esquerdo">
<div class="logo_fabricante" align="center"><?php print (file_exists(PATH_IMG_FABRICANTE_SITE.$fab->idFabricante.".".$fab->extImg1) ? "<img src='".PATH_IMG_FABRICANTE_SITE.$fab->idFabricante.".".$fab->extImg1."' border='1'  width='191' height='184' />" : "") ?></div>
<p><strong><?php print $fab->nome; ?></strong></p>
<ul>
    <li><a href="index.php?land=perfil_empresa"> > Perfil da Empresa</a></li>
    <li><a href="index.php?land=treinamentos&idFabricante=<?php print $fab->idFabricante; ?>"> > Treinamentos</a></li>
</ul>
<?php require_once "pages/premio_do_mes.php"; ?>
<form action="index.php?land=fabricantes" method="post">
    <p style="padding-left: 15px;"><strong>Busque um Fabricante:</strong></p>    
    <div style="text-align: center;"><input type="text" name="fNomeFabricante" class="inputText" maxlength="200" /></div>
	<div align="center"><input type='image' src="Util/img/bt_filtrar.jpg" /></div>
</form>
</div>
<div class="minha_contata_cot_direito">
<iframe width="518" height="179" frameborder="0" scrolling="0" src="<?php print $fab->linkBanner; ?>"></iframe>
<br /><br />
<p class="destaque_fonte"><?php print $fab->nome; ?></p>
<div class='pruduto'><?php print $fab->descricao; ?></div>
</div>