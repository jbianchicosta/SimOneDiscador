<?php
if ( !isset($tipo_db) || empty($tipo_db) ):
	$tipo_db = "sqlsrv";	// sqlsrv // mssql
endif;
	
require_once("funcoes_" . $tipo_db . ".php");

/*
function __autoload($classe) { 
	if(strpos(strtolower($_SERVER["SystemRoot"]),"win" )!=0) {
		$barra = "\\";
	} else {
		$barra = "/";
	}
	$pasta = dirname(__FILE__);
	$pasta = substr($pasta,0,strlen($pasta)-3);
	require_once($pasta."Model$barra$classe.class.php");
}
*/	
function gravaLog($err){
	if(strpos(strtolower($_SERVER["SystemRoot"]),"win" )!=0) 
		$barra = "\\";
	else
		$barra = "/";
		
	$arq = fopen(dirname(__FILE__).$barra."sql.log", "a+");
	$nome_arq = end(explode("/",$_SERVER["SCRIPT_FILENAME"]));
	$esc = fwrite($arq, "[".date("d/m/Y H:i:s")."][".$nome_arq."]".$err.PHP_EOL);
	fclose($arq);   	
}

function gravaDebug($deb){
	if(strpos(strtolower($_SERVER["SystemRoot"]),"win" )!=0) 
		$barra = "\\";
	else
		$barra = "/";
	
	$arq = fopen(dirname(__FILE__).$barra."debug.log", "a+");
	$nome_arq = end(explode("/",$_SERVER["SCRIPT_FILENAME"]));
	$esc = fwrite($arq, "[".date("d/m/Y H:i:s")."][".$nome_arq."]".$deb.PHP_EOL);
	fclose($arq);   	
}	
?>
