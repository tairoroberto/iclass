<?php
	require_once "inc/class/treinamento.php";
	require_once "inc/class/fabricante.php";
	
	//vê se está logado mesmo.
	if( !validaLoginSite() )
	{
		print("<script type='text/javascript'>location.href  ='index.php?land=login'; </script>");
		die();
	}
	
	$treinamento = new treinamento();
	
	//setando as sessões pro filtro
	if( $_SERVER['REQUEST_METHOD'] == 'POST' )
	{
		$_SESSION['fNomeTreinamento'] 	= $_POST['fNomeTreinamento'];
	}
	
	//esvaziando os filtros se for o primeiro acesso. 
	if( $_GET['begin'] )
	{
		$_SESSION['fNomeTreinamento'] 	= "";
	}
?>
<div class="minha_contata_cot_esquerdo">
<form action="index.php?land=treinamentos_busca" method="post">
    <p style="padding-left: 15px;"><strong>Busque um Treinamento:</strong></p>    
    <div style="text-align: center;"><input type="text" name="fNomeTreinamento" class="inputText" maxlength="200" /></div>
	<div align="center"><input type='image' src="Util/img/bt_filtrar.jpg" /></div>
</form>
</div>
<div class="minha_contata_cot_direito">
<img src="Util/img/banner_iomega.jpg" width="518" height="179"alt="Banner">         
<?php print $treinamento->BuscaTreinamentos("Nenhum Treinamento foi encontrado."); ?>

</div>