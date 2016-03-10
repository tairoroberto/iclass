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
    function insertChaveAcesso() {

        $sql 	= "INSERT INTO ".PRE."usuario (
							nomeUsuario,
							login,
							senha,
							isAdmin,
							dataCriacao)
					VALUES ('" . trim($_POST['nomeUsuario']) . "',
							'" . trim($_POST['login']) . "',
							'" . md5(trim($_POST['senha'])) . "',
							'" . (trim($_POST['isAdmin']) ? $_POST['isAdmin'] : "0") . "',
							NOW())";

        $query	= $this->db->query($sql);

        $_SESSION['msg'] = "Chave de acesso cdasrada com sucesso.";
        return 1;
    }

    //pega os perfis de um usuário
    function getChaveAcesso( $idChave ) {
        //perfis do usuário solicitado
        $sqlPerfisUsuarios = "SELECT p.* FROM ".PRE."perfil_usuario pu INNER JOIN ".PRE."perfil p ON p.idPerfil = pu.idPerfil WHERE pu.idUsuario = ".$idUsuario." ORDER BY p.nome";
        $queryPerfisUsuarios = $this->db->query($sqlPerfisUsuarios);
        while( $r = $this->db->fetchObject($queryPerfisUsuarios) )
            $linhas[] = $r;

        //todos os perfis
        $sqlPerfis = "SELECT * FROM ".PRE."perfil WHERE ativo = '1' ORDER BY nome";
        $queryPerfis = $this->db->query($sqlPerfis);

        $arrPerfis = array();
        while( $p = $this->db->fetchObject($queryPerfis) )
        {
            $arrPerfis["idPerfil"][] = $p->idPerfil;
            $arrPerfis["nome"][] = $p->nome;
            $arrPerfis["checked"][] = "";

            //verifica se o perfil em questão está selecionado para o usuário
            for( $i=0; $i<count($linhas); $i++ )
            {
                if( $p->idPerfil == $linhas[$i]->idPerfil )
                {
                    $arrPerfis["checked"][count($arrPerfis["checked"])-1] = "checked";
                    break;
                }
            }

        }
        return $arrPerfis;
    }


    //deleta um usuario
    function delChaveAcesso( $idChave ) {
        $sql = "DELETE FROM ".PRE."usuario WHERE idUsuario = " .$idUsuario;
        $query = $this->db->query($sql);

        if( $this->db->affectedRows() )
            $_SESSION['msg'] = "Usu&aacute;rio exclu&iacute;do com sucesso.";
        else
            $_SESSION['msg'] = "N&atilde;o foi poss&iacute;vel excluir o usu&aacute;rio. Tente novamente.";

        return;
    }


    //lista os usuários
    function listaChavesAcesso( $regPorPag ) {
        if( !isset($_GET['p']) )
            $p = 1;
        else
            $p = $_GET['p'];

        $start 		= ($p * $regPorPag) - $regPorPag;

        $sql	 	= "SELECT * FROM ".PRE."usuario WHERE 1=1 ";

        if( $_SESSION['fNomeUsuario'] != "" )
        {
            $sql .= "AND nomeUsuario LIKE '%". str_replace(' ', '%', $_SESSION['fNomeUsuario']) ."%'";
        }

        if( $_SESSION['fLogin'] != "" )
        {
            $sql .= "AND login LIKE '%". str_replace(' ', '%', $_SESSION['fLogin']) ."%'";
        }

        $orderBy = " ORDER BY nomeUsuario ASC ";

        $sql .= $orderBy;

        $tabela 	= paginacaoBar( $sql, $regPorPag, "listar.php?action=listar", $p );

        $sql		.= " LIMIT " . $start . ", " . $regPorPag;

        $query 		= $this->db->query($sql);

        while( $usuario = $this->db->fetchObject( $query ) )
            $r[] = $usuario;

        $grid = $this->montaGrid( $r );

        //grid com a paginacao
        $fullGrid = $grid . "<tr><td align='center' colspan='5'>" . $tabela . "</td></tr>";

        return $fullGrid;
    }
}

?>