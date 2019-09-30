<?php

include('./lib/phpwhois/whois.main.php');

$query = $_GET['q'];

?>
<html>
<head>
<title>WhoIs Lookup</title>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" type="text/css" href="./style/reset.css">
 <link rel="stylesheet" type="text/css" href="./style/simple/main.css">
</head>

<body>

<div id="page-wrapper">

<div class="search">
	<form action="<?=$_SERVER['PHP_SELF'];?>">
		<input type="text" name="q" class="searchbox" placeholder="Search" value="<?=$query;?>">
		<input type="submit" class="searchbutton" value="&#x1F50D;">
	</form>
</div>


	<?php
	if($query) {
		$query = trim($query);
		if(substr(strtolower($query), 0, 7) == "http://") $query = substr($query, 7);
		if(substr(strtolower($query), 0, 8) == "https://") $query = substr($query, 8);
		if(substr(strtolower($query), 0, 4) == "www.") $query = substr($query, 4);
		
		$whois = new Whois();
		$result = $whois->Lookup($query,false);
		
		echo "<div class=\"result\">\n";
		
			if ($whois->Query['status'] < 0){
				echo '<pre>	'.implode($whois->Query['errstr'],"\n").'</pre>';
			}
			else{	
				switch($whois->Query['tld']){
					case 'ip':
						echo '<pre>'.implode($result['rawdata'],"\n").'</pre>';
						break;
					case 'as':
						echo '<pre>'.implode($result['rawdata'],"\n").'</pre>';
						break;
					default:
						if($result['regrinfo']['registered'] == 'yes'){
							echo '<h1>'.$result['regrinfo']['domain']['name'].'</h1>';
							echo '<b>Registrar : '.$result['regyinfo']['registrar'].'</b><br />';
							echo '<b>Created : '.$result['regrinfo']['domain']['created'].'</b><br />';
							echo '<b>Changed : '.$result['regrinfo']['domain']['changed'].'</b><br />';
							echo '<b>Name Servers : '.implode($result['regrinfo']['domain']['nserver'],"<br />\n").'</b><br />';
						}
						else
							echo '<h1>THE DOMAIN '.$result['regrinfo']['domain']['name'].' IS AVAILABLE</h1>';
				}
			}
		
		echo "\n</div>\n";
		
		
		if(isset($_GET['d']) && $_GET['d'] == 'debug'){
			echo "<div class=\"result\"><pre>\n";
			print_r($result);
			echo "\n</pre></div>\n";
		}
		
		if(isset($_GET['d']) && $_GET['d'] == 'raw'){
			echo '<div class="result"><pre>'.implode($result['rawdata'],"\n").'</pre></div>';
		}
	}
	?>

</div>
</body>
</html>