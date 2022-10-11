<?php
// Drivers de Conexão Implementados: (mysql, mssql, sqlsrv)
//$tipo_db = "sqlsrv";
$tipo_db = "mssql";
//$tipo_db = "mysql";

include("funcoes_banco.php");

$con = conecta_mssql("192.168.1.50", "sa", "Invi5040", "SIGMA90");	// Conexao ao Banco SQL do Sigma

if ( !$con ) {
    //$retorno = array("status" => "error", "cause" => "Erro de conexão ou base não encontrada !!!");
    //echo json_encode($retorno);
    echo "Erro de conexao ou base nao encontrada !!!";

    desconecta_mssql($con);
    exit;
    }
define('DEBUG_SQL', false);
?>
