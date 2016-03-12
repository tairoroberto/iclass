<?
class usuario_site
{
	var $db;
	
	//construtora
	function usuario_site()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	//insere uma usuario_site
	function insertUsuarioSite( $fromSite = 0 )
	{
		/*if( !trim($_POST['nome']) ||
			!trim($_POST['email']) ||
			!trim($_POST['senha']) ||
			!trim($_POST['confirmacaoSenha']) ||
			!trim($_POST['dtNascimento']) ||
			!trim($_POST['idLoja']) ||
			!trim($_POST['cpf']) ||
			!trim($_POST['idCidade']) )
		{*/
		if( !trim($_POST['nome']) ||
			!trim($_POST['email']) ||
			!trim($_POST['senha']) ||
			!trim($_POST['confirmacaoSenha']) ||
			!trim($_POST['dtNascimento']) ||
			!trim($_POST['nomeLoja']) ||
			!trim($_POST['cpf']) ||
			!trim($_POST['idCidade']) ||
			!trim($_POST['chave_acesso']))
		{
			$_SESSION['msg'] = "Preencha todos os campos obrigatórios.";
			return 0;
		}
		else
		{
			if( strlen($_POST['senha']) < 6 )
			{
				$_SESSION['msg'] = "A senha precisa ter mais de 6 caracteres.";
				return 0;
			}

			if( okCPF(trim($_POST['cpf'])) )
			{
				$_SESSION['msg'] = "O CPF digitado não é válido Tente novamente.";
				return 0;			
			}

			if( trim($_POST['senha']) != trim($_POST['confirmacaoSenha']))
			{
				$_SESSION['msg'] = "A confirmação da senha é diferente da senha.";
				return 0;
			}

			if( !$_POST['chkTermos'] && $fromSite == 1 )
			{
				$_SESSION['msg'] = "Você precisa aceitar os termos de uso do site.";
				return 0;
			}

            /** Validações para chave de acesso */
            $sqlchave = "SELECT ch.* FROM ".PRE."chaves_acesso ch WHERE ch.valor_chave = '".$_POST['chave_acesso']."' ";

            $queryChaves = $this->db->query($sqlchave);
            $chave =  $this->db->fetchObject($queryChaves);

            if( !isset($chave->valor_chave) ) {
                $_SESSION['msg'] = "Chave de acesso não existe insira outra chave.";
                return 0;
            }

            if( $chave->ativa == 1 ) {
                $_SESSION['msg'] = "Chave de acesso já esta sendo usado por outro usuário.";
                return 0;
            }


			$sqlJaExiste = "SELECT * FROM ".PRE."usuario_site WHERE email = '".trataVarSql($_POST['email'])."' OR cpf = '".trataVarSql($_POST['cpf'])."'";
			$queryJaExiste = $this->db->query($sqlJaExiste) or die(mysql_error()." - ".$sqlJaExiste);
			if( $this->db->numRows($queryJaExiste) )
			{
				$_SESSION['msg'] = "Este usuário já existe no banco de dados.";
				return 0;
			}

			//montando cadastro das extensões das imagens do produto
			$extImg = "";
			for($m=0; $m<=0; $m++)
				$extImg[$m] = strtolower(substr($_FILES['imgUsuarioSite'.($m+1)]['name'], -3));

			$loja = new loja();
			
			$novoIdLoja = $loja->buscaLoja($_POST['nomeLoja']);
			if( !trim($novoIdLoja) )
				$novoIdLoja = $loja->insertLoja($_POST['nomeLoja']);	
			
			$_SESSION['msg'] = "";
			
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."usuario_site (nome, endereco, idCidade, idLoja, cpf, cep, dataNascto, telefone, cargo, email, senha, extImg, ativo, id_chave_acesso)
									VALUES 
								(	'" . trataVarSql(trim($_POST['nome'])) . "', 
									'" . trataVarSql(trim($_POST['endereco'])) . "',
									" . trim($_POST['idCidade']) . ",
									" . trim($novoIdLoja) . ",
									'" . trataVarSql(trim($_POST['cpf'])) . "',
									'" . trataVarSql(trim($_POST['cep'])) . "',
									'" . trim(formataDataSql($_POST['dtNascimento'])) . "', 
									'" . trataVarSql(trim($_POST['telefone'])) . "', 
									'" . trataVarSql(trim($_POST['cargo'])) . "', 
									'" . trataVarSql(trim($_POST['email'])) . "', 
									'" . trim($_POST['senha']) . "', 
									'" . $extImg[0] . "',
									'1',
									". $chave->id_chave."
								)";

			$query	= $this->db->query($sql) or die(mysql_error()." - ".$sql);
			
			if( $query )
			{
				$idUsuarioSite = $this->db->insertId();

                $sqlchave = "Update ".PRE."chaves_acesso set ativa = 1 WHERE ".PRE."chaves_acesso.id_chave = ".$chave->id_chave." ";
                $queryChaves = $this->db->query($sqlchave);

				for($m=1; $m<=1; $m++)
				{
					if( $_FILES['imgUsuarioSite'.$m]['name'] )
					{	
						$img = new imagem();
						$ext = strtolower(substr($_FILES['imgUsuarioSite'.$m]['name'], -3));

						if( $ext == "jpg" || $ext == "gif" )
						{
							$img->sobeGD( $_FILES['imgUsuarioSite'.$m]['tmp_name'], PATH_IMG_USUARIO_SITE_UPLOAD .$idUsuarioSite."_".$m.".".$ext, 191, 184);
						}
					}	
				}
				
				if( $fromSite == "1" )
				{
					//Se veio do site, então renomeia a foto subida
					if( file_exists($_SESSION['pathNovaFoto']) )
					{
						$extNovaFoto = strtolower(substr($_SESSION['pathNovaFoto'], -3));
						rename($_SESSION['pathNovaFoto'], "img_usuario_site/".$idUsuarioSite.".".$extNovaFoto);
					}
					
					//Envia e-mail para equipe do iclass
					$message = "";
					$loja = new loja();
					$objCidade = $this->getCidade($_POST['idCidade']);
					$objEstado = $this->getEstadoPeloMunicipio($_POST['idCidade']);
					$objLoja  = $loja->getOneLoja($_POST['idLoja']);
					$message .= "iClass - Cadastro de Usuário<br />---------------------------<br /><br />";
					$message .= "Olá!<br />Um usuário se cadastrou pelo site. Seguem os dados dele abaixo:<br /><br />Nome: " . $_POST['nome'] . "<br />E-mail: ". trim($_POST['email']) ."<br />Data de Nascimento: ". $_POST['dtNascimento'] . "<br />Telefone: ".$_POST['telefone']."<br />CPF: ".$_POST['cpf']."<br />Endereço: ".$_POST['endereco']."<br />Cidade: ".$objCidade->nome."<br />Estado: ".$objEstado->sigla."<br />CEP: ".$_POST['cep']."<br />Loja: ".$objLoja->nome."<br />Cargo: ".$_POST['cargo']."<br /><br />-- Equipe iClass --";
					$headers = "From: iClas <contato@itailers.com.br>\r\nContent-type: text/html; charset=utf-8\r\n";
					@mail(MAIL_CADASTRO_SITE, "iClass - Cadastro de Usuário", $message, $headers);

					//Envia e-mail para usuário cadastrado do site
					$mensage2 = "";
					$message2 .= "iClass - Cadastro Efetuado<br />---------------------------<br /><br />";
					$message2 .= "Olá, ".$_POST['nome']."!<br /><br />Seu cadastro foi realizado com sucesso no iClass. Você precisa aguardar a liberação pelos administradores.<br /><br />Seus dados de acesso são: <br />Login: " .  trim($_POST['email']) ."<br />Senha: ". $_POST['senha'] . "<br /><br />Obrigado!<br /><br /><br />-- Equipe iClass --";
					$headers = "From: iClas <contato@itailers.com.br>\r\nContent-type: text/html; charset=utf-8\r\n";
					@mail(trim($_POST['email']), "iClass - Cadastro Efetuado", $message2, $headers);

					$_SESSION['msg'] = "Cadastro realizado com sucesso! Aguarde a liberação pela equipe do iClass.";
				}
				else
					$_SESSION['msg'] = "Usuário do Site incluído com sucesso.";
				return 1;
			}
			else
			{
				if( $fromSite == "1" )
					$_SESSION['msg'] = "Ocorreu um erro durante seu cadastro. Tente novamente.";
				else
					$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Usu&aacute;rio do Site. Tente novamente.";
				return 0;
			}
		}
	}
	
	function salvarFoto()
	{
		$img = new imagem();
		@unlink("../".PATH_IMG_USUARIO_SITE_SITE.$_SESSION['sess_idUsuarioSite']."_1.jpg");
		if( rename(PATH_IMG_USUARIO_SITE_UPLOAD.$_SESSION['sess_idImagem'].".jpg", "../".PATH_IMG_USUARIO_SITE_SITE.$_SESSION['sess_idUsuarioSite']."_1.jpg") )
		{
			//atualiza no banco a imagem do usuário
			$sqlImagem = "UPDATE ".PRE."usuario_site SET extImg = 'jpg' WHERE idUsuarioSite = '".$_SESSION['sess_idUsuarioSite']."'";
			$this->db->query($sqlImagem);
			$_SESSION['sess_extImg'] = "jpg";
			return 1;
		}
		else
			return 0;
	}
	
	//altera um usuario_site
	function alterUsuarioSite()
	{
		if( !trim($_POST['nome']) ||
			!trim($_POST['dtNascimento']))
		{
			$_SESSION['msg'] = "Preencha todos os campos obrigat&oacute;rios.";
			return 0;
		}
		else
		{			
			$sqlExtImg = "SELECT extImg FROM ".PRE."usuario_site WHERE idUsuarioSite = ".$_POST['idUsuarioSite'];
			$queryExtImg = $this->db->query($sqlExtImg);
			$objExtImg = $this->db->fetchObject($queryExtImg);
			$extImg[0] = $objExtImg->extImg;
		
			for($m=0; $m<=1; $m++)
			{
				$extImgAux = strtolower(substr($_FILES['imgUsuarioSite'.($m+1)]['name'], -3));
				if( trim($extImgAux) )
				{
					$extImg[$m] = $extImgAux;
				}
			}
			
			if( trim($_POST['senha']) )
			{
				if( ($_POST['senha'] == $_POST['confirmacaoSenha']) && trim($_POST['senha']) )
				{
					if( strlen($_POST['senha']) < 6 )
					{
						$_SESSION['msg'] = "A senha precisa ter mais de 6 caracteres.";
						return 0;
					}
		
					$sqlSenha = "senha = '".$_POST['senha']."', ";
				}
				elseif( trim($_POST['senha']) != trim($_POST['confirmacaoSenha']))
				{
					$_SESSION['msg'] = "A confirma&ccedil;&atilde; da senha é diferente da senha.";
					return 0;
				}
			}
			
			if( !$_SESSION['sess_idUsuarioSite'] )
			{
				$sqlCpf = "cpf = '" .$_POST['cpf']. "', ";
				$sqlEmail = "email = '" .$_POST['email']. "', ";
			}
			
			/*$sql 	= "	UPDATE ".PRE."usuario_site SET
								nome = '" .$_POST['nome']. "',
								ativo = '" .$_POST['ativo']. "',
								endereco = '" .$_POST['endereco']. "',
								idCidade = " .$_POST['idCidade']. ",
								idLoja = " .$_POST['idLoja']. ",
								".$sqlCpf."
								cep = '" .$_POST['cep']. "',
								dataNascto = '" .trim(formataDataSql($_POST['dtNascimento'])). "',
								telefone = '" .$_POST['telefone']. "',
								cargo = '" .$_POST['cargo']. "',
								".$sqlEmail."
								".$sqlSenha."
								extImg = '" . $extImg[0] . "'
						WHERE idUsuarioSite = " .$_POST['idUsuarioSite'];*/
				
			$loja = new loja();
			$idLoja = $loja->buscaLoja($_POST['nomeLoja']);
			
			if( !trim($idLoja) )
				$idLoja = $loja->insertLoja($_POST['nomeLoja']);
			
			$_SESSION['msg'] = "";
			
			$sql 	= "UPDATE ".PRE."usuario_site SET
								nome = '" .$_POST['nome']. "',
								ativo = '" .$_POST['ativo']. "',
								endereco = '" .$_POST['endereco']. "',
								idCidade = " .$_POST['idCidade']. ",
								idLoja = " .$idLoja. ",
								".$sqlCpf."
								cep = '" .$_POST['cep']. "',
								dataNascto = '" .trim(formataDataSql($_POST['dtNascimento'])). "',
								telefone = '" .$_POST['telefone']. "',
								cargo = '" .$_POST['cargo']. "',
								".$sqlEmail."
								".$sqlSenha."
								extImg = '" . $extImg[0] . "'
						WHERE idUsuarioSite = " .$_POST['idUsuarioSite'];
						
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				for($m=1; $m<=1; $m++)
				{
					if( $_FILES['imgUsuarioSite'.$m]['name'] )
					{	
						$img = new imagem();
						$ext = strtolower(substr($_FILES['imgUsuarioSite'.$m]['name'], -3));
						
						if( $ext == "jpg" || $ext == "gif")
						{
							$img->sobeGD( $_FILES['imgUsuarioSite'.$m]['tmp_name'], PATH_IMG_USUARIO_SITE_UPLOAD .$_POST['idUsuarioSite']."_".$m.".".$ext, 256, 256);
						}
					}	
				}

				if( $fromSite == "1" )
				{
					//Se veio do site, então renomeia a foto subida
					if( file_exists($_SESSION['pathNovaFoto']) )
					{
						$extNovaFoto = strtolower(substr($_SESSION['pathNovaFoto'], -3));
						rename($_SESSION['pathNovaFoto'], "img_usuario_site/".$_POST['idUsuarioSite'].".".$extNovaFoto);
					}
					$_SESSION['msg'] = "Cadastro atualizado com sucesso!<br />";
				}
				else
					$_SESSION['msg'] = "Usu&aacute;rio do Site atualizado com sucesso.";
				return 1;
			}
			else
			{
				if( $fromSite == "1" )
					$_SESSION['msg'] = "Ocorreu um erro durante a atualização de seu cadastro. Tente novamente.";
				else
					$_SESSION['msg'] = "Ocorreu um erro durante a atualização do Usu&aacute;rio do Site. Tente novamente.";
				return 0;
			}
		}
	}
	
	//ativa um usuário do site. 
	function ativaUsuario($flagAtivo, $idUsuarioSite)
	{
		$sql = "UPDATE ".PRE."usuario_site SET ativo = '".$flagAtivo."' WHERE idUsuarioSite = '".$idUsuarioSite."'";
		$query = $this->db->query($sql);

		if( $query )
		{
			//Se pediu para ativar, então envia email
			if( $flagAtivo == "1" )
			{
				$usuario = $this->getOneUsuarioSite($idUsuarioSite);
				//Envia e-mail para equipe do iclass
				$message = "";
				$message .= "iClass - Ativação Concluída<br />---------------------------<br /><br />";
				$message .= "Olá!<br />Olá, ".$usuario->nome."!<br /><br />Seu pedido de ativação foi concluído. Agora você pode usufruir do site por completo. <br />Seguem seus dados de acesso:<br />--------------<br /><br />Login: ".$usuario->email."<br />Senha: ".$usuario->senha."<br /><br />Atenciosamente,<br /><br />-- Equipe iClass --";
				$headers = "From: iClas <contato@itailers.com.br>\r\nContent-type: text/html; charset=utf-8\r\n";
				@mail($usuario->email, "iClass - Ativação Concluída", $message, $headers);
			}
			
			$_SESSION['msg'] = "Usu&aacute;rio ".($flagAtivo == "1" ? "ativado" : "desativado")." com sucesso.";
		}
		else
			$_SESSION['msg'] = "N&atilde;o foi poss&iacute;vel concluir a ".($flagAtivo == "1" ? "ativa&ccedil;&atilde;o" : "desativa&ccedil;&atilde;o")." do usu&aacute;rio. Tente novamente.";
		return 1;
	}
	
	//deleta uma usuario_site
	function delUsuarioSite( $idUsuarioSite )
	{
		$sql = "DELETE FROM ".PRE."usuario_site WHERE idUsuarioSite = " .trataVarSql($idUsuarioSite);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
		{
			@unlink( PATH_IMG_USUARIO_SITE . $idUsuarioSite ."_1.gif" );
			@unlink( PATH_IMG_USUARIO_SITE . $idUsuarioSite ."_1.jpg" );
			$_SESSION['msg'] = "Usu&aacute;rio do Site excluído com sucesso.";
		}
		else
			$_SESSION['msg'] = "Não foi possível excluir o Usu&aacute;rio do Site selecionado. Tente novamente.";	
		return;
	}
	
	//lista as LINHAS
	function listaUsuarioSite( $regPorPag )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."usuario_site WHERE 1=1 ";

		if( $_SESSION['fNomeUsuarioSite'] != "" )
		{
			$sql .= "AND nome LIKE '%". str_replace(' ', '%', $_SESSION['fNomeUsuarioSite']) ."%'";
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
	
	function printRelatorio()
	{
		$sql = "SELECT DISTINCT u.nome as nomeUsuarioSite, u.cargo, lj.nome as nomeLoja, c.nome as nomeRede, est.sigla, cid.nome as nomeCidade, t.nome as nomeTreinamento, t.idTreinamento, u.idUsuarioSite, q.idQuiz, qp.acertos, (SELECT COUNT(*) FROM ".PRE."quiz_pontuacao WHERE idTreinamento = t.idTreinamento AND idUsuarioSite = u.idUsuarioSite) AS vezes_completado
				FROM ".PRE."quiz_pontuacao qp
				INNER JOIN ".PRE."usuario_site u ON qp.idUsuarioSite = u.idUsuarioSite
				INNER JOIN ".PRE."loja lj ON lj.idLoja = u.idLoja
				INNER JOIN ".PRE."categoria c ON lj.idCategoria = c.idCategoria
				INNER JOIN ".PRE."cidade cid ON u.idCidade = cid.idCidade
				INNER JOIN ".PRE."estado est ON cid.id_uf = est.id
				INNER JOIN ".PRE."treinamento t ON qp.idTreinamento = t.idTreinamento
				INNER JOIN ".PRE."quiz q ON q.idTreinamento = t.idTreinamento
				INNER JOIN ".PRE."fabricante f ON t.idFabricante = f.idFabricante
				WHERE 1=1 ";	
		
		
		
		if( $_POST['fNomeUsuarioSite'] != "" )
			$sql .= " AND u.nome LIKE '%". str_replace(' ', '%', $_POST['fNomeUsuarioSite']) ."%'";
	
		if( count($_POST['cargo']) > 0 )
			$sql .= " AND u.cargo IN ('".$_POST['cargo'][0]."', '".$_POST['cargo'][1]."', '".$_POST['cargo'][2]."')";

		if( $_POST['idCidade'] != "" )
			$sql .= " AND u.idCidade = ".$_POST['idCidade']." ";

		if( $_POST['idLoja'] != "" )
			$sql .= " AND u.idLoja = ".$_POST['idLoja']." ";
			
		if( $_POST['idCategoria'] != "" )
			$sql .= " AND c.idCategoria = ".$_POST['idCategoria']." ";
			
		if( $_POST['idFabricante'] != "" )
			$sql .= " AND f.idFabricante = ".$_POST['idFabricante']." ";

		if( $_POST['idTreinamento'] != "" )
			$sql .= " AND u.idTreinamento = ".$_POST['idTreinamento']." ";
		
		//se o usuário não for administrador, e não tiver redes associadas, não vê nada. 
		if( $_SESSION['sess_isAdmin'] != "1" )
		{
			$sqlCategorias = "SELECT * FROM ".PRE."usuario_categoria WHERE idUsuario = '".$_SESSION['sess_idUsuario']."'";
			$queryCategorias = $this->db->query($sqlCategorias);
			if( $this->db->numRows($queryCategorias) == 0 )
				return;
		}
		
		$sql .= " GROUP BY t.idTreinamento, u.idUsuarioSite ORDER BY u.nome";

		$query 		= $this->db->query($sql);
		return $this->montaGridRelatorio( $query );
	}
	
	//passar um array de objetos aqui
	function montaGridRelatorio( $query )
	{
		$tb = "";
		while( $linha = $this->db->fetchObject($query) )
		{
			//calcula a media baseada no treinamento
			//baseada sempre em porcentagem
			$sqlMedia = "SELECT acertos FROM ".PRE."quiz_pontuacao WHERE idTreinamento = ".$linha->idTreinamento." AND idUsuarioSite = ".$linha->idUsuarioSite;
			$queryMedia = $this->db->query($sqlMedia);
			$sqlQtdPerguntas = "SELECT COUNT(*) AS qtdPerguntas FROM ".PRE."quiz_pergunta WHERE idQuiz = ".$linha->idQuiz;
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

		
			$tb .= "<tr>
						<td style='padding-left: 5px;'>" . $linha->nomeUsuarioSite . "</td>
						<td style='padding-left: 5px;'>" . $linha->cargo ."</td>
						<td style='padding-left: 5px;'>" . $linha->nomeRede."/".$linha->nomeLoja ."</td>
						<td style='padding-left: 5px;'>" . $linha->nomeCidade ."</td>
						<td style='padding-left: 5px;'>" . $linha->sigla ."</td>
						<td style='padding-left: 5px;'>" . $linha->nomeTreinamento ."</td>
						<td style='padding-left: 5px;'>" . $linha->media ."</td>
					</tr>";
		}		
		
		return $tb;
	}
	
	function printRelatorioPremiacao()
	{
		$sql = "SELECT DISTINCT u.nome as nomeUsuarioSite, u.cargo, u.idLoja, lj.nome as nomeLoja, c.nome as nomeRede, est.sigla, cid.nome as nomeCidade, t.nome as nomeTreinamento, t.idTreinamento, u.idUsuarioSite, q.idQuiz, qp.acertos, (SELECT COUNT(*) FROM ".PRE."quiz_pontuacao WHERE idTreinamento = t.idTreinamento AND idUsuarioSite = u.idUsuarioSite) AS vezes_completado
				FROM ".PRE."quiz_pontuacao qp
				INNER JOIN ".PRE."usuario_site u ON qp.idUsuarioSite = u.idUsuarioSite
				INNER JOIN ".PRE."loja lj ON lj.idLoja = u.idLoja
				INNER JOIN ".PRE."categoria c ON lj.idCategoria = c.idCategoria
				INNER JOIN ".PRE."cidade cid ON u.idCidade = cid.idCidade
				INNER JOIN ".PRE."estado est ON cid.id_uf = est.id
				INNER JOIN ".PRE."treinamento t ON qp.idTreinamento = t.idTreinamento
				INNER JOIN ".PRE."quiz q ON q.idTreinamento = t.idTreinamento
				INNER JOIN ".PRE."fabricante f ON t.idFabricante = f.idFabricante
				WHERE 1=1 ";	
		
		
		
		if( $_POST['fNomeUsuarioSite'] != "" )
			$sql .= " AND u.nome LIKE '%". str_replace(' ', '%', $_POST['fNomeUsuarioSite']) ."%'";
	
		if( count($_POST['cargo']) > 0 )
			$sql .= " AND u.cargo IN ('".$_POST['cargo'][0]."', '".$_POST['cargo'][1]."', '".$_POST['cargo'][2]."')";

		if( $_POST['idCidade'] != "" )
			$sql .= " AND u.idCidade = ".$_POST['idCidade']." ";

		if( $_POST['idLoja'] != "" )
			$sql .= " AND u.idLoja = ".$_POST['idLoja']." ";
		
		if( $_POST['idCategoria'] != "" )
			$sql .= " AND c.idCategoria = ".$_POST['idCategoria']." ";
			
		if( $_POST['idFabricante'] != "" )
			$sql .= " AND f.idFabricante = ".$_POST['idFabricante']." ";

		if( $_POST['idTreinamento'] != "" )
			$sql .= " AND u.idTreinamento = ".$_POST['idTreinamento']." ";
		
		$sql .= " GROUP BY t.idTreinamento, u.idUsuarioSite ORDER BY u.nome";

		$query 		= $this->db->query($sql);
		return $this->montaGridRelatorioPremiacao( $query );
	}
	
	//passar um array de objetos aqui
	function montaGridRelatorioPremiacao( $query )
	{
		$tb = "";
		$mediaGeral = array();
		$mediasGerais = array();
		$idUsuarioSite_aux = "";
		$cUsers = 0;
		$counter = 0;
		$counterLinhaFinal = 0;
		
		while( $linha = $this->db->fetchObject($query) )
		{	
			//calcula a media baseada no treinamento
			//baseada sempre em porcentagem
			$sqlMedia = "SELECT acertos FROM ".PRE."quiz_pontuacao WHERE idTreinamento = ".$linha->idTreinamento." AND idUsuarioSite = ".$linha->idUsuarioSite;
			$queryMedia = $this->db->query($sqlMedia);
			$sqlQtdPerguntas = "SELECT COUNT(*) AS qtdPerguntas FROM ".PRE."quiz_pergunta WHERE idQuiz = ".$linha->idQuiz;
			$queryQtdPerguntas = $this->db->query($sqlQtdPerguntas);
			$linhaQtdPerguntas = $this->db->fetchObject($queryQtdPerguntas);
			$baseMedia = 0;
			$mediaTotal = 0.0;
			$countMedias = 0;
			$counter++;
			
			if( $linhaQtdPerguntas->qtdPerguntas > PERGUNTAS_POR_QUIZ )
				$baseMedia = PERGUNTAS_POR_QUIZ;
			else
				$baseMedia = $linhaQtdPerguntas->qtdPerguntas;

			while( $linhaMedia = $this->db->fetchObject($queryMedia) )
			{		
				$mediaTotal += round(($linhaMedia->acertos / ($baseMedia > 0 ? $baseMedia : 1)), 2) * 10; //dará a média de acertos por quiz. Ex: 7,5
				$countMedias++;
			}
			
			$cUsers++;
			
			//pega todos os quizes ativos que o usuário não fez e que ele teria direito de fazer
			/*$sqlAllQuiz = "SELECT count(*) AS treinamentosRestantes FROM ".PRE."quiz 
							WHERE idQuiz NOT IN(SELECT 
													q.idQuiz 
												FROM ".PRE."quiz q 
												INNER JOIN ".PRE."quiz_pontuacao qp ON qp.idTreinamento = q.idTreinamento 
												WHERE qp.idUsuarioSite = ".$linha->idUsuarioSite."
												AND q.ativo = '1' ) 
							AND ativo = '1' 
							AND idTreinamento IN (/*Treinamentos que o usuário ainda pode fazer
													SELECT DISTINCT t1.idTreinamento
													FROM ".PRE."treinamento t1
													INNER JOIN ".PRE."fabricante_categoria fc1 ON fc1.idFabricante = t1.idFabricante
													INNER JOIN ".PRE."categoria c1 ON fc1.idCategoria = c1.idCategoria
													INNER JOIN ".PRE."loja l1 ON l1.idCategoria = c1.idCategoria
													INNER JOIN ".PRE."usuario_site us1 ON us1.idLoja = l1.idLoja
													WHERE t1.idTreinamento NOT
													IN (
														SELECT DISTINCT t.idTreinamento
														FROM ".PRE."treinamento t
														LEFT JOIN ".PRE."quiz_pontuacao qp ON qp.idTreinamento = t.idTreinamento
														WHERE t.idTreinamento = qp.idTreinamento
														AND qp.idUsuarioSite = ".$linha->idUsuarioSite."
														AND t.ativo = '1'
													)
													AND us1.idUsuarioSite = ".$linha->idUsuarioSite."
													AND l1.idLoja = ".$linha->idLoja."
													AND t1.ativo = '1')";*/			
			
			/*** CÁLCULO DA MÉDIA GERAL ***/
			if( ($idUsuarioSite_aux == "") && ($counter == $this->db->numRows($query)) ) //se for a primeira linha e esta for a última, calcula a média geral final
			{
				$mediaGeral[] = round(($mediaTotal / ($countMedias > 0 ? $countMedias : 1)), 1); //faz a média antes de calcular a média geral 
				
				$tb = str_replace("[rspan]", "rowspan='".$cUsers."'", $tb);

				$totalMediaGeral = 0.0;
				for($i=0; $i<count($mediaGeral); $i++)
					$totalMediaGeral += $mediaGeral[$i]; //dará a média de acertos por quiz. Ex: 7,5
				
				$nTreinamentosRestantes = $this->getNumTreinamentosRestantes($linha->idUsuarioSite, $linha->idLoja, $_POST['idFabricante']);

				
				$mediasGerais[] = str_replace(".", ",", round(($totalMediaGeral / ((count($mediaGeral) + $nTreinamentosRestantes) > 0 ? (count($mediaGeral) + $nTreinamentosRestantes) : 1)), 1));				
				//print 'count($mediaGeral): ' . count($mediaGeral).' - objAllQuiz->treinamentosRestantes : '.$nTreinamentosRestantes.'<br />';
				$mediaGeral = array();			
			}
			elseif( ($idUsuarioSite_aux != "") && ($idUsuarioSite_aux != $linha->idUsuarioSite) )
			{
				$tb = str_replace("[rspan]", "rowspan='".$cUsers."'", $tb);

				$totalMediaGeral = 0.0;
				for($i=0; $i<count($mediaGeral); $i++)
					$totalMediaGeral += $mediaGeral[$i]; //dará a média de acertos por quiz. Ex: 7,5
				
				$userAux = $this->getOneUsuarioSite($idUsuarioSite_aux);
				$nTreinamentosRestantes = $this->getNumTreinamentosRestantes($idUsuarioSite_aux, $userAux->idLoja, $_POST['idFabricante']); //contamos quantos treinamentos falta para o usuário da linha atual. neste ponto, o $linha->idUsuarioSite é o próximo usuário!

				$mediasGerais[] = str_replace(".", ",", round(($totalMediaGeral / ((count($mediaGeral) + $nTreinamentosRestantes) > 0 ? (count($mediaGeral) + $nTreinamentosRestantes) : 1)), 1));				
				//print 'count($mediaGeral): ' . count($mediaGeral).' - objAllQuiz->treinamentosRestantes : '.$nTreinamentosRestantes.'<br />';
				$mediaGeral = array();			
				$mediaGeral[] = round(($mediaTotal / ($countMedias > 0 ? $countMedias : 1)), 1); //esta média já é a média do próximo usuário da linha
			}
			else
			{
				$mediaGeral[] = round(($mediaTotal / ($countMedias > 0 ? $countMedias : 1)), 1);
			}
			/********/
			
			$linha->media = str_replace(".", ",", round(($mediaTotal / ($countMedias > 0 ? $countMedias : 1)), 1));
			
			//var_dump($mediaGeral)."<br />";
			

			if( $idUsuarioSite_aux == "" || $idUsuarioSite_aux != $linha->idUsuarioSite )
			{
				$cUsers = 0;
				$tb .= "<tr>
							<td style='padding-left: 5px;' [rspan]>" . $linha->nomeUsuarioSite . "</td>
							<td style='padding-left: 5px;' [rspan]>" . $linha->cargo ."</td>
							<td style='padding-left: 5px;' [rspan]>" . $linha->nomeRede."/".$linha->nomeLoja ."</td>
							<td style='padding-left: 5px;' [rspan]>" . $linha->nomeCidade ."</td>
							<td style='padding-left: 5px;' [rspan]>" . $linha->sigla ."</td>
							<td style='padding-left: 5px;'>" . $linha->nomeTreinamento ."</td>
							<td style='padding-right: 5px; text-align: right;'>" . $linha->media ."</td>
							<td style='padding-left: 5px; text-align: center; vertical-align: middle;' [rspan]>[mediaGeral".$counterLinhaFinal."]</td>
						</tr>";
				
				$counterLinhaFinal++;
				$idUsuarioSite_aux = $linha->idUsuarioSite;
			}
			else
			{
				$tb .= "<tr>
							<td style='padding-left: 5px;'>" . $linha->nomeTreinamento ."</td>
							<td style='padding-right: 5px; text-align: right;'>" . $linha->media ."</td>
						</tr>";
				
				$idUsuarioSite_aux = $linha->idUsuarioSite;	
			}		
			
			//chegou na última iteração
			if( $counter == $this->db->numRows($query) )
			{
				$totalMediaGeral = 0.0;
				for($i=0; $i<count($mediaGeral); $i++)
					$totalMediaGeral += $mediaGeral[$i]; //dará a média de acertos por quiz. Ex: 7,5
				
				$nTreinamentosRestantes = $this->getNumTreinamentosRestantes($linha->idUsuarioSite, $linha->idLoja, $_POST['idFabricante']);

				$mediasGerais[] = str_replace(".", ",", round(($totalMediaGeral / ((count($mediaGeral) + $nTreinamentosRestantes) > 0 ? (count($mediaGeral) + $nTreinamentosRestantes) : 1)), 1));
				//print 'count($mediaGeral): ' . count($mediaGeral).' - objAllQuiz->treinamentosRestantes : '.$nTreinamentosRestantes.'<br />';
				$mediaGeral = array();
				$totalMediaGeral = 0.0;					
			}
		}		

		for($i=0; $i<count($mediasGerais); $i++)
			$tb = str_replace("[mediaGeral".$i."]", $mediasGerais[$i], $tb);	
		//print "<!-- $tb -->";
		$tb = str_replace("[rspan]", "rowspan='".($cUsers + 1)."'", $tb); //soma 1 para compensar o índice causado pelo cUsers = 0 lá em cima.
		
		return $tb;
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
					<td style='padding-left: 5px; text-align: center;'>
						" . "<form method='post'>
								<input type='hidden' name='idUsuarioSite' value='".$grid[$i]->idUsuarioSite."' />
								<input type='hidden' name='ativaUsuario' value='1' />
								".($grid[$i]->ativo == "1" ? "<input type='hidden' name='flagAtivo' value='0' /><input type='submit' name='btAtivar' value='Desativar' class='btAtivo' />" : "<input type='hidden' name='flagAtivo' value='1' /><input type='submit' name='btAtivar' value='Ativar' class='btDesativo' />") ."
							</form>
					</td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idUsuarioSite=" . $grid[$i]->idUsuarioSite . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o usuario_site ".$grid[$i]->nome."?\", \"listar.php?action=excluir&idUsuarioSite=" . $grid[$i]->idUsuarioSite ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega quantos treinamentos falta o usuário fazer
	function getNumTreinamentosRestantes($idUsuarioSite, $idLoja, $idFabricante)
	{
		$sqlAllQuiz = "SELECT count(DISTINCT t1.idTreinamento) as treinamentosRestantes
											FROM ".PRE."treinamento t1
											INNER JOIN ".PRE."fabricante_categoria fc1 ON fc1.idFabricante = t1.idFabricante
											INNER JOIN ".PRE."categoria c1 ON fc1.idCategoria = c1.idCategoria
											INNER JOIN ".PRE."loja l1 ON l1.idCategoria = c1.idCategoria
											INNER JOIN ".PRE."usuario_site us1 ON us1.idLoja = l1.idLoja
											WHERE t1.idTreinamento NOT
											IN (
												SELECT DISTINCT t.idTreinamento
												FROM ".PRE."treinamento t
												LEFT JOIN ".PRE."quiz_pontuacao qp ON qp.idTreinamento = t.idTreinamento
												WHERE t.idTreinamento = qp.idTreinamento
												AND qp.idUsuarioSite = ".$idUsuarioSite."
												AND t.ativo = '1'
											)
											AND us1.idUsuarioSite = ".$idUsuarioSite."
											AND l1.idLoja = ".$idLoja."
											".( $idFabricante ? " AND t1.idFabricante = ".$idFabricante." " : "" )."
											AND t1.ativo = '1'";

		$queryAllQuiz = $this->db->query($sqlAllQuiz);
		$obj = $this->db->fetchObject($queryAllQuiz);
		return $obj->treinamentosRestantes;
	}

	//pega um registro.
	function getOneUsuarioSite( $idUsuarioSite )
	{
		$sql = "SELECT *, ch.valor_chave
                  FROM ".PRE."usuario_site u
                INNER JOIN ".PRE."chaves_acesso ch
                    ON ch.id_chave = u.id_chave_acesso
                WHERE idUsuarioSite = '" .trataVarSql($idUsuarioSite)."'";
		$query = $this->db->query($sql);
		$objUsuario = $this->db->fetchObject( $query );
		
		//Pega a loja cadastrada pelo usuário
		$sqlLoja = "SELECT * FROM ".PRE."loja WHERE idLoja = '".$objUsuario->idLoja."'";
		$queryLoja = $this->db->query($sqlLoja);
		$objLoja = $this->db->fetchObject($queryLoja);
		
		//cria o atributo da loja no objeto
		$objUsuario->nomeLoja = $objLoja->nome;
		return $objUsuario;
	}
	
	//pega um registro a partir do email.
	function getUsuarioSiteEmail( $emailUsuarioSite )
	{
		$sql = "SELECT * FROM ".PRE."usuario_site WHERE email = '" .trataVarSql($emailUsuarioSite)."'";
		$query = $this->db->query($sql);
		return $this->db->fetchObject( $query );
	}
	
	function nomeUsuarioSite( $idUsuarioSite )
	{
		$sql = "SELECT * FROM ".PRE."usuario_site WHERE idUsuarioSite = " .trataVarSql((trim($idUsuarioSite) ? $idUsuarioSite : "0"));
		
		$query = $this->db->query($sql);
		
		$obj = $this->db->fetchObject( $query );
		
		return $obj->nome;
	}

	function delImgUsuarioSite( $idUsuarioSite, $site = 0 )
	{
		if( !$site )
		{
			@unlink( PATH_IMG_USUARIO_SITE . $idUsuarioSite ."_1.gif" );
			@unlink( PATH_IMG_USUARIO_SITE . $idUsuarioSite ."_1.jpg" );
		}
		else
		{
			@unlink( PATH_IMG_USUARIO_SITE_SITE . $idUsuarioSite ."_1.gif" );
			@unlink( PATH_IMG_USUARIO_SITE_SITE . $idUsuarioSite ."_1.jpg" );
		}
		$_SESSION['msg'] = "Imagem excluída com sucesso.";
	}

	function delFotoAtual( $idUsuarioSite )
	{
		@unlink( "../".PATH_IMG_USUARIO_SITE_SITE . $idUsuarioSite ."_1.jpg" );
		return 1;
	}

	function allUsuarioSites()
	{
		$sql = "SELECT * FROM ".PRE."usuario_site ORDER BY nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
	
	function buscaUsuarioSites( $needle )
	{
		if(trim($needle))
		{
			$sql = "SELECT * FROM ".PRE."usuario_site WHERE nome LIKE '%".trataVarSql(str_replace(" ", "%", $needle))."%' ORDER BY nome";
			$query = $this->db->query($sql);
		}
		else
			$query = "";
			
		return $query;
	}

	//pega todos os cadastros
	function allEstados()
	{
		$sql = "SELECT * FROM ".PRE."estado ORDER BY nome";
		
		$query = $this->db->query($sql);
		
		while( $estado = $this->db->fetchObject( $query ) )
			$r[] = $estado;
				
		return $r;
	}

	//pega um estado
	function getEstadoPeloMunicipio($idCidade = "0")
	{
		$sql = "SELECT * FROM ".PRE."cidade ".($idCidade ? "WHERE idCidade = ".$idCidade : "");
		$query = $this->db->query($sql);
		$obj = $this->db->fetchObject($query);
		if( $obj->id_uf )
		{
			$sqlEstado = "SELECT * FROM ".PRE."estado WHERE id = '".$obj->id_uf."'";
			$queryEstado = $this->db->query($sqlEstado);
			return $this->db->fetchObject($queryEstado);
		}
	}

	//pega uma cidade
	function getCidade($idCidade = "0")
	{
		$sql = "SELECT * FROM ".PRE."cidade ".($idCidade ? "WHERE idCidade = ".$idCidade : "");
		$query = $this->db->query($sql);
		return $this->db->fetchObject($query);
	}

	//pega todos os cadastros
	function municipiosPorEstado($idEstado)
	{
		$sql = "SELECT * FROM ".PRE."cidade WHERE id_uf = '".$idEstado."' ORDER BY nome";
		
		$query = $this->db->query($sql);
		
		while( $cidade = $this->db->fetchObject( $query ) )
			$r[] = $cidade;
				
		return $r;
	}
}
?>