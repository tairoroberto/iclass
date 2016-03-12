<?php
/**
 * Created by PhpStorm.
 * User: tairo
 * Date: 09/03/16
 * Time: 23:48
 */
class chaveacesso {
    var $db;

    //construtora
    function chaveacesso()
    {
        $this->db = $GLOBALS['db']; //a global db está declarada no config.inc.php
    }

    //insere um usuario do admin
    function insertChaveAcesso($chave) {

        $sql 	= "INSERT INTO ".PRE."chaves_acesso (
							valor_chave,
							ativa,
							data_cadastro)
					VALUES ('" .trim($chave['valor_chave']). "',
							" .$chave['ativa']. ",
							'" .$chave['data_cadastro']. "')";

        return $this->db->query($sql);
    }

    //pega uma chave de acesso pelo id
    function getChaveAcesso( $idChave ) {
        //perfis do usuário solicitado
        $sql = "SELECT ch.* FROM ".PRE."chaves_acesso ch WHERE ch.id_chave = ".$idChave." ";

        $queryChaves = $this->db->query($sql);

        return $this->db->fetchObject($queryChaves);
    }

    //pega uma chave de acesso pelo valor da chave
    function getChaveAcessoByValor($valor_chave ) {
        //perfis do usuário solicitado
        $sql = "SELECT ch.* FROM ".PRE."chaves_acesso ch WHERE ch.valor_chave = '".$valor_chave."' ";

        $queryChaves = $this->db->query($sql);

        return $this->db->fetchObject($queryChaves);
    }


    //deleta um usuario
    function delChaveAcesso( $idChave ) {
        $sql = "DELETE FROM ".PRE."chaves_acesso ch WHERE ch.id_chave = " .$idChave;
        $query = $this->db->query($sql);

        if( $this->db->affectedRows() )
            $_SESSION['msg'] = "Chave exclu&iacute;da com sucesso.";
        else
            $_SESSION['msg'] = "N&atilde;o foi poss&iacute;vel excluir a chave. Tente novamente.";

        return;
    }


    //lista os chaves inativas
    function listaChavesAcessoInativas() {
        $chaves = array();

        $sql	 	= "SELECT * FROM ".PRE."chaves_acesso WHERE ativa = 0";

        $query 		= $this->db->query($sql);

        while($chave = $this->db->fetchObject( $query )){
            $chaves[] = $chave;
        }

        return $chaves;
    }

    public function chavearUsuarios(){

        $usuariosSite = $this;
        $db = $GLOBALS['db'];

        $sql	 	= "SELECT * FROM ".PRE."usuario_site WHERE 1=1 ";
        $query 		= $db->query($sql);

        while( $usuario = $db->fetchObject( $query ) ){

            $token = substr(md5(uniqid(rand(), true)), 0, 10); // token de 10 digitos

            if($this->insertChaveAcesso(array('valor_chave' => $token, 'ativa' => 1, 'data_cadastro' => date('Y-m-d H:i:s')))){

                $db->query('Update iclass_usuario_site set id_chave_acesso = '.mysql_insert_id().' WHERE idUsuarioSite = '.$usuario->idUsuarioSite);
            }
        }
    }
}

?>