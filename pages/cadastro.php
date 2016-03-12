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
				//se n�o conseguir incluir, volta pra mesma p�gina
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
//verifica se os dados do formul�rio est�o ok
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
						label.html('Chave de acesso j� est� sendo usada por outro usu�rio');
					}else {
						label.css('display', 'block');
						label.css('color', '#006400');
						label.html('Chave de acesso permitida');
					}
				}else {
					label.css('display', 'block');
					label.css('color', 'red');
					label.html('Chave de acesso n�o existe');
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
	    Voc� poder� acessar o sistema assim que o mesmo for liberado por nossa equipe.</p>
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
      </span>Os campos com * s�o de preenchimento obrigat�rio.</p>

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
    
  
    <label>Endere�o:&nbsp;&nbsp;</label>
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
	<label id="labeChave" style="color: red; font-family: bold; font-size: 12px;display: none">Chave de Acesso Obrigat�ria</label>
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
TERMOS E CONDI��ES DE USO

1 � OBJETO. O presente instrumento de Termos & Condi��es de Uso cont�m as cl�usulas e condi��es que regem a utiliza��o, pelo Usu�rio, do sistema de cursos e treinamentos eletr�nicos denominado "IClass" (o "Sistema"), de propriedade da ITAILERS GEST�O EMPRESARIAL E MARKETING LTDA. ("Itailers Trade Marketing"), bem como a utiliza��o e processamento dos dados e informa��es fornecidas pelo respectivo Usu�rio durante seu cadastro no Sistema.

2 � CURSOS E TREINAMENTOS ELETR�NICOS. Uma vez realizado corretamente o cadastro pelo Usu�rio, este poder� acessar a �rea de cursos e treinamentos eletr�nicos disponibilizados pelo Sistema gratuitamente, mediante acesso ao site "http://www.itailers.com.br/iclass" com o Nome de Usu�rio e Senha de Acesso que lhe ser�o fornecidos no momento de seu cadastro.


3 � CADASTRO. O Usu�rio dever� preencher o cadastro com informa��es exatas, precisas e verdadeiras, obrigando-se a atualizar seus dados sempre que neles ocorrer alguma altera��o. O Usu�rio responde pela exatid�o, precis�o e veracidade de todas as informa��es fornecidas durante seu cadastro. A qualquer momento poder�o ser requeridos do Usu�rio documentos que comprovem os dados por ele fornecidos em seu cadastro.


4 � NOME DE USU�RIO E SENHA DE ACESSO. Uma vez realizado o cadastro, o Usu�rio receber� um "Nome de Usu�rio" (login) e escolher� uma "Senha de Acesso", de conhecimento exclusivamente seu. O Usu�rio obriga-se a manter sua Senha de Acesso sob estrito sigilo e confidencialidade e a n�o revel�-la a qualquer terceiro. O Usu�rio � respons�vel pelo sigilo e confidencialidade de sua Senha de Acesso, respondendo por qualquer uso indevido do Sistema que for realizado com seu Nome de Usu�rio e Senha de Acesso.


5 � USO N�O AUTORIZADO. O Usu�rio compromete-se a notificar a Itailers Trade Marketing imediatamente, por meio da �rea "Suporte", localizada na parte superior do site de Internet do Sistema, sempre que tiver conhecimento de qualquer uso n�o autorizado ao Sistema por seu Nome de Usu�rio, bem como o acesso n�o autorizado por terceiros � sua Senha de Acesso. 


6 � PROIBI��O DE USO DE TERCEIROS. Em nenhuma hip�tese o Usu�rio poder� facultar o acesso ao Sistema a quaisquer terceiros por meio de seu Nome de Usu�rio ou Senha de Acesso, sendo expressamente proibido seu empr�stimo, divulga��o, venda, loca��o, cess�o ou qualquer forma de transfer�ncia de seu Nome de Usu�rio ou Senha de Acesso a qualquer terceiro que n�o o pr�prio respectivo Usu�rio.


7 � REPRODU��O DE INFORMA��ES. Nenhuma das informa��es contidas ou utilizadas no Sistema, tais como, mas n�o se limitando a, textos, v�deos, imagens, sons ou qualquer outra, poder�o ser gravadas, reproduzidas, reapresentadas, disseminadas, armazenadas, cedidas ou transmitidas a qualquer terceiro, no todo ou em parte, para quaisquer fins, devendo tais informa��es serem utilizadas unicamente pelo Usu�rio, mediante os meios disponibilizados e durante seu acesso ao Sistema.


8 � TREINAMENTO FACULTATIVO E GRATUITO. O Usu�rio n�o � obrigado a utilizar o Sistema ou efetuar os cursos e treinamentos eletr�nicos disponibilizados no Sistema, tratando-se de mera faculdade sua. Os cursos e treinamentos eletr�nicos contidos no Sistema ser�o disponibilizados ao Usu�rio sem que lhe seja cobrado qualquer valor para tanto. O Usu�rio reconhece que seu cadastro e acesso ao Sistema � facultativo e que a realiza��o de qualquer curso ou treinamento pelo Usu�rio atrav�s do Sistema ocorre mediante livre e espont�nea vontade do Usu�rio.


9 � ACESSO E DISPONIBILIDADE DO SISTEMA. O Usu�rio poder� utilizar o Sistema mediante acesso ao site de Internet "http://www.itailers.com.br/iclass", com seu Nome de Usu�rio e Senha de Acesso. O Sistema poder� ser utilizado pelo Usu�rio a qualquer momento, em qualquer lugar, mediante computador com acesso � Internet. Caso o Sistema ou seu site de Internet estiver indispon�vel, o Usu�rio poder� notificar a Itailers Trade Marketing mediante e-mail para a "iclass@itailers.com.br". Por�m, de antem�o, a Itailers Trade Marketing n�o assegura ao Usu�rio qualquer n�vel de disponibilidade m�nima do Sistema ou de seu site de Internet.


9.1 � LINKS EXTERNOS. O portal poder� ter acesso a links para outros sites externos cujos conte�dos e pol�ticas de privacidade n�o s�o de responsabilidade da Itailers Trade Marketing. Assim, recomendamos que, ao serem redirecionados para sites externos, os usu�rios consultem sempre as respectivas pol�ticas de privacidade antes de fornecerem seus dados ou informa��es.

9.2 � USO DE COOKIES. Este site pode utilizar cookies e/ou web beacons quando um usu�rio tem acesso �s p�ginas. Os cookies que podem ser utilizados associam-se (se for o caso) unicamente com o navegador de um determinado computador. Os cookies que s�o utilizados neste site podem ser instalados pelo mesmo, os quais s�o originados dos distintos servidores operados por este, ou a partir dos servidores de terceiros que prestam servi�os e instalam cookies e/ou web beacons (por exemplo, os cookies que s�o empregados para prover servi�os de publicidade ou certos conte�dos atrav�s dos quais o usu�rio visualiza a publicidade ou conte�dos em tempo pr� determinados). O usu�rio poder� pesquisar o disco r�gido de seu computador conforme instru��es do pr�prio navegador. O usu�rio tem a possibilidade de configurar seu navegador para ser avisado, na tela do computador, sobre a recep��o dos cookies e para impedir a sua instala��o no disco r�gido. As informa��es pertinentes a esta configura��o est�o dispon�veis em instru��es e manuais do pr�prio navegador.
</textarea><br />
    
<div class="termos_de_uso"><p><input name="chkTermos" type="checkbox" value="1" <?php print ($_SESSION['sess_idUsuarioSite'] ? "checked" : ""); ?> />* Li e estou de acordo com os termos de uso e regras do programa.</p></div>
		<p><div class="botao_cadastre-se"><input type="submit" src="<?php print (!$_SESSION['sess_idUsuarioSite'] ? "Util/img/bt_registrar.jpg" : "Util/img/bt_atualizar.jpg"); ?>" alt="Registrar" value="Enviar" /><a href="index.php"><button style="line-height: normal">Voltar</button></a></div></p>
</div>
  
</form>