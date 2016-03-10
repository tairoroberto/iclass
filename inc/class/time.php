<?
class time
{
	var $db;
	
	//construtora
	function time()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	//insere uma time em inglês/portugues
	function insertTime( $arrTime )
	{
		if( !trim($arrTime['nome']) )
		{
			$_SESSION['msg'] = "Preencha o campo Nome corretamente.";
			return 0;
		}
		else
		{
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."time (nome)
									VALUES 
								(	'" . trim($arrTime['nome']) . "' )";
							
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$_SESSION['msg'] = "Time incluído com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Time. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera uma time
	function alterTime( $arrTime )
	{
		if( 	!trim($arrTime['nome']) 
			&& 	!trim($arrTime['idTime']) )
		{
			$_SESSION['msg'] = "Preencha corretamente o campo Nome.";
			return 0;
		}
		else
		{
			$sql 	= "	UPDATE ".PRE."time SET 
								nome = '" .$arrTime['nome']. "'
						WHERE idTime = " .$arrTime['idTime'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$_SESSION['msg'] = "Time alterado com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração do Time. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma time
	function delTime( $idTime )
	{
		$sql = "DELETE FROM ".PRE."time WHERE idTime = " .trataVarSql($idTime);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Time excluído com sucesso.";
		else
			$_SESSION['msg'] = "Não foi possível excluir o Time selecionado. Tente novamente.";	
		return;
	}
	
	//lista as LINHAS
	function listaTime( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."time WHERE 1=1 ";

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
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idTime=" . $grid[$i]->idTime . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o time ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idTime=" . $grid[$i]->idTime ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega um registro.
	function getOneTime( $idTime )
	{
		$sql = "SELECT * FROM ".PRE."time WHERE idTime = " .$idTime;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	function nomeTime( $idTime )
	{
		$sql = "SELECT * FROM ".PRE."time WHERE idTime = " .(trim($idTime) ? $idTime : "0");
		
		$query = $this->db->query(trataVarSql($sql));
		
		$obj = $this->db->fetchObject( $query );
		
		return $obj->nome;
	}

	function allTimes()
	{
		$sql = "SELECT * FROM ".PRE."time ORDER BY nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
	
	function buscaTimes( $needle )
	{
		if(trim($needle))
		{
			$sql = "SELECT * FROM ".PRE."time WHERE nome LIKE '%".trataVarSql(str_replace(" ", "%", $needle))."%' ORDER BY nome";
			$query = $this->db->query($sql);
		}
		else
			$query = "";
			
		return $query;
	}
}
?>