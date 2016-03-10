<?
class fabricante
{
	var $db;
	
	//construtora
	function fabricante()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	//insere uma fabricante em inglês/portugues
	function insertFabricante()
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
			for($m=0; $m<=1; $m++)
				$extImg[$m] = strtolower(substr($_FILES['imgFabricante'.($m+1)]['name'], -3));

			
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."fabricante (nome, descricao, linkBanner, extImg1, extImg2)
									VALUES 
								(	'" . trim($_POST['nome']) . "', 
									'" . trim($_POST['descricao']) . "',
									'" . trim($_POST['linkBanner']) . "',
									'" . $extImg[0] . "',
									'" . $extImg[1] . "'
								)";
							
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$idFabricante = $this->db->insertId();
				
				//insere as categorias (rede) para o fabricante
				for( $i=0; $i<count($_POST['idCategoria']); $i++ )
				{
					$sqlCategorias = "INSERT INTO ".PRE."fabricante_categoria (idFabricante, idCategoria) VALUES (".$idFabricante.", ".$_POST['idCategoria'][$i].")";
					$this->db->query($sqlCategorias);
				}
					
				if( $_FILES['imgFabricante1']['name'] ) //imgPrincipal
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgFabricante1']['name'], -3));

					if( $ext == "jpg" || $ext == "gif" )
					{
						$img->sobe( $_FILES['imgFabricante1']['tmp_name'], PATH_IMG_FABRICANTE_UPLOAD .$idFabricante.".".$ext);
					}
				}	
				
				if( $_FILES['imgFabricante2']['name'] ) //thumbnail
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgFabricante2']['name'], -3));

					if( $ext == "jpg" || $ext == "gif" )
					{
						$img->sobe( $_FILES['imgFabricante2']['tmp_name'], PATH_IMG_FABRICANTE_UPLOAD .$idFabricante."_thumb.".$ext);
					}
				}	
				
				$_SESSION['msg'] = "Fabricante incluído com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Fabricante. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera um fabricante
	function alterFabricante()
	{
		if( 	!trim($_POST['nome']) 
			&& 	!trim($_POST['idFabricante']) )
		{
			$_SESSION['msg'] = "Preencha corretamente o campo Nome.";
			return 0;
		}
		else
		{
			$sqlExtImg = "SELECT extImg1, extImg2 FROM ".PRE."fabricante WHERE idFabricante = ".$_POST['idFabricante'];
			$queryExtImg = $this->db->query($sqlExtImg);
			$objExtImg = $this->db->fetchObject($queryExtImg);
			$extImg[0] = $objExtImg->extImg1;
			$extImg[1] = $objExtImg->extImg2;

			for($m=0; $m<=1; $m++)
			{
				$extImgAux = strtolower(substr($_FILES['imgFabricante'.($m+1)]['name'], -3));
				if( trim($extImgAux) )
				{
					$extImg[$m] = $extImgAux;
				}
			}
			
			$sql 	= "	UPDATE ".PRE."fabricante SET 
								nome = '" .$_POST['nome']. "',
								ativo = '" .$_POST['ativo']. "',
								descricao = '" .$_POST['descricao']. "',
								linkBanner = '" .$_POST['linkBanner']. "',
								extImg1 = '" . $extImg[0] . "',
								extImg2 = '" . $extImg[1] . "'
						WHERE idFabricante = " .$_POST['idFabricante'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$sqlDelCategorias = "DELETE FROM ".PRE."fabricante_categoria WHERE idFabricante = ".$_POST['idFabricante'];
				$this->db->query($sqlDelCategorias);
				
				//insere as categorias (rede) para o fabricante
				for( $i=0; $i<count($_POST['idCategoria']); $i++ )
				{
					$sqlCategorias = "INSERT INTO ".PRE."fabricante_categoria (idFabricante, idCategoria) VALUES (".$_POST['idFabricante'].", ".$_POST['idCategoria'][$i].")";
					$this->db->query($sqlCategorias);
				}
				
				if( $_FILES['imgFabricante1']['name'] ) //imgPrincipal
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgFabricante1']['name'], -3));

					if( $ext == "jpg" || $ext == "gif"  )
					{
						$img->sobe( $_FILES['imgFabricante1']['tmp_name'], PATH_IMG_FABRICANTE_UPLOAD .$_POST['idFabricante'].".".$ext);
					}
				}	
				
				if( $_FILES['imgFabricante2']['name'] ) //thumbnail
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgFabricante2']['name'], -3));

					if( $ext == "jpg" || $ext == "gif")
					{
						$img->sobe( $_FILES['imgFabricante2']['tmp_name'], PATH_IMG_FABRICANTE_UPLOAD .$_POST['idFabricante']."_thumb.".$ext);
					}
				}	
				
				$_SESSION['msg'] = "Fabricante alterado com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração do Fabricante. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma fabricante
	function delFabricante( $idFabricante )
	{
		$sql = "DELETE FROM ".PRE."fabricante WHERE idFabricante = " .trataVarSql($idFabricante);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
		{
			@unlink( PATH_IMG_FABRICANTE . $idFabricante .".gif" );
			@unlink( PATH_IMG_FABRICANTE . $idFabricante . "_thumb.gif" );
			@unlink( PATH_IMG_FABRICANTE . $idFabricante .".jpg" );
			@unlink( PATH_IMG_FABRICANTE . $idFabricante . "_thumb.jpg" );
			$_SESSION['msg'] = "Fabricante excluído com sucesso.";
		}
		else
			$_SESSION['msg'] = "Não foi possível excluir o Fabricante selecionado. Tente novamente.";	
		return;
	}

	
	//lista as LINHAS
	function listaFabricantes( $regPorPag, $site = "0", $frase = "" )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		$join = "";
		
		if( !$site )
			$join = " LEFT ";
		else
			$join = " INNER ";
		
		$sql	 	= "SELECT DISTINCT f.* FROM ".PRE."fabricante f ".$join." JOIN ".PRE."fabricante_categoria fc ON f.idFabricante = fc.idFabricante ".$join." JOIN ".PRE."loja l ON fc.idCategoria = l.idCategoria ".$join." JOIN ".PRE."usuario_site us ON us.idLoja = l.idLoja WHERE 1=1 ";

		if( $_GET['begin'] == "1" )
			$_SESSION['fNomeFabricante'] = "";

		if( $_SESSION['fNomeFabricante'] != "" )
		{
			$sql .= "AND f.nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeFabricante']) ."%'";
		}
			
		if( $site )
			$sql .= " AND f.ativo = '1' AND us.idUsuarioSite = ".$_SESSION['sess_idUsuarioSite']." AND l.idLoja = ".$_SESSION['sess_idLoja']." ";
		
		$orderBy = " GROUP BY f.idFabricante ORDER BY f.nome ASC ";
		
		$sql .= $orderBy;

		if( !$site )
			$tabela 	= paginacaoBar( $sql, $regPorPag, "listar.php?action=listar", $p );
		else
			$tabela = paginacaoBarSite( $sql, $regPorPag, "index.php?land=fabricantes", $p, $frase );

		$sql		.= " LIMIT " . $start . ", " . $regPorPag;
		
		$query 		= $this->db->query($sql);
		
		while( $fabricante = $this->db->fetchObject( $query ) )
			$r[] = $fabricante;
		
		if( !$site )
			$grid = $this->montaGrid( $r );
		else
			$grid = $this->montaGridSite( $r );
		
		//grid com a paginacao
		if( !$site )
			$fullGrid = $grid . "<tr><td align='center' colspan='5'>" . $tabela . "</td></tr>";
		else
			$fullGrid = $grid . "<div>" . $tabela . "</div>";
		
		
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
			$tb .= "<td style='padding-left: 5px; text-align: center;'>" . (file_exists(PATH_IMG_FABRICANTE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2) ? "<img src='".PATH_IMG_FABRICANTE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2."' border='1' style='width: 80px; height: 50px;' />" : "") . "</td>
					<td style='padding-left: 5px;'>" . cortaTexto($grid[$i]->nome, 100) . "</td>
					<td style='padding-left: 5px;'>" . ($grid[$i]->ativo == "1" ? "<span style='color: green;'>sim</span>" : "<span style='color: red;'>n&atilde;o</span>") ."</td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idFabricante=" . $grid[$i]->idFabricante . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o fabricante ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idFabricante=" . $grid[$i]->idFabricante ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}
	
	//Monta a grid para o site
	function montaGridSite( $grid )
	{
		$tb = "<div style='width: 540px;'>";
		
		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";

		//levando em conta que $grid é um array de objetos.
		for( $i=0; $i<count($grid); $i++ )
		{
			/*$tb .= "<div class='pruduto'>
						<a href='index.php?land=fabricante_det&idFabricante=".$grid[$i]->idFabricante."'>".$grid[$i]->nome."</a>
						<a href='index.php?land=fabricante_det&idFabricante=".$grid[$i]->idFabricante."'>" . (file_exists(PATH_IMG_FABRICANTE_SITE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2) ? "<img class='img_minha_conta' src='".PATH_IMG_FABRICANTE_SITE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2."' border='1' style='width: 108px; height: 104px;' />" : "") . "</a>
						<p>" . cortaTexto($grid[$i]->descricao, 200) . "<p>
					</div>
					<img src='Util/img/linha_minha_conta.jpg' />";*/
					
			$sqlTreinamentos = "SELECT count(t.idTreinamento) as countTreinamento FROM ".PRE."treinamento t  INNER JOIN ".PRE."fabricante f ON f.idFabricante = t.idFabricante INNER JOIN ".PRE."fabricante_categoria fc ON f.idFabricante = fc.idFabricante INNER JOIN ".PRE."loja l ON fc.idCategoria = l.idCategoria INNER JOIN ".PRE."usuario_site us ON us.idLoja = l.idLoja AND t.ativo = '1' AND us.idUsuarioSite = ".$_SESSION['sess_idUsuarioSite']." AND l.idLoja = ".$_SESSION['sess_idLoja']." WHERE t.nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeTreinamento']) ."%' AND t.idFabricante = ".$grid[$i]->idFabricante. " ";
			$queryTreinamentos = $this->db->query($sqlTreinamentos);
			$obj = $this->db->fetchObject($queryTreinamentos);
		
			$tb .= "<div class='floatLeft' style='width: 108px; height: 150px; padding: 5px; border: 1px solid #cccccc; margin-right: 15px; margin-bottom: 5px;'>
						<div><a href='index.php?land=treinamentos_det&idFabricante=".$grid[$i]->idFabricante."'>" . (file_exists(PATH_IMG_FABRICANTE_SITE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2) ? "<img class='img_minha_conta' src='".PATH_IMG_FABRICANTE_SITE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2."' border='1' style='width: 108px; height: 104px;' />" : "") . "</a></div>
						<div style='font-weight: bold; text-align: center; color: #2B569F; '>".$grid[$i]->nome."&nbsp;&nbsp;<span style='color: #666666; font-weight: normal; font-size: 10px;'>".(($obj->countTreinamento == "1") ? "<br />1 Treinamento" : (($obj->countTreinamento >= 2) ? "<br />".$obj->countTreinamento." Treinamentos" : "<br />Nenhum Treinamento"))."</span></div>
					</div>";
		
			/*$tb .= "<div class='pruduto'>
						<a href='index.php?land=treinamentos_det&idFabricante=".$grid[$i]->idFabricante."'>".$grid[$i]->nome."&nbsp;&nbsp;".(($obj->countTreinamento == "1") ? "<strong>- 1 Treinamento disponível</strong>" : "<strong>- ".$obj->countTreinamento." Treinamentos disponíveis</strong>")."</a>
						<a href='index.php?land=treinamentos_det&idFabricante=".$grid[$i]->idFabricante."'>" . (file_exists(PATH_IMG_FABRICANTE_SITE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2) ? "<img class='img_minha_conta' src='".PATH_IMG_FABRICANTE_SITE.$grid[$i]->idFabricante."_thumb.".$grid[$i]->extImg2."' border='1' style='width: 108px; height: 104px;' />" : "") . "</a>
						<p>" . cortaTexto($grid[$i]->descricao, 200) . "...<p>
					</div>
					<img src='Util/img/linha_minha_conta.jpg' />";*/
		}
		$tb .= "	<div class='clear'></div>
				</div>";
		
		return $tb;
	}

	//pega um registro.
	function getOneFabricante( $idFabricante )
	{
		$sql = "SELECT * FROM ".PRE."fabricante WHERE idFabricante = " .$idFabricante;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	function nomeFabricante( $idFabricante )
	{
		$sql = "SELECT * FROM ".PRE."fabricante WHERE idFabricante = " .(trim($idFabricante) ? $idFabricante : "0");
		
		$query = $this->db->query(trataVarSql($sql));
		
		$obj = $this->db->fetchObject( $query );
		
		return $obj->nome;
	}

	function delImgFabricante( $idFabricante, $tipo )
	{
		@unlink( PATH_IMG_FABRICANTE . $idFabricante .$tipo.".gif" );
		@unlink( PATH_IMG_FABRICANTE . $idFabricante .$tipo.".jpg" );
		$_SESSION['msg'] = "Imagem excluída com sucesso.";
	}

	function allFabricantes()
	{
		$sql = "SELECT * FROM ".PRE."fabricante ORDER BY nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}

	function allFabricantesSemPremio($idPremio = 0)
	{
		$sql = "SELECT f.* FROM ".PRE."fabricante f WHERE f.ativo = '1'  AND f.idFabricante NOT IN(SELECT idFabricante FROM ".PRE."premio WHERE idFabricante = f.idFabricante ".(!$idPremio ? "" : "AND idPremio != ".$idPremio)." ) ORDER BY f.nome";
		$query = $this->db->query($sql);
			
		return $query;
	}
	
	function buscaFabricantes( $needle )
	{
		if(trim($needle))
		{
			$sql = "SELECT * FROM ".PRE."fabricante WHERE nome LIKE '%".trataVarSql(str_replace(" ", "%", $needle))."%' ORDER BY nome";
			$query = $this->db->query($sql);
		}
		else
			$query = "";
			
		return $query;
	}
}
?>