<?php

function num_registros($rs) {
	return @mysql_num_rows($rs);
}

function num_colunas($rs) {
	return mysql_num_fields($rs);
}

function consulta($sql, $msg = false, $descDebug = '') {
	
	if(DEBUG_SQL) gravaDebug($sql); 	

	if ( isset($_REQUEST['debug']) ) {
		$inicio = microtime(true);
	}

	if ( isset($_REQUEST['debug']) ) {
		echo "<br><br><b><font color='red'>$descDebug: </font></b><br>  ( $sql )";
	}

	$r = @mysql_query($sql);
	
	if ( isset($_REQUEST['debug']) ) {
		$tempo = round(microtime(true) - $inicio, 5);
		echo "<br><br>  Tempo: $tempo segundos";
	}
	
	if( !$r ){ 
		gravaLog(db_erro());
		if($msg) die("Erro no Banco de Dados:<br><br>".db_erro()); 
	}
	
	return $r;
}

function consulta_registro($sql, $msg = false, $descDebug = '') {

	$rs = consulta($sql, $msg, $descDebug);
	
	if ( $rs ) 
	{
		$dados = registroAssoc($rs);
		free($rs);
		return $dados;
	} 
	else 
	{
		if ( isset($_REQUEST['debug']) ) {
			ver2("Erro: ".db_erro());
		}
		return false;
	}
}

function consulta_campo($sql, $campo = 0, $msg = false, $descDebug = '') {

	$rs = consulta($sql, $msg, $descDebug);
	if ( $rs ) {
		$dados = registro($rs);
		free($rs);
		return $dados[$campo];
	} else {
		if ( isset($_REQUEST['debug']) ) {
			ver2("Erro: ".db_erro());
		}
		return null;
	}
}

function desconecta($lnk = null) {
	if ( $lnk == null ) {
		@mysql_close($GLOBALS["_conexao"]);
	} else {
		@mysql_close($lnk);
	}
}

function executa($sql, $msgErro = false, $descDebug = '') {
	
	if(DEBUG_SQL) gravaDebug($sql);
	
	if ( isset($_REQUEST['debug']) ) {
		echo "<br><br><b><font color='red'>$descDebug: </font></b><br>  ( $sql )";
		$inicio = microtime(true);
	}
	
	$res = @mysql_query($sql);
	if ( $res ) {
		if ( isset($_REQUEST['debug']) ) {
			$tempo = round(microtime(true) - $inicio, 5);
			echo "<br><br>  Tempo: $tempo segundos";
		}		
	} else {
		gravaLog(db_erro());
		if ( isset($_REQUEST['debug']) ) {
			ver2("Erro: ".db_erro());
		}
		elseif ( $msgErro ) {
			die("Erro no Banco de Dados:<br><br>".db_erro()); 
		}
	}
	return $res;
}

function objeto($res) {
	$reg = mysql_fetch_object($res);
	if ( isset($_REQUEST['debug']) ) {
		ver2($reg);
	}
	return $reg;
}

function registro($res) {
	$reg = mysql_fetch_array($res);
	if ( isset($_REQUEST['debug']) ) {
		ver2($reg);
	}
	return $reg;
}

function registroCampo($res) {
	$reg = mysql_fetch_assoc($res);
	if ( isset($_REQUEST['debug']) ) {
		ver2($reg);
	}
	return $reg;
}

function registroAssoc($res) {
	if ( $res ) {
		$reg = @mysql_fetch_array($res, MYSQL_ASSOC);
		if ( isset($_REQUEST['debug']) ) {
			ver2($reg);
		}
	} else {
		$reg = null;
	}
	return $reg;
}

function volta_primeiro_registro($res) {
	mysql_data_seek( $res, 0 );	
}

function linha($res) {
	$reg = mysql_fetch_row($res);
	if ( isset($_REQUEST['debug']) ) {
		ver2($reg);
	}
	return $reg;
}

function free($res) {
	return @mysql_free_result($res);
}

function ultimo_id() {
	return mysql_insert_id();
}

function db_erro() {
	return mysql_error();
}

function registros_afetados() {
	return mysql_affected_rows();
}

function conecta($serv, $_user, $pass, $banco) {
	$con = @mysql_connect($serv, $_user, $pass);
	If ( $con ) {
		if ( !mysql_select_db($banco, $con) ) {
			include("criar_banco.php");
			//$sql = "CREATE DATABASE moniligacao";		
		}
	} else {
		Die("<br>Erros de Conexão com o Banco de Dados !\n<br>".db_erro()); // : $banco ...
	}
	$GLOBALS["_conexao"] = $con;
	return $con;
}

?>