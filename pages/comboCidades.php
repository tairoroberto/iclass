<?php
	require_once "../inc/config.inc.php";
	require_once "../inc/class/usuario_site.php";
	
	$usuario_site = new usuario_site();
	
	$cidades = $usuario_site->municipiosPorEstado($_REQUEST['idEstado']);
	
	$combo = "<select name='idCidade' class='combo'>";
	for( $i=0; $i<count($cidades); $i++ )
	{
		if( $cidades[$i]->idCidade == $_REQUEST['idCidade'] )
			$selected = "selected";
		else
			$selected = "";
			
		$combo .= "<option value='". $cidades[$i]->idCidade ."' ". $selected .">". $cidades[$i]->nome ."</option>";
	}
	$combo .= "</select>";
	
	print $combo;
?>