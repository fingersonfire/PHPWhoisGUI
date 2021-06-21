<?php

include('./vendor/phpwhois/whois.main.php');
include_once('./vendor/tinybutstrong/tbs_class.php');

function cleanupQuery($query) {
	$queryclean = trim($query);
	if(substr(strtolower($queryclean), 0, 7) == "http://") $queryclean = substr($query, 7);
	if(substr(strtolower($queryclean), 0, 8) == "https://") $queryclean = substr($query, 8);
	if(substr(strtolower($queryclean), 0, 4) == "www.") $queryclean = substr($query, 4);
	
	return $queryclean;
} 

// Customization
$templatelocation = './template/default/';

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
		if($whois->Query['tld'] == 'ip' || $whois->Query['tld'] == 'as'){
				$rawdata = implode("\n",$result['rawdata']);
				$TBS->LoadTemplate($templatelocation.'showrawdata.html');
				$TBS->Show();
		}
		else {
			if($result['regrinfo']['registered'] == 'yes'){
				$domainname = $result['regrinfo']['domain']['name'];
				$registrar = $result['regyinfo']['registrar'];
				$domaincreation = $result['regrinfo']['domain']['created'];
				$domainupdate = $result['regrinfo']['domain']['changed'];
				$nameservers = implode("\n",$result['regrinfo']['domain']['nserver']);
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