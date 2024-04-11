<?php
//$_GET['url'] = 'https://janwillemstegink.nl';

if (!empty($_GET['url']))	{
	if (strlen($_GET['url']))	{
		$domain = str_replace('http://','', trim($_GET['url']));
		$domain = str_replace('https://','', $domain);
		$domain = str_replace('www.','', $domain);
		$strpos = mb_strpos($domain, '/');
		if ($strpos)	{
			$domain = mb_substr($domain, 0, $strpos);
		}
		$strpos = mb_strpos($domain, ':');
		if ($strpos)	{
			$domain = mb_substr($domain, 0, $strpos);
		}
		echo write_file($domain);
		die();
	}
	else	{	
		die("No domain name is filled as input");	
	}
}
else	{	
	die("No domain name variable as input");
}

function write_file($inputdomain)	{
	
$DNS_CNAME = '';
$array = dns_get_record($inputdomain, DNS_CNAME);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'target') {
			$DNS_CNAME .= $value2 . '.<br />';
		}
	}
}
if (strlen($DNS_CNAME))	{
	$pre = '(';
	$post = ')';
}
else	{
	$pre = '';
	$post = '';
}
$array = dns_get_record($inputdomain, DNS_A);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ip')	{
			$DNS_CNAME .= $pre.$value2.': '.gethostbyaddr($value2).$post.'<br />';	
		}	
	}
}
$array = dns_get_record($inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$DNS_CNAME .= $pre.$value2.': '.gethostbyaddr($value2).$post.'<br />';
		}	
	}
}
$DNS_CNAME_www = '';	
$array = dns_get_record('www.'.$inputdomain, DNS_CNAME);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'target') {
			$DNS_CNAME_www .= $value2 . '.<br />';
		}
	}
}
if (strlen($DNS_CNAME_www))	{
	$pre = '(';
	$post = ')';
}
else	{
	$pre = '';
	$post = '';
}	
$array = dns_get_record('www.'.$inputdomain, DNS_A);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ip') {
			$DNS_CNAME_www .= $pre.$value2.': '.gethostbyaddr($value2).$post.'<br />';	
		}
	}
}	
$array = dns_get_record('www.'.$inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$DNS_CNAME_www .= $pre.$value2.': '.gethostbyaddr($value2).$post.'<br />';
		}
	}
}
if (strlen($DNS_CNAME))	{
	$DNS_MX = '';
	$array = dns_get_record($inputdomain, DNS_MX);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'pri') {
				$DNS_MX .= 'priority target: '. $value2 . ' ';
			}	
			elseif ($key2 == 'target') {
				$DNS_MX .= $value2 . '.<br />';
				$DNS_MX .= get_mx_ips($value2);
			}	
		}
	}
}
else	{
	$DNS_MX = 'not applicable';	
}
if (strlen($DNS_CNAME_www))	{	
	$DNS_MX_www = '';	
	$array = dns_get_record('www.'.$inputdomain, DNS_MX);		
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'pri') {
				$DNS_MX_www .= 'priority target: '. $value2 . ' ';
			}	
			elseif ($key2 == 'target') {
				$DNS_MX_www .= $value2 . '.<br />';
				$DNS_MX_www .= get_mx_ips($value2);
			}
		}
	}	
}
else	{
	$DNS_MX_www = 'not applicable';	
}

if (strlen($DNS_CNAME))	{
	$DNS_TXT = '';	
	$array = dns_get_record($inputdomain, DNS_TXT);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
        	if ($key2 == 'txt') {
				$DNS_TXT .= $value2 . '<br />';
			}	
        }
    }
}
else	{
	$DNS_TXT = 'not applicable';	
}
if (strlen($DNS_CNAME_www))	{
	$DNS_TXT_www = '';	
	$array = dns_get_record('www.'.$inputdomain, DNS_TXT);		
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
        	if ($key2 == 'txt') {
				$DNS_TXT_www .= $value2 . '<br />';
			}	
        }
    }
}
else	{
	$DNS_TXT_www = 'not applicable';	
}
	
$security_txt_url_legacy = 'no legacy file';	
if (strlen($DNS_CNAME))	{
	$security_txt_url_legacy = 'https://'.$inputdomain.'/security.txt';
	$security_txt_legacy = nl2br(file_get_contents($security_txt_url_legacy));
	if (!strlen($security_txt_legacy))	{
		$security_txt_url_legacy = '<a href="https://'.$inputdomain.'/security.txt" target="_blank">https://'.$inputdomain.'/security.txt</a>';	
	}
}
$security_txt_url_www_legacy = 'no legacy file';	
if (strlen($DNS_CNAME_www))	{	
	$security_txt_url_www_legacy = 'https://www.'.$inputdomain.'/security.txt';
	$security_txt_www_legacy = nl2br(file_get_contents($security_txt_url_www_legacy));
	if (!strlen($security_txt_www_legacy))	{
		$security_txt_url_www_legacy = '<a href="https://www.'.$inputdomain.'/security.txt" target="_blank">https://www.'.$inputdomain.'/security.txt</a>';	
	}	
}	
	
$security_txt_url_site = 'not found';	
if (strlen($DNS_CNAME))	{
	$security_txt_url_site = 'https://'.$inputdomain.'/.well-known/security.txt';
	$security_txt_site = nl2br(file_get_contents($security_txt_url_site));
	if (!strlen($security_txt_site))	{
		$security_txt_url_site = '<a href="https://'.$inputdomain.'/.well-known/security.txt" target="_blank">https://'.$inputdomain.'/.well-known/security.txt</a>';	
	}
}
$security_txt_url_www_site = 'not found';	
if (strlen($DNS_CNAME_www))	{	
	$security_txt_url_www_site = 'https://www.'.$inputdomain.'/.well-known/security.txt';
	$security_txt_www_site = nl2br(file_get_contents($security_txt_url_www_site));
	if (!strlen($security_txt_www_site))	{
		$security_txt_url_www_site = '<a href="https://www.'.$inputdomain.'/.well-known/security.txt" target="_blank">https://www.'.$inputdomain.'/.well-known/security.txt</a>';	
	}	
}	
$ch = curl_init();	
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
$security_txt_url_effective = 'not found';
if (strlen($DNS_CNAME))	{	
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain.'/.well-known/security.txt'); 
	$html = curl_exec($ch);   
	$security_txt_url_effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	$security_txt_effective = nl2br(file_get_contents($security_txt_url_effective));
	if (!strlen($security_txt_effective))	{
		$security_txt_url_effective = '<a href="'.$security_txt_url_effective.'" target="_blank">'.$security_txt_url_effective.'</a>';	
	}
}
$security_txt_url_www_effective = 'not found';
if (strlen($DNS_CNAME_www))	{
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain.'/.well-known/security.txt'); 
	$html = curl_exec($ch); 
	$security_txt_url_www_effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	$security_txt_www_effective = nl2br(file_get_contents($security_txt_url_www_effective));
	if (!strlen($security_txt_www_effective))	{
		$security_txt_url_www_effective = '<a href="'.$security_txt_url_www_effective.'" target="_blank">'.$security_txt_url_www_effective.'</a>';
	}
}
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE); 	
curl_setopt($ch, CURLOPT_HEADER, true);
if (strlen($DNS_CNAME))	{	
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain);	
	$curl_server_headers = curl_exec($ch);
	$arr_server_headers = explode (",", $curl_server_headers);
	$server_headers = '';
	foreach($arr_server_headers as $key1 => $value1) {
		$server_headers .= $key1 . ':' . $value1 . '<br />';
	}
	$arr_transfer_information = curl_getinfo($ch);
	$transfer_information = '';	
	foreach($arr_transfer_information as $key1 => $value1) {
		$transfer_information .= $key1 . ': ' . $value1 . '<br />';	
	}
}
else	{
	$server_headers = 'not applicable';	
	$transfer_information = 'not applicable';	
}	

if (strlen($DNS_CNAME_www))	{	
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain);
	$curl_server_headers_www = curl_exec($ch);
	$arr_server_headers_www = explode (",", $curl_server_headers_www);
	$server_headers_www = '';	
	foreach($arr_server_headers_www as $key1 => $value1) {
		$server_headers_www .= $key1 . ': ' . $value1 . '<br />';		
	}
	$arr_transfer_information_www = curl_getinfo($ch);
	$transfer_information_www = '';	
	foreach($arr_transfer_information_www as $key1 => $value1) {
		$transfer_information_www .= $key1 . ': ' . $value1 . '<br />';
	}
}
else	{
	$server_headers_www = 'not applicable';	
	$transfer_information_www = 'not applicable';	
}	
	
curl_close($ch);	

$doc = new DOMDocument("1.0", "UTF-8");
$doc->xmlStandalone = true;	
$doc->formatOutput = true;		
	
$domains = $doc->createElement("domains");
$doc->appendChild($domains);
	
$domain = $doc->createElement("domain");	
$domains->appendChild($domain);
	
$domain->setAttribute("item", $inputdomain);

$domain_DNS_CNAME = $doc->createElement("DNS_CNAME");
$domain_DNS_CNAME->appendChild($doc->createCDATASection($DNS_CNAME));		
$domain->appendChild($domain_DNS_CNAME);	
	
$domain_DNS_CNAME_www = $doc->createElement("DNS_CNAME_www");
$domain_DNS_CNAME_www->appendChild($doc->createCDATASection($DNS_CNAME_www));		
$domain->appendChild($domain_DNS_CNAME_www);
	
$domain_DNS_MX = $doc->createElement("DNS_MX");
$domain_DNS_MX->appendChild($doc->createCDATASection($DNS_MX));		
$domain->appendChild($domain_DNS_MX);	
	
$domain_DNS_MX_www = $doc->createElement("DNS_MX_www");
$domain_DNS_MX_www->appendChild($doc->createCDATASection($DNS_MX_www));		
$domain->appendChild($domain_DNS_MX_www);	
	
$domain_DNS_TXT = $doc->createElement("DNS_TXT");
$domain_DNS_TXT->appendChild($doc->createCDATASection($DNS_TXT));		
$domain->appendChild($domain_DNS_TXT);	
	
$domain_DNS_TXT_www = $doc->createElement("DNS_TXT_www");
$domain_DNS_TXT_www->appendChild($doc->createCDATASection($DNS_TXT_www));		
$domain->appendChild($domain_DNS_TXT_www);
	
$domain_security_txt_url_legacy = $doc->createElement("security_txt_url_legacy");
$domain_security_txt_url_legacy->appendChild($doc->createCDATASection($security_txt_url_legacy));		
$domain->appendChild($domain_security_txt_url_legacy);	
	
$domain_security_txt_url_www_legacy = $doc->createElement("security_txt_url_www_legacy");
$domain_security_txt_url_www_legacy->appendChild($doc->createCDATASection($security_txt_url_www_legacy));		
$domain->appendChild($domain_security_txt_url_www_legacy);	
	
$domain_security_txt_url_site = $doc->createElement("security_txt_url_site");
$domain_security_txt_url_site->appendChild($doc->createCDATASection($security_txt_url_site));		
$domain->appendChild($domain_security_txt_url_site);	
	
$domain_security_txt_url_www_site = $doc->createElement("security_txt_url_www_site");
$domain_security_txt_url_www_site->appendChild($doc->createCDATASection($security_txt_url_www_site));		
$domain->appendChild($domain_security_txt_url_www_site);
	
$domain_security_txt_url_effective = $doc->createElement("security_txt_url_effective");
$domain_security_txt_url_effective->appendChild($doc->createCDATASection($security_txt_url_effective));		
$domain->appendChild($domain_security_txt_url_effective);	
	
$domain_security_txt_url_www_effective = $doc->createElement("security_txt_url_www_effective");
$domain_security_txt_url_www_effective->appendChild($doc->createCDATASection($security_txt_url_www_effective));		
$domain->appendChild($domain_security_txt_url_www_effective);	
	
$domain_security_txt_legacy = $doc->createElement("security_txt_legacy");
$domain_security_txt_legacy->appendChild($doc->createCDATASection($security_txt_legacy));		
$domain->appendChild($domain_security_txt_legacy);

$domain_security_txt_www_legacy = $doc->createElement("security_txt_www_legacy");
$domain_security_txt_www_legacy->appendChild($doc->createCDATASection($security_txt_www_legacy));		
$domain->appendChild($domain_security_txt_www_legacy);		
	
$domain_security_txt_site = $doc->createElement("security_txt_site");
$domain_security_txt_site->appendChild($doc->createCDATASection($security_txt_site));		
$domain->appendChild($domain_security_txt_site);	
	
$domain_security_txt_www_site = $doc->createElement("security_txt_www_site");
$domain_security_txt_www_site->appendChild($doc->createCDATASection($security_txt_www_site));		
$domain->appendChild($domain_security_txt_www_site);
	
$domain_security_txt_effective = $doc->createElement("security_txt_effective");
$domain_security_txt_effective->appendChild($doc->createCDATASection($security_txt_effective));		
$domain->appendChild($domain_security_txt_effective);	
	
$domain_security_txt_www_effective = $doc->createElement("security_txt_www_effective");
$domain_security_txt_www_effective->appendChild($doc->createCDATASection($security_txt_www_effective));		
$domain->appendChild($domain_security_txt_www_effective);	
	
$domain_server_headers = $doc->createElement("server_headers");
$domain_server_headers->appendChild($doc->createCDATASection($server_headers));		
$domain->appendChild($domain_server_headers);
	
$domain_transfer_information = $doc->createElement("transfer_information");
$domain_transfer_information->appendChild($doc->createCDATASection($transfer_information));
$domain->appendChild($domain_transfer_information);
	
$domain_server_headers_www = $doc->createElement("server_headers_www");
$domain_server_headers_www->appendChild($doc->createCDATASection($server_headers_www));		
$domain->appendChild($domain_server_headers_www);
	
$domain_transfer_information_www = $doc->createElement("transfer_information_www");
$domain_transfer_information_www->appendChild($doc->createCDATASection($transfer_information_www));
$domain->appendChild($domain_transfer_information_www);	
	
$domains->appendChild($domain);
$doc->appendChild($domains);
//return $doc->saveXML(NULL, LIBXML_NOEMPTYTAG);
return $doc->saveXML();
}

function get_mx_ips($inputurl)	{
	$output = '';
	$array = dns_get_record($inputurl, DNS_A);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ip')	{
				$output .= 'ipv4: '.$value2.'<br />';	
			}
		}	
	}
	$array = dns_get_record($inputurl, DNS_AAAA);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ipv6')	{
				$output .= 'ipv6: '.$value2.'<br />';	
			}
		}
	}
	return $output;
}
?>