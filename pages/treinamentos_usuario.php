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
<br />
<hr class="hrelvis" />
</div>
<div class="minha_contata_cot_direito">         
<p class="destaque_fonte">Seu Boletim:</p>
<p style="margin-left: 0px;">Aqui voc&ecirc; encontra todo seu hist&oacute;rico de participa&ccedil;&atilde;o nos treinamentos.<br />
Sinta-se a vontade em refazer os mesmos e com isso aumentar a sua m&eacute;dia conosco.</p>
<?php print $treinamento->listaTreinamentosUsuario(5, "Nenhum Treinamento foi encontrado."); ?>

</div>