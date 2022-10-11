<?php
$diretorio = "/var/spool/asterisk/monitor/2020/09/28/";
$arquivo  = $diretorio . $_GET["arquivo"];

$extensao = pathinfo($arquivo);
$bloquados = array('php', 'html', 'htm', 'asp');
$retorno = array();
$tipo = "";

$senha = 'dow';


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
                $retorno = array("status" => "error", "cause" => "Arquivo com extensão não permitida");
            }
        } else {
            $retorno = array("status" => "error", "cause" => "Arquivo não encontrado");
        }
    }
} else {
    $retorno = array("status" => "error", "cause" => "Link inválido: " . $arquivo);
}

echo $arquivo;
echo json_encode($retorno);


