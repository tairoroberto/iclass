<?php
	require_once "../inc/config.inc.php";
	require_once "../inc/class/loja.php";
	
	$loja = new loja();
	
	$arrLojas = $loja->getLojas($_REQUEST['idCategoria']);
	$combo = "";
	
	if( count($arrLojas) > 0 )
	{
		$combo = "<select name='idLoja' class='combo'>";
		for( $i=0; $i<count($arrLojas); $i++ )
		{
			if( $arrLojas[$i]->idLoja == $_REQUEST['idLoja'] )
				$selected = "selected";
			else
				$selected = "";
				
			$combo .= "<option value='". $arrLojas[$i]->idLoja ."' ". $selected .">". $arrLojas[$i]->nome ."</option>";
		}
		$combo .= "</select>";
	}
	
	print $combo;
?>