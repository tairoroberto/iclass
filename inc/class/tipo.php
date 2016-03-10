<?
class tipo
{
	var $db;
	
	//construtora
	function tipo()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	function insertTipo( $arrTipo )
	{
		if( !trim($arrTipo['nome']) )
		{
			$_SESSION['msg'] = "Preencha o campo Nome corretamente.";
			return 0;
		}
		else
		{
			$sql 	= "INSERT INTO ".PRE."tipo (nome)
									VALUES 
								(	'" . trim($arrTipo['nome']) . "' )";
							
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$_SESSION['msg'] = "Tipo incluído com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Tipo. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera uma tipo
	function alterTipo( $arrTipo )
	{
		if( 	!trim($arrTipo['nome']) 
			&& 	!trim($arrTipo['idTipo']) )
		{
			$_SESSION['msg'] = "Preencha corretamente o campo Nome.";
			return 0;
		}
		else
		{
			$sql 	= "	UPDATE ".PRE."tipo SET 
								nome = '" .$arrTipo['nome']. "'
						WHERE idTipo = " .$arrTipo['idTipo'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$_SESSION['msg'] = "Tipo alterado com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração do Tipo. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma tipo
	function delTipo( $idTipo )
	{
		$sql = "DELETE FROM ".PRE."tipo WHERE idTipo = " .trataVarSql($idTipo);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Tipo excluído com sucesso.";
		else
			$_SESSION['msg'] = "Não foi possível excluir o Tipo selecionado. Tente novamente.";	
		return;
	}
	
	//lista as LINHAS
	function listaTipo( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."tipo WHERE 1=1 ";

		if( $_SESSION['fNome'] != "" )
		{
			$sql .= "AND nome LIKE '%". str_replace(' ', '%', $_SESSION['fNome']) ."%'";
		}
			
		$orderBy = " ORDER BY nome ASC ";
		
		$sql .= $orderBy;
					
		$tabela 	= paginacaoBar( $sql, $regPorPag, "listar.php?action=listar", $p );
		
		$sql		.= " LIMIT " . $start . ", " . $regPorPag;
		
		$query 		= $this->db->query($sql);
		
		while( $cliente = $this->db->fetchObject( $query ) )
			$r[] = $cliente;
		
		$grid = $this->montaGrid( $r );
		
		//grid com a paginacao
		$fullGrid = $grid . "<tr><td align='center' colspan='4'>" . $tabela . "</td></tr>";
		
		return $fullGrid;
	}
	
	 //passar um array de objetos aqui
	function montaGrid( $grid )
	{
		$tb .= "<tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";

		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";

		//levando em conta que $grid é um array de objetos.
		for( $i=0; $i<count($grid); $i++ )
		{
			$tb .= "<td style='padding-left: 5px;'>" . cortaTexto($grid[$i]->nome, 100) . "</td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idTipo=" . $grid[$i]->idTipo . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o tipo ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idTipo=" . $grid[$i]->idTipo ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega um registro.
	function getOneTipo( $idTipo )
	{
		$sql = "SELECT * FROM ".PRE."tipo WHERE idTipo = " .(trim($idTipo) ? $idTipo : "0");
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	function nomeTipo( $idTipo )
	{
		$sql = "SELECT * FROM ".PRE."tipo WHERE idTipo = " .(trim($idTipo) ? $idTipo : "0");
		
		$query = $this->db->query(trataVarSql($sql));
		
		$obj = $this->db->fetchObject( $query );
		
		return $obj->nome;
	}
	
	function allTipos()
	{
		$sql = "SELECT * FROM ".PRE."tipo ORDER BY nome";
		
		$query = $this->db->query($sql);
		
		while( $tipo = $this->db->fetchObject( $query ) )
			$r[] = $tipo;
				
		return $r;
	}
}
?>