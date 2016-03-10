<?
class dataBase
{
	var $db;
	var $servidor;
	var $login;
	var $senha;
	
	//conecta na base
	function conecta($servidor, $login, $senha)
	{
		$this->servidor = $servidor;
		$this->login 	= $login;
		$this->senha	= $senha;
		$conn = mysql_connect($this->servidor, $this->login, $this->senha);
		
		if( !$conn )
			die("Não foi possível se conectar à base.<br>Erro: " . mysql_error());
		else
			return;
	}
	
	//seleciona a base de dados
	function selectDb( $db )
	{
		$this->db = $db;
		$sel = mysql_select_db($this->db);
		
		if( !$sel )
			die("Não foi possível selecionar o banco de dados escolhido.<br>Erro: " . mysql_error());
		else
			return;
	}
	
	//faz uma query na base
	function query( $query, $debug = 0 )
	{
		if( $debug )
			die( "Debugando! Query: " . $query );
		else
		{
			mysql_query("SET SQL_BIG_SELECTS=1");
			$q = mysql_query($query);
			return $q;
			mysql_close();
		}
	}
	
	//retorna o número de linhas de uma query
	function numRows( $result )
	{
		return mysql_num_rows( $result );
	}
	
	//faz um fetch_row
	function fetchRow( $result )
	{
		return mysql_fetch_row( $result );
	}
	
	//faz um fetch array
	function fetchArray( $result )
	{
		return mysql_fetch_array( $result );
	}
	
	function fetchObject( $result )
	{
		return mysql_fetch_object( $result );
	}
	
	//retorna o número de linhas afetadas.
	function affectedRows()
	{
		return mysql_affected_rows();
	}
	
	//retorna o último id inserido na base, num campo auto_increment.
	function insertId()
	{
		return mysql_insert_id();
	}
}
?>