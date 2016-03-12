<?php
/**
 * Created by PhpStorm.
 * User: tairo
 * Date: 12/03/16
 * Time: 13:42
 */
require_once "../../inc/config.inc.php";
require_once "../../inc/class/chaveacesso.php";
$chaves = new chaveacesso();

if(isset($_POST["valor_valor"],$_POST['action']) && $_POST["valor_valor"] != '' && $_POST['action'] == 'validar_chave'){
    $chave = $chaves->getChaveAcessoByValor($_POST["valor_valor"]);

    if(isset($chave->valor_chave)){

        echo json_encode($chave);
    }else{
        echo 'inexistente';
    }

}else{
    echo 'inexistente';
}

?>