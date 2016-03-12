<?php
$data = date('d-m-Y');
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Chaves-Acesso-".$data .".xlsx\"");
header("Cache-Control: max-age=0");

require_once "../../inc/config.inc.php";
require_once "../../inc/class/chaveacesso.php";
require_once "../../inc/class/PHPExcel/PHPExcel.php";

if(isset($_POST["acao"]) && $_POST["acao"] == 'excel'){

    $chave = new chaveacesso();
    $chaves = $chave->listaChavesAcessoInativas();

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chaves de Acesso');

    $celula = 2;
    foreach ($chaves as $ch) {
        $objPHPExcel->getActiveSheet()->setCellValue("A".$celula, $ch->valor_chave);
        $celula++;
    }

    //Deixa as coluna com redimensionamento automatico
    $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save("php://output");
    exit;
}
