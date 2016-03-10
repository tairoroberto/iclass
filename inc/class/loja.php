<?
class loja
{
	var $db;
	
	//construtora
	function loja()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	function insertLoja($nomeLoja = "")
	{
		if( !trim($_POST['nome']) )
		{
			$_SESSION['msg'] = "Preencha o campo Nome corretamente.";
			return 0;
		}
		else
		{
			if( trim($nomeLoja) )
			{
				$novoNome = $nomeLoja;
				$ativo = "1";
			}
			else
			{
				$novoNome = $_POST['nome'];
				$ativo = $_POST['ativo'];
			}
			
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."loja (nome, ativo, idCategoria)
									VALUES 
								(	'" . trim($novoNome) . "', '".$ativo."', ".$_POST['idCategoria']." )";
			$query	= $this->db->query($sql) or die(mysql_error()." - " . $sql);
			
			if( $query )
			{
				$ultimoId = $this->db->insertId();
				$_SESSION['msg'] = "Loja incluída com sucesso.";
				return $ultimoId;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão da Loja. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera uma loja
	function alterLoja($p_idLoja = "")
	{
		if( 	!trim($_POST['nome']) 
			&& 	!trim($_POST['idLoja']) )
		{
			$_SESSION['msg'] = "Preencha corretamente o campo Nome.";
			return 0;
		}
		else
		{
			if( trim($p_idLoja) )
				$cust_idLoja = $p_idLoja;
			else
				$cust_idLoja = $_POST['idLoja'];
				
			$sql 	= "	UPDATE ".PRE."loja SET 
								nome = '" .$_POST['nome']. "',
								ativo = '".$_POST['ativo']."',
								idCategoria = ".$_POST['idCategoria']."
						WHERE idLoja = " .$cust_idLoja;

			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$_SESSION['msg'] = "Loja alterada com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração da Loja. Tente novamente.";
				return 0;
			}
		}
	}
	
	function buscaLoja( $nome )
	{
		$sql = "SELECT * FROM ".PRE."loja WHERE UPPER(nome) = '".strtoupper($nome)."'";
		$query = @$this->db->query($sql);
		$obj = @$this->db->fetchObject($query);
		
		if( $obj->idLoja )
			return $obj->idLoja;
		else
			return "";
	}
	
	//deleta uma loja
	function delLoja( $idLoja )
	{
		$sql = "DELETE FROM ".PRE."loja WHERE idLoja = " .trataVarSql($idLoja);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Loja excluído com sucesso.";
		else
			$_SESSION['msg'] = "Não foi possível excluir o Loja selecionado. Tente novamente.";	
		return;
	}
	
	//lista as LINHAS
	function listaLojas( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."loja WHERE 1=1 ";

		if( $_SESSION['fNomeLoja'] != "" )
		{
			$sql .= "AND nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeLoja']) ."%'";
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
					<td style='padding-left: 5px;'>" . ($grid[$i]->ativo == "1" ? "<span style='color: green;'>sim</span>" : "<span style='color: red;'>n&atilde;o</span>") ."</td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idLoja=" . $grid[$i]->idLoja . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o loja ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idLoja=" . $grid[$i]->idLoja ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega um registro.
	function getOneLoja( $idLoja )
	{
		$sql = "SELECT * FROM ".PRE."loja WHERE idLoja = " .$idLoja;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	
	//pega um registro.
	function getLojas( $idCategoria = "0" )
	{
		if( !$idCategoria )
			$idCategoria = "0";
			
		$sql = "SELECT * FROM ".PRE."loja WHERE idCategoria = " .$idCategoria . " ORDER BY nome";

		$query = $this->db->query(trataVarSql($sql));
		
		$r = array();
		while( $linha = $this->db->fetchObject( $query ) )
			$r[] = $linha;

		return $r;
	}
	
	//pega uma loja
	function getCategoriaPelaLoja($idLoja = "0")
	{
		$sql = "SELECT * FROM ".PRE."loja WHERE idLoja= ".($idLoja ? $idLoja : "0");
		$query = $this->db->query($sql);
		$obj = $this->db->fetchObject($query);
		
		if( $obj->idCategoria )
		{
			$sqlCategoria = "SELECT * FROM ".PRE."categoria WHERE idCategoria = ".($obj->idCategoria ? $obj->idCategoria : "0");
			$queryCategoria = $this->db->query($sqlCategoria);
			return $this->db->fetchObject($queryCategoria);
		}
	}
	
	function allLojas()
	{
		$sql = "SELECT * FROM ".PRE."loja WHERE ativo = '1' ORDER BY nome ";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
}
?>