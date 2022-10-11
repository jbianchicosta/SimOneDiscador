<?php
include("conexao_mssql.php");

function TelefoneDDD($num) {

$n = "";
    for ( $x = 0; $x < strlen($num); $x++ ) {
	if ( substr($num,$x,1) >= "0" && substr($num,$x,1) <= "9" ) {
	    $n .= substr($num,$x,1);
	    }
	}
return $n;
    }

$numero = $argv[1];
$arquivo = $argv[2];

$sql = "select top 1 * from CHAMADA_EVENTO where NU_TELEFONE='$numero' order by CD_CHAMADA_EVENTO desc";

 if ( isset($debug) && $debug ) {
    echo "sql: $sql<br>";
    }

    $dados = consulta_registro_mssql($sql);

//extract($numero, EXTR_PREFIX_ALL, "n");
extract($dados, EXTR_PREFIX_ALL, "x");


 if ( $numero == TelefoneDDD($x_NU_TELEFONE) )
    {
      echo $numero, "\n";
      echo $x_NU_TELEFONE, "\n";
      echo $x_CD_CHAMADA_EVENTO;
      
      $update = "UPDATE CHAMADA_EVENTO set CD_CHAMADA='$arquivo' where CD_CHAMADA_EVENTO = '$x_CD_CHAMADA_EVENTO'";
      executa_mssql($update);
    }
    
 else
  {
    echo "Nada consta", "\n";
  }
//    var_dump($dados);

desconecta_mssql($con);
?>