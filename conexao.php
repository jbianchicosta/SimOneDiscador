<?php
//include_once("conexao_funcoes.php");

if ( !defined('MYSQL_ASSOC') ) { // versao das funcoes para o PHP 7
    include("mysqli_adapter.php");
}
//include_once("conexao_funcoes.php");
include_once("funcoes_banco.php");

$conn = false;

$base = "moniligacao";

$senha_mysql = 'one1837@2';

$conn = mysql_connect('localhost', 'root', $senha_mysql);

$titulo_pagina = 'Discador Moni';

        // Check connection
        if (!$conn) {
                //echo("Connection failed: " . mysqli_connect_error());
                $retorno = array("status" => "error", "cause" => "Banco de Dados nao encontrado, vamos criar!!! \n");
                //echo json_encode($retorno);
        }

        // Create database
        $sql = "CREATE DATABASE $base";
        if (mysql_query($conn, $sql)) {
                //echo "Database created successfully";
        } else {
                //echo "Error creating database: " . mysqli_error($conn) . "<BR>";
                //Die("<br>Sistema em manutenção !!! Desculpe o inconveniente...\n");
                //$retorno = array("status" => "error", "cause" => "Servidor de Banco em manutencao ou faltam as tabelas !!!");
                //echo json_encode($retorno);
                }

                //desconecta($conn);

        $conn = mysql_connect('localhost', 'root', $senha_mysql,'moniligacao');
                $sql_tabela = "CREATE TABLE IF NOT EXISTS LIGACOES (
                                        CD_CHAMADA int(5) NOT NULL auto_increment,
                                        NUM_RAMAL varchar(5) NOT NULL,
                                        NUM_TELEFONE varchar(15) NOT NULL,
                                        DT_INCLUSAO varchar(20) NOT NULL,
                                        DT_LIGACAO_EFETUADA varchar(20) NOT NULL,
                                        ARQUIVO varchar(100) NOT NULL,
                                        FG_LIGACAO_CONCLUIDA varchar(15) NOT NULL,
                                        CIDADE varchar(15) NOT NULL,
                                        UF varchar(4) NOT NULL,
                                        PRIMARY KEY  (CD_CHAMADA) )";

        if (mysql_query($conn, $sql_tabela)) {
                //echo "Tabela created successfully";
                } else {
                        //echo "Error creating table: " . mysqli_error($conn);
                }

        //desconecta($conn);

/*                      // inicio da criacao das tabelas
                        $sql_tabela = "CREATE TABLE IF NOT EXISTS LIGACOES (
                                        CD_CHAMADA int(5) NOT NULL auto_increment,
                                        NUM_RAMAL varchar(5) NOT NULL,
                                        NUM_TELEFONE varchar(15) NOT NULL,
                                        DT_INCLUSAO varchar(20) NOT NULL,
                                        DT_LIGACAO_EFETUADA varchar(20) NOT NULL,
                                        ARQUIVO varchar(100) NOT NULL,
                                        FG_LIGACAO_CONCLUIDA varchar(15) NOT NULL,
                                        CIDADE varchar(15) NOT NULL,
                                        UF varchar(4) NOT NULL,
                                        PRIMARY KEY  (CD_CHAMADA) )";
*/
