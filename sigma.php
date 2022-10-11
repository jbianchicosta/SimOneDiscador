<?php
include("conexao_mssql.php");

//$sql = "select top 1 * from CHAMADA_EVENTO order by CD_CHAMADA_EVENTO desc";

 if ( isset($debug) && $debug ) {
    echo "sql: $sql<br>";
    }

    extract($_GET, EXTR_PREFIX_ALL, "tel");

  //  echo ($tel_TELEFONE);

//    $dados = consulta($sql);

if (strlen($tel_TELEFONE) == 10){
    $tel_TELEFONE_FIXO = $tel_TELEFONE;

    echo " Telefone Fixo " .$tel_TELEFONE_FIXO;

}
if (strlen($tel_TELEFONE) == 11){

    $tel_TELEFONE_CELULAR = str_pad($tel_TELEFONE, 3,"-",STR_PAD_RIGHT);;
    

    echo " Telefone Celular " .$tel_TELEFONE_CELULAR;

}
    
$sql = "select CD_CLIENTE,ID_EMPRESA,ID_CENTRAL,FONE1,FONE2 from dbcentral 

where fg_ativo=1 and ctrl_central=1 and fone1 like '%$tel_TELEFONE%' or (fone2 like '%$tel_TELEFONE%') ";

$dados = consulta($sql);

while ($linhas = registro($dados) ) {
      
//    echo($linhas['CD_CLIENTE']."<br />");

    $cod_cliente = $linhas['CD_CLIENTE'] ;
    /*foreach ($linhas as $campo => $valor){
      echo $campo.' '.$valor.'<br />';
     }*/

}

echo "Cliente encontrado ".$cod_cliente;

//print_r($dados);
/*
extract($dados, EXTR_PREFIX_ALL, "x");

echo $x_CD_CHAMADA_EVENTO, "\n";

    var_dump($dados);
    */

desconecta($con);
?>