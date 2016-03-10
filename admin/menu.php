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
			//while(0)
			{
				$subMenus = $usuario->getSubMenus($item->idPagina, $_SESSION['sess_idUsuario']);
				if( $db->numRows($subMenus) )
				{
		?>
	        	<li><a href="#"><?php print $item->nome; ?></a>
        <?php
					if( $db->numRows($subMenus) > 0 )
					{
		?>
		        		<ul>
        <?php
						while( $subItem = $db->fetchObject($subMenus) )
						{
							$subSubMenus = $usuario->getSubMenus($subItem->idPagina, $_SESSION['sess_idUsuario']);
		?>
	        				<li><a href="<?php if( trim($subItem->url) ) print "javascript:redirectPage('".$subItem->url."');"; else print "#"; ?>"><?php print $subItem->nome;?></a>
        <?php
							if( $db->numRows($subSubMenus) > 0 )
							{
		?>
        						<ul>
        <?php
								while( $subSubItem = $db->fetchObject($subSubMenus) )
								{								
		?>
        							<li><a href="javascript:redirectPage('<?php print $subSubItem->url; ?>');"><?php print $subSubItem->nome;?></a></li>
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
<!-- <div id="myslidemenu" class="jqueryslidemenu">
    <ul>
		<li><a href="#">Prêmios</a>
				<ul>
					<li><a href="javascript:redirectPage('premios/incluir.php');">Incluir</a></li>
					<li><a href="javascript:redirectPage('premios/listar.php?begin=1');">Listar</a></li>
				</ul>
		</li>
		<li><a href="#">Redes</a>
			<ul>
				<li><a href="javascript:redirectPage('categorias/incluir.php');">Incluir Rede</a></li>
				<li><a href="javascript:redirectPage('categorias/listar.php?begin=1');">Listar Redes</a></li>
				<li><a href="#">Lojas</a>
					<ul>
						<li><a href="javascript:redirectPage('lojas/incluir.php');">Incluir Loja</a></li>
						<li><a href="javascript:redirectPage('lojas/listar.php?begin=1');">Listar Lojas</a></li>
					</ul>
				</li>
				<li><a href="#">Fabricantes</a>
					<ul>
						<li><a href="javascript:redirectPage('fabricantes/incluir.php');">Incluir</a></li>
						<li><a href="javascript:redirectPage('fabricantes/listar.php?begin=1');">Listar</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li><a href="#">Relatórios</a>
				<ul>
					<li><a href="javascript:redirectPage('relatorios/usuarios_do_site.php');">Por Usuário do Site</a></li>
				</ul>
		</li>
		<li><a href="#">Treinamentos</a>
			<ul>
				<li><a href="javascript:redirectPage('treinamentos/incluir.php');">Cadastrar Treinamento</a></li>
				<li><a href="javascript:redirectPage('treinamentos/listar.php');">Listar Treinamentos</a></li>
				<li><a href="#">Quiz</a>
					<ul>
						<li><a href="javascript:redirectPage('quiz/incluir.php');">Cadastrar Quiz</a></li>
						<li><a href="javascript:redirectPage('quiz/listar.php?begin=1');">Listar</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li><a href="#">Usuários</a>
			<ul>
				<li><a href="#">Usuários do Admin</a>
						<ul>
							<li><a href="javascript:redirectPage('usuarios/incluir.php');">Incluir Administrador</a></li>
							<li><a href="javascript:redirectPage('usuarios/listar.php?begin=1');">Listar Administradores</a></li>
						</ul>
				</li>
				<li><a href="#">Usuários do Site</a>
						<ul>
							<li><a href="javascript:redirectPage('usuarios_site/incluir.php');">Incluir Usuário</a></li>
							<li><a href="javascript:redirectPage('usuarios_site/listar.php?begin=1');">Listar Usuários</a></li>
						</ul>
				</li>
			</ul>
		<li><a href="index.php">Sair</a></li>
		<br style="clear: left" />
     </ul>
</div> -->