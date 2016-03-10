<?
//trata uma variável para ser posta no sql
function trataVarSql( $var )
{
	//verifica se magic_quotes está ativado
	if( !get_magic_quotes_gpc() )
		$str = mysql_real_escape_string( $var );
	else
		$str = $var;
		
	//retirando #
	$str = str_replace("#", "\#", $str);
	
	return $str;
}

function validaLogin()
{
	//se não existir a sessão do id do usuário e a sessId, então já não está logado.
	if( !isset($_SESSION['sess_idUsuario']) || !isset($_SESSION['sess_sessId']) )
		return 0;
	else
		return 1;
}

function validaLoginSite()
{
	//$db = new dataBase();
	//já setada a variável $db no config.inc.php, faz-se a comparação aqui com a sessão criada e atualiza uma nova sessão na base; 
	
	//se não existir a sessão do id do usuário e a sessId, então já não está logado.
	if( !$_SESSION['sess_idUsuarioSite'] )
		return 0;
	else
		return 1;
}
//corta um texto com o limite desejado e coloca 3 pontinhos ao final do mesmo.
function cortaTexto( $texto, $l = 30 )
{
	if( strlen($texto) > $l )
	{
		$rText = substr($texto, 0, $l);		
		$rText .= "...";
	}
	else
		$rText = $texto;
	
	return $rText;	
}

/* paginação de resultados. 
	$link: passa-se o link para a página de resultados, a variável p é aqui colocada.
	$sql: passa-se a consulta listando todos os elementos da tabela.
	$regPorPag: quantidade de registros por página
	$p: página atual;
	
	essa função deve ser usada em conjunto com a função javascript que carrega a página escolhida. 
	
	OBS: adequar a formatação dada aqui de acordo com o layout do sistema.
*/
function paginacaoBar( $sql, $regPorPag, $link, $p )
{
	$db 			= new dataBase();
	$query 			= $db->query($sql);
	
	$totalRows 		= $db->numRows($query);
	
	$totalPaginas 	= ceil( $totalRows / $regPorPag ); 
	
	//se não houver registros, não há paginação
	if( !$totalRows )
	{
		$table = 'N&atilde;o h&aacute; registros na base de dados.';
		return $table;
	}
	
	//consturindo a combo com as páginas
	$combo = "<select name='goToPage' onChange='location = \"". $link . "&p=\" + this.options[this.selectedIndex].value;' class='combo'>";
	
	for( $i=1; $i<=$totalPaginas; $i++ )
	{
		if( $i == $p )
			$selected = "selected";
		else
			$selected = "";
			
		$combo .= "<option value='" . $i . "' " . $selected . ">" . $i ."</option>";
	}
	$combo .= "</select>";	
	
	//construindo a tabela com o proximo, anterior, etc.
	if( $p == 1 && $p == $totalPaginas )
		$setas = "	<table cellpadding='0' cellspacing='0' border='0' align='right'>
						<tr>
							<td>" . $p . "</td>
						</tr>			
					</table>";
	elseif( $p == 1 && $p != $totalPaginas )
		$setas = "	<table cellpadding='0' cellspacing='0' border='0' align='right'>
						<tr>
							<td>" . $p . "&nbsp;&nbsp;-&nbsp;&nbsp;</td>
							<td onClick='location = \"". $link . "&p=" . ($p+1) . "\";' style='cursor: pointer; border: 1px solid #000000; background-color: #FFFFFF; font-weight: bold; color: #6BB46B; padding-left: 5px; padding-right: 5px; padding-top: 2px; padding-bottom: 2px;'> > próximo </td>
						</tr>			
					</table>";
	elseif( $p < $totalPaginas )
		$setas = "	<table cellpadding='0' cellspacing='0' border='0' align='right'>
						<tr>
							<td onClick='location = \"". $link . "&p=" . ($p-1) . "\";' style='cursor: pointer; border: 1px solid #000000; background-color: #FFFFFF; font-weight: bold; color: #6BB46B; padding-left: 5px; padding-right: 5px; padding-top: 2px; padding-bottom: 2px;'> < anterior </td>
							<td>&nbsp;&nbsp;-&nbsp;&nbsp;" . $p . "&nbsp;&nbsp;-&nbsp;&nbsp;</td>
							<td onClick='location = \"". $link . "&p=" . ($p+1) . "\";' style='cursor: pointer; border: 1px solid #000000; background-color: #FFFFFF; font-weight: bold; color: #6BB46B; padding-left: 5px; padding-right: 5px; padding-top: 2px; padding-bottom: 2px;'> > próximo </td>
						</tr>			
					</table>";
	else
		$setas = "	<table cellpadding='0' cellspacing='0' border='0' align='right'>
						<tr>
							<td onClick='location = \"". $link . "&p=" . ($p-1) . "\";' style='cursor: pointer; border: 1px solid #000000; background-color: #FFFFFF; font-weight: bold; color: #6BB46B; padding-left: 5px; padding-right: 5px; padding-top: 2px; padding-bottom: 2px;'> < anterior </td>
							<td>&nbsp;&nbsp;-&nbsp;&nbsp;" . $p . "</td>
						</tr>			
					</table>";
		
	
	$table = "	<table cellpadding='3' cellspacing='0' border='0' align='center' style='width: 100%'>
					<tr>
						<td align='right' style='width: 50%;'>" . $setas . "</td>
						<td style='width: 50%;' align='right'>
							<table cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<td>Vá para a página &nbsp;&nbsp;</td>
									<td>" . $combo . "</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>";
	
	return $table;	
}

function paginacaoBarSite( $sql, $regPorPag, $link, $p, $frase = "" )
{
	$db 			= new dataBase();
	$query 			= $db->query($sql);
	
	$totalRows 		= $db->numRows($query);
	
	$totalPaginas 	= ceil( $totalRows / $regPorPag ); 
	
	//se não houver registros, não há paginação
	if( !$totalRows )
	{
		if( trim($frase) )
			$table = $frase;
		else
			$table = 'N&atilde;o h&aacute; registros na base de dados.';
		return $table;
	}
	
	//consturindo a combo com as páginas
	$combo = "<select name='goToPage' onChange='location = \"". $link . "&p=\" + this.options[this.selectedIndex].value;' class='combo'>";
	
	for( $i=1; $i<=$totalPaginas; $i++ )
	{
		if( $i == $p )
			$selected = "selected";
		else
			$selected = "";
			
		$combo .= "<option value='" . $i . "' " . $selected . ">" . $i ."</option>";
	}
	$combo .= "</select>";	
	
	//construindo a tabela com o proximo, anterior, etc.
	if( $p == 1 && $p == $totalPaginas )
		$setas = "	<td>" . $p . "</td>";
	elseif( $p == 1 && $p != $totalPaginas )
		$setas = "	<li><a href='#'> ". $p . " </a></li>
					<span class='proximo'><a href='". $link . "&p=" . ($p+1) . "'> próximo </a></span>";
	elseif( $p < $totalPaginas )
		$setas = "	<span class='anterior'><a href='". $link . "&p=" . ($p-1) . "'> anterior </a></span>
					<li><a href='#'> ". $p . " </a></li>
					<span class='proximo'><a href='". $link . "&p=" . ($p+1) . "'> próximo </a></span>";
	else
		$setas = "  <span class='anterior'><a href='". $link . "&p=" . ($p-1) . "'> anterior </a></span>
					<li><a href='#'> ". $p . " </a></li>";
	
	$table = "	<!--PAGINAÇÃO-->
					<div>
						<div class='paginacao floatLeft'>
							<ul>" . $setas . "</ul>
						</div>
						<div class='paginacaoCombo floatRight'>
							<table cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<td>Vá para a página &nbsp;&nbsp;</td>
									<td>" . $combo . "</td>
								</tr>
							</table>
						</div>
						<div class='clear'></div>
					</div>
				<!--/PAGINAÇÃO-->";
	
	return $table;	
}

//formata os valores vindos do mysql. padrão a ser formatado: R$ 120,0 é 120.0, R$ 1234.5 é 1.234,5
function formataMoeda( $valor )
{
	$real 		= substr($valor, 0, (strlen($valor) -3));
	$centavos 	= substr($valor, -2);

	if( strlen($real) > 3 )
		$real = substr( $real, 0, (strlen($real) - 3) ) . "." . substr( $real, -3 );
	if( strlen($real) > 7 )
		$real = substr( $real, 0, (strlen($real) - 7) ) . "." . substr( $real, -7 );
	if( strlen($real) > 11 )
		$real = substr( $real, 0, (strlen($real) - 11) ) . "." . substr( $real, -11 );
	if( strlen($real) > 15 )
		$real = substr( $real, 0, (strlen($real) - 15) ) . "." . substr( $real, -15 );
	
	return $sinal.$real .",". $centavos;
}

//formata os valores para serem gravados no mysql. se vier 1.234,50 retorna 1234.50. retira todos os pontos e troca as vírgulas por pontos.
function preparaMoeda( $valor )
{
	$valor = str_replace( ".", "", $valor );
	$valor = str_replace( ",", ".", $valor );
	
	return $valor;
}

//formata a data vinda do sql e vice-versa
function formataDataSql( $date, $lang = "" )
{
	$newDate = explode( "-", $date );
	
	//se date tiver um length de 3, então está no formato sql. senão, está no formato padrão.
	if( count($newDate) == 3 )
	{
		$newDate = $newDate[2] ."/". $newDate[1] ."/". $newDate[0];
	}
	else
	{
		$newDate = explode( "/", $date );
		$newDate = $newDate[2] ."-". $newDate[1] ."-". $newDate[0];
	}
	
	return $newDate;
}

//formata a data vinda do sql e vice-versa
function formataDateTimeSql( $date )
{
	return $newDate;
}

function verificar_email($email)
{
   $mail_correcto = 0;
   //verifico umas coisas
   if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
      if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
         //vejo se tem caracter .
         if (substr_count($email,".")>= 1){
            //obtenho a terminação do dominio
            $term_dom = substr(strrchr ($email, '.'),1);
            //verifico que a terminação do dominio seja correcta
         if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
            //verifico que o de antes do dominio seja correcto
            $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
            $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
            if ($caracter_ult != "@" && $caracter_ult != "."){
               $mail_correcto = 1;
            }
         }
      }
   }
}

if ($mail_correcto)
   return 1;
else
   return 0;
}

function loadLinkArquivoImpressao( $arquivo )
{
	if( file_exists(PATH_ARQUIVO_IMPRESSAO.$img) && $arquivo != "."  )
		print "<a href='".PATH_ARQUIVO_IMPRESSAO . $arquivo."'>arquivo</a>";
} 

function okCPF($cpf)
 {
  try
   {
    $proibidos = array('11111111111','22222222222','33333333333',
                 '44444444444','55555555555','66666666666','77777777777',
                 '88888888888','99999999999','00000000000', '12345678909');
    $cpf = ereg_replace('[^0-9]', '', $cpf);
     if(in_array($cpf, $proibidos))
      {
       throw new Exception('');//Erro: CPF Nulo !
      }
    $a=0;
     for($i=0; $i < 9; $i++)
      {
       $a += ($cpf[$i]*(10 - $i));
      }
    $b = ($a % 11);
    $a = (($b > 1) ? (11 - $b) : 0);
     if($a != $cpf[9])
      {
       throw new Exception(''); //Erro: CPF Invalido !
      }
    $a=0;
     for ($i=0; $i < 10; $i++)
      {
       $a += ($cpf[$i]*(11 - $i));
      }
    $b= ($a % 11);
    $a = (($b > 1) ? (11 - $b) : 0);
     if( $a != $cpf[10])
      {
       throw new Exception(''); //Erro: CPF Invalido !
      }
     }
  catch(Exception $e)
   {
    //echo $e->getMessage();
   }
   if($cpf != '' && !isset($e))
    {
     //echo 'OK ! CPF Valido !';
    }
}

function generatePassword($length=6,$level=2)
{
   list($usec, $sec) = explode(' ', microtime());
   srand((float) $sec + ((float) $usec * 100000));

   $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
   $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
   $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

   $password  = "";
   $counter   = 0;

   while ($counter < $length) {
	 $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);

	 // All character must be different
	 if (!strstr($password, $actChar)) {
		$password .= $actChar;
		$counter++;
	 }
   }

   return $password;
}	

?>