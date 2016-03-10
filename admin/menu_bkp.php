<?php
	//Monta o menu de acordo com as permissões do usuário
	$usuario = new usuario();
	$rootMenus = $usuario->getRootMenus();
?>
<div id="myslidemenu" class="jqueryslidemenu">
    <ul>
    	<?php
			//monta menu raiz
			while( $item = $db->fetchObject($rootMenus) )
			{
				$subMenus = $usuario->getSubMenus($item->idPagina, $_SESSION['sess_idUsuario']);
				if( $subMenus )
				{
		?>
	        	<li><a href="#"><?php print $item->nome; ?></a>
        <?php
					if($subMenus)
					{
		?>
		        		<ul>
        <?php
						while( $subItem = $db->fetchObject($subMenus) )
						{
		?>
	        				<li><a href="javascript:redirectPage('<?php print $subItem->url; ?>');"><?php print $subItem->nome;?></a></li>
        <?php
						}
		?>
        				</ul>
        <?php
					}
		?>
				</li>
        <?php
				}
			}
		?>
		<li><a href="index.php">Sair</a></li>
		<br style="clear: left" />
     </ul>
</div>