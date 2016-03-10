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
</div>
<div class="minha_contata_cot_direito">
Conteudo da home aqui!
</div>