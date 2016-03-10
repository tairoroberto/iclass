<?
class perfil
{
	var $db;
	
	//construtora
	function perfil()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	function insertPerfil()
	{
		if( !trim($_POST['nome']) )
		{
			$_SESSION['msg'] = "Preencha o campo Nome corretamente.";
			return 0;
		}
		else
		{
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."perfil (nome)
									VALUES 
								(	'" . trim($_POST['nome']) . "' )";
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				//Insere os relacionamentos para os perfis
				$idPerfil = $this->db->insertId();
				for( $i=0; $i<count($_POST['pagina']); $i++ )
				{
					/**** verifica se existe pai para esta página. se existir e não for 0, tenho que inserir ****/
						$sqlPagina = "SELECT * FROM ".PRE."pagina WHERE idPagina = ".$_POST['pagina'][$i];
						$queryPagina = $this->db->query($sqlPagina);
						$objPagina = $this->db->fetchObject($queryPagina);
						
						//pega o pai do pai deste item. se não for 0, insere.
						$sqlPai = "SELECT * FROM ".PRE."pagina WHERE idPagina = ".$objPagina->idPagina_Pai;
						$queryPai = $this->db->query($sqlPai);
						$objPai = $this->db->fetchObject($queryPai);
						
						if( $objPai->idPagina_Pai != "0" )
						{
							$sqlPaginasPai = "INSERT INTO ".PRE."perfil_pagina (idPerfil, idPagina) VALUES (".$idPerfil.", ".$objPai->idPagina.")";
							$this->db->query($sqlPaginasPai);								
						}
					/********/
					$sqlPaginas = "INSERT INTO ".PRE."perfil_pagina (idPerfil, idPagina) VALUES (".$idPerfil.", ".$_POST['pagina'][$i].")";
					$this->db->query($sqlPaginas);
				}
				
				$_SESSION['msg'] = "Perfil incluído com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Perfil. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera uma perfil
	function alterPerfil()
	{
		if( 	!trim($_POST['nome']) 
			&& 	!trim($_POST['idPerfil']) )
		{
			$_SESSION['msg'] = "Preencha corretamente o campo Nome.";
			return 0;
		}
		else
		{
			$sql 	= "	UPDATE ".PRE."perfil SET 
								nome = '" .$_POST['nome']. "'
						WHERE idPerfil = " .$_POST['idPerfil'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				//Insere os relacionamentos para os perfis
				$sqlDelPerfilPagina = "DELETE FROM ".PRE."perfil_pagina WHERE idPerfil = ".$_POST["idPerfil"];
				$this->db->query($sqlDelPerfilPagina);
				for( $i=0; $i<count($_POST['pagina']); $i++ )
				{
					/**** verifica se existe pai para esta página. se existir e não for 0, tenho que inserir ****/
						$sqlPagina = "SELECT * FROM ".PRE."pagina WHERE idPagina = ".$_POST['pagina'][$i];
						$queryPagina = $this->db->query($sqlPagina);
						$objPagina = $this->db->fetchObject($queryPagina);
						
						//pega o pai do pai deste item. se não for 0, insere.
						$sqlPai = "SELECT * FROM ".PRE."pagina WHERE idPagina = ".$objPagina->idPagina_Pai;
						$queryPai = $this->db->query($sqlPai);
						$objPai = $this->db->fetchObject($queryPai);
						
						if( $objPai->idPagina_Pai != "0" )
						{
							$sqlPaginasPai = "INSERT INTO ".PRE."perfil_pagina (idPerfil, idPagina) VALUES (".$_POST['idPerfil'].", ".$objPai->idPagina.")";
							$this->db->query($sqlPaginasPai);
						}
					/********/
					
					$sqlPaginas = "INSERT INTO ".PRE."perfil_pagina (idPerfil, idPagina) VALUES (".$_POST['idPerfil'].", ".$_POST['pagina'][$i].")";
					$this->db->query($sqlPaginas);
				}
				
				$_SESSION['msg'] = "Perfil alterado com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração do Perfil. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma perfil
	function delPerfil( $idPerfil )
	{
		$sql = "DELETE FROM ".PRE."perfil WHERE idPerfil = " .trataVarSql($idPerfil);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Perfil excluído com sucesso.";
		else
			$_SESSION['msg'] = "Não foi possível excluir o Perfil selecionado. Tente novamente.";	
		return;
	}
	
	//pega as áreas do site para montar a gama de opções
	function getAreas( $idPerfil = "0" )
	{
		//perfis do usuário solicitado
		$sqlPerfisPaginas = "SELECT * FROM ".PRE."perfil_pagina WHERE idPerfil = ".$idPerfil;
		$queryPerfisPaginas = $this->db->query($sqlPerfisPaginas);
		while( $r = $this->db->fetchObject($queryPerfisPaginas) )
			$linhas[] = $r;	
		
		//todos os perfis
		$sqlPaginas = "SELECT * FROM ".PRE."pagina WHERE idPagina_Pai = '0' ORDER BY nome";
		$queryPaginas = $this->db->query($sqlPaginas);

		$arrAreas = array();
		while( $p = $this->db->fetchObject($queryPaginas) )
		{
			$arrAreas["idPagina"][] = $p->idPagina;
			$arrAreas["idPagina_Pai"][] = $p->idPagina_Pai;
			$arrAreas["nome"][] = $p->nome;
			$arrAreas["url"][] = $p->url;
			$arrAreas["checked"][] = ""; 
			$arrAreas["isPai"][] = "1"; //os que tem pai = 0 são sempre pai de alguém
			$arrAreas["havePai"][] = "";
			$sqlSubPaginas = "SELECT * FROM ".PRE."pagina WHERE idPagina_Pai = '".$p->idPagina."'";
			$querySqlSubPaginas = $this->db->query($sqlSubPaginas);
			while( $sp = $this->db->fetchObject($querySqlSubPaginas) )
			{
				$arrAreas["idPagina"][] = $sp->idPagina;
				$arrAreas["idPagina_Pai"][] = $sp->idPagina_Pai;
				$arrAreas["nome"][] = $sp->nome;
				$arrAreas["url"][] = $sp->url;
				$arrAreas["checked"][] = "";
				$arrAreas["isPai"][] = "";
				$arrAreas["havePai"][] = ""; //apesar de ter pai, este controle é só usado para terceira linha de menu
				
				$sqlPai = "SELECT * FROM ".PRE."pagina WHERE idPagina_Pai = '".$sp->idPagina."'";
				$queryPai = $this->db->query($sqlPai);
				
				//verifica se é pai de alguém.
				if( $this->db->numRows($queryPai) > 0 )
					$arrAreas["isPai"][count($arrAreas["isPai"])-1] = "1";
				
				//verifica se a pagina em questão está selecionada para o perfil
				for( $i=0; $i<count($linhas); $i++ )
				{
					if( $sp->idPagina == $linhas[$i]->idPagina )
					{
						$arrAreas["checked"][count($arrAreas["checked"])-1] = "checked";
						break;
					}
				}

				$sqlSubSubPaginas = "SELECT * FROM ".PRE."pagina WHERE idPagina_Pai = '".$sp->idPagina."'";
				$querySqlSubSubPaginas = $this->db->query($sqlSubSubPaginas);
				while( $ssp = $this->db->fetchObject($querySqlSubSubPaginas) )
				{
					$arrAreas["idPagina"][] = $ssp->idPagina;
					$arrAreas["idPagina_Pai"][] = $ssp->idPagina_Pai;
					$arrAreas["nome"][] = $ssp->nome;
					$arrAreas["url"][] = $ssp->url;
					$arrAreas["checked"][] = "";
					$arrAreas["isPai"][] = "";
					$arrAreas["havePai"][] = "1"; //este parâmetro para dizer que tem um pai só é usado para o terceiro nível
					
					//verifica se a pagina em questão está selecionada para o perfil
					for( $i=0; $i<count($linhas); $i++ )
					{
						if( $ssp->idPagina == $linhas[$i]->idPagina )
						{
							$arrAreas["checked"][count($arrAreas["checked"])-1] = "checked";
							break;
						}
					}
				}
			}
		}
		return $arrAreas;
	}
	
	//lista as LINHAS
	function listaPerfis( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."perfil WHERE 1=1 ";

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
					<td style='padding-left: 5px;'>" . ($grid[$i]->ativo == "1" ? "<span style='color: green;'>sim</span>" : "<span style='color: red;'>n&atilde;o</span>") ."</td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idPerfil=" . $grid[$i]->idPerfil . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o perfil ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idPerfil=" . $grid[$i]->idPerfil ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega um registro.
	function getOnePerfil( $idPerfil )
	{
		$sql = "SELECT * FROM ".PRE."perfil WHERE idPerfil = " .$idPerfil;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	function allPerfis()
	{
		$sql = "SELECT * FROM ".PRE."perfil ORDER BY nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
}
?>