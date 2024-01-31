<?php

if ( !defined('MYSQL_ASSOC') ) { // vers�o da fun��es para o PHP 7
    
  define('MYSQL_ASSOC', 1);
  define('MYSQL_NUM', 2);
  define('MYSQL_BOTH', 3);

  $last_conn = null;

  if (!function_exists('sql_regcase')) {
    function sql_regcase($string,$encoding='auto'){
        $max=mb_strlen($string,$encoding);
        $ret='';
        for ($i = 0; $i < $max; $i++) {
          $char=mb_substr($string,$i,1,$encoding);
          $up=mb_strtoupper($char,$encoding);
          $low=mb_strtolower($char,$encoding);
          $ret.=($up!=$low)?'['.$up.$low.']' : $char;
        }
        return $ret;
    }
  }

  function mysql_connect($server, $username, $password) {
    global $last_conn;
    $last_conn = mysqli_connect("p:".$server, $username, $password, "");
    return $last_conn;
  }

  function mysql_pconnect($server, $username, $password) {
    global $last_conn;
    $last_conn = mysqli_connect("p:".$server, $username, $password, "");
    return $last_conn;
  }

  function mysql_select_db($db, $res = null) {
    global $last_conn;
    return $res ? $res->select_db($db) : $last_conn->select_db($db);
  }

  function mysql_real_escape_string($string, $res = null) {
    global $last_conn;
    return $res ? $res->real_escape_string($string) : $last_conn->real_escape_string($string);
  }

  function mysql_query($query, $res = null) {
    global $last_conn;
    return $res ? $res->query($query) : $last_conn->query($query);
  }

  function mysql_fetch_array($result, $type = MYSQL_BOTH) {
    if ($type == MYSQL_BOTH) {
      return mysqli_fetch_array($result, MYSQLI_BOTH);
    } else if($type == MYSQL_ASSOC) {
      return mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
      return mysqli_fetch_array($result, MYSQLI_NUM);
    }
  }

  function mysql_fetch_row($result) {
    return $result->fetch_row();
  }

  function mysql_num_rows($result) {
    return $result->num_rows;
  }

  function mysql_free_result($result) {
    if ($result) {
      @$result->free();
    }
  }

  function mysql_data_seek($result, $offset) {
    return $result->data_seek($offset);
  }

  function mysql_close($res = null) {
    //global $last_conn;
    //return $res ? $res->close() : $last_conn->close();
    return true;
  }

  function mysql_insert_id($res = null) {
    global $last_conn;
    return $res ? $res->insert_id : $last_conn->insert_id;
  }

  function mysql_error($res = null) {
    global $last_conn;
    return $res ? $res->error : $last_conn->error;
  }

  function mysql_errno($conex = null) {
    global $last_conn;
    return $conex ? $conex->errno : $last_conn->errno;
  }

  function mysql_affected_rows($conex = null) {
    global $last_conn;
    return $conex ? $conex->affected_rows : $last_conn->affected_rows;
  }

}