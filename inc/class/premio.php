<?
class premio
{
	var $db;
	
	//construtora
	function premio()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	//insere uma premio
	function insertPremio()
	{
		if( !trim($_POST['nome']) )
		{
			$_SESSION['msg'] = "Preencha o campo Nome corretamente.";
			return 0;
		}
		else
		{
			//montando cadastro das extensões das imagens do produto
			$extImg = "";
			for($m=0; $m<=3; $m++)
				$extImg[$m] = strtolower(substr($_FILES['imgPremio'.($m+1)]['name'], -3));

			
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."premio (nome, descricao, idFabricante, extImg1, extImg2, extImg3, extImg4)
									VALUES 
								(	'" . trim($_POST['nome']) . "', 
									'" . trim($_POST['descricao']) . "',
									" . trim($_POST['idFabricante']) . ",
									'" . $extImg[0] . "',
									'" . $extImg[1] . "',
									'" . $extImg[2] . "',
									'" . $extImg[3] . "'
								)";
							
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$idPremio = $this->db->insertId();
				
				for($m=1; $m<=4; $m++)
				{
					if( $_FILES['imgPremio'.$m]['name'] )
					{	
						$img = new imagem();
						$ext = strtolower(substr($_FILES['imgPremio'.$m]['name'], -3));

						if( $ext == "jpg" || $ext == "gif" )
						{
							$img->sobe( $_FILES['imgPremio'.$m]['tmp_name'], PATH_IMG_PREMIO_UPLOAD .$idPremio."_".$m.".".$ext);
						}
					}	
				}
				
				$_SESSION['msg'] = "Pr&ecirc;mio incluído com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Pr&ecirc;mio. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera um premio
	function alterPremio()
	{
		if( 	!trim($_POST['nome']) 
			&& 	!trim($_POST['idPremio']) )
		{
			$_SESSION['msg'] = "Preencha corretamente o campo Nome.";
			return 0;
		}
		else
		{
			$sqlExtImg = "SELECT extImg1, extImg2, extImg3, extImg4 FROM ".PRE."premio WHERE idPremio = ".$_POST['idPremio'];
			$queryExtImg = $this->db->query($sqlExtImg);
			$objExtImg = $this->db->fetchObject($queryExtImg);
			$extImg[0] = $objExtImg->extImg1;
			$extImg[1] = $objExtImg->extImg2;
			$extImg[2] = $objExtImg->extImg3;
			$extImg[3] = $objExtImg->extImg4;

			for($m=0; $m<=1; $m++)
			{
				$extImgAux = strtolower(substr($_FILES['imgPremio'.($m+1)]['name'], -3));
				if( trim($extImgAux) )
				{
					$extImg[$m] = $extImgAux;
				}
			}
			
			$sql 	= "	UPDATE ".PRE."premio SET 
								nome = '" .$_POST['nome']. "',
								ativo = '" .$_POST['ativo']. "',
								descricao = '" .$_POST['descricao']. "',
								idFabricante = " .$_POST['idFabricante']. ",
								extImg1 = '" . $extImg[0] . "',
								extImg2 = '" . $extImg[1] . "',
								extImg2 = '" . $extImg[2] . "',
								extImg2 = '" . $extImg[3] . "'
						WHERE idPremio = " .$_POST['idPremio'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				for($m=1; $m<=4; $m++)
				{
					if( $_FILES['imgPremio'.$m]['name'] )
					{	
						$img = new imagem();
						$ext = strtolower(substr($_FILES['imgPremio'.$m]['name'], -3));
						
						if( $ext == "jpg" || $ext == "gif" )
						{
							$img->sobe( $_FILES['imgPremio'.$m]['tmp_name'], PATH_IMG_PREMIO_UPLOAD .$_POST['idPremio']."_".$m.".".$ext);
						}
					}	
				}

				$_SESSION['msg'] = "Premio alterado com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração do Premio. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma premio
	function delPremio( $idPremio )
	{
		$sql = "DELETE FROM ".PRE."premio WHERE idPremio = " .trataVarSql($idPremio);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
		{
			for( $i=1; $i<=4; $i++ )
			{
				@unlink( PATH_IMG_PREMIO . $idPremio ."_".$i.".gif" );
				@unlink( PATH_IMG_PREMIO . $idPremio ."_".$i.".jpg" );
			}
			$_SESSION['msg'] = "Premio excluído com sucesso.";
		}
		else
			$_SESSION['msg'] = "Não foi possível excluir o Premio selecionado. Tente novamente.";	
		return;
	}
	
	//lista as LINHAS
	function listaPremios( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."premio WHERE 1=1 ";

		if( $_SESSION['fNomePremio'] != "" )
		{
			$sql .= "AND nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomePremio']) ."%'";
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
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idPremio=" . $grid[$i]->idPremio . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o premio ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idPremio=" . $grid[$i]->idPremio ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega um registro.
	function getOnePremio( $idPremio )
	{
		$sql = "SELECT * FROM ".PRE."premio WHERE idPremio = " .$idPremio;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	function getPremioPorFabricante( $idFabricante )
	{
		$sql = "SELECT * FROM ".PRE."premio WHERE idFabricante = " .$idFabricante;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}

	function nomePremio( $idPremio )
	{
		$sql = "SELECT * FROM ".PRE."premio WHERE idPremio = " .(trim($idPremio) ? $idPremio : "0");
		
		$query = $this->db->query(trataVarSql($sql));
		
		$obj = $this->db->fetchObject( $query );
		
		return $obj->nome;
	}

	function delImgPremio( $idPremio, $i )
	{
		@unlink( PATH_IMG_PREMIO . $idPremio ."_".$i.".gif" );
		@unlink( PATH_IMG_PREMIO . $idPremio ."_".$i.".jpg" );
		$_SESSION['msg'] = "Imagem excluída com sucesso.";
	}

	function allPremios()
	{
		$sql = "SELECT * FROM ".PRE."premio ORDER BY nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
	
	function buscaPremios( $needle )
	{
		if(trim($needle))
		{
			$sql = "SELECT * FROM ".PRE."premio WHERE nome LIKE '%".trataVarSql(str_replace(" ", "%", $needle))."%' ORDER BY nome";
			$query = $this->db->query($sql);
		}
		else
			$query = "";
			
		return $query;
	}
}
?>