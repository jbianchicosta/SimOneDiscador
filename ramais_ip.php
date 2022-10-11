<?php

/**
 * @author ronny
 * @adaptado Juliano
 * @copyright 2017
 */

header('Content-Type: json; charset=utf-8');
//Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

$ramais = lista_ramais();

echo json_encode($ramais);


function lista_ramais() {

	//$result = shell_exec("sudo asterisk -rx 'sip show peers' '{print $1\":\"$2\":\"$3\":\"$4\":\"$5\":\"$6\":\"$7\":\"$8\":\"$9}' | grep ms | sort -n");

	$result = shell_exec("asterisk -rx 'sip show peers' | awk ' {print $1\":\"$2\":\"$3\":\"$4\":\"$5\":\"$6\":\"$7\":\"$8\":\"$9} ' | sort -n");

	//mod para verificar o status do usuario no outro servidor tb
    //	$result2 = shell_exec("sudo ssh 201.76.44.83 -p 142 \"asterisk -rx 'sip show peers'\" | awk ' {print $1\":\"$2\":\"$3\":\"$4\":\"$5\":\"$6\":\"$7\":\"$8\":\"$9} ' | sort -n");

	// Ler também os ramais IAX2
//  $result2 = shell_exec("asterisk -rx 'iax2 show peers' | awk ' {print $1\":\"$2\":\"$3\":\"$4\":\"$5\":\"$6\":\"$7\":\"$8\":\"$9} ' | sort -n");
//  $result2 = strtr($result2, array(":ms)" => " ms)"));
	$result2 = array();

	$resultado = array();
    
    $num_server = "1";

	for ( $j = 1; $j <= 2; $j ++ ) {

		if ( $j == 1 ) {
			$arr = explode("\n",$result);
            $tipo = "s";    // sip
		} else {
			$arr = explode("\n",$result2);
            $tipo = "i";    // iax2
		}

		foreach($arr as $temp) {

			$linha = explode(":",$temp);
            
            //Name/username:Host:Dyn:Forcerport:ACL:Port:Status
            $username = $linha[0];
            $ip = $linha[1];
            $porta = $linha[6];
            $status = $linha[7]." ".$linha[8];
            /*
            if ($linha[2]=='(D)') {
				$username=$linha[0];
				$ip=$linha[1];
				$porta=$linha[4];
				$status=trim($linha[5])." ".$linha[6];
			} elseif ($linha[2]=='D' && $linha[3]=='N' ) {
				$username=$linha[0];
				$ip=$linha[1];
				$porta=$linha[4];
				$status=$linha[5]." ".$linha[6]." ".$linha[7];
			} elseif ($linha[2]!='D') {
				if ($linha[2]!='N') {
					$username=$linha[0];
					$ip=$linha[1];
					$porta=$linha[2];
					$status=$linha[3]." ".$linha[4]." ".$linha[5];
				} else {
					$username=$linha[0];
					$ip=$linha[1];
					$porta=$linha[3];
					$status=$linha[4]." ".$linha[5]." ".$linha[6];
				}
			} elseif ($linha[2]=='D') {
				if ($linha[3]!='N') {
					$username=$linha[0];
					$ip=$linha[1];
					$porta=$linha[3];
					$status=$linha[4]." ".$linha[5]." ".$linha[6];
				}
			}
            */
            
			if($username=='Name/username') continue;
			if($ip=='(Unspecified)') continue;

			if(!is_numeric($porta)) continue;
            
			if ( !( strpos($username,"/") === false ) ) {
                $temp2 = explode("/",$username);
                $username = $temp2[0];
            }

            if(!is_numeric($username)) continue;

			if ( SubStr($status,0,2) == "OK" ) {
				//$aux = array("username" => $username, "ip" => "$ip", "porta" => "$porta", "status" => "$status" . "$num_server");
				$resultado[] = array("ramal" => $username, "ip" => "$ip" );   //, "tp" => $tipo);

				//$resultado[ array("ip" => "$ip")]; 
				json_encode($resultado);
				//$resultado['ramais'][] = intval($username);
			}

		}

	}

	return ($resultado);
}

function status_ramal($ramal, $lst) {
    if ( isset($lst[$ramal]) ) {
        $temp = $lst[$ramal];
		if ( SubStr($temp["status"],0,2) == "OK" ) {
			return $temp["ip"] . ":" . $temp["porta"] . " " . StrTr($temp["status"], Array("OK " => ""));
		} else {
			return "";
		}
    }
	return "";
}
?>