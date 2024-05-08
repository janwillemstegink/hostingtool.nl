<?php
//$_GET['url'] = 'hostingtool.nl';
if (!empty($_GET['url']))	{
	if (strlen($_GET['url']))	{
		$domain = trim($_GET['url']);
		$domain = mb_strtolower($domain);
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
		$domain = urlencode($domain);
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
//$php_version = (float)phpversion();
	
$DNS_CNAME = get_cname_target($inputdomain);
if (strlen($DNS_CNAME))	{
	$DNS_CNAME = $DNS_CNAME.'<br />';
	$pre = '(';
	$post = ')';
	$cname_limited = true;
}
else	{
	$pre = '';
	$post = '';
	$cname_limited = false;
}
$matches_server = false;	
$array = dns_get_record($inputdomain, DNS_A);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ip')	{
			$rDNS = gethostbyaddr($value2);
			$DNS_CNAME .= $pre.'A: '.$value2.' - '.$rDNS.$post.'<br />';
			if ($value2 == $own_ip)	$same_server = true;
			if ($rDNS == $inputdomain) $matches_server = true;
		}	
	}
}
$array = dns_get_record($inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$rDNS = gethostbyaddr($value2);
			$DNS_CNAME .= $pre.'AAAA: '.$value2.' - '.$rDNS.$post.'<br />';
			if ($value2 == $own_ip)	$same_server = true;
			if ($rDNS == $inputdomain) $matches_server = true;
		}	
	}
}
$DNS_CNAME_www = get_cname_target('www.'.$inputdomain);
if (strlen($DNS_CNAME_www))	{
	$DNS_CNAME_www = $DNS_CNAME_www.'<br />';
	$pre = '(';
	$post = ')';
	$cname_limited_www = true;
}
else	{
	$pre = '';
	$post = '';
	$cname_limited_www = false;
}
$matches_server_www = false;
$array = dns_get_record('www.'.$inputdomain, DNS_A);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ip') {
			$rDNS = gethostbyaddr($value2);
			$DNS_CNAME_www .= $pre.'A: '.$value2.' - '.$rDNS.$post.'<br />';
			if ($value2 == $own_ip)	$same_server_www = true;
			if ($rDNS == 'www'.$inputdomain) $matches_server_www = true;
		}
	}
}	
$array = dns_get_record('www.'.$inputdomain, DNS_AAAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'ipv6') {
			$rDNS = gethostbyaddr($value2);
			$DNS_CNAME_www .= $pre.'AAAA: '.$value2.' - '.$rDNS.$post.'<br />';
			if ($value2 == $own_ip)	$same_server_www = true;
			if ($rDNS == 'www'.$inputdomain) $matches_server_www = true;
		}
	}
}
$DNS_MX = '';
$DNS_MX_notice = 0;	
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
if (strlen($DNS_MX))	{
	if (strpos($DNS_MX, 'IPv6 after request'))	{
		$DNS_MX_notice = 1;		
	}	
}
else	{	
	if ($cname_limited)	{
		$DNS_MX_notice = 1;	
		$DNS_MX .= '(Null MX in destination DNS would combine with CNAME)<br />';
	}
	elseif (!strlen($DNS_CNAME))	{
		$DNS_MX .= 'not applicable';		
	}	
	else	{
		$DNS_MX_notice = 1;
		$DNS_MX .= '("0 ." would block email to A/AAAA; Null MX not in cPanel)<br />';
	}	
}	
$DNS_MX_www = '';
$DNS_MX_www_notice = 0;		
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
if (strlen($DNS_MX_www))	{
	if (strpos($DNS_MX_www, 'IPv6 after request'))	{
		$DNS_MX_www_notice = 1;
	}	
}
else	{
	if ($cname_limited_www)	{
		$DNS_MX_www_notice = 1;
		$DNS_MX_www .= '(Null MX in destination DNS would combine with CNAME)<br />';
	}
	elseif (!strlen($DNS_CNAME_www))	{
		$DNS_MX_www .= 'not applicable';		
	}
	else	{
		$DNS_MX_www_notice = 1;
		$DNS_MX_www .= '("0 ." would block email to A/AAAA; Null MX not in cPanel)<br />';
	}	
}	
$DNS_TXT = '';	
$DNS_TXT_notice = 0;
$array = dns_get_record($inputdomain, DNS_TXT);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
       	if ($key2 == 'txt') {
			$DNS_TXT .= $value2 . '<br />';
		}
	}	
}
if (!strlen($DNS_TXT))	{
	if ($cname_limited)	{
		$DNS_TXT_notice = 1;
		$DNS_TXT .= '("v=spf1 -all" in destination DNS would combine with CNAME)<br />';
	}
	elseif (!strlen($DNS_CNAME))	{
		$DNS_TXT .= 'not applicable';		
	}
	elseif ($matches_server)	{
		$DNS_TXT_notice = 1;
		$DNS_TXT .= '("v=spf1 +a ~all" would secure email)<br />';
	}
	else	{
		$DNS_TXT_notice = 1;
		$DNS_TXT .= '("v=spf1 -all" would secure email)<br />';
	}	
}	
	
$DNS_TXT_www = '';
$DNS_TXT_www_notice = 0;	
$array = dns_get_record('www.'.$inputdomain, DNS_TXT);		
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
       	if ($key2 == 'txt') {
			$DNS_TXT_www .= $value2 . '<br />';
		}	
    }
}
if (!strlen($DNS_TXT_www))	{
	if ($cname_limited_www)	{
		$DNS_TXT_www_notice = 1;
		$DNS_TXT_www .= '("v=spf1 -all" in destination DNS would combine with CNAME)<br />';
	}
	elseif (!strlen($DNS_CNAME_www))	{
		$DNS_TXT_www .= 'not applicable';		
	}
	elseif ($matches_server_www)	{
		$DNS_TXT_www_notice = 1;
		$DNS_TXT_www .= '("v=spf1 +a ~all" would secure email)<br />';
	}
	else	{
		$DNS_TXT_www_notice = 1;
		$DNS_TXT_www .= '("v=spf1 -all" would secure email)<br />';
	}
}
$DNS_DMARC = dmarc_list($inputdomain);
$DNS_DMARC_notice = 0;	
if (!strlen($DNS_DMARC))	{
	if (!strlen($DNS_CNAME))	{
		$DNS_DMARC .= 'not applicable';		
	}
	else	{
		$DNS_DMARC_notice = 1;
		$DNS_DMARC .= '(DMARC misses email settings)<br />';	
	}	
}
$DNS_DMARC_www = dmarc_list('www.'.$inputdomain);
$DNS_DMARC_www_notice = 0;	
if (!strlen($DNS_DMARC_www))	{
	if (!strlen($DNS_CNAME_www))	{
		$DNS_DMARC_www .= 'not applicable';		
	}
	else	{
		$DNS_DMARC_www_notice = 1;
		$DNS_DMARC_www .= '(DMARC misses in email settings)<br />';
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

$http_code_initial = 'initial: not applicable';
$server_header = 'not applicable';	
$transfer_information = 'not applicable';	
$http_code_destination = 'destination: not applicable';
if (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputdomain);	
	$curl_server_header = curl_exec($ch);
	$http_code_initial = 'initial: ';
	if (!curl_errno($ch)) {
		$http_code_initial .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. 
			curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
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
	
$http_code_initial_www = 'initial: not applicable';
$server_header_www = 'not applicable';	
$transfer_information_www = 'not applicable';
$http_code_destination_www = 'destination: not applicable';	
if (strlen($DNS_CNAME_www))	{	
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain);
	$curl_server_header_www = curl_exec($ch);
	$http_code_initial_www = 'initial: ';
	if (!curl_errno($ch)) {
		$http_code_initial_www .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. 
			curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
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
	
$security_txt_notice = 0;	
$security_txt_www_notice = 0;	
	
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
			elseif ($matches_server)	{
				$security_txt_legacy = 'No HTTP 200 OK (the file is not expected for the server).';
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
			elseif ($matches_server)	{
				$security_txt_relocated = 'No HTTP 200 OK (the file is not expected for the server).';
			}	
			else	{
				$security_txt_notice = 1;
				$security_txt_relocated = 'No HTTP 200 OK.';
			}
		}
		else	{
			$security_txt_notice = 1;
			$security_txt_relocated = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$security_txt_notice = 1;
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
			elseif ($matches_server_www)	{
				$security_txt_legacy_www = 'No HTTP 200 OK (the file is not expected for the server).';
			}
			else	{
				$security_txt_www_legacy = 'No HTTP 200 OK';
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
			elseif ($matches_server_www)	{
				$security_txt_www_relocated = 'No HTTP 200 OK (the file is not expected for the server).';
			}
			else	{
				$security_txt_www_notice = 1;
				$security_txt_www_relocated = 'No HTTP 200 OK.';
			}
		}
		else	{
			$security_txt_www_notice = 1;
			$security_txt_www_relocated = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$security_txt_www_notice = 1;
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
			$http_code_destination .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. 
				curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		}		
		else	{
			$http_code_destination .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$http_code_destination .= '(No cURL on the same server)';		
	}
}
if (strlen($DNS_CNAME_www))	{
	$http_code_destination_www = 'destination: ';
	if (!$same_server_www)	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputdomain);		
		$target = curl_exec($ch);
		if (!curl_errno($ch)) {	
			$http_code_destination_www .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. 
				curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		}		
		else	{
			$http_code_destination_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$http_code_destination_www .= '(No cURL on the same server)';	
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
	
$domain_DNS_MX_notice = $doc->createElement("DNS_MX_notice");
$domain_DNS_MX_notice->appendChild($doc->createCDATASection($DNS_MX_notice));		
$domain->appendChild($domain_DNS_MX_notice);	
	
$domain_DNS_MX_www_notice = $doc->createElement("DNS_MX_www_notice");
$domain_DNS_MX_www_notice->appendChild($doc->createCDATASection($DNS_MX_www_notice));		
$domain->appendChild($domain_DNS_MX_www_notice);	
	
$domain_DNS_TXT = $doc->createElement("DNS_TXT");
$domain_DNS_TXT->appendChild($doc->createCDATASection($DNS_TXT));		
$domain->appendChild($domain_DNS_TXT);	
	
$domain_DNS_TXT_www = $doc->createElement("DNS_TXT_www");
$domain_DNS_TXT_www->appendChild($doc->createCDATASection($DNS_TXT_www));
$domain->appendChild($domain_DNS_TXT_www);	
	
$domain_DNS_TXT_notice = $doc->createElement("DNS_TXT_notice");
$domain_DNS_TXT_notice->appendChild($doc->createCDATASection($DNS_TXT_notice));		
$domain->appendChild($domain_DNS_TXT_notice);	
	
$domain_DNS_TXT_www_notice = $doc->createElement("DNS_TXT_www_notice");
$domain_DNS_TXT_www_notice->appendChild($doc->createCDATASection($DNS_TXT_www_notice));
$domain->appendChild($domain_DNS_TXT_www_notice);
	
$domain_DNS_DMARC = $doc->createElement("DNS_DMARC");
$domain_DNS_DMARC->appendChild($doc->createCDATASection($DNS_DMARC));		
$domain->appendChild($domain_DNS_DMARC);	
	
$domain_DNS_DMARC_www = $doc->createElement("DNS_DMARC_www");
$domain_DNS_DMARC_www->appendChild($doc->createCDATASection($DNS_DMARC_www));
$domain->appendChild($domain_DNS_DMARC_www);	
	
$domain_DNS_DMARC_notice = $doc->createElement("DNS_DMARC_notice");
$domain_DNS_DMARC_notice->appendChild($doc->createCDATASection($DNS_DMARC_notice));		
$domain->appendChild($domain_DNS_DMARC_notice);	
	
$domain_DNS_DMARC_www_notice = $doc->createElement("DNS_DMARC_www_notice");
$domain_DNS_DMARC_www_notice->appendChild($doc->createCDATASection($DNS_DMARC_www_notice));
$domain->appendChild($domain_DNS_DMARC_www_notice);	
	
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
	
$domain_security_txt_notice = $doc->createElement("security_txt_notice");
$domain_security_txt_notice->appendChild($doc->createCDATASection($security_txt_notice));
$domain->appendChild($domain_security_txt_notice);	
	
$domain_security_txt_www_notice = $doc->createElement("security_txt_www_notice");
$domain_security_txt_www_notice->appendChild($doc->createCDATASection($security_txt_www_notice));
$domain->appendChild($domain_security_txt_www_notice);	
	
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

function get_cname_target($inputdomain)	{	
	$output = '';
	$array = dns_get_record($inputdomain, DNS_CNAME);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'target') {
				$output .= $value2;
			}	
		}
	}
	return $output;
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
	if (mb_strpos($inputurl, 'mail.protection.outlook.com'))	{
		if (mb_strpos($output, 'AAAA'))	{
		}
		else	{	
			$output .= '(IPv6 after request at protection.outlook.com)<br />';
		}		
	}	
	return $output;
}

function remove_subdomain($inputurl)	{
	$strpos = mb_strpos($inputurl, '.');
	$inputurl = mb_substr($inputurl, $strpos + 1);
	return $inputurl;
}

function dmarc_list($inputurl)	{ //ACEACE //ACEIACEIb6a.nl   _dmarc.b6a.nl                 ACEDACEDb6a.nl: v=spf1 -all  _dmarc.b6a.nl: v=DMARC1;p=reject;
	$output = '';
	$strpos = 1;
	while ($strpos)	{
		$array = dns_get_record('_dmarc.'.$inputurl, DNS_TXT);
		$cname_value = get_cname_target('_dmarc.'.$inputurl);
		foreach($cname_value as $key1 => $value1) {
			foreach($cname_value as $key2 => $value2) {
				$inputurl = $cname_value;
				$array = dns_get_record($inputurl, DNS_TXT);							
			}
		}
		$temp1 = '';
		$temp2 = '';
		foreach($array as $key1 => $value1) {
			foreach($value1 as $key2 => $value2) {
				if ($key2 == 'host') {
					$temp1 = $value2;
				}
				if ($key2 == 'txt') {
					$temp2 = $value2;
				}				
			}
		}
		if	(!stristr($temp2, 'v=DMARC1;'))	{
			$cname_value = get_cname_target($inputurl);
			foreach($cname_value as $key1 => $value1) {
				foreach($cname_value as $key2 => $value2) {
					foreach($array as $key1 => $value1) {
						foreach($value1 as $key2 => $value2) {
							if ($key2 == 'host') {
								$temp1 = $value2;
							}
							if ($key2 == 'txt') { 
								$temp2 = $value2;
							}				
						}
					}
				}	
			}
		}
		if (strlen($temp1) and stristr(str_replace(' ', '', $temp2), 'v=DMARC1;'))	{
			$output .= $temp1 . ': ' . $temp2 . '<br />';
		}
		elseif (strlen($temp1))	{
			$output .= $temp1 . '<br />';
		}
		if (mb_strpos($output, 'v=DMARC1;'))	{
			break;
		}		
		$inputurl = remove_subdomain($inputurl);
		if (!strpos($inputurl, '.'))	{
			break;	
		}		
	}
	return $output;
}
?>