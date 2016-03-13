<?
// Inicializa o uso de sessões
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(0);
session_start();

$_SESSION['arrLetras'] = "abcdefghijklmnopqrstuvwxyz";

ini_set("session.gc_maxlifetime", 1500); // Seta o tempo da sessão (em milisegundos)

set_time_limit(300); //5 minutos de tempo limite para execução de scripts.

// Evitando cache de arquivo
header('Content-Type: text/html; charset=ISO-8859-1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once "class/dataBase.php";

$db = new dataBase();
	
/*$db->conecta("localhost", "root", "root"); //banco de homologação
$db->selectDb("iclass");
$pre = "iclass_";*/
/*$db->conecta("mysql01.redehost.com.br", "fsimoes", "fabianosim"); //banco de homologação
$db->selectDb("fabianosimoes");
$pre = "iclass_"; */

$db->conecta("localhost", "root", "tairo1507"); //banco de homologação
//$db->conecta("iclassbd.db.6209664.hostedresource.com", "iclassbd", "Zuza2143"); //banco de homologação

$db->selectDb("iclassbd");
$pre = "iclass_";

/*** CONSTANTES GERAIS ****/
define("PRE", $pre); //prefixo de todas as tabelas
define("M_OVER_MENU_COLOR", "#5ee574");
define("M_OUT_MENU_COLOR", "");
define("M_OVER_ITEM_MENU_COLOR", "#dfdfdf");
define("M_OUT_ITEM_MENU_COLOR", "");
define("MENU_TEXT_COLOR", "#328d3b");
define("BG_MENU_COLOR", "#efefef");
define("TITULO_INTERNAS_COLOR", "#FFFFFF");
define("TITULO_INTERNAS_BGCOLOR", "#a3a3a3");
define("MSG_WELCOME_COLOR" , "#003093");
define("PERGUNTAS_POR_QUIZ", "10");
define("MAIL_CADASTRO_SITE", "contato@itailers.com.br");

/* CAMINHOS */
define("PATH_IMG_FABRICANTE", "../../img_fabricantes/");
define("PATH_IMG_FABRICANTE_SITE", "../../img_fabricantes/");
define("PATH_IMG_FABRICANTE_UPLOAD", "../../img_fabricantes/");
//define("PATH_IMG_FABRICANTE_UPLOAD", "E:\\Domains\\fabianosimoes.com.br\\wwwroot\\projetos\\itailers\\iclass\\site\\img_fabricantes\\");
//define("PATH_IMG_FABRICANTE_UPLOAD", "D:\\Projetos\\freelances\\itailers\\iclass\\site\\img_fabricantes\\");
define("PATH_IMG_TREINAMENTO", "../../img_treinamentos/");
define("PATH_IMG_TREINAMENTO_SITE", "../../img_treinamentos/");
define("PATH_IMG_TREINAMENTO_UPLOAD", "../../img_treinamentos/");
//define("PATH_IMG_TREINAMENTO_UPLOAD", "D:\\Hosting\\6209664\\html\\iclass\\img_treinamentos\\");
//define("PATH_IMG_TREINAMENTO_UPLOAD", "../../img_treinamentos/");
//define("PATH_IMG_TREINAMENTO_UPLOAD", "E:\\Domains\\fabianosimoes.com.br\\wwwroot\\projetos\\itailers\\iclass\\site\\img_treinamentos\\");
//define("PATH_IMG_TREINAMENTO_UPLOAD", "C:\\Users\\Fabiano\\Projetos\\freelances\\itailers\\iclass\\site\\img_treinamentos\\");
define("PATH_IMG_USUARIO_SITE", "../../img_usuarios_site/");
define("PATH_IMG_USUARIO_SITE_SITE", "../../img_usuarios_site/");
define("PATH_IMG_USUARIO_SITE_UPLOAD", "../../img_usuarios_site/");
//define("PATH_IMG_USUARIO_SITE_UPLOAD", "E:\\Domains\\fabianosimoes.com.br\\wwwroot\\projetos\\itailers\\iclass\\site\\img_usuarios_site\\");
//define("PATH_IMG_USUARIO_SITE_UPLOAD", "D:\\Projetos\\freelances\\itailers\\iclass\\site\\img_usuarios_site\\");
define("PATH_IMG_PREMIO", "../../img_premios/");
define("PATH_IMG_PREMIO_SITE", "../../img_premios/");
define("PATH_IMG_PREMIO_UPLOAD", "../../img_premios/");
//define("PATH_IMG_PREMIO_UPLOAD", "E:\\Domains\\fabianosimoes.com.br\\wwwroot\\projetos\\itailers\\iclass\\site\\img_premios\\"); //caminho das imagens dos usuários do site
define("HORAS_GMT", "3"); //MUDAR ESTA CONSTANTE PARA O AVANÇO DE HORAS CERTAS DE ACORDO COM O HORÁRIO DE VERÃO.
//define("PATH_IMG_PREMIO_UPLOAD", "D:\\Projetos\\freelances\\itailers\\iclass\\site\\img_premios\\"); //caminho das imagens dos usuários do site


require_once "functions.inc.php";
require_once "class/usuario.php";

?>
