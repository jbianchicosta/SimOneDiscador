<?php
@Header("Access-Control-Allow-Origin: *");
@Header("Content-Type: text/html; charset=iso-8859-1");
@Header("Cache-Control: no-cache, must-revalidate");
@Header("Pragma: no-cache");

//discador moni
include_once("conexao.php");
//include_once("conexao_mssql.php");

// conta do cliente e o ip do webservice sigma e soap
//$CONTA_CLIENTE = "740";

//$SERVIDOR_WS = "192.168.250.101";

$chave = "123456";

$key = $argv[1];

$RAMAL = $argv[2];

$TELEFONE = $argv[3];

$CIDADE = $argv[4];

$UF = $argv[5];

$AUXILIAR = $argv[6];

$ID_CENTRAL = $argv[7];

$ID_CLIENTE = $argv[8];
//http://192.168.250.222/?key=123456&RAMAL=[RAMAL]&TELEFONE=[TELEFONE]&cidade=[CIDADE]&uf=[UF]

extract($_GET, EXTR_PREFIX_ALL, "l");

if ($l_BaixarGravacao == "sigma" || $l_BaixarGravacao == "sim") {

	//$cdr_conn = mysql_connect('localhost', 'root', $senha_mysql, 'asteriskcdrdb');

	$cdr_sql = "select * from asteriskcdrdb.cdr where uniqueid='$l_ID' order by calldate desc limit 1";

	$cdr_dados = consulta_registro($cdr_sql, false);
	//echo $cdr_sql;

	if ($cdr_dados != NULL) {
		extract($cdr_dados, EXTR_PREFIX_ALL, "cdr");
		$retorno = array("status" => "ok", "cause" => "Id cdr da Ligacao: $cdr_uniqueid");
		echo json_encode($retorno);
		//echo $p_CD_CHAMADA;
		//echo json_encode($retorno); // resultado da ultima chamada aqui
		desconecta($conn);
	}
	if ($cdr_dados == NULL) {
		$sql = "select * from asteriskcdrdb.cdr where uniqueid = (select arquivo recordingfile from moniligacao.LIGACOES where cd_chamada='$l_ID' order by cd_chamada desc limit 1)";
		$grav_dados = consulta_registro($sql, false);
		extract($grav_dados, EXTR_PREFIX_ALL, "cdr");
		$retorno = array("status" => "ok", "cause" => "Id da Ligacao: $grav_dados_cd_chamada");
		echo json_encode($retorno);
		//echo $sql;
		//echo $p_CD_CHAMADA;
		//echo json_encode($retorno); // resultado da ultima chamada aqui
		desconecta($conn);
		//var_dump($grav_dados);
	}


	$diretorio = "/var/spool/asterisk/monitor/";

	//$arquivo  = $diretorio . $_GET["arquivo"];

	$extensao = pathinfo($arquivo);
	$bloquados = array('php', 'html', 'htm', 'asp');
	$retorno = array();
	$tipo = "";

	$senha = 'dow';

	$caminho = substr(strrchr($cdr_recordingfile, "/"), 1);
	//echo "<BR>";
	//echo "com barra : " . $caminho;

	if ($caminho == false) {
		$arquivo = $pasta = $diretorio . $dir = (substr(str_replace("-", "/", $cdr_calldate), 0, 10)) . '/' . $cdr_recordingfile;
	}
	if ($caminho == true) {
		$arquivo = $cdr_recordingfile;
	}

	if (count($extensao) > 0) {
		if (in_array($extensao['extension'], $bloquados)) {
			$retorno = array("status" => "error", "cause" => "Arquivo com extensão não permitida");
		} else {
			if (isset($arquivo) && file_exists($arquivo)) {
				switch (strtolower(substr(strrchr(basename($arquivo), "."), 1))) {
					case "pdf":
						$tipo = "application/pdf";
						break;
					case "gsm":
						$tipo = "application/gsm";
						break;
					case "wav":
						$tipo = "application/wav";
						break;
						//  case "exe": $tipo="application/octet-stream"; break;
						//  case "zip": $tipo="application/zip"; break;
						//  case "doc": $tipo="application/msword"; break;
						//  case "xls": $tipo="application/vnd.ms-excel"; break;
						//  case "ppt": $tipo="application/vnd.ms-powerpoint"; break;
						//  case "gif": $tipo="image/gif"; break;
						//  case "png": $tipo="image/png"; break;
						//  case "jpg": $tipo="image/jpg"; break;
						//  case "mp3": $tipo="audio/mpeg"; break;
				}

				if (!empty($tipo)) {
					header("Content-Type: " . $tipo);
					header("Content-Length: " . filesize($arquivo));
					header("Content-Disposition: attachment; filename=" . basename($arquivo));
					header('Content-Description: File Transfer');
					header('Content-Transfer-Encoding: binary');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Expires: 0');

					ob_end_clean();
					flush();
					readfile($arquivo);
				} else {
					$retorno = array("status" => "error", "cause" => "Arquivo com extensao nao permitida");
				}
			} else {
				$retorno = array("status" => "error", "cause" => "Arquivo nao encontrado");
			}
		}
	} else {
		$retorno = array("status" => "error", "cause" => "Link inválido: " . $arquivo);
	}

	if ($l_debug == true) {

		echo "<BR>";
		echo "BaixarGravacao" . "<BR>";
		echo "SQL : " . $cdr_sql . "<BR>";
		echo "SQL 2: " . $sql . "<BR>";
		echo "ID : " . $l_ID . "<BR>";
		echo "Data Gravacao : " . $cdr_calldate . "<BR>";
		echo "Diretorio de gravacao : " . $arquivo . "<BR>";
		echo "Arquivo da ligacao : " . $cdr_recordingfile . "<BR>";
		echo "Data : " . $cdr_calldate . "<BR>";
		echo "Pasta completa : " . $pasta . "<BR>";

		echo json_encode($retorno) . "<BR>";
	}
	desconecta($cdr_conn);
	//break;
	return;
}

if (strlen($l_sigma) == 7) { // recebe get do asterisk ?sigma=setor+conta_cliente
	$setor =  substr($l_sigma, 0, 3);
	//echo "</br>";
	$id_central = substr($l_sigma, 3, 7);

	$sql = "select TOP 1 c.ID_CENTRAL,h.CD_EVENTO,h.NU_AUXILIAR,h.CD_HISTORICO from HISTORICO h 
			inner join dbCENTRAL c
			ON h.CD_CLIENTE = c.CD_CLIENTE where h.NU_AUXILIAR=$setor and c.ID_CENTRAL='$id_central' order by DT_RECEBIDO desc";

	// $dados = free_mssql($sql);

	$dados = consulta_registro_mssql($sql);

	extract($dados, EXTR_PREFIX_ALL, "y");
	//print_r ($dados);


	echo $y_CD_HISTORICO;
	echo ($y_ID_CENTRAL);
	echo ($y_CD_EVENTO);
	echo ($l_gravacao);

	$data =	date("Y-m-d H:i:s");

	$sql = "INSERT INTO CHAMADA_EVENTO (CD_EVENTO,CD_CHAMADA,DT_CHAMADA,NU_TELEFONE) VALUES ('$y_CD_HISTORICO','$l_gravacao','$data','$l_interfone')";
	executa_mssql($sql);
} else if ($chave == $l_key & isset($l_RAMAL)) {

	$sql = "INSERT INTO moniligacao.LIGACOES (CD_CHAMADA, NUM_RAMAL , NUM_TELEFONE ,DT_INCLUSAO, DT_LIGACAO_EFETUADA, FG_LIGACAO_CONCLUIDA ,CIDADE , UF) VALUES (cd_chamada, '$l_RAMAL', '$l_TELEFONE', NOW() , '', '', '$l_CIDADE', '$l_UF' )";
	executa($sql, false);
	/*	echo "$l_key";	echo "$l_TELEFONE..."; */
	$sql = "SELECT CD_CHAMADA,NUM_TELEFONE,DT_INCLUSAO FROM moniligacao.LIGACOES WHERE NUM_RAMAL='$l_RAMAL' ORDER BY DT_INCLUSAO DESC LIMIT 1";
	$dados = consulta_registro($sql);

	if ($dados) {
		$webroot = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$ip_central = "http://". $_SERVER['SERVER_ADDR'] . $webroot . "?BaixarGravacao=sim&ID=";
		extract($dados, EXTR_PREFIX_ALL, "p");
		$retorno = array("status" => "ok", "cause" => "Id da Ligacao: $p_CD_CHAMADA", "urlAudio => $ip_central$p_CD_CHAMADA");
		echo json_encode($retorno);
		//echo $p_CD_CHAMADA;
		//echo json_encode($retorno); // resultado da ultima chamada aqui
		//desconecta($conn);
	}

	discar();
} else if ($l_sigma == "URA") {

	if ($l_sigma == "URA" && $l_id_central > 0) {

		//$sql = "select top 1 * from CHAMADA_EVENTO where NU_TELEFONE='$l_numero_discado' order by DT_CHAMADA desc";

		$sql = "select TOP 1 * FROM HISTORICO where CD_CLIENTE=$l_id_central order by DT_RECEBIDO desc";

		// $dados = free_mssql($sql);

		$dados = consulta_registro_mssql($sql);

		extract($dados, EXTR_PREFIX_ALL, "h");
		//print_r ($dados);

		echo $h_CD_EVENTO;
		echo ($h_CD_CHAMADA_EVENTO);
		echo ($h_CD_HISTORICO);
		echo ($l_gravacao);
		echo "If l_sigma e id_cliente : ";

		$data =	date("Y-m-d H:i:s");

		//$sql = "UPDATE CHAMADA_EVENTO set CD_CHAMADA='$l_gravacao' where CD_CHAMADA_EVENTO='$h_CD_CHAMADA_EVENTO'";
		$sql = "INSERT INTO CHAMADA_EVENTO (CD_EVENTO,CD_CHAMADA,DT_CHAMADA,NU_TELEFONE) VALUES ('$h_CD_HISTORICO','$l_gravacao','$data','$l_numero_discado')";
		executa_mssql($sql);
	}

	if ($l_sigma == "URA" && $l_id_central == NULL) {

		//$l_numero_discado = str_replace(array('\'', '"', ',', ';', '<', '>'), ' ', $l_TELEFONE_SIGMA);

		//$sql = "select top 5 * from CHAMADA_EVENTO order by DT_CHAMADA desc";
		$sql = "select top 1 * from CHAMADA_EVENTO where NU_TELEFONE='$l_numero_discado' order by DT_CHAMADA desc";

		// $dados = free_mssql($sql);

		//$dados = consulta_registro_mssql($sql);

		extract($dados, EXTR_PREFIX_ALL, "h");
		//print_r ($dados);

		/*
		$dados = consulta_mssql($sql);

		while ($linhas = registro_mssql($dados)) {
			//echo $linhas['NU_TELEFONE'];
			//echo $res = preg_replace("/[^0-9]/", "", $linhas['NU_TELEFONE'])."<BR>";
			$numero_formatado = preg_replace("/[^0-9]/", "", $linhas['NU_TELEFONE']);
			if ($numero_formatado == $l_numero_discado) {
				$numero_discado = $numero_formatado;
				$chamada_evento = $linhas['CD_CHAMADA_EVENTO'];
			}
		}
		*/

		//echo $h_cd_chamada_evento;
		echo ($l_gravacao);
		echo $l_numero_discado;
		//$res = preg_replace("/[^0-9]/", "", $h_NU_TELEFONE);
		//echo $res;

		$data =	date("Y-m-d H:i:s");

		//$sql = "UPDATE CHAMADA_EVENTO set CD_CHAMADA='$l_gravacao' where CD_CHAMADA_EVENTO='$chamada_evento'";
		$sql = "UPDATE CHAMADA_EVENTO set CD_CHAMADA='$l_gravacao' where CD_CHAMADA_EVENTO='$h_CD_CHAMADA_EVENTO'";
		//executa_mssql($sql);
	}

	//$l_numero_discado = str_replace(array('\'', '"', ',', ';', '<', '>'), ' ', $l_TELEFONE);


} else if ($l_SimCloud == "sim") {
	extract($_GET, EXTR_PREFIX_ALL, "cloud");

	$sql = "SELECT CD_CHAMADA FROM moniligacao.LIGACOES WHERE NUM_RAMAL='$cloud_RamalUsuario' (ARQUIVO IS NULL OR ARQUIVO LIKE '') ORDER BY DT_INCLUSAO DESC LIMIT 1";
	$cloud_cd_chamada = consulta_registro($sql, false);

	//echo json_encode($cloud_uniqueid);
	//echo "cloud_cd_chamada ".$sql."\n";
	//echo "cloud_cd_chamada ".$cloud_cd_chamada['CD_CHAMADA']."\n";
	//echo "cloud_AudioGravacao ".$cloud_AudioGravacao."\n";
	//echo "cloud_RamalUsuario ".$cloud_RamalUsuario."\n";
	$update = "UPDATE moniligacao.LIGACOES set ARQUIVO='$cloud_AudioGravacao' where CD_CHAMADA='$cloud_cd_chamada[CD_CHAMADA]' AND NUM_RAMAL='$cloud_RamalUsuario'";
	//echo "update ".$update;
	executa($update, false);

} else {
	echo ("Erro ao atualizar chamada ou faltando a chave key, ou ramal e telefone não informado!");
}

function discar()
{
	extract($_GET, EXTR_PREFIX_ALL, "l");
	//echo "criar arq discagem...\n";
	//CONFIGURACOES PARA O SISTEMA DE LIGACAO FIXO
	$dir = "/tmp/outgoing/";
	$spool_dir = "/var/spool/asterisk/outgoing/";
	$MaxRetries = "0";
	$RetryTime = "10";
	$wait_time = "40";
	$priority = "1";
	$archive = "yes";
	$context = "from-internal";

	//PREPARA O CONTEÚDO A SER GRAVADO
	//$conteudo = "Channel: Local/0164".$telefone."@outbound-allroutes".
	//$conteudo = "Channel: Local/".$empresa.$telefone."@outbound-allroutes".
	$canal = "Channel: Local/";
	//$canal = "Channel: SIP/";
	$canalsip = "Channel: SIP/";
	$conteudo = $canalsip . $l_RAMAL .
		//$conteudo = "Channel: Local/".$telefone."@outrt-019-Vono".
		"\nCallerID:" . $l_RAMAL .
		//"\nCallerID: 210 ".
		"\nMaxRetries: " . $MaxRetries .
		//"\nRetryTime: ".$RetryTime.
		"\nWaitTime: " . $wait_time .
		"\nContext: " . $context .
		"\nExtension: " . $l_TELEFONE .
		//"\nExtension: 210 ".
		"\nAccount: URA: $l_ID_CLIENTE" .
		"\nArchive:" . $archive .
		"\nData: URA " .
		//"\nSet: PassedInfo=" . $dddtel .
		"\nSet: PassedInfo=" .
		"\nPriority: " . $priority;
		"\nApplication: Dial " .

		//ARQUIVO TXT
		$arquivo = $spool_dir . $l_TELEFONE . ".call";

	//TENTA ABRIR O ARQUIVO TXT
	if (!$abrir = fopen($arquivo, "w")) {
		echo "Erro abrindo arquivo ($arquivo)";
		exit;
	}

	//ESCREVE NO ARQUIVO TXT
	if (!fwrite($abrir, $conteudo)) {
		print "Erro escrevendo no arquivo ($arquivo)";
		exit;
	}

	//FECHA O ARQUIVO DA PERMISSAO AO ARQUIVO E FECHA
	//shell_exec("sudo mv /var/spool/asterisk/outgoing/$ddd$telefone.call /home/asterisk/");
	//shell_exec("sudo chown asterisk:asterisk  /tmp/outgoing/$telefone.call");
	//shell_exec("sudo mv /tmp/outgoing/$telefone.call /var/spool/asterisk/outgoing/$telefone.call");

	fclose($abrir);
}
