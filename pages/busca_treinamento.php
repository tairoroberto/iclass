<?php
	require_once "inc/class/treinamento.php";
	//vê se está logado mesmo.
	if( !validaLoginSite() )
	{
		header("Location: index.php?land=login");
		die();
	}
	
	$treinamento = new treinamento();
	
	//setando as sessões pro filtro
	if( $_SERVER['REQUEST_METHOD'] == 'POST' )
	{
		$_SESSION['fNomeTreinamento'] 	= $_POST['fNomeTreinamento'];
	}
	
	//esvaziando os filtros se for o primeiro acesso. 
	if( $_GET['begin'] )
	{
		$_SESSION['fNomeTreinamento'] 	= "";
	}
?>
<div class="minha_contata_cot_esquerdo">
<?php require_once "pages/info_usuario.php"; ?>
<form action="index.php?land=treinamentos_busca" method="post">
    <p style="padding-left: 15px;"><strong>Busque um Treinamento:</strong></p>    
    <div style="text-align: center;"><input type="text" name="fNomeFabricante" class="inputText" maxlength="200" /></div>
	<div align="center"><input type='image' src="Util/img/bt_filtrar.jpg" /></div>
</form>
</div>
<div class="minha_contata_cot_direito">
<img src="Util/img/banner_iomega.jpg" width="518" height="179"alt="Banner">         
<p align="center">Foram encontrados 4 resultados para sua pesquisa:</p>
                    
                    <a href="#">Iomega</a>
                    <p><strong>(5) Treinamento(s) disponíveis</strong></p>
                    <img src="Util/img/linha_minha_conta.jpg" />
                    
                    <a href="#">Kaspersky Labs</a>
                    <p><strong>(3) Treinamento(s) disponíveis</strong></p>
                    <img src="Util/img/linha_minha_conta.jpg" />
                    
                    <a href="#">Microsoft Corporation</a>
                    <p><strong>(13) Treinamento(s) disponíveis</strong></p>
                    <img src="Util/img/linha_minha_conta.jpg" />
                    
                    <a href="#">Aple Inc</a>
                    <p><strong>(8) Treinamento(s) disponíveis</strong></p>
                    <img src="Util/img/linha_minha_conta.jpg" />
                    
                    
                    
                    <!--PAGINAÇÃO-->
                    <div class="paginacao">
                    <ul>
                    	<span class="anterior"><a href="#"> anterior </a></span>
                        <li><a href="#"> 1 </a></li>
                        <li><a href="#"> 2 </a></li>
                        <li><a href="#"> 3 </a></li>
                        <li><a href="#"> 4 </a></li>
                        <span class="proximo"><a href="#"> proximo </a></span>
                       
                    </ul>
                    </div>
                    <!--/PAGINAÇÃO-->
<?php print $treinamento->listaTreinamentos(5, 1, "Nenhum Treinamento foi encontrado."); ?>

</div>