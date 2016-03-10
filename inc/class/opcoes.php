<?
class opcoes

{

	var $db;

	

	//construtora

	function opcoes()

	{

		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php

	}

	

	//altera opções

	function alteraOpcoes( $arrOpcoes )

	{

		if( 	!trim($arrOpcoes['vlr_max_trava']) ||
				!trim($arrOpcoes['vlr_max_trava_dupla']) ||
				!trim($arrOpcoes['vlr_max_trava_tripla']) ||
				!trim($arrOpcoes['vlr_max_trava_vitoria']) ||
				!trim($arrOpcoes['vlr_max_trava_empate']) ||
				!trim($arrOpcoes['vlr_max_premio']))

		{

			$campos = "";

			if( !trim($arrOpcoes['vlr_max_trava']) )

				$campos .= "Valor M&aacute;ximo da Trava de Pule Acumulada, ";
			if( !trim($arrOpcoes['vlr_max_trava_dupla']) )

				$campos .= "Valor M&aacute;ximo da Trava de Pule Dupla, ";
			if( !trim($arrOpcoes['vlr_max_trava_tripla']) )

				$campos .= "Valor M&aacute;ximo da Trava de Pule Tripla, ";
			if( !trim($arrOpcoes['vlr_max_trava_vitoria']) )

				$campos .= "Valor M&aacute;ximo da Trava de Pule Vit&oacute;ria, ";
			if( !trim($arrOpcoes['vlr_max_trava_empate']) )

				$campos .= "Valor M&aacute;ximo da Trava de Pule Empate, ";				
			if( !trim($arrOpcoes['vlr_max_premio']) )

				$campos .= "Valor M&aacute;ximo do Pr&ecirc;mio Bruto, ";		

			$_SESSION['msg'] = "Preencha os seguintes campos corretamente: " .substr($campos,0,(strlen($campos) - 2) );

			return 0;

		}

		

		$sql 	= "	UPDATE ".PRE."opcoes SET vlr_max_trava = '" . preparaMoeda($arrOpcoes['vlr_max_trava']) . "', vlr_max_trava_dupla = '" . preparaMoeda($arrOpcoes['vlr_max_trava_dupla']) . "', vlr_max_trava_tripla = '" . preparaMoeda($arrOpcoes['vlr_max_trava_tripla']) . "', vlr_max_trava_empate = '" . preparaMoeda($arrOpcoes['vlr_max_trava_empate']) . "', vlr_max_trava_vitoria = '" . preparaMoeda($arrOpcoes['vlr_max_trava_vitoria']) . "', vlr_max_premio = '" . preparaMoeda($arrOpcoes['vlr_max_premio']) . "'";



		$query	= $this->db->query($sql);

		

		if( $query )

		{

			$_SESSION['msg'] = "Op&ccedil;&otilde;es alteradas com sucesso.";

			return 1;

		}

		else

		{

			$_SESSION['msg'] = "Ocorreu um erro ao alterar as op&ccedil;&otilde;es. Tente novamente.";

			return 0;

		}

	}

	

	//pega as opções

	function getOpcoes()

	{

		$sql = "SELECT * FROM ".PRE."opcoes LIMIT 1";

		$query = $this->db->query($sql);

		$obj = $this->db->fetchObject($query);

		return $obj;

	}

}

?>