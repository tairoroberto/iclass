// funções para o menu

//muda a cor de fundo de um objeto passado
function changeColor(obj, color)
{
	obj.style.backgroundColor = color;	
}

//mostra o menu escondido
function showMenu( idMenu )
{
	var menu = document.getElementById( idMenu );
	
	if( menu.style.display == "" )
		menu.style.display = "none";
	else
		menu.style.display = "";
}

//redireciona uma pagina do menu para o iframe
function redirectPage( href )
{
	var iframe = document.getElementById("iframePrincipal");
	
	iframe.src = href;
}