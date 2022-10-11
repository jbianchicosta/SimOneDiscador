<?php
function num_registros($rs) {
	return mysql_num_rows($rs);
}

function num_colunas($rs) {
	return mysql_num_fields($rs);
}

function consulta($conn, $sql, $msg = false) {
	$r = mysqli_query($conn, $sql) or ( $msg ? die("Erro !!!<br><br>".mysqli_error()) : false );
	return $r;
}

function consulta_registro($sql, $msg = false) {
	$rs = consulta($sql, $msg);
	if ( $rs ) {
		$dados = registro($rs);
		free($rs);
		return $dados;
	} else {
		return false;
	}
}

function consulta_campo($sql, $campo = 0, $msg = false) {
	$rs = consulta($sql, $msg);
	if ( $rs ) {
		$dados = registro($rs);
		free($rs);
		return $dados[$campo];
	} else {
		return null;
	}
}

function desconecta($conn) {
	return mysqli_close($conn);
}

function executa($conn, $sql, $msgErro = false) {
	$res = mysqli_query($conn, $sql);
	if ( !$res and $msgErro ):
		 die("Erro !!: <br><br>".mysqli_error()); // $sql
	endif;
	return $res;
}

function registro($res, $tipo_resultado = MYSQL_BOTH) {
	if ( $res ) {
		$reg = mysqli_fetch_array($res, $tipo_resultado);
	} else {
		$reg = null;
	}
	return $reg;
}
function registroAssoc($conn, $res, $tipo_resultado = MYSQL_ASSOC) {
	if ( $res ) {
		$reg = mysqli_fetch_array($conn, $res, $tipo_resultado);
	} else {
		$reg = null;
	}
	return $reg;
}

function linha($res) {
	$reg = mysqli_fetch_row($res);
	return $reg;
}

function free($res) {
	return mysqli_free_result($res);
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
?>