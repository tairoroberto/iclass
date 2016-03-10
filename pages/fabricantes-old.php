<?php
	require_once "inc/class/fabricante.php";
	//vê se está logado mesmo.
	if( !validaLoginSite() )
	{
		print("<script type='text/javascript'>location.href  ='index.php?land=login'; </script>");
		die();
	}
	
	$fabricante = new fabricante();
	
	//setando as sessões pro filtro
	if( $_SERVER['REQUEST_METHOD'] == 'POST' )
	{
		$_SESSION['fNomeFabricante'] 	= $_POST['fNomeFabricante'];
	}
	
	//esvaziando os filtros se for o primeiro acesso. 
	if( $_GET['begin'] )
	{
		$_SESSION['fNomeFabricante'] 	= "";
		$_SESSION['fNomeTreinamento'] 	= ""; //este vem da busca
	}
?>
<div class="minha_contata_cot_esquerdo">
<?php require_once "pages/info_usuario.php"; ?>
<br />
<hr class="hrelvis" />
<form action="index.php?land=fabricantes" method="post">
    <p style="padding-left: 15px;"><strong>Busque um Fabricante:</strong></p>    
    <div style="text-align: center;"><input type="text" name="fNomeFabricante" class="inputText" maxlength="200" /></div>
	<div align="center"><input type="submit" value="Filtrar"  /></div>
</form>
</div>
<div class="minha_contata_cot_direito"> <a href="index.php?land=treinamentos_det&idFabricante=13"><img src="Util/img/banner-home.gif" width="541" height="160"alt="Banner" style="margin-left:-10px"></a>      
<p class="destaque_fonte" style="margin-left:0">Confira os fabricantes registrados no iClass!</p>

<?php print $fabricante->listaFabricantes(12, 1, "Nenhum fabricante foi encontrado."); ?>

</div>