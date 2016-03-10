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
<?php require_once "pages/info_usuario.php"; ?>
<br />
<hr class="hrelvis" />
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
print $treinamento->listaTreinamentos(5, 1, "Nenhum treinamento foi encontrado para este fabricante.", $_GET['idFabricante']); 
?></p>
</div>