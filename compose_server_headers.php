<?php
//$_GET['url'] = 'hostingtool.nl';
if (!empty($_GET['url']))	{
	if (strlen($_GET['url']))	{
		$domain = trim($_GET['url']);
		$domain = urlencode($domain);
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
	
$own_ip = $_SERVER['SERVER_ADDR'];
$same_server = false;
$same_server_www = false;
	
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
			if ($value2 == $own_ip)	$same_server = true;	
		}	
	}
}
$array = dns_get_record($inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$DNS_CNAME .= $pre.'AAAA: '.$value2.': '.gethostbyaddr($value2).$post.'<br />';
			if ($value2 == $own_ip)	$same_server = true;
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
			if ($value2 == $own_ip)	$same_server_www = true;
		}
	}
}	
$array = dns_get_record('www.'.$inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$DNS_CNAME_www .= $pre.'AAAA: '.$value2.': '.gethostbyaddr($value2).$post.'<br />';
			if ($value2 == $own_ip)	$same_server_www = true;
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

$http_code_initial = 'not applicable';
$server_header = 'not applicable';	
$transfer_information = 'not applicable';	
$http_code_destination = 'not applicable';
if (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain);	
	$curl_server_header = curl_exec($ch);
	$http_code_initial = 'initial: ';
	if (!curl_errno($ch)) {
		$http_code_initial .= curl_getinfo($ch, CURLINFO_HTTP_CODE);
	}
	else	{
		$http_code_initial .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
	$arr_server_header = explode (",", $curl_server_header);
	$server_header = '';
	foreach($arr_server_header as $key1 => $value1) {
		$server_header .= $key1 . ':' . $value1 . '<br />';
	}
	$arr_transfer_information = curl_getinfo($ch);
	$transfer_information = '';	
	foreach($arr_transfer_information as $key1 => $value1) {
		$transfer_information .= $key1 . ': ' . $value1 . '<br />';	
	}
}
	
$http_code_initial_www = 'not applicable';
$server_header_www = 'not applicable';	
$transfer_information_www = 'not applicable';
$http_code_destination_www = 'not applicable';	
if (strlen($DNS_CNAME_www))	{	
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain);
	$curl_server_header_www = curl_exec($ch);
	$http_code_initial_www = 'initial: ';
	if (!curl_errno($ch)) {
		$http_code_initial_www .= curl_getinfo($ch, CURLINFO_HTTP_CODE);
	}
	else	{
		$http_code_initial_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}	
	$arr_server_header_www = explode (",", $curl_server_header_www);
	$server_header_www = '';	
	foreach($arr_server_header_www as $key1 => $value1) {
		$server_header_www .= $key1 . ': ' . $value1 . '<br />';		
	}
	$arr_transfer_information_www = curl_getinfo($ch);
	$transfer_information_www = '';	
	foreach($arr_transfer_information_www as $key1 => $value1) {
		$transfer_information_www .= $key1 . ': ' . $value1 . '<br />';
	}
}

$security_txt_legacy = '';	
$security_txt_url_legacy = 'not applicable';
$security_txt_www_legacy = '';
$security_txt_url_www_legacy = 'not applicable';	
	
$security_txt_relocated = '';
$security_txt_url_relocated = 'not applicable';	
$security_txt_www_relocated = '';
$security_txt_url_www_relocated = 'not applicable';	
	
curl_setopt($ch, CURLOPT_HEADER, 0);		
curl_setopt($ch, CURLOPT_NOBODY, 0);	
	
if (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain.'/security.txt');	
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$security_txt_legacy = curl_exec($ch);
	$security_txt_url_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (mb_strpos($effective_url, '/security.txt'))	{
		if ($effective_url == $security_txt_url_legacy)	{
		}
		else	{
			$security_txt_url_legacy .= '<br />'.$effective_url;
		}
		if (!curl_errno($ch)) {
			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
				$security_txt_legacy = $effective;	
			}
			else	{
				$security_txt_legacy = 'No HTTP 200 OK.';
			}
		}
		else	{
			$security_txt_legacy = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$security_txt_legacy = 'No security.txt';		
	}
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain.'/.well-known/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);		
	$security_txt_relocated = curl_exec($ch);
	$security_txt_url_relocated = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		if (mb_strpos($effective_url, '/security.txt'))	{
		if ($effective_url == $security_txt_url_relocated)	{
		}
		else	{
			$security_txt_url_relocated .= '<br />'.$effective_url;
		}
		if (!curl_errno($ch)) {
			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
				$security_txt_relocated = $effective;	
			}
			else	{
				$security_txt_relocated = 'No HTTP 200 OK.';
			}
		}
		else	{
			$security_txt_relocated = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$security_txt_relocated = 'No security.txt';		
	}
}	
if (strlen($DNS_CNAME_www))	{
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain.'/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$security_txt_www_legacy = curl_exec($ch);
	$security_txt_url_www_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (mb_strpos($effective_url, '/security.txt'))	{
		if ($effective_url == $security_txt_url_www_legacy)	{
		}
		else	{
			$security_txt_url_www_legacy .= '<br />'.$effective_url;
		}
		if (!curl_errno($ch)) {
			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
				$security_txt_www_legacy = $effective;	
			}
			else	{
				$security_txt_www_legacy = 'No HTTP 200 OK.';
			}
		}
		else	{
			$security_txt_www_legacy = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$security_txt_www_legacy = 'No security.txt';		
	}	
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain.'/.well-known/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);	
	$security_txt_www_relocated = curl_exec($ch);
	$security_txt_url_www_relocated = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (mb_strpos($effective_url, '/security.txt'))	{
		if ($effective_url == $security_txt_url_www_relocated)	{
		}
		else	{
			$security_txt_url_www_relocated .= '<br />'.$effective_url;
		}
		if (!curl_errno($ch)) {
			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)	{
				$security_txt_www_relocated = $effective;	
			}
			else	{
				$security_txt_www_relocated = 'No HTTP 200 OK.';
			}
		}
		else	{
			$security_txt_www_relocated = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$security_txt_www_relocated = 'No security.txt';		
	}
}
if (strlen($DNS_CNAME))	{
	$http_code_destination = 'destination: ';
	if (!$same_server)	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain);		
		$target = curl_exec($ch);
		if (!curl_errno($ch)) {	
			$http_code_destination .= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}		
		else	{
			$http_code_destination .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$http_code_destination .= 'From PHP 8.2 cURL works on the same server.';		
	}
}	
if (strlen($DNS_CNAME_www))	{
	$http_code_destination_www = 'destination: ';
	if (!$same_server_www)	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain);		
		$target = curl_exec($ch);
		if (!curl_errno($ch)) {	
			$http_code_destination_www .= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}		
		else	{
			$http_code_destination_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$http_code_destination_www .= 'From PHP 8.2 cURL works on the same server.';	
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
$domain_url->appendChild($doc->createCDATASection(htmlentities($inputdomain)));		
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
	
$domain_security_txt_url_relocated = $doc->createElement("security_txt_url_relocated");
$domain_security_txt_url_relocated->appendChild($doc->createCDATASection($security_txt_url_relocated));		
$domain->appendChild($domain_security_txt_url_relocated);	
	
$domain_security_txt_url_www_relocated = $doc->createElement("security_txt_url_www_relocated");
$domain_security_txt_url_www_relocated->appendChild($doc->createCDATASection($security_txt_url_www_relocated));		
$domain->appendChild($domain_security_txt_url_www_relocated);	
	
$domain_security_txt_legacy = $doc->createElement("security_txt_legacy");
$domain_security_txt_legacy->appendChild($doc->createCDATASection(nl2br(htmlentities($security_txt_legacy))));
$domain->appendChild($domain_security_txt_legacy);

$domain_security_txt_www_legacy = $doc->createElement("security_txt_www_legacy");
$domain_security_txt_www_legacy->appendChild($doc->createCDATASection(nl2br(htmlentities($security_txt_www_legacy))));
$domain->appendChild($domain_security_txt_www_legacy);		
	
$domain_security_txt_relocated = $doc->createElement("security_txt_relocated");
$domain_security_txt_relocated->appendChild($doc->createCDATASection(nl2br(htmlentities($security_txt_relocated))));
$domain->appendChild($domain_security_txt_relocated);	
	
$domain_security_txt_www_relocated = $doc->createElement("security_txt_www_relocated");
$domain_security_txt_www_relocated->appendChild($doc->createCDATASection(nl2br(htmlentities($security_txt_www_relocated))));
$domain->appendChild($domain_security_txt_www_relocated);
	
$domain_http_code_initial = $doc->createElement("http_code_initial");
$domain_http_code_initial->appendChild($doc->createCDATASection($http_code_initial));
$domain->appendChild($domain_http_code_initial);

$domain_http_code_destination = $doc->createElement("http_code_destination");
$domain_http_code_destination->appendChild($doc->createCDATASection($http_code_destination));
$domain->appendChild($domain_http_code_destination);	
	
$domain_server_header = $doc->createElement("server_header");
$domain_server_header->appendChild($doc->createCDATASection(nl2br(htmlentities($server_header))));		
$domain->appendChild($domain_server_header);
	
$domain_transfer_information = $doc->createElement("transfer_information");
$domain_transfer_information->appendChild($doc->createCDATASection($transfer_information));
$domain->appendChild($domain_transfer_information);	
	
$domain_http_code_initial_www = $doc->createElement("http_code_initial_www");
$domain_http_code_initial_www->appendChild($doc->createCDATASection($http_code_initial_www));
$domain->appendChild($domain_http_code_initial_www);
	
$domain_http_code_destination_www = $doc->createElement("http_code_destination_www");
$domain_http_code_destination_www->appendChild($doc->createCDATASection($http_code_destination_www));
$domain->appendChild($domain_http_code_destination_www);	

$domain_server_header_www = $doc->createElement("server_header_www");
$domain_server_header_www->appendChild($doc->createCDATASection(nl2br(htmlentities($server_header_www))));		
$domain->appendChild($domain_server_header_www);
	
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