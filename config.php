<?php
include("funcoes_mssql.php");

$ip_pabx = "localhost";
$db_servidor = "localhost";
$db_usuario = "root";
$db_senha = "one1837@2";
$db_banco ="moniligacao";
$db_tipo = "mysql";	// Drivers de Conexao Implementados: (mysql, mssql, sqlsrv)

@define('DEBUG_SQL', false);	// Se setado para true, grava todos os comandos SQL no arquivo /inc/debug.log

?>