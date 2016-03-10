<?php
	require_once "inc/class/fabricante.php";
	require_once "inc/class/treinamento.php";
	require_once "inc/class/premio.php";
	//vê se está logado mesmo.
	if( !validaLoginSite() )
	{
		print("<script type='text/javascript'>location.href  ='index.php?land=login'; </script>");
		die();
	}

	$fabricante = new fabricante();
	$treinamento = new treinamento();
	$premio = new premio();
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
<div class="logo_fabricante" align="center"><?php print (file_exists(PATH_IMG_FABRICANTE_SITE.$fab->idFabricante.".".$fab->extImg1) ? "<img src='".PATH_IMG_FABRICANTE_SITE.$fab->idFabricante.".".$fab->extImg1."' border='1'  width='108' height='104' />" : "") ?></div>
<p><strong><?php print $fab->nome; ?></strong></p>
<ul>
    <li><a href="index.php?land=perfil_empresa&idFabricante=<?php print $fab->idFabricante; ?>"> > Perfil da Empresa</a></li>
    <li><a href="index.php?land=treinamentos&idFabricante=<?php print $fab->idFabricante; ?>"> > Treinamentos</a></li>
</ul>
<?php require_once "pages/premio_do_mes.php"; ?>
<form action="index.php?land=fabricantes" method="post">
<hr class="hrelvis"/>
    <p style="padding-left: 15px;"><strong>Busque um Fabricante:</strong></p>  
    <div style="text-align: center;"><input type="text" name="fNomeFabricante" class="inputText" maxlength="200" /></div>
	<div align="center"><input type="submit" value="Filtrar" /></div>
</form>
</div>
<div class="minha_contata_cot_direito">
<p class="destaque_fonte">Atualize-se com nossos últimos treinamentos e concorra à muitos prêmios!</p>
<br />
<?php
if( trim($_SESSION['fNomeTreinamento']) )
{
?>
<p class="destaque_fonte">Treinamentos com a palavra-chave <?php print $_SESSION['fNomeTreinamento']; ?>:</p>
<p><?php
}
print $treinamento->listaTreinamentos(3, 1, "Nenhum treinamento foi encontrado para este fabricante.", $_GET['idFabricante']); 
?></p>
</div>