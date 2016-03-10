<?
class sendMail
{
	var $remetente;
	var $destinatario;
	var $assunto;
	var $corpo;
	var $anexos;
	var $header;
	var $boundary;
	var $bcc;
	var $tipo;

	# Construtor
	function sendMail($remetente, $assunto)
	{
		$this->assunto		= $assunto;
		$this->remetente	= $remetente;
		$this->boundary		= "------------MSG-BOUNDARY";
	}
	
	# Seta o tipo de e-mail
	# tipo (=1: Só texto|=2: HTML|=3: com anexo)
	function type($tipo)
	{
		$this->tipo = $tipo;
		switch ( $tipo ) {
			case 1:
				$this->header	.= "From: ".$this->remetente."\n";
				$this->header .= "Return-Path: <lucianacatao@lucianatacao.com.br>\n";
			break;
			case 2:
				$this->header = "From: ". $this->remetente ."\r\n";
				$this->header .= "Return-Path: <lucianacatao@lucianatacao.com.br>\n";
				$this->header .= "Content-type: text/html; charset=iso-8859-1\r\n";
			break;
			case 3:
				$this->header	= "MIME-version: 1.0\n";
				$this->header	.= "From: ".$this->remetente."\n";
				$this->header .= "Return-Path: <lucianacatao@lucianatacao.com.br>\n";
				$this->header	.= "Content-type: MULTIPART/MIXED; BOUNDARY=\"".$this->boundary."\"\n\n\n";
				$this->corpo	= "\n\n--".$this->boundary."\nContent-Type: text/html;\n\tcharset=\"iso-8859-1\"\nContent-Transfer-Encoding: 8bit\n\n";
			break;
		}
	}
	
	# Coloca corpo
	function textBody($corpo)
	{
		$this->corpo .= $corpo;
	}
	
	# Coloca o corpo com base em arquivo
	# file = caminho do arquivo
	# arr (contém arrays com o que e para quê mudar) Ex: arr = array( array('oque', 'peloque'), array('isso', 'praisso') )
	function fileBody($file, $arr)
	{
		$fp = fopen($file, 'r');
		$content = fread($fp, filesize($file));
		fclose($fp);
		
		for ( $i=0; $i<count($arr); $i++ ) {
			$content = str_replace($arr[$i][0], $arr[$i][1], $content);
		}
		$this->corpo .= $content;
	}
	
	# Coloca um anexo
	function attach($file, $name)
	{
		$fp				= fopen($file, 'r');
		$content		= fread($fp, filesize($file));
		$encoded_attach	= chunk_split(base64_encode($content));
		fclose($fp);
		
		$this->anexos	.= "\n\n--".$this->boundary."\n";
		$this->anexos	.= "Content-Type: application/octet-stream;\n\tname=\"".$name."\"\n";
		$this->anexos	.= "Content-Transfer-Encoding: base64\n";
		$this->anexos	.= "Content-Disposition: attachment;\n\tfilename=\"".$name."\"\n\n";
		$this->anexos	.= "".$encoded_attach."\n\n";
	}
	
	# Envia
	function send($destinatario, $bcc='')
	{		
		if ( $this->tipo == 3 )	$this->corpo .= $this->anexos . "\n\n--".$this->boundary."--\n";
	
		if ( $bcc )	$this->header .= "Bcc: ". $bcc;		
		return mail($destinatario, $this->assunto, $this->corpo, $this->header);
	}
	
	
}
?>