<?
class categoria
{
	var $db;
	
	//construtora
	function categoria()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	function insertCategoria()
	{
		if( !trim($_POST['nome']) )
		{
			$_SESSION['msg'] = "Preencha o campo Nome corretamente.";
			return 0;
		}
		else
		{
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."categoria (nome, ativo)
									VALUES 
								(	'" . trim($_POST['nome']) . "', '".trim($_POST['ativo'])."' )";
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$_SESSION['msg'] = "Categoria incluída com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão da Categoria. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera uma categoria
	function alterCategoria()
	{
		if( 	!trim($_POST['nome']) 
			&& 	!trim($_POST['idCategoria']) )
		{
			$_SESSION['msg'] = "Preencha corretamente o campo Nome.";
			return 0;
		}
		else
		{
			$sql 	= "	UPDATE ".PRE."categoria SET 
								nome = '" .$_POST['nome']. "',
								ativo = '".$_POST['ativo']."'
						WHERE idCategoria = " .$_POST['idCategoria'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$_SESSION['msg'] = "Rede alterada com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração da Rede. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma categoria
	function delCategoria( $idCategoria )
	{
		$sql = "DELETE FROM ".PRE."categoria WHERE idCategoria = " .trataVarSql($idCategoria);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Rede excluída com sucesso.";
		else
			$_SESSION['msg'] = "Não foi possível excluir a Rede selecionado. Tente novamente.";	
		return;
	}
	
	//lista as LINHAS
	function listaCategorias( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."categoria WHERE 1=1 ";

		if( $_SESSION['fNomeCategoria'] != "" )
		{
			$sql .= "AND nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeCategoria']) ."%'";
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
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idCategoria=" . $grid[$i]->idCategoria . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o categoria ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idCategoria=" . $grid[$i]->idCategoria ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega um registro.
	function getOneCategoria( $idCategoria )
	{
		$sql = "SELECT * FROM ".PRE."categoria WHERE idCategoria = " .$idCategoria;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	function allCategorias()
	{
		$sql = "SELECT * FROM ".PRE."categoria WHERE ativo = '1' ORDER BY nome ";
		$query = $this->db->query($sql);
		return $query;
	}
	
	function allCategoriasUsuario()
	{
		$sqlCategorias = "SELECT c.* FROM ".PRE."usuario_categoria uc INNER JOIN ".PRE."categoria c ON c.idCategoria = uc.idCategoria WHERE uc.idUsuario = '".$_SESSION['sess_idUsuario']."' AND c.ativo = '1' ORDER BY c.nome ";
		$query = $this->db->query($sqlCategorias) or die(mysql_error()." - ".$sqlCategorias);
		return $query;
	}

	//pega todas as redes e as relaciona com um determinado fabricante.
	function getRedes( $idFabricante = "0" )
	{
		//fabricantes vinculados à categoria
		$sqlFabricantesRedes = "SELECT * FROM ".PRE."fabricante_categoria WHERE idFabricante = ".$idFabricante;
		$queryFabricantesRedes = $this->db->query($sqlFabricantesRedes);
		while( $r = $this->db->fetchObject($queryFabricantesRedes) )
			$linhas[] = $r;	
		
		//todas as categorias
		$sqlCategorias1 = "SELECT * FROM ".PRE."categoria ORDER BY nome";
		$queryCategorias1 = $this->db->query($sqlCategorias1);

		$arrObj = array();
		while( $p = $this->db->fetchObject($queryCategorias1) )
		{
			$arrObj["idCategoria"][] = $p->idCategoria;
			$arrObj["idFabricante"][] = $p->idFabricante;
			$arrObj["nome"][] = $p->nome;
			$arrObj["checked"][] = "";
			//verifica se o fabricante em questão está relacionado com a rede
			for( $i=0; $i<count($linhas); $i++ )
			{
				if( $p->idCategoria == $linhas[$i]->idCategoria )
				{
					$arrObj["checked"][count($arrObj["checked"])-1] = "checked";
					break;
				}
			}
		}
		return $arrObj;
	}
	
	//pega todas as redes e as relaciona com um determinado usuario.
	function getRedesUsuario( $idUsuario = "0" )
	{
		//fabricantes vinculados à categoria
		$sqlUsuariosRedes = "SELECT * FROM ".PRE."usuario_categoria WHERE idUsuario = ".$idUsuario;
		$queryUsuariosRedes = $this->db->query($sqlUsuariosRedes);
		while( $r = $this->db->fetchObject($queryUsuariosRedes) )
			$linhas[] = $r;	
		
		//todas as categorias
		$sqlCategorias1 = "SELECT * FROM ".PRE."categoria ORDER BY nome";
		$queryCategorias1 = $this->db->query($sqlCategorias1);

		$arrObj = array();
		while( $p = $this->db->fetchObject($queryCategorias1) )
		{
			$arrObj["idCategoria"][] = $p->idCategoria;
			$arrObj["idUsuario"][] = $p->idUsuario;
			$arrObj["nome"][] = $p->nome;
			$arrObj["checked"][] = "";
			//verifica se o fabricante em questão está relacionado com a rede
			for( $i=0; $i<count($linhas); $i++ )
			{
				if( $p->idCategoria == $linhas[$i]->idCategoria )
				{
					$arrObj["checked"][count($arrObj["checked"])-1] = "checked";
					break;
				}
			}
		}
		return $arrObj;
	}
}
?>