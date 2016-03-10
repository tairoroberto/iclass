NUM_DIGITOS_CPF = 11;

NUM_DIGITOS_CNPJ = 14;

NUM_DGT_CNPJ_BASE = 8;



//consiste uma data passada

function consisteData(obj)

{

		var arrData = obj.value.split("/");

		// se não vierem 3 células, ou a primeira e a segunda célula não tiverem 2 caracteres ou a última não tiver 4, já dá erro

		if( arrData[0].length != 2 || arrData[1].length != 2 || arrData[2].length != 4 )

		{

			alert('Formato de data inválido.');

			obj.value = "";

			obj.focus();

			return false;

		}	

		else

		{

			//verificando se não digitou tudo zero

			if( arrData[0] == "00" || arrData[1] == "00" || arrData[2] == "0000" )

			{

				alert("Formato de data inválido.");

				obj.value = "";

				obj.focus();

				return false;				

			}



			if( arrData[0] < 10 )//tirando o zero que o cara digitou

				arrData[0] = arrData[0].substr(1,1);

			if( arrData[1] < 10 )//tirando o zero que o cara digitou

				arrData[1] = arrData[1].substr(1,1);



			//verificando se não digitou um mês maior que 12 ou menor que 1

			if( parseInt(arrData[1]) < 1 || parseInt(arrData[1]) > 12 )

			{

				alert("O mês digitado deve ir de 01 até 12 apenas.");

				obj.value = "";

				obj.focus();

				return false;

			} 

			//verificando mês de fevereiro

			if( ( (parseInt(arrData[2]) - 1900) % 4 ) == 0 ) //ano bissexto

				finalDiaFev = 29;

			else

				finalDiaFev = 28;

			

			if( arrData[1] == "02" && parseInt(arrData[0]) > finalDiaFev )

			{

				alert("Este mês de fevereiro possui " + finalDiaFev + " dias.");

				obj.value = "";

				obj.focus();

				return false;

			}

			else

			{

				if( arrData[1] % 2 == 0 )

					finalDia = 30;

				else

					finalDia = 31;

					

				if( arrData[0] > finalDia )

				{

					alert("Este mês não pode ter mais que " + finalDia + " dias.");

					obj.value = "";

					obj.focus();

					return false; 

				}

				

			} 

		}

		return true;

}



//muda a cor de fundo de uma linha inteira. ele muda a cor de fundo das colunas, basta passar a linha como objeto e a cor.

function changeColorRow( tr, color )

{

	var colunas = tr.childNodes;



	for( var i=0; i<colunas.length; i++ )

	{

		if( colunas[i].tagName == "TD" )

			colunas[i].style.backgroundColor = color;	

	}

}



//formatação de moeda

function formataMoeda(campo,tammax,teclapres,decimal) 

{ 

	var tecla = teclapres.keyCode; 

	vr = Limpar(campo.value,"0123456789"); 

	tam = vr.length; 

	dec=decimal 

	

	if (tam < tammax && tecla != 8){ tam = vr.length + 1 ; } 

	

	if (tecla == 8 ) 

		tam = tam - 1 ; 

	

	if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) 

	{ 

		if ( tam <= dec ) 

			campo.value = vr ; 

		

		if ( (tam > dec) && (tam <= 5) )

			campo.value = vr.substr( 0, tam - 2 ) + "," + vr.substr( tam - dec, tam ) ; 

		if ( (tam >= 6) && (tam <= 8) )

			campo.value = vr.substr( 0, tam - 5 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ; 

		if ( (tam >= 9) && (tam <= 11) )

			campo.value = vr.substr( 0, tam - 8 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ; 

		if ( (tam >= 12) && (tam <= 14) )

			campo.value = vr.substr( 0, tam - 11 ) + "." + vr.substr( tam - 11, 3 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ; 

		if ( (tam >= 15) && (tam <= 17) )

			campo.value = vr.substr( 0, tam - 14 ) + "." + vr.substr( tam - 14, 3 ) + "." + vr.substr( tam - 11, 3 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - 2, tam ) ; 

	} 



} 

/*<input type="text" name="texto" size="20" onKeydown="formataMoeda(this,20,event,2)"> */



//remove caracteres inválidos de uma string

function Limpar(valor, validos) 

{ 

	// retira caracteres invalidos da string 

	var result = ""; 

	var aux; 

	for (var i=0; i < valor.length; i++) { 

		aux = validos.indexOf(valor.substring(i, i+1)); 

		if (aux>=0) 

			result += aux;  

	} 

	return result; 

} 



//retira caracteres em ranco à esquerda da string

function leftTrim(sString)

{

	while (sString.substr(0,1) == ' ')

	{

		sString = sString.substr(1, sString.length);

	}

	return sString;

}



//retira caracteres em ranco à direita da string

function rightTrim(sString)

{

	while (sString.substr(sString.length-1, sString.length) == ' ')

	{

		sString = sString.substr(0,sString.length-1);

	}

	return sString;

}



//retira caracteres em branco dos dois lados da string

function trim(sString)

{

	while (sString.substr(0,1) == ' ')

	{

		sString = sString.substr(1, sString.length);

	}

	while (sString.substr(sString.length-1, sString.length) == ' ')

	{

		sString = sString.substr(0,sString.length-1);

	}

	return sString;

}



//exemplo de uso do noLetters: onKeyDown="return noLetters(event);"

function noLetters(e)

{

	var tecla;

	if(!e)

		e = window.event;

	

	// verificação cross-browser

	if(e.which)

		tecla = e.which;

	else if(e.keyCode)

		tecla = e.keyCode;



	if( (tecla >= 48 && tecla <= 57) || (tecla >= 96 && tecla <= 105) || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 46 || tecla == 9 )

	{

		/*9: tab; 8 = backspace; 37 = seta pra esquerda; 39 = seta pra direita; 46 = delete*/

		return true;

	}

	else

	{

		return false;

	}

}



//setLoadingMessageUserMode: modifica alguns estilos da mensagem de carregamento para se ajustar às requisições da página

function setLoadingMessageUserMode()

{

	//estilo da div de carregamento

	var styleDivProcessando = "border: 1px solid #990000; display: none; background-color: #FF0000; padding-left: 20px; padding-right: 20px; padding-top: 10px; width: 150px; padding-bottom: 10px; text-align: center; color: #FFFFFF; font-weight: bold; font-family: verdana; font-size: 11px; left: 100%; top: 0px; margin-left: -192px; position: absolute; z-index: 3;";

	document.getElementById("loadingMessage").setAttribute( "style", styleDivProcessando ); //Standard-based

	document.getElementById("loadingMessage").style.cssText = styleDivProcessando; //Internet Explorer

}



//formata uma data corretamente

function formataData( obj )

{

	if( parseInt(obj.value.length) == 2 )

		obj.value += "/";

	if( parseInt(obj.value.length) == 5 )

		obj.value += "/";

}

//formata uma data corretamente

function formataCEP( obj )

{

	if( parseInt(obj.value.length) == 5 )

		obj.value += "-";

}



//formata CPF

function formataCPF( obj )

{		

	if( obj.value.charAt(3) != "." && obj.value.charAt(3) != "" )

		obj.value = obj.value.substring(0,3) + "." + obj.value.substring(3);

	if( obj.value.charAt(7) != "." && obj.value.charAt(7) != "" )

	    obj.value = obj.value.substring(0,7) + "." + obj.value.substring(7);   

	if( obj.value.charAt(11) != "-" && obj.value.charAt(11) != "" )

	    obj.value = obj.value.substring(0,11) + "-" + obj.value.substring(11);

}



//formata CNPJ

function formataCNPJ( obj )

{



	if( obj.value.charAt(2) != "." && obj.value.charAt(2) != "" )

		obj.value = obj.value.substring(0,2) + "." + obj.value.substring(2);

	if( obj.value.charAt(6) != "." && obj.value.charAt(6) != "" )

	    obj.value = obj.value.substring(0,6) + "." + obj.value.substring(6);   

	if( obj.value.charAt(10) != "/" && obj.value.charAt(10) != "" )

	    obj.value = obj.value.substring(0,10) + "/" + obj.value.substring(10);

	if( obj.value.charAt(15) != "-" && obj.value.charAt(15) != "" )

	    obj.value = obj.value.substring(0,15) + "-" + obj.value.substring(15);

}







/**

* Calcula os 2 dígitos verificadores para o número-efetivo pEfetivo de

* CNPJ (12 dígitos) ou CPF (9 dígitos) fornecido. pIsCnpj é booleano e

* informa se o número-efetivo fornecido é CNPJ (default = false).

* @param String pEfetivo

* 	String do número-efetivo (SEM dígitos verificadores) de CNPJ ou CPF.

* @param boolean pIsCnpj

* 	Indica se a string fornecida é de um CNPJ.

* 	Caso contrário, é CPF. Default = false (CPF).

* @return String com os dois dígitos verificadores.

*/

function dvCpfCnpj(pEfetivo, pIsCnpj) {

    if (pIsCnpj == null) pIsCnpj = false;

    var i, j, k, soma, dv;

    var cicloPeso = pIsCnpj ? NUM_DGT_CNPJ_BASE : NUM_DIGITOS_CPF;

    var maxDigitos = pIsCnpj ? NUM_DIGITOS_CNPJ : NUM_DIGITOS_CPF;

    var calculado = formatCpfCnpj(pEfetivo + "00", false, pIsCnpj);

    calculado = calculado.substring(0, maxDigitos - 2);

    var result = "";



    for (j = 1; j <= 2; j++) {

        k = 2;

        soma = 0;

        for (i = calculado.length - 1; i >= 0; i--) {

            soma += (calculado.charAt(i) - '0') * k;

            k = (k - 1) % cicloPeso + 2;

        }

        dv = 11 - soma % 11;

        if (dv > 9) dv = 0;

        calculado += dv;

        result += dv

    }



    return result;

} //dvCpfCnpj





/**

* Testa se a String pCpf fornecida é um CPF válido.

* Qualquer formatação que não seja algarismos é desconsiderada.

* @param String pCpf

* 	String fornecida para ser testada.

* @return <code>true</code> se a String fornecida for um CPF válido.

*/

function isCpf(pCpf) {

    var numero = formatCpfCnpj(pCpf, false, false);

    if (numero.length > NUM_DIGITOS_CPF) return false;



    var base = numero.substring(0, numero.length - 2);

    var digitos = dvCpfCnpj(base, false);

    var algUnico, i;



    // Valida dígitos verificadores

    if (numero != "" + base + digitos) return false;



    /* Não serão considerados válidos os seguintes CPF:

    * 000.000.000-00, 111.111.111-11, 222.222.222-22, 333.333.333-33, 444.444.444-44,

    * 555.555.555-55, 666.666.666-66, 777.777.777-77, 888.888.888-88, 999.999.999-99.

    */

    algUnico = true;

    for (i = 1; algUnico && i < NUM_DIGITOS_CPF; i++) {

        algUnico = (numero.charAt(i - 1) == numero.charAt(i));

    }

    return (!algUnico);

} //isCpf





/**

* Testa se a String pCnpj fornecida é um CNPJ válido.

* Qualquer formatação que não seja algarismos é desconsiderada.

* @param String pCnpj

* 	String fornecida para ser testada.

* @return <code>true</code> se a String fornecida for um CNPJ válido.

*/

function isCnpj(pCnpj) {

    var numero = formatCpfCnpj(pCnpj, false, true);

    if (numero.length > NUM_DIGITOS_CNPJ) return false;



    var base = numero.substring(0, NUM_DGT_CNPJ_BASE);

    var ordem = numero.substring(NUM_DGT_CNPJ_BASE, 12);

    var digitos = dvCpfCnpj(base + ordem, true);

    var algUnico;



    // Valida dígitos verificadores

    if (numero != "" + base + ordem + digitos) return false;



    /* Não serão considerados válidos os CNPJ com os seguintes números BÁSICOS:

    * 11.111.111, 22.222.222, 33.333.333, 44.444.444, 55.555.555,

    * 66.666.666, 77.777.777, 88.888.888, 99.999.999.

    */

    algUnico = numero.charAt(0) != '0';

    for (i = 1; algUnico && i < NUM_DGT_CNPJ_BASE; i++) {

        algUnico = (numero.charAt(i - 1) == numero.charAt(i));

    }

    if (algUnico) return false;



    /* Não será considerado válido CNPJ com número de ORDEM igual a 0000.

    * Não será considerado válido CNPJ com número de ORDEM maior do que 0300

    * e com as três primeiras posições do número BÁSICO com 000 (zeros).

    * Esta crítica não será feita quando o no BÁSICO do CNPJ for igual a 00.000.000.

    */

    if (ordem == "0000") return false;

    return (base == "00000000"

		|| parseInt(ordem, 10) <= 300 || base.substring(0, 3) != "000");

} //isCnpj





/**

* Testa se a String pCpfCnpj fornecida é um CPF ou CNPJ válido.

* Se a String tiver uma quantidade de dígitos igual ou inferior

* a 11, valida como CPF. Se for maior que 11, valida como CNPJ.



* Qualquer formatação que não seja algarismos é desconsiderada.

* @param String pCpfCnpj

* 	String fornecida para ser testada.

* @return <code>true</code> se a String fornecida for um CPF ou CNPJ válido.

*/

function isCpfCnpj(sender, args) {

    var numero = args.Value.replace(/\D/g, "");

    if (numero.length > NUM_DIGITOS_CPF)

        args.IsValid = isCnpj(args.Value)

    else

        args.IsValid = isCpf(args.Value);

} //isCpfCnpj



function validarCpf(sender, args) {

    args.IsValid = isCpf(args.Value);

}



function validarCnpj(sender, args) {

    args.IsValid = isCnpj(args.Value)

}

//formata os valores float do javascript. mostra valores em reais
function floatToReal( valor )
{
	var real = valor.toString().substr(0, (valor.length -3));
	var centavos = valor.toString().substr((valor.length - 2), 2);

	if( real.length > 3 )
		real = real.substr(0, (real.length - 3)) + "." + real.substr((real.length -3), 3);
	if( real.length > 7 )
		real = real.substr(0, (real.length - 7)) + "." + real.substr((real.length -7), 7);
	if( real.length > 11 )
		real = real.substr(0, (real.length - 11)) + "." + real.substr((real.length -11), 11);
	if( real.length > 15 )
		real = real.substr(0, (real.length - 15)) + "." + real.substr((real.length -15), 15);
	
	return real + "," + centavos;
}

//acerta casas decimais pra 2
function acertaCasasDecimais(valor)
{
	var _auxArr = valor.toString().split(".");
	
	if(_auxArr.length == 2)
	{
		if( _auxArr[1].length > 2 )
			_auxArr[1] = _auxArr[1].substr(0,2);
		else if( _auxArr[1].length < 2 )
			_auxArr[1] += "0";
			
		valor = _auxArr.join(".");
	}
	else
		valor += ".00";//coloca 2 casas deciimais no rateio arredondado
	
	return valor;
}


// verifica se o valor é realmente um número
function parseValorFloat( valor )
{
	var r = valor.replace(".", "");
	r = r.replace(",", ".");

	if(isNaN(parseFloat(r)) || (parseFloat(r) == 0.0))
		return 0;
	else
		return parseFloat(r);
		
}