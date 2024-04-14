<?php
//$_GET['url'] = 'rijkswaterstaat.nl';
if (!empty($_GET['url']))	{
	if (strlen($_GET['url']))	{
		$domain = urlencode(trim($_GET['url']));
		$domain = str_replace('http://','', $domain);
		$domain = str_replace('https://','', $domain);
		$domain = str_replace('www.','', $domain);
		$strpos = mb_strpos($domain, '?');
		if ($strpos)	{
			$domain = mb_substr($domain, 0, $strpos);
		}
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
		die("No URL has been entered as input.");	
	}
}
else	{	
	die("No input has been entered.");
}

function write_file($inputdomain)	{
	
$DNS_CNAME = '';
$array = dns_get_record($inputdomain, DNS_CNAME);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'target') {
			$DNS_CNAME .= 'CNAME: '.$value2 . '.<br />';
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
			$DNS_CNAME .= $pre.'A: '.$value2.': '.gethostbyaddr($value2).$post.'<br />';	
		}	
	}
}
$array = dns_get_record($inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$DNS_CNAME .= $pre.'AAAA: '.$value2.': '.gethostbyaddr($value2).$post.'<br />';
		}	
	}
}
$DNS_CNAME_www = '';	
$array = dns_get_record('www.'.$inputdomain, DNS_CNAME);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'target') {
			$DNS_CNAME_www .= 'CNAME: '.$value2 . '.<br />';
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
			$DNS_CNAME_www .= $pre.'A: '.$value2.': '.gethostbyaddr($value2).$post.'<br />';	
		}
	}
}	
$array = dns_get_record('www.'.$inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$DNS_CNAME_www .= $pre.'AAAA: '.$value2.': '.gethostbyaddr($value2).$post.'<br />';
		}
	}
}
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
$DNS_TXT = '';	
$array = dns_get_record($inputdomain, DNS_TXT);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
       	if ($key2 == 'txt') {
			$DNS_TXT .= $value2 . '<br />';
		}
	}	
}
$DNS_TXT_www = '';	
$array = dns_get_record('www.'.$inputdomain, DNS_TXT);		
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
       	if ($key2 == 'txt') {
			$DNS_TXT_www .= $value2 . '<br />';
		}	
    }
}
	
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);	
curl_setopt($ch, CURLOPT_NOBODY, 1);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);	
	
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
	
$security_txt_legacy = '';	
$security_txt_url_legacy = 'not applicable';
$security_txt_www_legacy = '';
$security_txt_url_www_legacy = 'not applicable';	
	
$security_txt_site = '';
$security_txt_url_site = 'not applicable';	
$security_txt_www_site = '';
$security_txt_url_www_site = 'not applicable';	
	
curl_setopt($ch, CURLOPT_HEADER, 0);		
curl_setopt($ch, CURLOPT_NOBODY, 0);	
	
if (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain.'/security.txt');	
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$security_txt_legacy = nl2br(curl_exec($ch));
	$security_txt_url_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = nl2br(curl_exec($ch));
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if ($effective_url == $security_txt_url_legacy)	{
		$security_txt_url_legacy .= '<br />';
	}
	else	{
		$security_txt_url_legacy .= '<br />'.$effective_url.'<br />';
	}
	if (!curl_errno($ch)) {
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
			$security_txt_legacy = $effective;	
		}
		else	{
			$security_txt_legacy = 'No return of HTTP code 200';
		}
	}
	else	{
		$security_txt_legacy = 'Curl error '.curl_errno($ch).': '.curl_error($ch);
	}
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain.'/.well-known/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);		
	$security_txt_site = nl2br(curl_exec($ch));
	$security_txt_url_site = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = nl2br(curl_exec($ch));
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if ($effective_url == $security_txt_url_site)	{
		$security_txt_url_site .= '<br />';
	}
	else	{
		$security_txt_url_site .= '<br />'.$effective_url.'<br />';
	}
	if (!curl_errno($ch)) {		
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
			$security_txt_site = $effective;	
		}
		else	{
			$security_txt_site = 'No return of HTTP code 200';
		}	
	}		
	else	{
		$security_txt_site = 'Curl error '.curl_errno($ch).': '.curl_error($ch);
	}
}	
if (strlen($DNS_CNAME_www))	{
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain.'/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$security_txt_www_legacy = nl2br(curl_exec($ch));
	$security_txt_url_www_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = nl2br(curl_exec($ch));
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if ($effective_url == $security_txt_url_www_legacy)	{
		$security_txt_url_www_legacy .= '<br />';
	}
	else	{
		$security_txt_url_www_legacy .= '<br />'.$effective_url.'<br />';
	}
	if (!curl_errno($ch)) {	
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
			$security_txt_www_legacy = $effective;
		}
		else	{
			$security_txt_www_legacy = 'No return of HTTP code 200';
		}
	}
	else	{
		$security_txt_www_legacy = 'Curl error '.curl_errno($ch).': '.curl_error($ch);
	}
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain.'/.well-known/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);	
	$security_txt_www_site = nl2br(curl_exec($ch));
	$security_txt_url_www_site = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = nl2br(curl_exec($ch));
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if ($effective_url == $security_txt_url_www_site)	{
		$security_txt_url_www_site .= '<br />';
	}
	else	{
		$security_txt_url_www_site .= '<br />'.$effective_url.'<br />';
	}
	if (!curl_errno($ch)) {
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
			$security_txt_www_site = $effective;
		}
		else	{
			$security_txt_www_site = 'No return of HTTP code 200';
		}	
	}
	else	{
		$security_txt_www_site = 'Curl error '.curl_errno($ch).': '.curl_error($ch);
	}
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
	
$domain_url = $doc->createElement("url");
$domain_url->appendChild($doc->createCDATASection($inputdomain));		
$domain->appendChild($domain_url);	

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
				$output .= 'A: '.$value2.'<br />';	
			}
		}	
	}
	$array = dns_get_record($inputurl, DNS_AAAA);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ipv6')	{
				$output .= 'AAAA: '.$value2.'<br />';	
			}
		}
	}
	return $output;
}

?>