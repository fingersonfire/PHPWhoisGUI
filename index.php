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
$query = $_GET['q'];

if(isset($query) && $query != "") {

	$query = cleanupQuery($query);	
	$whois = new Whois();
	$result = $whois->Lookup($query,false);
		
	if ($whois->Query['status'] == 'error'){
		$whoisError = implode($whois->Query['errstr'],"\n");
		$TBS->LoadTemplate($templatelocation.'showerror.html');
		$TBS->Show();
	}
	else{	
		if($whois->Query['tld'] == 'ip' || $whois->Query['tld'] == 'as'){
				$rawdata = implode($result['rawdata'],"\n");
				$TBS->LoadTemplate($templatelocation.'showrawdata.html');
				$TBS->Show();
		}
		else {
			if($result['regrinfo']['registered'] == 'yes'){
				$domainname = $result['regrinfo']['domain']['name'];
				$registrar = $result['regyinfo']['registrar'];
				$domaincreation = $result['regrinfo']['domain']['created'];
				$domainupdate = $result['regrinfo']['domain']['changed'];
				$nameservers = implode($result['regrinfo']['domain']['nserver'],"\n");
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