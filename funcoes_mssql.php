<?php
//@dl('php_mssql.dll');

function num_registros_mssql($rs) {
	return @mssql_num_rows($rs);	
}

function num_colunas_mssql($rs) {
	return mssql_num_fields_mssql($rs);
}

function conecta_mssql($serv, $user, $pass, $banco) {
	//include("config.php");
	$con = mssql_connect($serv, $user, $pass);
	If ( $con ) {
	    if ( @mssql_select_db("[$banco]", $con) ) {
			 mssql_query("set DATEFORMAT Ymd", $con);
		} else {
		    Die("<br>Erro ao selecionar o Banco de Dados: [$banco] ...\n<br>".db_erro($con));
		}
	} else {
	    Die("<br>Erro de Conexao com o Banco de Dados...\n<br>".db_erro($con));
	}
	$GLOBALS["_conexao"] = $con;
	return $con;
}

function consulta_mssql($sql, $msgErro = false) {
	$sql = strtr($sql, array('Date_Format(' => 'dbo.Date_Format(', 'now()' => 'getDate()', 'uuid()' => 'convert(varchar(36),newid())') );
	
	if(DEBUG_SQL) gravaDebug($sql);
	
	if ( $msgErro ) {
		$r = @mssql_query($sql, $GLOBALS["_conexao"]);
	} else {
		$r = @mssql_query($sql, $GLOBALS["_conexao"]);
	}
	if ( !$r and $msgErro ):
		gravaLog(db_erro_mssql());
		 //die("Erro executando: ($sql) <br><br>".db_erro());
		 die("Erro selecionando registros do banco de dados.<br>".db_erro());
	endif;
	return $r;
}

function consulta_registro_mssql($sql, $msg = false) {

	if(DEBUG_SQL) gravaDebug($sql);
	
	$rs = consulta_mssql($sql, $msg);
	if ( $rs ) {
		$dados = registro_mssql($rs);
		free_mssql($rs);
		return $dados;
	} else {
		return false;
	}
}

function consulta_campo_mssql($sql, $campo = 0, $msg = false) {
	$rs = consulta_mssql($sql, $msg);
	if ( $rs ) {
		$dados = registro($rs);
		free_mssql($rs);
		return $dados[$campo];
	} else {
		return null;
	}
}

function desconecta_mssql($lnk = null) {
	if ( $lnk == null ) {
		@mssql_close($GLOBALS["_conexao"]);
	} else {
		@mssql_close($lnk);
	}
}

function executa_mssql($sql, $msgErro = true) {
	$sql = strtr($sql, array('Date_Format(' => 'dbo.Date_Format(', 'now()' => 'getDate()', 'uuid()' => 'convert(varchar(36),newid())'));
	//
	
	if(DEBUG_SQL) gravaDebug($sql);
	
	$res = mssql_query("set DATEFORMAT Ymd", $GLOBALS["_conexao"]);
	//	
	$res = mssql_query($sql, $GLOBALS["_conexao"]);
	if ( !$res and $msgErro ):
		gravaLog(db_erro());
		 die("Erro executando: $sql<br><br>".db_erro());
	endif;
	return $res;
}

function objeto_mssql($res) {
	$reg = mssql_fetch_object($res);
	return $reg;
}

function registro_mssql($res) {
	$reg = @mssql_fetch_array($res);
	return $reg;
}

function registroAssoc_mssql($res) {
	$reg = @mssql_fetch_array($res, MSSQL_ASSOC);
	return $reg;
}

function volta_primeiro_registro_mssql($res) {
	mssql_data_seek( $res, 0 );	
}

function linha_mssql($res) {
	$reg = mssql_fetch_row($res);
	return $reg;
}

function free_mssql($res) {
	return mssql_free_result($res);
}

function ultimo_id_mssql() {
	$rs = consulta("select SCOPE_IDENTITY() AS id");
	return mssql_result($rs, 0, 'id');	//mssql_insert_id();
}

function db_erro_mssql($res = null) {
	return mssql_get_last_message();
}

function registros_afetados_mssql() {
	return mssql_affected_rows();
}
?>
