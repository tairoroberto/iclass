<?
class imagem
{
	#este m้todo pega a imagem duma pasta e copia para outra pasta de destino.	
	#$nameImg ้ a imagem e seu caminho completo.
	function sobe($pathImg,$nameImg)
	{
		if( !move_uploaded_file($pathImg,$nameImg) )
			return 0;
		 else 
			return 1;
	}
	
	#este m้todo pega a imagem duma pasta, copia e renomeia jogando em outra pasta de destino.	
	#$nameImg ้ a imagem e seu caminho completo.
	#ษ NECESSมRIA A COMPILAวรO DO PHP COM BIBLIOTECA GD2
	function sobeGD($pathImg,$nameImg,$w,$h)
	{
		$ext = substr($nameImg,-3);
		#criando nova imagem. para permitir mais extens๕es, basta adicionar mais ifs.
		if($ext == "jpg")
			$img 	= imagecreatefromjpeg($pathImg);
		elseif( $ext == "gif" )
			$img 	= imagecreatefromgif($pathImg);
		else
			return 0;
			
		#testando se a imagem foi carregada com sucesso e setando suas novas dimensoes.
		if($img) 
		{
			$widthOrig 	= imagesx($img);
			$heightOrig	= imagesy($img);
			
			/** se vier porcentagem nas strings, entใo redimensiono com porcentagem **/
			if( substr($w, -1) == "%" )
			{
				//formula de porcentagem
				$w = ( $widthOrig * substr($w, 0, strlen($w) -1) ) / 100; //porcentagem
			}
			
			if( substr($h, -1) == "%" )
			{
				//formula de porcentagem
				$h = ( $heightOrig * substr($h, 0, strlen($h) -1) ) / 100; //porcentagem
			}
			
						
				#criando imagem temporแria
				$novaImg	= imagecreatetruecolor($w,$h);
				if( $novaImg )
				{
					#alocando nova imagem redimensionada. 
					if( imagecopyresampled($novaImg,$img,0,0,0,0,$w,$h,$widthOrig,$heightOrig) )
					{
						if($ext == "jpg" || $ext == "jpeg")
							imagejpeg( $novaImg,$nameImg,'100' );
						elseif( $ext == "gif" )
							imagegif( $novaImg,$nameImg,'100' );
						#destroi imagem temporaria
						imagedestroy( $img );	
						imagedestroy( $novaImg );
					}
				} else {
					return 0;
				}
								
		} else {
			return 0;
		}
	}
}
?>