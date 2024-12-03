<?php
session_start();  // is needed with no PHP Generator Scriptcase
//session_start();  // is needed with no PHP Generator Scriptcase
echo '<!DOCTYPE html><html lang="en" style="font-size: 90%"><head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta charset="UTF-8" />
<meta http-equiv="x-ua-compatible" content="ie=edge" />
<meta name="robots" content="index" />
<title>Server Header Data</title>';
?><script>
	
function SwitchDisplay(type) {

	if (type == 11)			{ // about redirect 
		var pre = '11';
		var max = 4
	}
	else if (type == 12)	{ // about regulation
		var pre = '12';
		var max = 5
	}
	else if (type == 13)	{ // about server headers
		var pre = '13';
		var max = 10
	}
	else if (type == 14)	{ // about robots.txt
		var pre = '14';
		var max = 2
	}
	else if (type == 21)	{ // HTTP respons
		var pre = '21';
		var max = 2
	}
	else if (type == 22)	{ // HTTPS respons
		var pre = '22';
		var max = 2
	}
	else if (type == 31)	{ // CNAME
		var pre = '31';
		var max = 1
	}
	else if (type == 32)	{ // MX
		var pre = '32';
		var max = 1
	}
	else if (type == 33)	{ // TXT
		var pre = '33';
		var max = 1
	}
	else if (type == 34)	{ // DMARC
		var pre = '34';
		var max = 1
	}
	else if (type == 35)	{ // AS
		var pre = '35';
		var max = 4
	}
	else if (type == 36)	{ // SOA
		var pre = '36';
		var max = 2
	}
	else if (type == 41)	{ // robots.txt
		var pre = '41';
		var max = 2
	}
	else if (type == 42)	{ // meta tags
		var pre = '42';
		var max = 1
	}
	else if (type == 43)	{ // security.txt legacy
		var pre = '43';
		var max = 2
	}
	else if (type == 44)	{ // security.txt .well-known
		var pre = '44';
		var max = 2
	}
	else if (type == 51)	{ // hsts headers
		var pre = '51';
		var max = 1
	}
	else if (type == 52)	{ // server headers
		var pre = '52';
		var max = 1
	}
	else if (type == 61)	{ // transfer information
		var pre = '61';
		var max = 1
	}
	else	{
		return;	
	}
	
	for (let i = 1; i <= max; i++) {
		var id = pre + i.toString();
		if (typeof(document.getElementById(id)) != 'undefined' && document.getElementById(id) != null )	{
			if (document.getElementById(id).style.display == "table-row")	{
				document.getElementById(id).style.display = "none";	
			}
			else	{
				document.getElementById(id).style.display = "table-row";
			}
		}
	}
		
	function echo( ...s )	{
   		for(var i = 0; i < s.length; i++ ) {
    		document.write(s[i] + ' ');
		}
	}
}

</script><?php

echo '</head>';
if (!function_exists('simplexml_load_file')) {
	die('simpleXML functions are not available.');
}
if (ini_get("allow_url_fopen") == 1)	{
}
else	{	
	die('allow_url_fopen does not work.'); 	
}
if (empty($_GET['url']))	{
	$viewserver = 'url';
	$defaultdisplay = 'table-row';
}
else	{
	$_GET['url'] = trim($_GET['url']);
	$_GET['url'] = clean_url($_GET['url']);	
	$_GET['url'] = rawurldecode($_GET['url']);
	$viewserver = $_GET['url'];
	$defaultdisplay = 'none';
}

$html_text = '<body><div style="border-collapse:collapse; line-height:120%">
<table style="font-family:Helvetica, Arial, sans-serif; font-size: 1rem; table-layout: fixed; width:1200px; overflow-wrap: break-word">
<tr><th style="width:300px"></th><th style="width:300px"></th><th style="width:600px"></th></tr>';
$html_text .= '<tr style="font-size: .8rem"><td style="font-size: 1.3rem;color:blue;font-weight:bold">Server Header Data</td>
<td><form action='.htmlentities($_SERVER['PHP_SELF']).' method="get"><label for="url">Paste a URL and press Enter</label><input type="text" style="width:90%;font-size: 1.2rem" id="url" name="url" value='.$viewserver.'></form></td><td> <a style="font-size: 0.9rem" href="https://github.com/janwillemstegink/hostingtool.nl/issues" target="_blank">issues on GitHub</a> - <a style="font-size: 0.9rem" href="https://webhostingtech.nl/security-setup/set-up-htaccess/" target="_blank">conditional redirect in .htaccess</a> - <a style="font-size: 0.9rem" href="https://janwillemstegink.nl/" target="_blank">janwillemstegink.nl</a></td></tr>';
$html_text .= '<tr><td colspan="3" style="cursor:pointer;font-size:1.0rem">Settings to optimize are colored orange.</td></tr>';
$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
$html_text .= '<tr><td colspan="3"><button style="cursor:pointer;font-size:0.9rem" onclick="SwitchDisplay(11)">About redirection from an alias +/-</button></td></tr>';
$html_text .= '<tr id="111" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">RFC 1033 forbids the use of CNAME for the registered, apex domain. The apex domain is the main domain without subdomains, such as ‘example.com’.</td></tr>';
$html_text .= '<tr id="112" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">CNAME affects subdomain email settings because MX and SPF cannot differ. Upcoming ANAME is flattened CNAME to just A/AAAA. Outsourced hosting can then be safe.</td></tr>';
$html_text .= '<tr id="113" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">The <b>www</b> subdomain is not unnecessary. There are some useful aspects. If you are hosting elsewhere, you will need CNAME, as allowed for subdomain www.</td></tr>';
$html_text .= '<tr id="114" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">And for a website with a subdomain, HSTS can be set more precisely. <a style="font-size: 0.9rem" href="https://datatracker.ietf.org/doc/html/draft-ietf-dnsop-aname-01" target="_blank">An RFC draft from PowerDNS and DNSimple on ANAME</a> - <a style="font-size: 0.9rem" href="https://blog.cloudflare.com/introducing-cname-flattening-rfc-compliant-cnames-at-a-domains-root/" target="_blank">Cloudflare about ANAME</a> - <a style="font-size: 0.9rem" href="https://webhostingtech.nl/dns-setup/a-aaaa-or-cname/" target="_blank">Me about CNAME</a></td></tr>';
$html_text .= '<tr><td colspan="3"><button style="cursor:pointer;font-size:0.9rem" onclick="SwitchDisplay(12)">About security.txt Content Expiry +/-</button></td></tr>';
$html_text .= '<tr id="121" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">RFC 9116: "The "Expires" field indicates the date and time after which the data contained in the "security.txt" file is considered stale and should not be used (as per Section 5.3)".</td></tr>';
$html_text .= '<tr id="122" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">RFC 9116: "It is RECOMMENDED that the value of this field be less than a year into the future to avoid staleness."</td></tr>';
$html_text .= '<tr id="123" style="display:'.$defaultdisplay.';font-style:italic;font-style:italic"><td colspan="3">Suggestion 1: The data contained in the "security.txt" file MUST expire on the date and time as in the "Expires" field, due to the desirability of an annual audit cycle.</td></tr>';
$html_text .= '<tr id="124" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">Suggestion 2: For the one-off annual cycle check to work, the "Expires" field date and time is maximally 398 (366+31+1) days into the future, equal to the TLS Certificate Lifespan.</td></tr>';
$html_text .= '<tr id="125" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">Suggestion 3: Annual audit requires a scheduled date on an office calendar; and customer requests cannot be dealt with if concentrated in one part of the year.</td></tr>';
$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:0.9rem" onclick="SwitchDisplay(13)">About Security Header Requirements +/-</button></td><td></td></tr>';
$html_text .= '<tr id="131" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">RFC 6797, 8.1: "If a UA receives more than one STS header field in an HTTP response message over secure transport, then the UA MUST process only the first such header field."</td></tr>';
$html_text .= '<tr id="132" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">Strict Transport Security over secure HTTPS is called HSTS. The server header is only compliant, even if it is just a URL redirect, with a functioning HSTS security header.</td></tr>';
$html_text .= '<tr id="133" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">Although browsers do not strictly enforce this rule above, the internet.nl tool tests that the URL is also the first URL over HTTPS for a security header to work.</td></tr>';
$html_text .= '<tr id="134" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">With multiple HSTS header values - an application can also set a security header - strictly speaking, the first security header applies to the user agent (UA).</td></tr>';
$html_text .= '<tr id="135" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">The internet.nl tool does test for an initial header in the initial server header area.</td></tr>';	
$html_text .= '<tr id="136" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">Web browser Chrome and the securityheaders.com tool, show values from application to server header level. The first value, starting from server header level, should be set.</td></tr>';
$html_text .= '<tr id="137" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3"><b>Note:</b> The securityheaders.com tool does not test and report correctly on rewrite to HTTPS and redirection.</td></tr>';
$html_text .= '<tr id="138" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3"><b>General approach:</b> Comply with proper initial reading of security headers from the server header(s), and note the interpretation of a subsequent value from an identical security header.</td></tr>';
$html_text .= '<tr id="139" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">First rewrite the URL to HTTPS using the checkbox in the control panel, secondly set security header values, and finally, if applicable, (conditionally) redirect in the 301 or 302 way.</td></tr>';
$html_text .= '<tr id="1310" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">A server header requires sufficient settings before public Internet access can be used safely. And avoid the HSTS preload list without understanding its implications.</td></tr>';
$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:0.9rem" onclick="SwitchDisplay(14)">About Clean Up of Search Engine Results +/-</button></td><td></td></tr>';
$html_text .= '<tr id="141" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">For search engines in general, a no-indexing statement is necessary to clean up. For deletion in Google Search, even re-registration of the domain may be necessary.</td></tr>';
$html_text .= '<tr id="142" style="display:'.$defaultdisplay.';font-style:italic"><td colspan="3">Note that robots.txt content - for more control over crawling - can block any processing by a search engine, such as the desired removal of search results.</td></tr>';
$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
$html_text .= '<tr><td colspan="3" style="cursor:pointer;font-size:1.2rem">Analysis:</td></tr>';
$html_text .= '</table>';
echo $html_text;
$html_text = '';
if (strlen($viewserver) and $viewserver != 'url')	{	
$html_text .= '<table style="font-family:Helvetica, Arial, sans-serif; font-size: 1rem; table-layout: fixed; width:1200px; overflow-wrap: break-word">
<tr><th style="width:300px"></th><th style="width:300px"></th><th style="width:600px"></th></tr>';
//$server_url = 'https://hostingtool.nl/compose_server_headers/index.php?url='.$viewserver;	
$server_url = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
$server_url .= '://'. $_SERVER['HTTP_HOST'];
$server_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);	
$server_url = dirname($server_url).'/compose_server_headers/index.php?url='.$viewserver;
if (@get_headers($server_url))	{	
	$xml1 = simplexml_load_file($server_url, "SimpleXMLElement", LIBXML_NOCDATA) or die("An entered url could not be read.");
}
if	(is_null($xml1))	{
	$display_message = str_replace("'", "\'", "A result could not be retrieved.");	
	echo "<script>alert('$display_message');</script>";
	$reopen = $server_url.'/server_headers/index.php';
	sc_redir($reopen);
}	
foreach ($xml1->xpath('//domain') as $item)	{
	simplexml_load_string($item->asXML());
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2" style="cursor:pointer;font-size:1.6rem">'.$item->url.'</td><td style="cursor:pointer;font-size:1.6rem">www.'.$item->url.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';	
	if ($item->http_code_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(21)">HTTP response +/-</button></td>';
	}
	elseif ($item->http_code_notice == "0" and !str_contains($item->http_code_initial, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(21)">HTTP response +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(21)">HTTP response +/-</button></td>';
	}
	if ($item->http_code_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(21)">HTTP response +/-</button></td></tr>';
	}
	elseif ($item->http_code_www_notice == "0" and !str_contains($item->http_code_initial_www, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(21)">HTTP response +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(21)">HTTP response +/-</button></td></tr>';
	}
	$html_text .= '<tr id="211" style="display:none;vertical-align:top"><td colspan="2">'.$item->http_code_initial.'</td><td>'.$item->http_code_initial_www.'</td></tr>';
	$html_text .= '<tr id="212" style="display:none;vertical-align:top"><td colspan="2">'.$item->http_code_destination.'</td><td>'.$item->http_code_destination_www.'</td></tr>';
	if ($item->https_code_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(22)">HTTPS response +/-</button></td>';
	}
	elseif ($item->https_code_notice == "0" and !str_contains($item->https_code_initial, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(22)">HTTPS response +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(22)">HTTPS response +/-</button></td>';
	}
	if ($item->https_code_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(22)">HTTPS response +/-</button></td></tr>';
	}
	elseif ($item->https_code_www_notice == "0" and !str_contains($item->https_code_initial_www, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(22)">HTTPS response +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(22)">HTTPS response +/-</button></td></tr>';
	}	
	$html_text .= '<tr id="221" style="display:none;vertical-align:top"><td colspan="2">'.$item->https_code_initial.'</td><td>'.$item->https_code_initial_www.'</td></tr>';
	$html_text .= '<tr id="222" style="display:none;vertical-align:top"><td colspan="2">'.$item->https_code_destination.'</td><td>'.$item->https_code_destination_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';	
	if ($item->DNS_CNAME_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(31)">CNAME, A, quad A - FCrDNS +/-</button></td>';
	}
	elseif ($item->DNS_CNAME_notice == "0" and strlen($item->DNS_CNAME))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(31)">CNAME, A, quad A - FCrDNS +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(31)">CNAME, A, quad A, FCrDNS +/-</button></td>';
	}
	if ($item->DNS_CNAME_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(31)">CNAME, A, quad A - FCrDNS +/-</button></td></tr>';
	}
	elseif ($item->DNS_CNAME_www_notice == "0" and strlen($item->DNS_CNAME_www))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(31)">CNAME, A, quad A - FCrDNS +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(31)">CNAME, A, quad A, FCrDNS +/-</button></td></tr>';
	}	
	$html_text .= '<tr id="311" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_CNAME.'</td><td>'.$item->DNS_CNAME_www.'</td></tr>';
	if ($item->DNS_MX_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(32)">MX +/-</button></td>';
	}
	elseif ($item->DNS_MX_notice == "0" and !str_contains($item->DNS_MX, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(32)">MX +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(32)">MX +/-</button></td>';		
	}
	if ($item->DNS_MX_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(32)">MX +/-</button></td></tr>';
	}
	elseif ($item->DNS_MX_www_notice == "0" and !str_contains($item->DNS_MX_www, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(32)">MX +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(32)">MX +/-</button></td></tr>';		
	}
	$html_text .= '<tr id="321" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_MX.'</td><td>'.$item->DNS_MX_www.'</td></tr>';
	if ($item->DNS_TXT_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(33)">TXT +/-</button></td>';
	}
	elseif ($item->DNS_TXT_notice == "0" and !str_contains($item->DNS_TXT, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(33)">TXT +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(33)">TXT +/-</button></td>';
	}
	if ($item->DNS_TXT_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(33)">TXT +/-</button></td></tr>';
	}
	elseif ($item->DNS_TXT_www_notice == "0" and !str_contains($item->DNS_TXT_www, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(33)">TXT +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(33)">TXT +/-</button></td></tr>';		
	}
	$html_text .= '<tr id="331" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_TXT.'</td><td>'.$item->DNS_TXT_www.'</td></tr>';
	if ($item->DNS_DMARC_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(34)">DMARC +/-</button></td>';
	}
	elseif ($item->DNS_DMARC_notice == "0" and !str_contains($item->DNS_DMARC, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(34)">DMARC +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(34)">DMARC +/-</button></td>';
	}
	if ($item->DNS_DMARC_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(34)">DMARC +/-</button></td></tr>';
	}
	elseif ($item->DNS_DMARC_www_notice == "0" and !str_contains($item->DNS_DMARC_www, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(34)">DMARC +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(34)">DMARC +/-</button></td></tr>';		
	}
	$html_text .= '<tr id="341" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_DMARC.'</td><td>'.$item->DNS_DMARC_www.'</td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(35)">AS +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(35)">AS +/-</button></td></tr>';
	$html_text .= '<tr id="351" style="display:none;vertical-align:top"><td colspan="3">(To name and achieve the desired situation: different AS, AnyCast, DNSSEC algorithm 13, different DNS software)</td></tr>';
	$html_text .= '<tr id="352" style="display:none;vertical-align:top"><td colspan="2"><b>Autonomous system IPv4:</b><br />'.$item->AS_A.'</td><td><b>Autonomous system IPv4:</b><br />'.$item->AS_A_www.'</td></tr>';
	$html_text .= '<tr id="353" style="display:none"><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr id="354" style="display:none;vertical-align:top"><td colspan="2"><b>Autonomous system IPv6:</b><br />'.$item->AS_AAAA.'</td><td><b>Autonomous system IPv6:</b><br />'.$item->AS_AAAA_www.'</td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(36)">SOA +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(36)">SOA +/-</button></td></tr>';
	$html_text .= '<tr id="361" style="display:none;vertical-align:top"><td colspan="2"><b>Start of Authority:</b><br />'.$item->DNS_SOA.'</td><td><b>Start of Authority:</b><br />'.$item->DNS_SOA_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';	
	if ($item->robots_txt_notice == "1")	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(41)">
		robots.txt +/-</button></td>';
	}
	elseif ($item->robots_txt_notice == "0" and !str_contains($item->robots_txt, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(41)">
		robots.txt +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(41)">robots.txt +/-</button></td>';
	}
	if ($item->robots_txt_www_notice == "1")	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(41)">
		robots.txt +/-</button></td></tr>';
	}
	elseif ($item->robots_txt_www_notice == "0" and !str_contains($item->robots_txt_www, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(41)">
		robots.txt +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(41)">robots.txt +/-</button></td></tr>';
	}	
	$html_text .= '<tr id="411" style="display:none;vertical-align:top"><td colspan="2"><em>'.$item->robots_txt_url.'</em></td><td colspan="1"><em>'.$item->robots_txt_url_www.'</em></td></tr>';
	$html_text .= '<tr id="412" style="display:none;vertical-align:top"><td colspan="2">'.$item->robots_txt.'</td><td colspan="1">'.$item->robots_txt_www.'</td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(42)">meta tags +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(42)">meta tags +/-</button></td></tr>';
	$html_text .= '<tr id="421" style="display:none;vertical-align:top"><td colspan="2">'.$item->meta_tags.'</td><td>'.$item->meta_tags_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	if ($item->security_txt_legacy_notice == "1")	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(43)">legacy security.txt +/-</button></td>';
	}
	elseif ($item->security_txt_legacy_notice == "0" and !str_contains($item->security_txt_url_legacy, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(43)">legacy security.txt +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(43)">legacy security.txt +/-</button></td>';
	}
	if ($item->security_txt_www_legacy_notice == "1")	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(43)">legacy security.txt +/-</button></td></tr>';
	}
	elseif ($item->security_txt_www_legacy_notice == "0" and !str_contains($item->security_txt_url_www_legacy, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(43)">legacy security.txt +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(43)">legacy security.txt +/-</button></td></tr>';
	}
	$html_text .= '<tr id="431" style="display:none;vertical-align:top"><td colspan="2"><em>'.$item->security_txt_url_legacy.'</em></td><td><em>'.$item->security_txt_url_www_legacy.'</em></td></tr>';
	$html_text .= '<tr id="432" style="display:none;vertical-align:top"><td colspan="2">'.$item->security_txt_legacy.'</td><td>'.$item->security_txt_www_legacy.'</td></tr>';
	if ($item->security_txt_relocated_notice == "1")	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(44)">
		.well-known/security.txt +/-</button></td>';
	}
	elseif ($item->security_txt_relocated_notice == "0" and !str_contains($item->security_txt_url_relocated, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(44)">
		.well-known/security.txt +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(44)">.well-known/security.txt +/-</button></td>';
	}
	if ($item->security_txt_www_relocated_notice == "1")	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(44)">
		.well-known/security.txt +/-</button></td></tr>';
	}
	elseif ($item->security_txt_www_relocated_notice == "0" and !str_contains($item->security_txt_url_www_relocated, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(44)">
		.well-known/security.txt +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(44)">.well-known/security.txt +/-</button></td></tr>';
	}
	$html_text .= '<tr id="441" style="display:none;vertical-align:top"><td colspan="2"><em>'.$item->security_txt_url_relocated.'</em></td><td><em>'.$item->security_txt_url_www_relocated.'</em></td></tr>';
	$html_text .= '<tr id="442" style="display:none;vertical-align:top"><td colspan="2">'.$item->security_txt_relocated.'</td><td>'.$item->security_txt_www_relocated.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';		
	if ($item->hsts_header_notice == "1")	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(51)">
		HSTS +/-</button></td>';
	}
	elseif ($item->hsts_header_notice == "0" and !str_contains($item->hsts_header, 'not applicable'))	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(51)">
		HSTS +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(51)">HSTS +/-</button></td>';
	}
	if ($item->hsts_header_www_notice == "1")	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#FFD580;border-color:#FFD580" onclick="SwitchDisplay(51)">
		HSTS +/-</button></td></tr>';
	}
	elseif ($item->hsts_header_www_notice == "0" and !str_contains($item->hsts_header_www, 'not applicable'))	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:#C7F6C7;border-color:#C7F6C7" onclick="SwitchDisplay(51)">
		HSTS +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(51)">HSTS +/-</button></td></tr>';
	}	
	$html_text .= '<tr id="511" style="display:none;vertical-align:top"><td colspan="2">'.$item->hsts_header.'</td><td colspan="1">'.$item->hsts_header_www.'</td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(52)">server header +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(52)">server header +/-</button></td></tr>';
	$html_text .= '<tr id="521" style="display:none;vertical-align:top"><td colspan="2">'.$item->server_header.'</td><td colspan="1">'.$item->server_header_www.'</td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(61)">transfer information +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(61)">transfer information +/-</button></td></tr>';
	$html_text .= '<tr id="611" style="display:none;vertical-align:top"><td colspan="2">'.$item->transfer_information.'</td><td>'.$item->transfer_information_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '</table>';
}
}	
$html_text .= '</div></body></html>';	
echo $html_text;

function clean_url($inputurl)	{
		$output = $inputurl;
		$output = mb_strtolower($output);
		$output = str_replace('http://','', $output);
		$output = str_replace('https://','', $output);
		$output = str_replace('www.','', $output);
		$strpos = mb_strpos($output, '?');
		if ($strpos)	{
			$output = mb_substr($output, 0, $strpos);
		}
		$strpos = mb_strpos($output, '/');
		if ($strpos)	{
			$output = mb_substr($output, 0, $strpos);
		}
		$strpos = mb_strpos($output, ':');
		if ($strpos)	{
			$output = mb_substr($output, 0, $strpos);
		}
		return $output;
}
?>