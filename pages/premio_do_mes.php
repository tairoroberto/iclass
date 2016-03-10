<?php
	require_once "inc/class/premio.php";
	$premio = new premio();
	
	$premioDoMes = $premio->getPremioPorFabricante($_REQUEST['idFabricante']);
				
	if( $premioDoMes )
	{
?>
    <img src="Util/img/premio_do_mes.jpg" width="213" height="49" alt="Prêmio do Mês" class="ganhadores_do_mes" />
    <p>Complete seus treinamentos,<br />aumente seu score e concorra<br />a este <strong>Super Prêmio !</strong></p>
                
	<div align="center"><?php print (file_exists(PATH_IMG_PREMIO_SITE.$premioDoMes->idPremio."_1.".$premioDoMes->extImg1) ? "<img src='".PATH_IMG_PREMIO_SITE.$premioDoMes->idPremio."_1.".$premioDoMes->extImg1."' border='1'  width='131' height='93' />" : "") ?></div>
                
    <p><?php print $premioDoMes->descricao; ?></p>
<?php 
	} 
?>