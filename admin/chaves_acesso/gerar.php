<?
require_once "../../inc/config.inc.php";
require_once "../../inc/class/chaveacesso.php";

//vê se está logado mesmo.
if( !validaLogin() ) {
	header("Location: index.php");
	die();
}

$chave  = new chaveacesso();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script src="../js/functions.inc.js" type="text/javascript"></script>
	<link href="../css/main.css" rel="stylesheet" type="text/css">
    <link href="../css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <script type="application/javascript" src="../js/jquery-1.12.0.min.js"></script>
    <script type="application/javascript" src="../js/bootstrap.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title></title>

    <style type="text/css">
        .jquery-waiting-base-container {
            position: absolute;
            left: 0px;
            top: 20%;
            margin:0px;
            width: 100%;
            height: 400px;
            display:block;
            z-index: 9999997;
            opacity: 0.65;
            -moz-opacity: 0.65;
            filter: alpha(opacity = 65);
            background: black;
            background-image: url('../img/loading_bar.gif');
            background-repeat: no-repeat;
            background-position:50% 50%;
            text-align: center;
            overflow: hidden;
            font-weight: bold;
            color: white;
            padding-top: 25%;
        }
    </style>

    <script type="text/javascript" language="javascript">

        $(document).ready(function(){
            $("#alertError").css('display', 'none');
            formChaves.action = '';
        });

        //verifica se os dados do formulário estão ok
        function submitForm() {
            var qtdChaves = $("#qtdChaves").val();

            if (qtdChaves != ''){
                $('#acao').val('gerar_chaves');
                formChaves.submit();
                $('.div-ajax-carregamento-pagina').fadeOut('fast');

            }else {
                $('#alertError').css('display', 'block');
                $('.div-ajax-carregamento-pagina').fadeOut('fast');
            }
        }

        /*Função que envia para ferar o arquivo excel com as chaves*/
        function gerarExcel(){
            $('#acao').val('excel');
            formChaves.action = 'excel_download.php';
            formChaves.target = '_blank';
            formChaves.submit();
            setTimeout(function(){
                window.location.href = 'gerar.php';
            }, 2000);
        }

    </script>
</head>

<body style="background-color: #FFFFFF;"><table cellpadding="0" cellspacing="0" border="0" style="width: 90%;" align="center">

    <tr>
		<td style="border-bottom: 1px solid  #CCCCCC; text-align: center; padding-top: 5px; padding-bottom: 5px; color: #FFFFFF; font-weight: bold; background-color: <?=TITULO_INTERNAS_BGCOLOR?>;">G E R A R &nbsp;&nbsp;&nbsp;&nbsp; C H A V E S &nbsp;&nbsp;&nbsp;&nbsp; D E &nbsp;&nbsp;&nbsp;&nbsp; A C E S S O</td>
	</tr>
	<tr>
		<td style="padding-top: 5px;">
			<!-- filtros -->
			<table style="width: 100%;" cellpadding="3" cellspacing="0">
				<tr>
                    <form action="" method="POST" id="formChaves" name="formChaves">
                        <input type="hidden" id="acao" name="acao" value=""/>
					<td style="height: 60px; padding-top: 10px; border: 1px solid #CCCCCC; background-color: #EFEFEF;">

							<table border="0" cellpadding="4" cellspacing="0" style="width: 100%;">
								<tr>
									<td class="msgError" style="text-align: center;"></td>
                                    <div class="alert alert-danger" id="alertError">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <strong>Selecione a quantidade de chaves a ser gerada</strong>
                                    </div>
								</tr>
							</table>
							<table border="0" cellpadding="2" cellspacing="0">
								<tr>
									<td id="txtNome">Quantidade de Chaves:</td>
									<td><select name="qtdChaves" id="qtdChaves" class="form-control">
                                            <option value=''>-- Selecione --</option>
                                            <option value='5'>5</option>
                                            <option value='10'>10</option>
                                            <option value='15'>15</option>
                                            <option value='20'>20</option>
                                            <option value='25'>25</option>
                                            <option value='30'>30</option>
                                            <option value='35'>35</option>
                                            <option value='40'>40</option>
                                            <option value='45'>45</option>
                                            <option value='50'>50</option>
                                            <option value='55'>55</option>
                                            <option value='60'>60</option>
                                            <option value='70'>70</option>
                                            <option value='80'>80</option>
                                            <option value='90'>90</option>
                                            <option value='100'>100</option>
                                    </td>
								</tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
								<tr>
                                    <!-- Trigger the modal with a button -->
									<td>
                                        <input type="button" style="background-color: #4F6AA8; color: #FFFFFF;" class="btn btn-default" onclick="submitForm();" value="Gerar" name="btGerarChaves" id="btGerarChaves" />
                                    </td>
                                    <td>
                                        <input type="button" style="float:right;background-color: #4F6AA8; color: #FFFFFF;" class="btn " onclick="gerarExcel();" value="Gerar Excel" name="btGerarExcel" id="btGerarExcel" />
                                    </td>
								</tr>
							</table>

                    </form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!--Div Carregando-->
<div id="Carregando" style="display: none;" class="jquery-waiting-base-container">Carregando...</div>

    <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog" data-backdrop="static">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Chaves de acesso</h4>
                    </div>

                    <div class="modal-body" id="modal_body">

                    </div>

                    <div class="modal-footer">
                        <button type="button" onclick="gerarExcel();" style="float: left;background-color: #4F6AA8; color: #FFFFFF;" class="btn btn-default" data-dismiss="modal">Gerar Excel</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    </div>
                </div>

            </div>
        </div>
    <!--Modal-->

<?php

    /** Validações para chaves */
    if(isset($_POST["qtdChaves"], $_POST["acao"]) && $_POST["qtdChaves"] != '' && $_POST["acao"] == 'gerar_chaves'){
        $sql = "";
        $qtdChaves = $_POST["qtdChaves"];

        for($i =0; $i < $qtdChaves; $i++){
            $token = substr(md5(uniqid(rand(), true)), 0, 10); // token de 10 digitos

            $chave->insertChaveAcesso(
                array('valor_chave' => $token, 'ativa' => 0, 'data_cadastro' => date('Y-m-d H:i:s'))
            );
        }


        $chaves = $chave->listaChavesAcessoInativas();

        $table = '"<table class=\'table table-condensed table-hover\'>" +
                        	"<thead>" +
                        	"</thead>" +
                        	"<tbody>" + ';

        $count = 0;
        $td = '';
        foreach ($chaves as $ch) {

            if($count == 0){
                $table .= '"<tr style=\'text-align: center\'>" +';
            }

            $td .= '"<td><h5>'.$ch->valor_chave.'</h5></td>" +';

            //Fecha a tag
            if($count == 4){
                $table .= $td;
                $table .= '"</tr>" + ';
                $td = ''; // limpo as Tds
                $count = 0; //Zero o contador
                continue;
            }

            $count++;
        }

        $table .= '"</tbody>" +
                  "</table>"';

        echo "<script>
                $('#modal_body').html(".$table.");
                $('#myModal').modal('show');
              </script>";
    }
?>
</body>
</html>
