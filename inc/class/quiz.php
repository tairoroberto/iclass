<?
class quiz
{
	var $db;
	
	//construtora
	function quiz()
	{
		$this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
	}
	
	//insere uma quiz
	function insertQuiz()
	{
		if( !trim($_POST['textoNome']) )
		{
			$_SESSION['msg'] = "Preencha todos os campos obrigatórios.";
			return 0;
		}
		else
		{
			//sempre cadastro os dois, independente se veio só 1. na listagem, só exibo o que foi cadastrado
			$sql 	= "INSERT INTO ".PRE."quiz (nome, descricao, idTreinamento, ativo)
									VALUES 
								(	'" . trim($_POST['textoNome']) . "', 
									'" . trim($_POST['descricao']) . "',
									" . trim($_POST['idTreinamento']) . ",
									'" . trim($_POST['ativo']) . "'
								)";
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				$idQuiz = $this->db->insertId();
				//$this->cadastraPerguntas($idQuiz);
				$_SESSION['msg'] = "Quiz incluído com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inclusão do Quiz. Tente novamente.";
				return 0;
			}
		}
	}
	
	//altera um quiz
	function alterQuiz()
	{
		if( !trim($_POST['textoNome']) )
		{
			$_SESSION['msg'] = "Preencha todos os campos obrigatórios.";
			return 0;
		}
		else
		{
			$sql 	= "	UPDATE ".PRE."quiz SET 
								nome = '" .$_POST['textoNome']. "',
								ativo = '" .$_POST['ativo']. "',
								descricao = '" .$_POST['descricao']. "',
								idTreinamento = " .$_POST['idTreinamento']. "
						WHERE idQuiz = " .$_POST['idQuiz'];
			
			$query	= $this->db->query($sql);
			
			if( $query )
			{
				//$this->cadastraPerguntas($_POST['idQuiz']);
				$_SESSION['msg'] = "Quiz alterado com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a alteração do Quiz. Tente novamente.";
				return 0;
			}
		}
	}
	
	//deleta uma quiz
	function delQuiz( $idQuiz )
	{
		$this->apagaRegistrosQuiz($idQuiz);
		$sql = "DELETE FROM ".PRE."quiz WHERE idQuiz = " .trataVarSql($idQuiz);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Quiz excluído com sucesso.";
		else
			$_SESSION['msg'] = "Não foi possível excluir o Quiz selecionado. Tente novamente.";	
		return;
	}
	
	function delPergunta( $idQuizPergunta )
	{
		$this->apagaRespostas($idQuizPergunta);
		$sql = "DELETE FROM ".PRE."quiz_pergunta WHERE idQuizPergunta = " .trataVarSql($idQuizPergunta);
		$query = $this->db->query($sql);

		if( $this->db->affectedRows() )
			$_SESSION['msg'] = "Pergunta excluída com sucesso.";
		else
			$_SESSION['msg'] = "Não foi possível excluir a Pergunta selecionada. Tente novamente.";	
		return;
	}
	
	//apaga os registros do quiz
	function apagaRegistrosQuiz($idQuiz)
	{
		//apaga registros anteriores
		$sqlQuiz = "SELECT * FROM ".PRE."quiz WHERE idQuiz = ".$idQuiz;
		$queryQuiz = $this->db->query($sqlQuiz);
		$objQuiz = $this->db->fetchObject($queryQuiz);
		
		$sqlPerguntas = "SELECT * FROM ".PRE."quiz_pergunta WHERE idQuiz = ".$idQuiz;
		$queryPerguntas = $this->db->query($sqlPerguntas);
		
		while($objPergunta = $this->db->fetchObject($queryPerguntas) )
			$this->db->query("DELETE FROM ".PRE."quiz_resposta WHERE idQuizPergunta = ".$objPergunta->idQuizPergunta);
		$this->db->query("DELETE FROM ".PRE."quiz_pergunta WHERE idQuiz = ".$idQuiz);	
	}
	
	function apagaRespostas($idQuizPergunta)
	{		
		$this->db->query("DELETE FROM ".PRE."quiz_resposta WHERE idQuizPergunta = ".$idQuizPergunta);
	}
	
	function getPerguntas($idQuiz, $random = 0, $limit = 0)
	{
		if( trim($idQuiz) )
		{
			$sql = "SELECT * FROM ".PRE."quiz_pergunta WHERE idQuiz = ".$idQuiz.($random ? " ORDER BY RAND() " : "").($limit ? " LIMIT ".PERGUNTAS_POR_QUIZ : "");
			$query = $this->db->query($sql);
			while($r = $this->db->fetchObject($query))
				$linhas[] = $r;
			return $linhas;
		}
	}
	
	//verifica se a resposta é correta de acordo com a pergunta.
	function pegaRespostaCorreta($idQuizPergunta = "0")
	{
		$sql = "SELECT * FROM ".PRE."quiz_resposta WHERE idQuizPergunta = ".$idQuizPergunta." AND certa = '1'";
		$query = $this->db->query($sql);
		$obj = $this->db->fetchObject($query);
		return $obj->idQuizResposta;
	}
	
	function getPergunta($idQuizPergunta = "0")
	{
		$sql = "SELECT * FROM ".PRE."quiz_pergunta WHERE idQuizPergunta = ".$idQuizPergunta;
		$query = $this->db->query($sql);
		return $this->db->fetchObject($query);
	}
	
	//computa o resultado do quiz
	function computaResultado()
	{
		$sql = "INSERT INTO ".PRE."quiz_pontuacao (idTreinamento, idUsuarioSite, acertos, data) VALUES (".$_SESSION['respostaUsuario']["idTreinamento"].", ".$_SESSION['sess_idUsuarioSite'].", ".($_SESSION['respostaUsuario']["acertos"] ? $_SESSION['respostaUsuario']["acertos"] : "0").", NOW())";	
		$this->db->query($sql);
	}
	
	function getRespostas($idQuizPergunta = "0", $random = 0)
	{
		if( trim($idQuizPergunta) )
		{
			$sql = "SELECT * FROM ".PRE."quiz_resposta WHERE idQuizPergunta = ".$idQuizPergunta.($random ? " ORDER BY RAND() " : "");

			$query = $this->db->query($sql);
			while($r = $this->db->fetchObject($query))
				$linhas[] = $r;
			return $linhas;
		}
	}
	
	//cadastra perguntas pro quiz
	function cadastraRespostas($idQuizPergunta)
	{
		$this->apagaRespostas($idQuizPergunta);
		
		for( $i=0; $i<count($_POST['txtResposta']); $i++ )
		{
			if( trim($_POST['txtResposta'][$i]) )
			{
				if( $_POST['respostaCerta'] == ($i+1) )
					$certa = "1";
				else
					$certa = "0";
					
				$sqlResposta = "INSERT INTO ".PRE."quiz_resposta (idQuizPergunta, resposta, certa) VALUES (".$idQuizPergunta.", '".utf8_encode($_POST['txtResposta'][$i])."', '".$certa."')";
				$this->db->query($sqlResposta);
			}
		}
		
		return 1;
	}
	
	function cadastraPerguntaAvulsa($idQuiz)
	{
		if( trim($_POST['txtPergunta']) )
		{
			$sqlPergunta = "INSERT INTO ".PRE."quiz_pergunta (idQuiz, pergunta, tempo) VALUES (".$idQuiz.", '".trataVarSql($_POST['txtPergunta'])."', '".trataVarSql($_POST['txtTempo'])."')";

			$queryPergunta = $this->db->query($sqlPergunta);
			if( $queryPergunta )
			{
				$_SESSION['msg'] = "Pergunta inserida com sucesso.";
				return 1;
			}
			else
			{
				$_SESSION['msg'] = "Ocorreu um erro durante a inserção da Pergunta. Tente novamente.";
				return 0;		
			}
		}
		else
		{
			$_SESSION['msg'] = "Preencha o texto da pergunta corretamente.";
			return 0;	
		}
	}
	
	function alteraPerguntaAvulsa($idQuizPergunta)
	{
		if( trim($_POST['txtPergunta']) )
		{
			$sqlPergunta = "UPDATE ".PRE."quiz_pergunta SET pergunta = '".trataVarSql($_POST['txtPergunta'])."', tempo = '".trataVarSql($_POST['txtTempo'])."' WHERE idQuizPergunta = '".trataVarSql($idQuizPergunta)."'";
			$queryPergunta = $this->db->query($sqlPergunta);
			if( $queryPergunta )
			{
				return "Pergunta atualizada com sucesso.";
			}
			else
			{
				return "Ocorreu um erro durante a atualização da Pergunta. Tente novamente.";		
			}
		}
		else
		{
			return "Preencha o texto da pergunta corretamente.";	
		}
	}
	
	//lista as LINHAS
	function listaQuizs( $regPorPag )
	{
		if( !trim($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."quiz WHERE 1=1 ";

		if( $_SESSION['fNomeQuiz'] != "" )
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
		$fullGrid = $grid . "<tr><td align='center' colspan='7'>" . $tabela . "</td></tr>";
		
		return $fullGrid;
	}
	
	//lista as LINHAS
	function listaPerguntas( $idQuiz )
	{
		if( !isset($_GET['p']) )
			$p = 1;
		else
			$p = $_GET['p'];
		
		$start 		= ($p * $regPorPag) - $regPorPag;
		
		$sql	 	= "SELECT * FROM ".PRE."quiz_pergunta WHERE 1=1 AND idQuiz = '".trataVarSql($idQuiz)."' ";
			
		$orderBy = " ORDER BY pergunta ASC ";
		
		$sql .= $orderBy;
					
		//$tabela 	= paginacaoBar( $sql, $regPorPag, "questoes.php?action=listar", $p );
		
		//$sql		.= " LIMIT " . $start . ", " . $regPorPag;
		$query 		= $this->db->query($sql);
		
		while( $cliente = $this->db->fetchObject( $query ) )
			$r[] = $cliente;
		
		$grid = $this->montaGridPerguntas( $idQuiz, $r );
		
		//grid com a paginacao
		$fullGrid = $grid . "<tr><td align='center' colspan='7'>" . $tabela . "</td></tr>";
		
		return $fullGrid;
	}
	
	//passar um array de objetos aqui
	function montaGridPerguntas( $idQuiz, $grid )
	{
		$tb .= "<tr style='cursor: pointer;' onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";

		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";

		
		
		//levando em conta que $grid é um array de objetos.
		for( $i=0; $i<count($grid); $i++ )
		{
			$respostas = $this->getRespostas($grid[$i]->idQuizPergunta);
			$formRespostas = "	<tr id='trFormRespostas".$grid[$i]->idQuizPergunta."' style='display: none;'>
									<td colspan='4'>
									<form name='formRespostas".$grid[$i]->idQuizPergunta."' id='formRespostas".$grid[$i]->idQuizPergunta."' method='post'>
										<input type='hidden' name='idQuizPergunta' value='".$grid[$i]->idQuizPergunta."' />
										<input type='hidden' name='idLoader' value='loader_respostas".$grid[$i]->idQuizPergunta."' />
										<table id='tableRespostas".$grid[$i]->idQuizPergunta."' style='width: 777px;'>
											<tr>
												<td style='text-align: center; padding: 5px;'>Insira ou altere as respostas da questão abaixo. Ao terminar, clique em <b>Salvar Alterações</b> para completar o cadastro.</td>
											</tr>
											<tr>
												<td><div class='floatLeft' style='padding: 2px; color: #666666; text-align: center; font-weight: bold; width: 535px;'>Alternativa</div><div class='floatLeft' style='width: 80px; text-align: center; margin-left: 3px;'>Correta?</div><div class='clear'></div></div><div style='height: 5px;'></td>
											</tr>";
											$j = 0;
											for( $j=0; $j<count($respostas); $j++ )
											{
												$formRespostas .= '	<tr id="trResposta'.$respostas[$j]->idQuizResposta.'">
																		<td><div class="floatLeft" style="padding: 2px; color: #666666; width: 535px;"><input type="text" name="txtResposta[]" value="'.utf8_decode($respostas[$j]->resposta).'" class="inputText" size="95" /></div><div class="floatLeft" style="width: 80px; text-align: center; margin-left: 3px;  pading-top: 1px; padding-bottom: 2px;"><input type="radio" name="respostaCerta" value="'. ($j+1) .'" '.(($respostas[$j]->certa == "1") ? "checked" : "").' /></div><div class="floatLeft" style="width: 100px; text-align: center;"><input type="button" name="btRemover" value="Remover" class="inputButton" onclick="removeAlternativa(this)" /></div><div class="clear"></div></div><div style="height: 5px;"></td>
																	</tr>';
											}
			$formRespostas .= "			</table>
										<table style='width: 777px;'>
											<tr>												
												<td style='text-align: right; width: 400px; '><input type='button' name='btEnviaRespostas' class='inputButton' value='Salvar Alterações' onclick='atualizaRespostas(\"".$grid[$i]->idQuizPergunta."\")' />&nbsp;&nbsp;<input type='button' name='btAdicionaResposta' class='inputButton' onclick='adicionaNovaResposta(\"tableRespostas".$grid[$i]->idQuizPergunta."\", \"".$grid[$i]->idQuizPergunta."\")' value='Adicionar uma Resposta' /></td>
												<td style='width: 50px; padding-left: 10px;'><img src='../img/loader.gif' border='0' id='loader_respostas".$grid[$i]->idQuizPergunta."' style='display: none;'  /></td>
												<td class='msgError' id='msgErrorPergunta".$grid[$i]->idQuizPergunta."'></td>
											</tr>
										</table>
									</form>
									<iframe style='display: none;' id='iframe_".$grid[$i]->idQuizPergunta."' name='iframe_".$grid[$i]->idQuizPergunta."'></iframe>
									</td>
								</tr>";
			
			$tb .= "<td style='padding-left: 5px;' onMouseOut='changeColorRow( this, \"\");'><form  name='formPergunta".$grid[$i]->idQuizPergunta."' id='formPergunta".$grid[$i]->idQuizPergunta."' action='questoes.php?idQuiz=".$grid[$i]->idQuiz."&idQuizPergunta=".$grid[$i]->idQuizPergunta."&action=alterar' method='post'><div class='floatLeft' style='width: 540px;'>Pergunta: <input type='text' name='txtPergunta". $grid[$i]->idQuizPergunta ."' value='" . utf8_decode($grid[$i]->pergunta) . "' size='60' maxlength='200' style='color: #999999;' />&nbsp;&nbsp;Tempo: <input type='text' class='inputText' name='txtTempo".$grid[$i]->idQuizPergunta."' style='width: 28px;' maxlength='10' value='".$grid[$i]->tempo."' onkeydown='return noLetters(event);' /></div><div class='floatLeft'  style='width: 50px; padding-top: 8px;'><img src='../img/loader.gif' border='0' id='loader".$grid[$i]->idQuizPergunta."' style='display: none;'  /></div><div class='clear'></div></form></td>
					<td align='center'><input type='button' class='inputButton' value='Alterar Pergunta' onclick='alteraPergunta(\"".$grid[$i]->idQuizPergunta."\");' title=\"Atualizar o texto da pergunta\" /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir esta pergunta?\", \"questoes.php?action=excluir&idQuiz=" . $grid[$i]->idQuiz ."&idQuizPergunta=".$grid[$i]->idQuizPergunta."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>
					<td align='center'><img style='cursor: pointer;' onclick='abreRespostas(\"trFormRespostas".$grid[$i]->idQuizPergunta."\", \"".$grid[$i]->idQuizPergunta."\")' src='../img/bt_visualizar.gif' border='0' alt='Ver Alternativas desta pergunta' /></td>
					</tr>
					".$formRespostas."			
					<tr style='cursor: pointer;' onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"\");'>";
			
			$formRespostas = "";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}
	
	 //passar um array de objetos aqui
	function montaGrid( $grid )
	{
		$tb .= "<tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"#FFFFFF\");'>";

		if( $_GET['p'] )
			$p = "p=".$_GET['p'];
		else
			$p = "begin=1";

		$treinamento = new treinamento();
		
		//levando em conta que $grid é um array de objetos.
		for( $i=0; $i<count($grid); $i++ )
		{
			$objTreinamento = $treinamento->getOneTreinamento($grid[$i]->idTreinamento);
			$tb .= "<td style='padding-left: 5px;'>" . cortaTexto($grid[$i]->nome, 100) . "</td>
					<td style='padding-left: 5px;'>" . cortaTexto($objTreinamento->nome, 100) . "</td>
					<td style='padding-left: 5px;'>" . ($grid[$i]->ativo == "1" ? "<span style='color: green;'>sim</span>" : "<span style='color: red;'>n&atilde;o</span>") ."</td>
					<td align='center'><input type='button' name='btQuestoes' onclick='verQuestoes(\"".$grid[$i]->idQuiz."\");' class='inputButton' value='Perguntas' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='redirectHref(\"incluir.php?action=alterar&p=". (trim($_GET['p'])? $_GET['p'] : "1") ."&idQuiz=" . $grid[$i]->idQuiz . "\");' ' src='../img/editar.gif' border='0' alt='Alterar este registro.' /></td>
					<td align='center'><img style='cursor: pointer;' onClick='confirma(\"Deseja realmente excluir o quiz ".$grid[$i]->pergunta."?\", \"listar.php?action=excluir&idQuiz=" . $grid[$i]->idQuiz ."\");' src='../img/excluir.gif' border='0' alt='Excluir este registro' /></td>";
			$tb .= "</tr><tr onMouseOver='changeColorRow( this, \"#EFEFEF\");' onMouseOut='changeColorRow( this, \"#FFFFFF\");'>";
		}
		
		$tb .= "</tr>";
		
		return $tb;
	}

	//pega um registro.
	function getOneQuiz( $idQuiz )
	{
		$sql = "SELECT * FROM ".PRE."quiz WHERE idQuiz = " .$idQuiz;
		
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}
	
	function getOneQuizFromTreinamento( $idTreinamento)
	{
		$sql = "SELECT * FROM ".PRE."quiz WHERE idTreinamento = " .$idTreinamento;
		$query = $this->db->query(trataVarSql($sql));
				
		return $this->db->fetchObject( $query );
	}

	function allQuizs()
	{
		$sql = "SELECT * FROM ".PRE."quiz ORDER BY nome";
		
		$query = $this->db->query($sql);
			
		return $query;
	}
	
	function buscaQuizs( $needle )
	{
		if(trim($needle))
		{
			$sql = "SELECT * FROM ".PRE."quiz WHERE nome LIKE '%".trataVarSql(str_replace(" ", "%", $needle))."%' ORDER BY nome";
			$query = $this->db->query($sql);
		}
		else
			$query = "";
			
		return $query;
	}
}
?>