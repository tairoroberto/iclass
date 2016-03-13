<?php 
require_once "config.inc.php";
require_once "inc/class/usuario_site.php";

$usuario_site = new usuario_site();
$page = "";

//Logout
if($_REQUEST['logout'] == "1")
{
	$_SESSION['sess_idUsuarioSite'] = "";
	$_SESSION['sess_nomeUsuarioSite'] = "";	
	unset($_SESSION['sess_idUsuarioSite'], $_SESSION['sess_nomeUsuarioSite']);
}

// Esqueci minha senha
if($_POST['esqueci_senha'] == "1")
{
	if( trim($_POST['txtEmailLembrete']) )
	{
		$uSite = $usuario_site->getUsuarioSiteEmail($_POST['txtEmailLembrete']);
		if(trim($uSite->email))
		{
			$message = "iClass - Lembrete de Senha<br />---------------------------<br /><br />";
			$message .= "Olá, ".$uSite->nome."!<br />Você solicitou que sua senha fosse lembrada. Para isto, enviamos este e-mail com a sua senha antiga para que possa utilizá-la novamente. <br /><br />Sua senha é: ".$uSite->senha."<br /><br />Atenciosamente,<br /><br />Equipe iClass";
			$headers = "From: iClass <contato@itailers.com.br>\r\nContent-type: text/html; charset=utf-8\r\n";
			mail($_POST['txtEmailLembrete'], "iClass - Lembrete de Senha", $message, $headers);
			
			$js = "alert('Lembrete solicitado com sucesso! Verifique seu e-mail para obter sua antiga senha.');";
		}
		else
		{
			$js = "alert('Este usuário não está cadastrado no sistema.');";
		}
	}
	else
	{
		$js = "alert('Preencha o e-mail para lembrete de senha corretamente.');";
	}
}

switch($_REQUEST['land'])
{
	case "cadastro":
		$page = "pages/cadastro.php";
		if( $_SESSION['sess_idUsuarioSite'] )
			$breadcrumb = '<li><a href="index.php?land=cadastro">Alterar Cadastro</a></li>';
		else
			$breadcrumb = '<li><a href="index.php?land=cadastro">Cadastro</a></li>';
	break;
	case "cadastro_ok":
		$page = "pages/cadastro_ok.php";
		$breadcrumb = '<li><a href="index.php?land=cadastro">Cadastro</a></li>';
	break;
	case "login":
		$page = "pages/login.php";
		$breadcrumb = '<li><a href="index.php?land=login">Login</a></li>';
	break;
	/*case "fabricantes":
		$page = "pages/fabricantes.php";
		$breadcrumb = '<li><a href="index.php?land=fabricantes&begin=1">Fabricantes</a></li>';
	break;
	case "fabricante_det":
		$page = "pages/fabricante_det.php";
		$breadcrumb = '<li><a href="index.php?land=fabricantes'.($_GET['p'] ? "&p=".$_GET['p'] : "").'">Voltar para Fabricantes</a></li>';
	break;*/
	case "treinamentos":
		$page = "pages/fabricantes.php";
		$breadcrumb = '<li><a href="index.php?land=treinamentos&begin=1">Meu Perfil</a></li>';
	break;
	case "treinamentos_det":
		$page = "pages/fabricante_det.php";
		$breadcrumb = '<li><a href="index.php?land=treinamentos_det&idFabricante=13">Treinamentos</a></li>';
	break;
	case "perfil_empresa":
		$page = "pages/perfil_empresa.php";
		$breadcrumb = '<li><a href="index.php?land=perfil_empresa&idFabricante='.$_GET['idFabricante'].'">Perfil da Empresa</a></li>';
	break;
	/*case "treinamentos":
		$page = "pages/treinamentos.php";
		$breadcrumb = '<li><a href="index.php?land=treinamentos&begin=1">Treinamentos</a></li>';
	break;
	case "treinamentos_busca":
		$page = "pages/busca_treinamentos.php";
		$breadcrumb = '<li><a href="index.php?land=treinamentos_busca&begin=1">Busca de Treinamentos</a></li>';
	break;
	*/
	case "treinamentos_usuario":
		$page = "pages/treinamentos_usuario.php";
		$breadcrumb = '<li><a href="index.php?land=treinamentos_usuario&begin=1">Meu Boletim</a></li>';
	break;
	case "manuais":
		$page = "pages/manuais.php";
		$breadcrumb = '<li><a href="index.php?land=manuais&begin=1">Manuais de Produtos</a></li>';
	break;
	case "faq":
		$page = "pages/faq.php";
		$breadcrumb = '<li><a href="index.php?land=faq&begin=1">FAQ</a></li>';
	break;
	case "suporte":
		$page = "pages/suporte.php";
		$breadcrumb = '<li><a href="index.php?land=suporte&begin=1">Suporte</a></li>';
	break;
	case "premios":
		$page = "pages/premios.php";
		$breadcrumb = '<li><a href="index.php?land=premios">Prêmios</a></li>';
	break;
	case "comofunciona":
		$page = "pages/comofunciona.php";
		$breadcrumb = '<li><a href="index.php?land=comofunciona">Como Funciona?</a></li>';
	break;
	case "oquee":
		$page = "pages/oquee.php";
		$breadcrumb = '<li><a href="index.php?land=oquee">O que é?</a></li>';
	break;
	case "cases":
		$page = "pages/cases.php";
		$breadcrumb = '<li><a href="index.php?land=cases">Cases</a></li>';
	break;
	case "anuncie":
		$page = "pages/anuncie.php";
		$breadcrumb = '<li><a href="index.php?land=anuncie">Anuncie</a></li>';
	break;
	case "faleconosco":
		$page = "pages/faleconosco.php";
		$breadcrumb = '<li><a href="index.php?land=faleconosco">Fale Conosco</a></li>';
	break;
	case "ajuda":
		$page = "pages/ajuda.php";
		$breadcrumb = '<li><a href="index.php?land=ajuda">Ajuda</a></li>';
	break;
	case "hometeste":
		$page = "pages/hometeste.php";
		$breadcrumb = '<li><a href="index.php?land=hometeste">Teste</a></li>';
	break;
	case "home":
		$page = "pages/homedef.php";
		$breadcrumb = '<li><a href="index.php?land=home">Home</a></li>';
	break;
	default:
		if($_SESSION['sess_idUsuarioSite'])
		{
			$page = "pages/homedef.php";
			$breadcrumb = '<li><a href="index.php?land=home&begin=1"></a></li>';
		}
		else
		{
			$page = "pages/login.php";
			$breadcrumb = "";
		}
}

if( $_POST['postLogin'] == "1" )
{
	if( !trim($_POST['email']) || !trim($_POST['senha']) )
	{
		$js = "alert('Login ou senha incorretos.');";
	}
	else
	{
		$sql = "SELECT idUsuarioSite, email, extImg, senha, nome, idLoja FROM ".PRE."usuario_site WHERE email = '" . trataVarSql($_POST['email']) . "' AND senha = '" . trataVarSql($_POST['senha']) . "' AND ativo = '1'";
		$query = $db->query($sql);
		$linha = $db->fetchObject($query);

		if( ( $_POST['email'] == $linha->email) && ( $_POST['senha'] == $linha->senha ) )
		{
			/*
				login e senha: uma vez que o login e a senha foram validados, é gravada uma sessão com md5 na base de dados.
				essa sessão será validada em todas as páginas. uma vez validada, faz-se uma nova query na base, desta vez, 
				cadastrando um novo valor aleatório para a sessão. isso impede que uma mesma sessão vague por todas as páginas.
				a encriptação é um md5() com 3 números.			
			*/
			//gravando a sessão na base.
			/*$_SESSION['sess_sessId'] = md5(rand(012,978));
			$sql = "UPDATE ".PRE."usuario_site SET sessId = '" . $_SESSION['sess_sessId'] . "' WHERE email = '" . $linha->email . "' AND senha = '" . $linha->senha . "' AND ativo = '1'";
			$db->query($sql); */
								
			//sessão com o id do usuário logado (usada para conferir se o cara tá logado ou não)
			$_SESSION['sess_idUsuarioSite'] = $linha->idUsuarioSite;
			$_SESSION['sess_nomeUsuarioSite'] = $linha->nome;
			$_SESSION['sess_idLoja'] = $linha->idLoja;
			$_SESSION['sess_extImg'] = $linha->extImg;
			
			header("Location: index.php?land=home");
		}
		else
			$js = "alert('Login ou senha incorretos.');";	
			
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>iClass - Treinamento Online para Promotores, Vendedores e Gerentes de Varejo.</title>
<link href="Util/css/index.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="Util/css/lightbox.css" />
<link rel="stylesheet" type="text/css" media="all" href="Util/jScrollPane/jScrollPane.css" />
<link href="Util/css/scrollBoxProdutos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript" src="Util/js/lightbox.js"></script>
<script type="text/javascript" src="Util/jScrollPane/jquery.mousewheel.js"></script>
<script type="text/javascript" src="Util/jScrollPane/jScrollPane.js"></script>
<script type="text/javascript" src="Util/js/function.js"></script>
<script type="text/javascript">
	function esqueciSenha()
	{
		$("#lightBox").attr("class", "windowEsqueciSenha");
		$("#boxEsqueciSenha").show();
		$("#boxCadastroFoto").hide();
		abrirLightBox();
	}
	
	function cadastroFoto()
	{
		$("#lightBox").attr("class", "windowCadastroFoto");
		$("#boxEsqueciSenha").hide();
		$("#boxCadastroFoto").show();
		$("#frameCadastroFoto").html('<iframe frameborder="0" width="600" height="430" scrolling="auto" src="pages/cadastro_foto.php"></iframe>');
		abrirLightBox();
		
	}
	
	<?php print $js; ?>
	
	$(function() {
		//$('.scroll01 .contentScroll').jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16});
	});
	
	//abre um treinamento
	function abrir( p_idTreinamento ) 
	{
		document.getElementById('modal').style.filter = "alpha('opacity=1.0')";
		document.getElementById('modal').style.opacity = "1.0";
		document.getElementById('modal').style.top = "50%";
		document.getElementById('modal').style.left = "50%";
		document.getElementById('transp').style.display = "block";
		
		idTreinamentoAtual = p_idTreinamento;
		$("#tituloTreinamento").html("Carregando Treinamento...");
		$("#divBtEsq").hide();
		$(".divFrameTreinamento").show();
		$(".divFrameTreinamento").html("<iframe id='frameTreinamento' name='frameTreinamento' width='650' height='410' frameborder='0'  scrolling='no'></iframe>");
		$(".divFrameQuiz").hide();
		$(".navegacaoTreinamento").show();
		$(".divLoadingQuiz").hide();
		$("#divBtDir").show();
		slideAtual = 1;
		
		$.post('pages/dadosTreinamento.php', { idTreinamento: p_idTreinamento }, function(data) {
			slides = data.split("|");
			$("#tituloTreinamento").html("iClass");
			$("#divPaginasSlides").html(slideAtual + " de " + slides.length);
			$("#frameTreinamento").attr("src",slides[slideAtual-1]);
			
			if( slideAtual == slides.length )
			{
				$.post('pages/existeQuiz.php', { idTreinamento: p_idTreinamento }, function(data) {
					if( data == "1" )
						$("#divPaginasSlides").html("<a href='#' onclick='abreQuiz()'>Faça o Quiz!</a>");
				});

				$("#divBtDir").hide();	
			}
		});
	}

	// CONTROLE DE SLIDES
	function proximoSlide()
	{
		if( slideAtual < slides.length )
		{
			slideAtual++;
			if( slideAtual == slides.length )
			{
				$.post('pages/existeQuiz.php', { idTreinamento: p_idTreinamento }, function(data) {
					if( data == "1" )
						$("#divPaginasSlides").html("<a href='#' onclick='abreQuiz()'>Faça o Quiz!</a>");
				});
				$("#divBtDir").hide();
			}
			else
			{
				$("#divPaginasSlides").html(slideAtual + " de " + slides.length);		
				$("#divBtEsq").show();
				$("#divBtDir").show();
			}
			$("#frameTreinamento").attr("src",slides[slideAtual-1]);
		}
	}
	
	function prevSlide()
	{
		if( slideAtual > 1 )
		{
			slideAtual--;
			if( slideAtual == 1 )
				$("#divBtEsq").hide();
			else
				$("#divBtEsq").show();
			$("#divPaginasSlides").html(slideAtual + " de " + slides.length);		
			$("#frameTreinamento").attr("src",slides[slideAtual-1]);
		}
		$("#divBtDir").show();
	}

</script>
<style type="text/css">
	.scroll01 .contentScroll {
		width: 750px;
		height: 450px;
	}
	
	.scroll01 .jScrollPaneTrack {
		background: url(Util/jScrollPane/images/osx_track.gif) repeat-y;
	}
	.scroll01 .jScrollPaneDrag {
		background: url(Util/jScrollPane/images/osx_drag_middle.gif) repeat-y;
	}
	.scroll01 .jScrollPaneDragTop {
		background: url(Util/jScrollPane/images/osx_drag_top.gif) no-repeat;
		height: 6px;
	}
	.scroll01 .jScrollPaneDragBottom {
		background: url(Util/jScrollPane/images/osx_drag_bottom.gif) no-repeat;
		height: 7px;
	}
	.scroll01 a.jScrollArrowUp {
		height: 24px;
		background: url(Util/jScrollPane/images/osx_arrow_up.png) no-repeat 0 -30px;
	}
	.scroll01 a.jScrollArrowUp:hover {
		background-position: 0 0;
	}
	.scroll01 a.jScrollArrowDown {
		height: 24px;
		background: url(Util/jScrollPane/images/osx_arrow_down.png) no-repeat 0 -30px;
	}
	.scroll01 a.jScrollArrowDown:hover {
		background-position: 0 0;
	}
	
	.left .jScrollPaneTrack {
		left: 0;
		right: auto;
	}
	.left a.jScrollArrowUp {
		left: 0;
		right: auto;
	}
	.left a.jScrollArrowDown {
		left: 0;
		right: auto;
	}
	
	/* IE SPECIFIC HACKED STYLES */
	* html .scroll01 .jScrollPaneDragBottom {
		bottom: -1px;
	}
	/* /IE SPECIFIC HACKED STYLES */
</style>
</head>

<body>
	<!-- CONTEUDO GERAL -->
	<div class="geral">
    	<!-- HEDER -->
        <div class="header">
        
        	<!-- LOGO -->
            <div class="logo" onclick="location.href='index.php?begin=1'">
            	<h1>I Class</h1>
            </div>
            <!-- /LOGO -->
            
            <!-- TEXTO HEADER -->
            <div class="slogan">
            	<h2>TREINAMENTO ONLINE PARA PROMOTORES, <br />VENDEDORES E GERENTES DE VAREJO.</h2>
            </div>
            <!-- /TEXTO HEADER -->
            
            <!-- SAUDAÇÃO -->
            <div class="saudacao">
            	<?php
				if( !$_SESSION['sess_idUsuarioSite'] )
				{
				?>
				<ul>
                	<li><a href="index.php?land=login">Login</a> |</li>
                    <li><a href="index.php?land=cadastro">Registre-se</a> |</li>
                    <li><a href="#" onclick="esqueciSenha();">Esqueceu sua senha?</a></li>
                </ul>
				<?php
				}
				else
				{
				?>
				<ul>
					<li>Olá, seja bem vindo <strong><?php print $_SESSION['sess_nomeUsuarioSite']; ?></strong>. Se não é você, <a href="index.php?logout=1">clique aqui.</a>
					</li>
				</ul>
				<?php
				}
				?>
            </div>

            <!-- /SAUDAÇÃO -->
            
            <!-- MENU -->
            <div class="menu">
            	<?php 
				if( $_SESSION['sess_idUsuarioSite'] )
				{
				?>
                <div class="texto_menu">
                	<ul>
                    <!-- <li><a href="index.php?land=fabricantes&begin=1">Fabricantes</a></li> -->
                    <li><a href="index.php?land=treinamentos_det&idFabricante=13">Treinamentos</a></li>
                    <li><a href="index.php?land=premios">Prêmios</a></li>
                    <li><a href="http://kaspersky-cyberstat.com/br/" target="_blank">Estatísticas em tempo real</a></li>
                    <li><a href="index.php?land=ajuda">Ajuda</a></li>
                </ul></div>
                <?php
				}
				else
				{
				?>
                <div class="texto_menu"><ul>
                    <li><a href="index.php?land=oquee">O que é?</a></li>
                    <li><a href="index.php?land=comofunciona">Como Funciona</a></li>
                    <!--<li><a href="index.php?land=cases">Cases</a></li>-->
                    <!-- <li><a href="index.php?land=anuncie">Anuncie</a></li> -->
                    <li><a href="index.php?land=faleconosco">Fale Conosco</a></li>
                </ul></div>
                <?php	
				}
				?>
            </div>
            <!-- /MENU -->
            
            <!-- BREANDCRUMB -->
            <div class="breandcrumb">
            	<div class="texto_breandcrumb">
                  <ul>
                      <li><a href="index.php?begin=1">Inicio ></a></li>
                      <?php print $breadcrumb; ?>
                  </ul>
               </div>
            </div>
            <!-- /BREANDCRUMB -->
            
        </div>
        <!-- /HEDER -->
        
        <!-- CONTEUDO -->
        <div class="conteudo">
        	<div class="borda_sup"></div>
            <div class="msgErro"><?php print $_SESSION['msg']; $_SESSION['msg'] = ""; ?></div>
            <?php require_once $page; ?>
        	<div class="borda_inf"></div>
        </div><!-- /CONTEUDO -->
        
        <!-- RODAPÉ -->
        <div class="rodape">
        
            <!-- LOGO RODAPÉ -->
            <div class="logo_rodape">
            	<h1>Itailers</h1>
            </div>
            <!-- /LOGO RODAPÉ -->
            
            <!-- TEXTO RODAPE -->
            <div class="texto_rodape">
            	<p>O <strong>iClass</strong> é uma solução de treinamento online criada, desenvolvida e gerenciada por <br />
<strong>Itailers Trade Marketing</strong>, todos os direitos reservados. Caso queira saber mais, entre em <br />
 contato pelo telefone <span class="destaque_fonte">(11) 2359-2422</span>, ou por e-mail em <span class="destaque_fonte">iclass@itailers.com.br</span>.</p>
            </div>
            <!-- /TEXTO RODAPE -->
        	<div class="clear"></div>    
        </div>
        <!-- /RODAPÉ -->
        <div class="clear"></div>
    </div>
    <!-- /CONTEUDO GERAL -->

<!--LIGHTBOX -->                    
<div id="lightBox">
	<div style="position: relative; display: none;" id="boxEsqueciSenha">
		<div class="boxEsqueciSenha">
			<div style="position: relative;">
				<div style="border: 1px solid #000000; background-color: #FFFFFF; color: #990000; font-weight: bold; width: 20px; height: 20px; right: 0px; position: absolute; cursor: pointer; font-size: 12px; text-align: center;" onclick="fechaLightBox('windowEsqueciSenha');">X</div>
			</div>
			<form action="index.php" method="post">
            	<input type="hidden" name="esqueci_senha" value="1" />
				<div style="width: 400px; padding-bottom: 7px; background:url(Util/img/breandcrumb.png) repeat-x; font-weight: bold; text-align: center; background-color: #034db3; color: #FFFFFF;">ESQUECEU SUA SENHA?</div>
				<div style="padding-top: 20px; text-align: center;">Preencha seu e-mail abaixo e uma mensagem lhe será enviada com o lembrete de sua senha.</div>
				<div style="text-align: center; padding-top: 5px; padding-bottom: 10px;">E-mail: <input type="text" name="txtEmailLembrete" size="20" maxlength="100" class="inputText" /></div>
				<div style="text-align: center; padding-bottom: 10px;"><input type="image" src="Util/img/bt_enviar.jpg" /></div>
			</form>
		</div>
	</div>
    <div style="position: relative; display: none;" id="boxCadastroFoto">
		<div class="boxCadastroFoto">
			<div style="position: relative;">
				<div style="border: 1px solid #000000; background-color: #FFFFFF; color: #990000; font-weight: bold; width: 20px; height: 20px; right: 0px; position: absolute; cursor: pointer; font-size: 12px; text-align: center;" onclick="fechaLightBox('windowCadastroFoto'); location.reload();">X</div>
			</div>
            <div style="width: 600px; padding-bottom: 7px; background:url(Util/img/breandcrumb.png) repeat-x; font-weight: bold; text-align: center; background-color: #034db3; color: #FFFFFF;">ATUALIZAÇÃO DE FOTO</div>
            <div id="frameCadastroFoto">
            	<iframe frameborder="0" width="600" height="430" scrolling="auto" src="pages/cadastro_foto.php"></iframe>
            </div>
		</div>
	</div>
</div>
<div id="mask"></div>
<!--//LIGHTBOX -->

<!-- /Modal -->
<div id="transp"></div>
<div id="modal">
<form id="formTreinamento" name="formTreinamento" method="post">
    <div class="contentScroll">
        <div class="produto">
            <div>
            	<div class="floatRight" id="tituloTreinamento"></div>
                <div class="floatRight" id="tempoQuestao"></div>
                <div class="floatRight"><a href="javascript:fechar();" class="fechar"><b>fechar</b></a></div>
                <div class="clear"></div>
            </div>
            <div class="divFrameTreinamento"><iframe id="frameTreinamento" name='frameTreinamento' width="650" height="410" frameborder='0'  scrolling="no"></iframe></div>
            <div class="divFrameQuiz">
            	<div class="divLoadingQuiz">Carregando...<br /><img src="Util/img/loader_quiz.gif" border="0" /></div>
            </div>
        </div>
        <div class="navegacaoTreinamento">
        	<div class="floatRight" style="padding-right: 10px;" id="divBtEsq"><img src="Util/img/esq.gif" border="0" style='cursor: pointer;' onclick='prevSlide();' /></div>
            <div class="floatRight" id="divPaginasSlides"></div>
            <div class="floatRight" style="padding-left: 10px;" id="divBtDir"><img src="Util/img/dir.gif" border="0" style='cursor: pointer;' onclick='proximoSlide();' /></div>
            <div class="clear"></div>
        </div>
        <div class="navegacaoQuiz" style="display: none;">
            <div class="floatRight" id="divNumeroPerguntas"></div>
            <div class="floatRight" style="padding-left: 10px;" id="divBtDirQuiz"><img src="Util/img/dir.gif" border="0" style='cursor: pointer;' onclick='proximaQuestao();' /></div>
            <div class="clear"></div>
        </div>
    </div>
</form>  
</div>

<!-- /Modal -->

</body>
</html>
