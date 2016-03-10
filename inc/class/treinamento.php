<?
class treinamento
{
	var $db;
	
	//construtora
	function treinamento()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	//insere uma treinamento
	function insertTreinamento()
	{
		if( !trim($_POST['nome']) )
		{
			$_SESSION['msg'] = "Preencha todos os campos obrigatórios.";
			return 0;
		}
		else
		{
			//montando cadastro das extensões das imagens do produto
			$extImg = "";
			for($m=0; $m<=1; $m++)
				$extImg[$m] = strtolower(substr($_FILES['imgTreinamento'.($m+1)]['name'], -3));
				
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."treinamento (nome, descricao, idFabricante, ativo, slides, extImg1, extImg2, data)
									VALUES 
								(	'" . trim($_POST['nome']) . "', 
									'" . trim($_POST['descricao']) . "',
									" . trim($_POST['idFabricante']) . ",
									'" . trim($_POST['ativo']) . "',
									'" . ($_POST['slides'] ? trim(implode("|", $_POST['slides'])) : "") . "',
									'" . $extImg[0] . "',
									'" . $extImg[1] . "',
									NOW()
								)";

			$query	= $this->db->query($sql);
			
			if( $query )
			{			
				$idTreinamento = $this->db->insertId();
				if( $_FILES['imgTreinamento1']['name'] ) //imgPrincipal
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgTreinamento1']['name'], -3));

					if( $ext == "jpg" || $ext == "gif" )
					{
						$img->sobe( $_FILES['imgTreinamento1']['tmp_name'], PATH_IMG_TREINAMENTO_UPLOAD .$idTreinamento.".".$ext);
					}
				}	
				
				if( $_FILES['imgTreinamento2']['name'] ) //thumbnail
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgTreinamento2']['name'], -3));

					if( $ext == "jpg" || $ext == "gif"  )
					{
						$img->sobe( $_FILES['imgTreinamento2']['tmp_name'], PATH_IMG_TREINAMENTO_UPLOAD .$idTreinamento."_thumb.".$ext);
					}
				}
				
				$_SESSION['msg'] = "Treinamento incluído com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Treinamento. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera um treinamento
	function alterTreinamento()
	{
		if( !trim($_POST['nome']))
		{
			$_SESSION['msg'] = "Preencha todos os campos obrigatórios.";
			return 0;
		}
		else
		{
			$sqlExtImg = "SELECT extImg1, extImg2 FROM ".PRE."treinamento WHERE idTreinamento = ".$_POST['idTreinamento'];
			$queryExtImg = $this->db->query($sqlExtImg);
			$objExtImg = $this->db->fetchObject($queryExtImg);
			$extImg[0] = $objExtImg->extImg1;
			$extImg[1] = $objExtImg->extImg2;

			for($m=0; $m<=1; $m++)
			{
				$extImgAux = strtolower(substr($_FILES['imgTreinamento'.($m+1)]['name'], -3));
				if( trim($extImgAux) )
				{
					$extImg[$m] = $extImgAux;
				}
			}
			
			$sql 	= "	UPDATE ".PRE."treinamento SET 
								nome = '" .$_POST['nome']. "',
								ativo = '" .$_POST['ativo']. "',
								descricao = '" .$_POST['descricao']. "',
								idFabricante = " .$_POST['idFabricante']. ",
								slides = '" .($_POST['slides'] ? implode("|", $_POST['slides']) : ""). "',
								extImg1 = '" . $extImg[0] . "',
								extImg2 = '" . $extImg[1] . "'
						WHERE idTreinamento = " .$_POST['idTreinamento'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				if( $_FILES['imgTreinamento1']['name'] ) //imgPrincipal
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgTreinamento1']['name'], -3));

					if( $ext == "jpg" || $ext == "gif" )
					{
						$img->sobe( $_FILES['imgTreinamento1']['tmp_name'], PATH_IMG_TREINAMENTO_UPLOAD .$_POST['idTreinamento'].".".$ext);
							
					}
				}	
				
				if( $_FILES['imgTreinamento2']['name'] ) //thumbnail
				{	
					$img = new imagem();
					$ext = strtolower(substr($_FILES['imgTreinamento2']['name'], -3));

					if( $ext == "jpg" || $ext == "gif" )
					{
						$img->sobe( $_FILES['imgTreinamento2']['tmp_name'], PATH_IMG_TREINAMENTO_UPLOAD .$_POST['idTreinamento']."_thumb.".$ext);
					}
				}
				
				$_SESSION['msg'] = "Treinamento alterado com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração do Treinamento. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma treinamento
	function delTreinamento( $idTreinamento )
	{
		$sql = "DELETE FROM ".PRE."treinamento WHERE idTreinamento = " .trataVarSql($idTreinamento);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
		{
			@unlink( PATH_IMG_TREINAMENTO . $idTreinamento .".gif" );
			@unlink( PATH_IMG_TREINAMENTO . $idTreinamento . "_thumb.gif" );
			@unlink( PATH_IMG_TREINAMENTO . $idTreinamento .".jpg" );
			@unlink( PATH_IMG_TREINAMENTO . $idTreinamento . "_thumb.jpg" );
			$_SESSION['msg'] = "Treinamento excluído com sucesso.";
		}
		else
			$_SESSION['msg'] = "Não foi possível excluir o Treinamento selecionado. Tente novamente.";	
		return;
	}
	
	//lista as LINHAS
	function listaTreinamentos( $regPorPag, $site = "0", $frase = "", $idFabricante = "0")
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		if( $site )
			$innerUsuarioSite = " INNER JOIN ".PRE."usuario_site us ON us.idLoja = l.idLoja ";
		else
			$innerUsuarioSite = "";
			
		$sql	 	= "SELECT DISTINCT t.idTreinamento, t.nome, t.ativo, t.extImg1, t.extImg2, t.descricao, DATE_FORMAT(t.data , '%d/%m/%Y  %H:%i:%s') AS dataCriacao FROM ".PRE."treinamento t INNER JOIN ".PRE."fabricante f ON f.idFabricante = t.idFabricante INNER JOIN ".PRE."fabricante_categoria fc ON f.idFabricante = fc.idFabricante INNER JOIN ".PRE."loja l ON fc.idCategoria = l.idCategoria ".$innerUsuarioSite." WHERE 1=1 ".($idFabricante ? " AND t.idFabricante = ".$idFabricante." " : "")." ";

		if( $_SESSION['fNomeTreinamento'] != "" )
		{
			$sql .= "AND t.nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeTreinamento']) ."%'";
		}		
		
		if( $site )
			$sql .= " AND t.ativo = '1' AND us.idUsuarioSite = ".$_SESSION['sess_idUsuarioSite']." AND l.idLoja = ".$_SESSION['sess_idLoja']." ";
			
		$orderBy = " ORDER BY t.data DESC ";
		$sql .= $orderBy;
					
		if( !$site )
			$tabela = paginacaoBar( $sql, $regPorPag, "listar.php?action=listar", $p );
		else
			$tabela = paginacaoBarSite( $sql, $regPorPag, "index.php?land=treinamentos_det&idFabricante=".$_GET['idFabricante']."", $p, $frase );
		
		
		$sql		.= " LIMIT " . $start . ", " . $regPorPag;

		$query 		= $this->db->query($sql);

		while( $cliente = $this->db->fetchObject( $query ) )
			$r[] = $cliente;
		
		if( !$site )
			$grid = $this->montaGrid( $r );
		else
			$grid = $this->montaGridSite( $r );
		
		//grid com a paginacao
		$fullGrid = $grid . "<tr><td align='center' colspan='6'>" . $tabela . "</td></tr>";
		
		return $fullGrid;
	}
	
	function listaTreinamentosUsuario($regPorPag, $frase = "")
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT DISTINCT t.idTreinamento, t.nome as nomeTreinamento, t.ativo, t.extImg1, t.extImg2, t.descricao, DATE_FORMAT(t.data , '%d/%m/%Y, às %H:%i:%s') AS dataCriacao, q.idQuiz, qp.acertos, (SELECT COUNT(*) FROM ".PRE."quiz_pontuacao WHERE idTreinamento = t.idTreinamento AND idUsuarioSite = ".$_SESSION['sess_idUsuarioSite'].") AS vezes_completado, DATE_FORMAT((SELECT data FROM ".PRE."quiz_pontuacao WHERE idTreinamento = t.idTreinamento AND idUsuarioSite =  ".$_SESSION['sess_idUsuarioSite']." ORDER BY data DESC LIMIT 1), '%d/%m/%Y, às %H:%i:%s') AS dataRealizado, u.idUsuarioSite FROM ".PRE."treinamento t INNER JOIN ".PRE."quiz q ON t.idTreinamento = q.idTreinamento INNER JOIN ".PRE."quiz_pontuacao qp ON qp.idTreinamento = q.idTreinamento INNER JOIN ".PRE."usuario_site u ON u.idUsuarioSite = qp.idUsuarioSite INNER JOIN ".PRE."fabricante f ON f.idFabricante = t.idFabricante INNER JOIN ".PRE."fabricante_categoria fc ON f.idFabricante = fc.idFabricante INNER JOIN ".PRE."loja l ON fc.idCategoria = l.idCategoria INNER JOIN ".PRE."usuario_site us ON us.idLoja = l.idLoja WHERE qp.idUsuarioSite = ".$_SESSION['sess_idUsuarioSite']." ";

		if( $_SESSION['fNomeTreinamento'] != "" )
		{
			$sql .= "AND t.nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeTreinamento']) ."%'";
		}		
		
		if( $site )
			$sql .= " AND t.ativo = '1' AND us.idUsuarioSite = ".$_SESSION['sess_idUsuarioSite']." AND l.idLoja = ".$_SESSION['sess_idLoja']." ";
			
		$orderBy = " GROUP BY t.idTreinamento ORDER BY t.data DESC";
		
		$sql .= $orderBy;
		
		$tabela = paginacaoBarSite( $sql, $regPorPag, "index.php?land=treinamentos_usuario", $p, $frase );
		
		$sql		.= " LIMIT " . $start . ", " . $regPorPag;
		
		$query 		= $this->db->query($sql);
		
		while( $linha = $this->db->fetchObject( $query ) )
		{
			//calcula a media baseada no treinamento
			//baseada sempre em porcentagem
			$sqlMedia = "SELECT acertos FROM ".PRE."quiz_pontuacao WHERE idTreinamento = ".$linha->idTreinamento." AND idUsuarioSite = ".$linha->idUsuarioSite;
			$queryMedia = $this->db->query($sqlMedia);
;			$sqlQtdPerguntas = "SELECT COUNT(*) AS qtdPerguntas FROM ".PRE."quiz_pergunta WHERE idQuiz = ".$linha->idQuiz;
			$queryQtdPerguntas = $this->db->query($sqlQtdPerguntas);
			$linhaQtdPerguntas = $this->db->fetchObject($queryQtdPerguntas);
			$baseMedia = 0;
			$mediaTotal = 0.0;
			$countMedias = 0;
			if( $linhaQtdPerguntas->qtdPerguntas > PERGUNTAS_POR_QUIZ )
				$baseMedia = PERGUNTAS_POR_QUIZ;
			else
				$baseMedia = $linhaQtdPerguntas->qtdPerguntas;

			while( $linhaMedia = $this->db->fetchObject($queryMedia) )
			{		
				$mediaTotal += round(($linhaMedia->acertos / ($baseMedia > 0 ? $baseMedia : 1)), 2) * 10; //dará a média de acertos por quiz. Ex: 7,5
				$countMedias++;
			}
			$linha->media = str_replace(".", ",", round(($mediaTotal / ($countMedias > 0 ? $countMedias : 1)), 1));
			
			$r[] = $linha;
		}
		$grid = $this->montaGridUsuarioSite( $r );
		
		//grid com a paginacao
		$fullGrid = $grid . "<tr><td align='center' colspan='6'>" . $tabela . "</td></tr>";
		
		return $fullGrid;
	}
	
	function existeQuiz($idTreinamento)
	{
		$sqlExiste = "SELECT count(*) as countQuiz FROM ".PRE."quiz WHERE idTreinamento = '".$idTreinamento."'";
		$queryExiste = $this->db->query($sqlExiste);
		
		$objExiste = $this->db->fetchObject($queryExiste);
		return $objExiste->countQuiz;
			
	}
	
	//lista as LINHAS
	function BuscaTreinamentos( $frase = "" )
	{		
		$sqlFabricantes = "SELECT DISTINCT t.idFabricante FROM ".PRE."treinamento t INNER JOIN ".PRE."fabricante f ON f.idFabricante = t.idFabricante INNER JOIN ".PRE."fabricante_categoria fc ON f.idFabricante = fc.idFabricante INNER JOIN ".PRE."loja l ON fc.idCategoria = l.idCategoria INNER JOIN ".PRE."usuario_site us ON us.idLoja = l.idLoja WHERE t.nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeTreinamento']) ."%'  AND t.ativo = '1' AND us.idUsuarioSite = ".$_SESSION['sess_idUsuarioSite']." AND l.idLoja = ".$_SESSION['sess_idLoja']." ";
		$queryFabricantes = $this->db->query($sqlFabricantes);
		$tb = "";
		$totalEncontrados = 0;
		$fabricante = new fabricante();
		
		while( $linha = $this->db->fetchObject( $queryFabricantes ) )
		{
			$sqlTreinamentos = "SELECT count(t.idTreinamento) as countTreinamento FROM ".PRE."treinamento t  INNER JOIN ".PRE."fabricante f ON f.idFabricante = t.idFabricante INNER JOIN ".PRE."fabricante_categoria fc ON f.idFabricante = fc.idFabricante INNER JOIN ".PRE."loja l ON fc.idCategoria = l.idCategoria INNER JOIN ".PRE."usuario_site us ON us.idLoja = l.idLoja AND t.ativo = '1' AND us.idUsuarioSite = ".$_SESSION['sess_idUsuarioSite']." AND l.idLoja = ".$_SESSION['sess_idLoja']." WHERE t.nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeTreinamento']) ."%' AND t.idFabricante = ".$linha->idFabricante. " ";

			$query = $this->db->query($sqlTreinamentos);
			$queryTreinamentos = $this->db->query($sqlTreinamentos);
			$obj = $this->db->fetchObject($queryTreinamentos);
			$tb .= '<a href="index.php?land=fabricante_det&idFabricante='.$linha->idFabricante.'">'.$fabricante->nomeFabricante($linha->idFabricante).'</a>
                    <p>'.(($obj->countTreinamento == "1") ? "<strong>(1) Treinamento disponível</strong>" : "<strong>(".$obj->countTreinamento.") Treinamentos disponíveis</strong>").'</p>
                    <img src="Util/img/linha_minha_conta.jpg" />';
			$totalEncontrados += $obj->countTreinamento;
		}
		
		if( $totalEncontrados )
			$head = '<p align="center">'.(($totalEncontrados == 1) ? "Foi encontrado 1 treinamento" : "Foram encontrados ".$totalEncontrados." treinamentos").' para a sua pesquisa:</p>';
		return (trim($tb) ? $head.$tb : $frase);
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
			$tb .= "<td style='padding-left: 5px; text-align: center;'>" . (file_exists(PATH_IMG_TREINAMENTO.$grid[$i]->idTreinamento."_thumb.".$grid[$i]->extImg2) ? "<img src='".PATH_IMG_TREINAMENTO.$grid[$i]->idTreinamento."_thumb.".$grid[$i]->extImg2."' border='1' style='width: 80px; height: 50px;' />" : "") . "</td>
					<td style='padding-left: 5px;'>" . cortaTexto($grid[$i]->nome, 100) . "</td>
					<td style='padding-left: 5px; text-align: center;'>" . $grid[$i]->dataCriacao . "</td>
					<td style='padding-left: 5px;'>" . ($grid[$i]->ativo == "1" ? "<span style='color: green;'>sim</span>" : "<span style='color: red;'>n&atilde;o</span>") ."</td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idTreinamento=" . $grid[$i]->idTreinamento . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o treinamento ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idTreinamento=" . $grid[$i]->idTreinamento ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}
	
	//Monta a grid para o site
	function montaGridSite( $grid )
	{
		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";

		//levando em conta que $grid é um array de objetos.
		$tb = "";
		for( $i=0; $i<count($grid); $i++ )
		{
			$tb .= "<div class='pruduto'>
						<a href='javascript:abrir(\"".$grid[$i]->idTreinamento."\");'>".$grid[$i]->nome."</a>
						<a href='javascript:abrir(\"".$grid[$i]->idTreinamento."\");'><div style='width: 108px; height: 104px;'  class='img_minha_conta'>" . (file_exists(PATH_IMG_TREINAMENTO_SITE.$grid[$i]->idTreinamento."_thumb.".$grid[$i]->extImg2) ? "<img src='".PATH_IMG_TREINAMENTO_SITE.$grid[$i]->idTreinamento."_thumb.".$grid[$i]->extImg2."' border='1' style='width: 108px; height: 104px;' />" : "<img src='Util/img/thumb_treinamento_default.jpg' />") . "</div></a>
						<p>" . cortaTexto($grid[$i]->descricao, 200) . "<p>
						<div class='pruduto_publicado'> <a href='javascript:abrir(\"".$grid[$i]->idTreinamento."\");'>Publicado em ".$grid[$i]->dataCriacao."</a></div>
					</div>
					<img src='Util/img/linha_minha_conta.jpg' />";
		}
		
		return $tb;
	}
	
	//Monta a grid para o site
	function montaGridUsuarioSite( $grid )
	{
		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";

		//levando em conta que $grid é um array de objetos.
		for( $i=0; $i<count($grid); $i++ )
		{
			$tb .= "<div class='pruduto'>
						<a href='javascript:abrir(\"".$grid[$i]->idTreinamento."\");'>".$grid[$i]->nomeTreinamento."</a>
						<a href='javascript:abrir(\"".$grid[$i]->idTreinamento."\");'><div style='width: 108px; height: 104px;'  class='img_minha_conta'>" . (file_exists(PATH_IMG_TREINAMENTO_SITE.$grid[$i]->idTreinamento."_thumb.".$grid[$i]->extImg2) ? "<img src='".PATH_IMG_TREINAMENTO_SITE.$grid[$i]->idTreinamento."_thumb.".$grid[$i]->extImg2."' border='1' style='width: 108px; height: 104px;' />" : "<img src='Util/img/thumb_treinamento_default.jpg' />") . "</div></a>

                        <p>Quiz completado <strong>".$grid[$i]->vezes_completado.(($grid[$i]->vezes_completado > 1) ? " vezes" : " vez")."</strong><br/>
						Última conclusão em <strong>".$grid[$i]->dataRealizado."</strong><br />
						Média Final: <strong>".$grid[$i]->media."</strong></p>
					</div>
					<img src='Util/img/linha_minha_conta.jpg' />";
		}
		
		return $tb;
	}

	//pega um registro.
	function getOneTreinamento( $idTreinamento )
	{
		$sql = "SELECT * FROM ".PRE."treinamento WHERE idTreinamento = " .$idTreinamento;

		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}

	function allTreinamentos()
	{
		$sql = "SELECT * FROM ".PRE."treinamento WHERE ativo = '1' ORDER BY nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
	
	function allTreinamentosSemQuiz($idQuiz = 0)
	{
		$sql = "SELECT t.* FROM ".PRE."treinamento t WHERE t.ativo = '1'  AND t.idTreinamento NOT IN(SELECT idTreinamento FROM ".PRE."quiz WHERE idTreinamento = t.idTreinamento ".(!$idQuiz ? "" : "AND idQuiz != ".$idQuiz)." ) ORDER BY t.nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
	
	function delImgTreinamento( $idTreinamento, $tipo )
	{
		@unlink( PATH_IMG_TREINAMENTO . $idTreinamento .$tipo.".gif" );
		@unlink( PATH_IMG_TREINAMENTO . $idTreinamento .$tipo.".jpg" );
		$_SESSION['msg'] = "Imagem excluída com sucesso.";
	}

}
?>