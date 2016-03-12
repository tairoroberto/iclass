<?php
	require_once "inc/class/imagem.php";
	require_once "inc/class/loja.php";
	require_once "inc/class/categoria.php";
	require_once "inc/class/chaveacesso.php";

	$loja = new loja();
	$categoria = new categoria();
	$chaves = new chaveacesso();

	if( $_REQUEST['subaction'] == "delImg" )
		$usuario_site->delImgUsuarioSite($_SESSION['sess_idUsuarioSite'], 1);

	$js = "";
	//incluindo
	if( $_SERVER['REQUEST_METHOD'] == "POST" )
	{
		if( !$_REQUEST['uploadImage'] && !trim($_FILES['image']['name']) )
		{
			if( !$_SESSION['sess_idUsuarioSite'] )
			{
				//se não conseguir incluir, volta pra mesma página
				if( $usuario_site->insertUsuarioSite(1) )
				{
					$js = "alert('".$_SESSION['msg']."'); location.href = 'index.php';";
				}
				else
				{
					$js = "alert('".$_SESSION['msg']."');";
				}
			}
			else
			{
				if( $usuario_site->alterUsuarioSite(1) )
				{
					$js = "alert('".$_SESSION['msg']."');  location.href = 'index.php';";
				}
				else
				{
					$js = "alert('".$_SESSION['msg']."');";	
				}
			}
		}
		$_SESSION['msg'] = "";
	}

	$disabledLoja = "";
	if( $_SESSION['sess_idUsuarioSite'] )
	{
		$linhaReg		= $usuario_site->getOneUsuarioSite($_SESSION['sess_idUsuarioSite']);
		$objEstado 		= $usuario_site->getEstadoPeloMunicipio($linhaReg->idCidade);
		$jsComboCidade 	= "changeEstado('".$objEstado->id."','".$linhaReg->idCidade."');";
		$objCategoria	= $loja->getCategoriaPelaLoja($linhaReg->idLoja);
		$disabledLoja = "disabled";
		//$jsComboCategoria 	= " changeCategoria('".$objCategoria->idCategoria."','".$linhaReg->idLoja."'); ";
	}
	

	if( $_POST['idEstado'] )
	{
		$objEstado 		= $usuario_site->getEstadoPeloMunicipio($_POST['idCidade']);
		$jsComboCidade 	= "changeEstado('".$_POST['idEstado']."','".$_POST['idCidade']."');";	
	}
	
	if( $_POST['idCategoria'] )
	{
		$objCategoria	= $loja->getCategoriaPelaLoja($_POST["idLoja"]);
		//$jsComboCategoria 	= "changeCategoria('".$_POST['idCategoria']."','".$_POST['idLoja']."');";
	}
	
	function loadImage( $img )
	{
		if( file_exists(PATH_IMG_USUARIO_SITE_SITE .$img) && ($img != ".") )
			print PATH_IMG_USUARIO_SITE_SITE . $img;
		else
			print "Util/img/default.gif";
	}

	//montando combo de estados
	$estados = $usuario_site->allEstados();
	$comboEstados = "<select name='idEstado' class='combo' onchange='changeEstado(this.value, \"".$_REQUEST['idCidade']."\")'>
						<option value=''>-- Selecione --</option>";
	for( $i=0; $i<count($estados); $i++ )
	{
		if( $objEstado->id == $estados[$i]->id )
			$selected = "selected";
		else
			$selected = "";
			
		$comboEstados .= "<option value='". $estados[$i]->id ."' ". $selected .">". $estados[$i]->nome ."</option>";
	}
	$comboEstados .= "</select>";

	//montando combo de categorias
	$categorias = $categoria->allCategorias();
	while($linha = $db->fetchObject($categorias))
		$arrCategorias[] = $linha;
		
	//$comboCategorias = "<select name='idCategoria' class='combo' onchange='changeCategoria(this.value, \"".$_REQUEST['idLoja']."\")'><option value=''>-- Selecione --</option>";
	$comboCategorias = "<select name='idCategoria' ".$disabledLoja." class='combo'><option value=''>-- Selecione --</option>";
	for( $i=0; $i<count($arrCategorias); $i++ )
	{
		if( $objCategoria->idCategoria == $arrCategorias[$i]->idCategoria )
			$selected = "selected";
		else
			$selected = "";
			
		$comboCategorias .= "<option value='". $arrCategorias[$i]->idCategoria ."' ". $selected .">". $arrCategorias[$i]->nome ."</option>";
	}
	$comboCategorias .= "</select>";

	//montando combo de lojas
	/*$lojas = $loja->allLojas();
	while($linha = $db->fetchObject($lojas))
		$arrLojas[] = $linha;
		
	$comboLojas = "<select name='idLoja' class='combo'><option value=''>-- Selecione --</option>";
	for( $i=0; $i<count($arrLojas); $i++ )
	{
		if( $linhaReg->idLoja == $arrLojas[$i]->idLoja )
			$selected = "selected";
		else
			$selected = "";
			
		$comboLojas .= "<option value='". $arrLojas[$i]->idLoja ."' ". $selected .">". $arrLojas[$i]->nome ."</option>";
	}
	$comboLojas .= "</select>";*/

?>
<script type="text/javascript">
/**/
//verifica se os dados do formulário estão ok
function submitForm()
{
	/*var form = document.forms['formCadastro'];
	var erro = new Array();
	
	if( !trim(form.nome.value) )
		erro.push("Nome");
	if( !trim(form.email.value) )
		erro.push("E-mail");
	if( !trim(form.dtNascimento.value) )
		erro.push("Data de Nascimento");


	if( erro.length > 0 )
	{
		alert("Preencha os seguintes campos corretamente: " + erro.join(', '));
		return false;
	}
	else*/
		return true;
}

function changeEstado( p_idEstado, p_idCidade )
{
	$("#lblMunicipio").html("Carregando Cidades...");
	
	$.post('pages/comboCidades.php', { idCidade: p_idCidade, idEstado: p_idEstado }, function(data) {
	  $('#lblMunicipio').html(data);
	});

}

function changeCategoria( p_idCategoria, p_idLoja )
{
	$("#lblLojas").html("Carregando Lojas...");
	
	$.post('pages/comboLojas.php', { idLoja: p_idLoja, idCategoria: p_idCategoria }, function(data) {
	  $('#lblLojas').html(data);
	});

}


$(document).ready(function() { 
<?php
print $jsComboCidade;
print $jsComboCategoria;
print $js;
?>
});

function delImg(id, numImg)
{
	location.href = "index.php?land=cadastro&numImg=" + numImg + "&subaction=delImg";
}


function validarChaveAcesso(){
	var chave = $('#chave_acesso').val();
	var action = $('#action_chave').val();
	var label = $('#labeChave');

	if(chave != ''){
		$.ajax({
			url: 'admin/chaves_acesso/validar_chaves.php',
			data: "valor_valor=" + chave + "&action=" + action,
			type: "POST",
			success: function(json) {
				if(json != 'inexistente'){
					json = JSON.parse(json);
					if(json.ativa == 1){
						label.css('display', 'block');
						label.css('color', 'red');
						label.html('Chave de acesso já está sendo usada por outro usuário');
					}else {
						label.css('display', 'block');
						label.css('color', '#006400');
						label.html('Chave de acesso permitida');
					}
				}else {
					label.css('display', 'block');
					label.css('color', 'red');
					label.html('Chave de acesso não existe');
				}
			}
		});
	}
}
</script>

<form id="formCadastro"  name="formCadastro" method="POST" onsubmit="return submitForm();" enctype="multipart/form-data">
	<input type="hidden" value="<?php print $_SESSION['sess_idUsuarioSite']; ?>" name="idUsuarioSite" />
	
    	<?php 
		if( !$_SESSION['sess_idUsuarioSite'] )
		{
		?>
        <p><strong>Seja bem vindo ao iClass</strong></p>
  <p>Preencha os campos abaixo e clique em <b>Enviar</b> para realizar seu cadastro.<br />
	    Você poderá acessar o sistema assim que o mesmo for liberado por nossa equipe.</p>
		<?php
		}
		else
		{
		?>
      <p><strong>Atualize seu cadastro<br />
      </strong>Preencha os campos abaixo e clique em <b>Enviar</b> para atualizar seu cadastro.</p>
        <?php 
		}
		?>
      <p><span class="destaque_fonte">DADOS PESSOAIS<br />
      </span>Os campos com * são de preenchimento obrigatório.</p>

<div class="formulario">
     
    <!-- <label>* Loja/Filial:&nbsp;&nbsp;</label> -->
    <p><label>* Revenda:&nbsp;&nbsp;</label>
    <label id="lblLojas" name="divLojas" style>
    	<input type="text" name="nomeLoja" id="nomeLoja" maxlength="100" <?php print $disabledLoja; ?> size="40" value="<?=htmlentities($linhaReg->nomeLoja, ENT_QUOTES)?><?=htmlentities($_POST['nomeLoja'], ENT_QUOTES)?>" />
    </label>
    <!-- <label>* Varejista/Distribuidor:&nbsp;&nbsp;</label> -->
      <label>* Tipo:&nbsp;&nbsp;</label>
	  <?php print $comboCategorias; ?> <br />
 
      <label>* Nome Completo:&nbsp;&nbsp;</label>
	  <input type="text" style='text-align: left;' size="50" name="nome" id="nome" maxlength="100" class="inputText" value="<?=htmlentities($linhaReg->nome, ENT_QUOTES)?><?=htmlentities($_POST['nome'], ENT_QUOTES)?>" />
	  <label class="cpf">* CPF:&nbsp;&nbsp;</label>
	  <input type="text" class="inputText" <?php print ($_SESSION['sess_idUsuarioSite'] ? "disabled" : ""); ?> name="cpf" MaxLength="14" size="20" onKeyDown="return noLetters(event);" onKeyUp="formataCPF(this);" value="<?=htmlentities($linhaReg->cpf, ENT_QUOTES)?><?=htmlentities($_POST['cpf'], ENT_QUOTES)?>" /><br />
      
    <label>Cargo:&nbsp;&nbsp;</label>
    <input type="radio" name="cargo" value="Vendedor" <?php if($_POST['cargo'] == "Vendedor" || $linhaReg->cargo == "Vendedor" || (!$_POST['cargo'] && !$linhaReg->cargo)) print "checked"; ?> />
    <label>Vendedor&nbsp;&nbsp;&nbsp;&nbsp;</label>
    
    <input type="radio" name="cargo" value="Gerente" <?php if($_POST['cargo'] == "Gerente" || $linhaReg->cargo == "Gerente") print "checked"; ?> />
    <label>Gerente&nbsp;&nbsp;&nbsp;&nbsp;</label>
    
    <input type="radio" name="cargo" value="Promotor" <?php if($_POST['cargo'] == "Promotor" || $linhaReg->cargo == "Promotor") print "checked"; ?> />
    <label>Promotor&nbsp;&nbsp;&nbsp;&nbsp;</label><br />
    
  
    <label>Endereço:&nbsp;&nbsp;</label>
    <input type="text" style='text-align: left;' name="endereco" id="endereco" maxlength="100" size="45" class="inputText" value="<?=htmlentities($linhaReg->endereco, ENT_QUOTES)?><?=htmlentities($_POST['endereco'], ENT_QUOTES)?>" /><br />
    
    <label>CEP:&nbsp;&nbsp;</label>
    <input type="text" name="cep" id="cep" onKeyDown="return noLetters(event);" onKeyUp="formataCEP(this);" style='text-align: left;' maxlength="9" size="20" class="inputText" value="<?=htmlentities($linhaReg->cep, ENT_QUOTES)?><?=htmlentities($_POST['cep'], ENT_QUOTES)?>" />
    
    <label>* UF:&nbsp;&nbsp;</label>
    <?php print $comboEstados; ?>&nbsp;&nbsp;
    
    <label>* Cidade:&nbsp;&nbsp;</label>
    <label id="lblMunicipio" name="lblMunicipio"></label><br />
    
    <label>Data de nasc.:</label>
    <input type="text" name="dtNascimento" id="dtNascimento" style='text-align: left;' class="inputText" onkeydown="return noLetters(event);" onkeyup="formataData(this);" maxlength="10" size="12" value="<?=(trim($linhaReg->dataNascto) ? formataDataSql(substr($linhaReg->dataNascto, 0, 10)) : "")?><?=$_POST['dtNascimento']?>" />
    
    <label>Telefone:&nbsp;&nbsp;</label>
    <input type="text" name="telefone" id="telefone" style='text-align: left;' maxlength="40" size="30" class="inputText" value="<?=htmlentities($linhaReg->telefone, ENT_QUOTES)?><?=htmlentities($_POST['telefone'], ENT_QUOTES)?>" /><br />

	<label>* Chave de acesso:&nbsp;&nbsp;</label>
	<input type="text" name="chave_acesso" id="chave_acesso"
           onblur="validarChaveAcesso();" style='text-align: left;' maxlength="10" size="15" class="inputText"
           <?php if(isset($linhaReg->valor_chave))print 'disabled';?>
           value="<?=htmlentities($linhaReg->valor_chave, ENT_QUOTES)?><?=htmlentities($_POST['valor_chave'], ENT_QUOTES)?>" />
	<label id="labeChave" style="color: red; font-family: bold; font-size: 12px;display: none">Chave de Acesso Obrigatória</label>
	<input type="hidden" name="action_chave" id="action_chave" value="validar_chave"><br />

    <label>* E-mail:&nbsp;&nbsp;</label>
    <input type="text" name="email" id="email" style='text-align: left;' <?php print ($_SESSION['sess_idUsuarioSite'] ? "disabled" : ""); ?> maxlength="300" size="30" class="inputText" value="<?=htmlentities($linhaReg->email, ENT_QUOTES)?><?=htmlentities($_POST['email'], ENT_QUOTES)?>" /><br />
    
    <label>* Senha:&nbsp;&nbsp;</label>
    <input type="password" name="senha" id="senha" style='text-align: left;' maxlength="12" size="15" class="inputText" /> <b><span style="color: #666;">(m&iacute;nimo 6 d&iacute;gitos)</span></b>&nbsp;&nbsp;&nbsp;
    
    <label>* Repita sua senha:&nbsp;&nbsp;</label>
    <input type="password" name="confirmacaoSenha" id="confirmacaoSenha" style='text-align: left;' maxlength="12" size="15" class="inputText" /><br />
    <!--
        <label>Sua foto (100px de largura por 150px de altura):</label>
        <br />
        <label><img id="imagem1" src="<?=loadImage($_SESSION['sess_idUsuarioSite']."_1.".$linhaReg->extImg)?>" border='0' style='width: 100px; height: 100px;' /><br /></label>
        <?=(file_exists(PATH_IMG_USUARIO_SITE_SITE .$_SESSION['sess_idUsuarioSite']."_1.".$linhaReg->extImg) && ($_SESSION['sess_idUsuarioSite'].".".$linhaReg->extImg1 != ".")  ? "<label><div style='height: 6px;'></div><img src='Util/img/bt_excluir_imagem.jpg' onclick='delImg(\"".$_SESSION['sess_idUsuarioSite']."\", \"1\")' name='btExcluiImg1' alt='Excluir Imagem de Perfil' style='cursor: pointer;' /><div style='height: 6px;'></div></label>" : "")?>
		<br />
        <label><input type="file" id="imgUsuarioSite1" name='imgUsuarioSite1' class="inputText" /></label>-->
    <br />
    <textarea class="textAreaCadastro inputText" style="font-family:'Courier New', Courier, monospace; font-size:12px;">
TERMOS E CONDIÇÕES DE USO

1 – OBJETO. O presente instrumento de Termos & Condições de Uso contém as cláusulas e condições que regem a utilização, pelo Usuário, do sistema de cursos e treinamentos eletrônicos denominado "IClass" (o "Sistema"), de propriedade da ITAILERS GESTÃO EMPRESARIAL E MARKETING LTDA. ("Itailers Trade Marketing"), bem como a utilização e processamento dos dados e informações fornecidas pelo respectivo Usuário durante seu cadastro no Sistema.

2 – CURSOS E TREINAMENTOS ELETRÔNICOS. Uma vez realizado corretamente o cadastro pelo Usuário, este poderá acessar a área de cursos e treinamentos eletrônicos disponibilizados pelo Sistema gratuitamente, mediante acesso ao site "http://www.itailers.com.br/iclass" com o Nome de Usuário e Senha de Acesso que lhe serão fornecidos no momento de seu cadastro.


3 – CADASTRO. O Usuário deverá preencher o cadastro com informações exatas, precisas e verdadeiras, obrigando-se a atualizar seus dados sempre que neles ocorrer alguma alteração. O Usuário responde pela exatidão, precisão e veracidade de todas as informações fornecidas durante seu cadastro. A qualquer momento poderão ser requeridos do Usuário documentos que comprovem os dados por ele fornecidos em seu cadastro.


4 – NOME DE USUÁRIO E SENHA DE ACESSO. Uma vez realizado o cadastro, o Usuário receberá um "Nome de Usuário" (login) e escolherá uma "Senha de Acesso", de conhecimento exclusivamente seu. O Usuário obriga-se a manter sua Senha de Acesso sob estrito sigilo e confidencialidade e a não revelá-la a qualquer terceiro. O Usuário é responsável pelo sigilo e confidencialidade de sua Senha de Acesso, respondendo por qualquer uso indevido do Sistema que for realizado com seu Nome de Usuário e Senha de Acesso.


5 – USO NÃO AUTORIZADO. O Usuário compromete-se a notificar a Itailers Trade Marketing imediatamente, por meio da área "Suporte", localizada na parte superior do site de Internet do Sistema, sempre que tiver conhecimento de qualquer uso não autorizado ao Sistema por seu Nome de Usuário, bem como o acesso não autorizado por terceiros à sua Senha de Acesso. 


6 – PROIBIÇÃO DE USO DE TERCEIROS. Em nenhuma hipótese o Usuário poderá facultar o acesso ao Sistema a quaisquer terceiros por meio de seu Nome de Usuário ou Senha de Acesso, sendo expressamente proibido seu empréstimo, divulgação, venda, locação, cessão ou qualquer forma de transferência de seu Nome de Usuário ou Senha de Acesso a qualquer terceiro que não o próprio respectivo Usuário.


7 – REPRODUÇÃO DE INFORMAÇÕES. Nenhuma das informações contidas ou utilizadas no Sistema, tais como, mas não se limitando a, textos, vídeos, imagens, sons ou qualquer outra, poderão ser gravadas, reproduzidas, reapresentadas, disseminadas, armazenadas, cedidas ou transmitidas a qualquer terceiro, no todo ou em parte, para quaisquer fins, devendo tais informações serem utilizadas unicamente pelo Usuário, mediante os meios disponibilizados e durante seu acesso ao Sistema.


8 – TREINAMENTO FACULTATIVO E GRATUITO. O Usuário não é obrigado a utilizar o Sistema ou efetuar os cursos e treinamentos eletrônicos disponibilizados no Sistema, tratando-se de mera faculdade sua. Os cursos e treinamentos eletrônicos contidos no Sistema serão disponibilizados ao Usuário sem que lhe seja cobrado qualquer valor para tanto. O Usuário reconhece que seu cadastro e acesso ao Sistema é facultativo e que a realização de qualquer curso ou treinamento pelo Usuário através do Sistema ocorre mediante livre e espontânea vontade do Usuário.


9 – ACESSO E DISPONIBILIDADE DO SISTEMA. O Usuário poderá utilizar o Sistema mediante acesso ao site de Internet "http://www.itailers.com.br/iclass", com seu Nome de Usuário e Senha de Acesso. O Sistema poderá ser utilizado pelo Usuário a qualquer momento, em qualquer lugar, mediante computador com acesso à Internet. Caso o Sistema ou seu site de Internet estiver indisponível, o Usuário poderá notificar a Itailers Trade Marketing mediante e-mail para a "iclass@itailers.com.br". Porém, de antemão, a Itailers Trade Marketing não assegura ao Usuário qualquer nível de disponibilidade mínima do Sistema ou de seu site de Internet.


9.1 – LINKS EXTERNOS. O portal poderá ter acesso a links para outros sites externos cujos conteúdos e políticas de privacidade não são de responsabilidade da Itailers Trade Marketing. Assim, recomendamos que, ao serem redirecionados para sites externos, os usuários consultem sempre as respectivas políticas de privacidade antes de fornecerem seus dados ou informações.

9.2 – USO DE COOKIES. Este site pode utilizar cookies e/ou web beacons quando um usuário tem acesso às páginas. Os cookies que podem ser utilizados associam-se (se for o caso) unicamente com o navegador de um determinado computador. Os cookies que são utilizados neste site podem ser instalados pelo mesmo, os quais são originados dos distintos servidores operados por este, ou a partir dos servidores de terceiros que prestam serviços e instalam cookies e/ou web beacons (por exemplo, os cookies que são empregados para prover serviços de publicidade ou certos conteúdos através dos quais o usuário visualiza a publicidade ou conteúdos em tempo pré determinados). O usuário poderá pesquisar o disco rígido de seu computador conforme instruções do próprio navegador. O usuário tem a possibilidade de configurar seu navegador para ser avisado, na tela do computador, sobre a recepção dos cookies e para impedir a sua instalação no disco rígido. As informações pertinentes a esta configuração estão disponíveis em instruções e manuais do próprio navegador.
</textarea><br />
    
<div class="termos_de_uso"><p><input name="chkTermos" type="checkbox" value="1" <?php print ($_SESSION['sess_idUsuarioSite'] ? "checked" : ""); ?> />* Li e estou de acordo com os termos de uso e regras do programa.</p></div>
		<p><div class="botao_cadastre-se"><input type="submit" src="<?php print (!$_SESSION['sess_idUsuarioSite'] ? "Util/img/bt_registrar.jpg" : "Util/img/bt_atualizar.jpg"); ?>" alt="Registrar" value="Enviar" /><a href="index.php"><button style="line-height: normal">Voltar</button></a></div></p>
</div>
  
</form>