<?php
	require_once "inc/class/treinamento.php";
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
<?php require_once "pages/info_usuario.php"; ?>
<form action="index.php?land=treinamentos_busca" method="post">
    <p style="padding-left: 15px;"><strong>Busque um Treinamento:</strong></p>    
    <div style="text-align: center;"><input type="text" name="fNomeTreinamento" class="inputText" maxlength="200" /></div>
	<div align="center"><input type="submit" value="Filtrar"  /></div>
</form>
</div>
<div class="minha_contata_cot_direito">
<img src="Util/img/banner_home.gif" width="518" height="179"alt="Banner">         
<p class="destaque_fonte">Confira os últimos Treinamentos:</p>

<?php print $treinamento->listaTreinamentos(5, 1, "Nenhum Treinamento foi encontrado."); ?>

</div>