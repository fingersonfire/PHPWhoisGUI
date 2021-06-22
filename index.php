<?php

include('./vendor/phpwhois/whois.main.php');
include_once('./vendor/tinybutstrong/tbs_class.php');

// Functions
function cleanupQuery($query) {
	$queryclean = trim($query);
	if(substr(strtolower($queryclean), 0, 7) == "http://") $queryclean = substr($query, 7);
	if(substr(strtolower($queryclean), 0, 8) == "https://") $queryclean = substr($query, 8);
	if(substr(strtolower($queryclean), 0, 4) == "www.") $queryclean = substr($query, 4);
	
	return $queryclean;
} 

function implode_nserver($arraykv){
	return implode("\n", array_map(
		function ($v, $k) {
			if(is_array($v)){
				return $k.'[]='.implode('&'.$k.'[]=', $v);
			}else{
				return $k.' ('.$v.')';
			}
		}, 
		$arraykv, 
		array_keys($arraykv)
	));
}

// Global Variables
$templatelocation = './template/default/';
$showrawdata = array('ip', 'as');
$missingdatamessage = "Unknown";

// Initialsation 
$TBS = new clsTinyButStrong;


if(isset($_GET['q']) && $_GET['q'] != "") {

	$query = $_GET['q'];
	
	$query = cleanupQuery($query);	
	$whois = new Whois();
	$result = $whois->Lookup($query,false);
		
	if ($whois->Query['status'] == 'error'){
		$whoisError = implode("\n",$whois->Query['errstr']);
		$TBS->LoadTemplate($templatelocation.'showerror.html');
		$TBS->Show();
	}
	else{
		// Show RAW data based on global array
		if(in_array($whois->Query['tld'], $showrawdata)){
				$rawdata = implode("\n",$result['rawdata']);
				$TBS->LoadTemplate($templatelocation.'showrawdata.html');
				$TBS->Show();
		}
		else {

			if($result['regrinfo']['registered'] == 'yes'){
				$domainname = (isset($result['regrinfo']['domain']['name'])) ? $result['regrinfo']['domain']['name'] : $missingdatamessage;
				$registrar = (isset($result['regyinfo']['registrar'])) ? $result['regyinfo']['registrar'] : $missingdatamessage;
				$domaincreation = (isset($result['regrinfo']['domain']['created'])) ? $result['regrinfo']['domain']['created'] : $missingdatamessage;
				$domainupdate = (isset($result['regrinfo']['domain']['changed'])) ? $result['regrinfo']['domain']['changed'] : $missingdatamessage;
				$nameservers = (isset($result['regrinfo']['domain']['nserver'])) ? implode_nserver($result['regrinfo']['domain']['nserver']) : $missingdatamessage;
				$TBS->LoadTemplate($templatelocation.'showdomain.html');
				$TBS->Show();
			}
			else {
				$TBS->LoadTemplate($templatelocation.'showdomainavailable.html');
				$TBS->Show();
			}
		}
	}
}
else{
	$TBS->LoadTemplate($templatelocation.'index.html');
	$TBS->Show();
}


?>