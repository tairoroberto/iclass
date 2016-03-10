//funções para listagem de dados

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

//redireciona para uma página.
function redirectHref( link )
{
	location.href =  link;	
}

//redireciona para algum link dependendo da resposta do usuário.
function confirma( frase, link )
{
	if( confirm(frase) )
		location.href = link;
	else
		return false;
}