<?
class ftp
{
	var $server, $user, $pass;
	var $conn;

	function ftp($server, $user, $pass)
	{
		$this->server	= $server;
		$this->user		= $user;
		$this->pass		= $pass;
	}

	# Conecta com o servidor
	function connect()
	{
		if ( !$this->conn = @ftp_connect($this->server) )
			return 0;
		if ( !@ftp_login($this->conn, $this->user, $this->pass) )	
			return 0;
		
		return 1;
	}
		
	# Retorna uma array com os nomes dos arquivos e diretórios
	function ls($dir)
	{
		return @ftp_nlist($this->conn, $dir);
	}
	
	# Descobre o nome do diretório atual
	function pwd()
	{
		return @ftp_pwd($this->conn);
	}
	
	# Faz o upload de um arquivo
	function upload($origem, $destino)
	{	
		#Se a extensão está em caixa alta, passa para caixa baixa
		$tmp = explode('.', $destino);
		$tmp[count($tmp)-1] = strtolower($tmp[count($tmp)-1]);
		$destino = implode('.', $tmp);
	
		if ( @ftp_put($this->conn, $destino, $origem, FTP_BINARY) )	
			return 1;
		else
			return 0;
	}
	
	# Exclui um arquivo no servidor
	function del( $path )
	{
		if ( @ftp_delete($this->conn, $path ) )	
			return 1;
		else
			return 0;
	}
	
	# Fecha a conexão
	function close()
	{
		@ftp_close($this->conn);
	}
}
?>