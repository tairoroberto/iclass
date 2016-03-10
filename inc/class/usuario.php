<?
class usuario
{
	var $db;
	
	//construtora
	function usuario()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	//insere um usuario do admin
	function insertUsuario()
	{
		if( 	!trim($_POST['nomeUsuario']) 
			|| 	!trim($_POST['login']) 
			|| 	( strlen($_POST['senha']) < 6))
		{
			$campos = "";

			if( !trim($_POST['nomeUsuario']) )
				$campos .= "Nome, ";
				
			if( !trim($_POST['login']) )
				$campos .= "Login, ";
			
			if( strlen($_POST['senha']) < 6 )
				$campos .= "Senha, ";
			
			$_SESSION['msg'] = "Preencha corretamente os seguintes campos: " .substr($campos,0,(strlen($campos) - 2) );
			return 0;
		}

		$sql 	= "INSERT INTO ".PRE."usuario (
							nomeUsuario,
							login,
							senha,
							isAdmin,
							dataCriacao) 
					VALUES ('" . trim($_POST['nomeUsuario']) . "',
							'" . trim($_POST['login']) . "',
							'" . md5(trim($_POST['senha'])) . "',
							'" . (trim($_POST['isAdmin']) ? $_POST['isAdmin'] : "0") . "',
							NOW())";

		$query	= $this->db->query($sql);

		if( $query )
		{
			$idUsuario = $this->db->insertId();
			
			//Insere os perfis selecionados para o usuário
			$sqlPerfis = "";
			for( $i=0; $i<count($_POST['perfil']); $i++ )
			{
				$sqlPerfis .= "INSERT INTO ".PRE."perfil_usuario (idPerfil, idUsuario) VALUES ('".$_POST['perfil'][$i]."', '".$idUsuario."');";
			}
			if( $sqlPerfis )
				$this->db->query($sqlPerfis);

			//insere as categorias (rede) para o fabricante
			for( $i=0; $i<count($_POST['idCategoria']); $i++ )
			{
				$sqlCategorias = "INSERT INTO ".PRE."usuario_categoria (idUsuario, idCategoria) VALUES (".$idUsuario.", ".$_POST['idCategoria'][$i].")";
				$this->db->query($sqlCategorias);
			}

			$_SESSION['msg'] = "Usu&aacute;rio cadastrado com sucesso.";
			return 1;
		}
		else
		{
			$_SESSION['msg'] = "Ocorreu um erro ao cadastrar o usu&aacute;rio. Tente novamente.";
			return 0;
		}
	}
	
	//pega os perfis de um usuário
	function getPerfis( $idUsuario = "0" )
	{
		//perfis do usuário solicitado
		$sqlPerfisUsuarios = "SELECT p.* FROM ".PRE."perfil_usuario pu INNER JOIN ".PRE."perfil p ON p.idPerfil = pu.idPerfil WHERE pu.idUsuario = ".$idUsuario." ORDER BY p.nome";
		$queryPerfisUsuarios = $this->db->query($sqlPerfisUsuarios);
		while( $r = $this->db->fetchObject($queryPerfisUsuarios) )
			$linhas[] = $r;	
		
		//todos os perfis
		$sqlPerfis = "SELECT * FROM ".PRE."perfil WHERE ativo = '1' ORDER BY nome";
		$queryPerfis = $this->db->query($sqlPerfis);

		$arrPerfis = array();
		while( $p = $this->db->fetchObject($queryPerfis) )
		{
			$arrPerfis["idPerfil"][] = $p->idPerfil;
			$arrPerfis["nome"][] = $p->nome;
			$arrPerfis["checked"][] = "";

			//verifica se o perfil em questão está selecionado para o usuário
			for( $i=0; $i<count($linhas); $i++ )
			{
				if( $p->idPerfil == $linhas[$i]->idPerfil )
				{
					$arrPerfis["checked"][count($arrPerfis["checked"])-1] = "checked";
					break;
				}
			}
			
		}
		return $arrPerfis;
	}

	//altera um usuario
	function alterUsuario()
	{
		if( 	!trim($_POST['nomeUsuario']) 
			|| 	!trim($_POST['login']) )
		{
			$campos = "";
			if( !trim($_POST['nomeUsuario']) )
				$campos .= "Nome, ";
				
			if( !trim($_POST['login']) )
				$campos .= "Login, ";
			
			$_SESSION['msg'] = "Preencha os seguintes campos corretamente: " .substr($campos,0,(strlen($campos) - 2) );
			return 0;
		}
		
		//se veio senha, cadastra
		if( trim($_POST['senha']) )
		{
			if( strlen($_POST['senha']) < 6 )
			{
				$_SESSION['msg'] = "A senha precisa ter, pelo menos, 6 caracteres.";
				return 0;
			}
			$senha = ", senha = '" . trim(md5($_POST['senha'])) . "'";
		}
		
		$sql 	= "	UPDATE ".PRE."usuario SET 
							nomeUsuario = '" . trim($_POST['nomeUsuario']) . "',
							login = '" . trim($_POST['login']) . "',
							isAdmin = '". (trim($_POST['isAdmin']) ? $_POST['isAdmin'] : "0") ."'
							".$senha."
					WHERE idUsuario = " .$_POST['idUsuario'];

		$query	= $this->db->query($sql);
		
		if( $query )
		{
			//Insere os perfis selecionados para o usuário
			$sqlDelPerfis = "DELETE FROM ".PRE."perfil_usuario WHERE idUsuario = ".$_POST['idUsuario'];
			$this->db->query($sqlDelPerfis);
			
			$sqlDelCategorias = "DELETE FROM ".PRE."usuario_categoria WHERE idUsuario = ".$_POST['idUsuario'];
			$this->db->query($sqlDelCategorias);
				
			for( $i=0; $i<count($_POST['perfil']); $i++ )
			{
				$this->db->query("INSERT INTO ".PRE."perfil_usuario (idPerfil, idUsuario) VALUES ('".$_POST['perfil'][$i]."', '".$_POST['idUsuario']."')");
			}
			
			//insere as categorias (rede) para o fabricante
			for( $i=0; $i<count($_POST['idCategoria']); $i++ )
			{
				$sqlCategorias = "INSERT INTO ".PRE."usuario_categoria (idUsuario, idCategoria) VALUES (".$_POST['idUsuario'].", ".$_POST['idCategoria'][$i].")";
				$this->db->query($sqlCategorias);
			}

			$_SESSION['msg'] = "Usu&aacute;rio alterado com sucesso.";
			return 1;
		}
		else
		{
			$_SESSION['msg'] = "Ocorreu um erro ao alterar o usu&aacute;rio. Tente novamente.";
			return 0;
		}
	}
	
	//deleta um usuario
	function delUsuario( $idUsuario )
	{
		$sql = "DELETE FROM ".PRE."usuario WHERE idUsuario = " .$idUsuario;
		$query = $this->db->query($sql);
		
		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Usu&aacute;rio exclu&iacute;do com sucesso.";
		else
			$_SESSION['msg'] = "N&atilde;o foi poss&iacute;vel excluir o usu&aacute;rio. Tente novamente.";
			
		return;
	}
	
	//lista os usuários
	function listaUsuarios( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."usuario WHERE 1=1 ";

		if( $_SESSION['fNomeUsuario'] != "" )
		{
			$sql .= "AND nomeUsuario LIKE '%". str_replace(' ', '%', $_SESSION['fNomeUsuario']) ."%'";
		}
		
		if( $_SESSION['fLogin'] != "" )
		{
			$sql .= "AND login LIKE '%". str_replace(' ', '%', $_SESSION['fLogin']) ."%'";
		}
		
		$orderBy = " ORDER BY nomeUsuario ASC ";
		
		$sql .= $orderBy;
					
		$tabela 	= paginacaoBar( $sql, $regPorPag, "listar.php?action=listar", $p );
		
		$sql		.= " LIMIT " . $start . ", " . $regPorPag;
		
		$query 		= $this->db->query($sql);
		
		while( $usuario = $this->db->fetchObject( $query ) )
			$r[] = $usuario;
		
		$grid = $this->montaGrid( $r );
		
		//grid com a paginacao
		$fullGrid = $grid . "<tr><td align='center' colspan='5'>" . $tabela . "</td></tr>";
		
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
			$tb .= "<td style='padding-left: 5px;'>" . htmlentities(cortaTexto($grid[$i]->nomeUsuario, 60), ENT_QUOTES) . "</td>
					<td style='padding-left: 5px;'>" . htmlentities(cortaTexto($grid[$i]->login, 60), ENT_QUOTES) . "</td>
					<td style='padding-left: 5px;'>" . formataDataSql($grid[$i]->dataCriacao) . "</td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idUsuario=" . $grid[$i]->idUsuario . "\");' ' src='../img/editar.gif' border='0' alt='Update this register.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Do you really want to erase the admin user ".htmlentities($grid[$i]->nomeUsuario, ENT_QUOTES)."?\", \"listar.php?action=excluir&idUsuario=" . $grid[$i]->idUsuario . "&". $p ."\");' src='../img/excluir.gif' border='0' alt='Erase this register.' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega todas as usuarios
	function allUsuarios()
	{
		$sql = "SELECT * FROM ".PRE."usuario";
		
		$query = $this->db->query($sql);
		
		while( $inds = $this->db->fetchObject($query) )
			$r[] = $inds;
		
		return $r;
	}

	//pega um usuario.
	function getOneUsuario( $idUsuario )
	{
		$sql = "SELECT * FROM ".PRE."usuario WHERE idUsuario = " .$idUsuario;
		$query = $this->db->query(trataVarSql($sql));

		return $this->db->fetchObject( $query );
	}
	
	//pega todos os itens de menu raiz
	function getRootMenus()
	{
		$sql = "SELECT * FROM ".PRE."pagina WHERE idPagina_pai = 0 ORDER BY nome ";
		$query = $this->db->query($sql);
		return $query;
	}
	
	//pega todos os itens de submenu, dado um menu
	function getSubMenus( $idPagina, $idUsuario)
	{
		$sql = "SELECT DISTINCT p.* FROM ".PRE."pagina p	INNER JOIN ".PRE."perfil_pagina pp ON p.idPagina = pp.idPagina INNER JOIN ".PRE."perfil_usuario pu ON pp.idPerfil = pu.idPerfil	WHERE pu.idUsuario = ".$idUsuario."	AND p.idPagina_pai = ".$idPagina." ORDER BY p.nome ";	
		$query = $this->db->query($sql);
		return $query;
	}
}
?>