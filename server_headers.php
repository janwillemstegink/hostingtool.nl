<?php
session_start();  // is needed with no Scriptcase PHP Generator
echo '<!DOCTYPE html><html lang="en" style="font-size: 90%"><head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta charset="UTF-8" />
<meta http-equiv="x-ua-compatible" content="ie=edge" />
<meta name="robots" content="index" />
<title>Server Header Related Info</title>';
?><script>
	
function SwitchDisplay(type) {

	if (type == 21)			{ // HTTP respons code
		var pre = '21';
		var max = 2
	}
	else if (type == 22)	{ // redirect explanation
		var pre = '22';
		var max = 5
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
	else if (type == 40)	{ // regulation
		var pre = '40';
		var max = 5
	}
	else if (type == 41)	{ // security.txt legacy
		var pre = '41';
		var max = 2
	}
	else if (type == 42)	{ // security.txt .well-known
		var pre = '42';
		var max = 2
	}
	else if (type == 50)	{ // about server headers
		var pre = '50';
		var max = 10
	}
	else if (type == 51)	{ // server headers
		var pre = '51';
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
if (!empty($_GET['url']))	{
	$viewserver = $_GET['url'];
}
else	{
	$viewserver = 'hostingtool.nl';
}

$server_url = 'https://hostingtool.nl/compose_server_headers/index.php?url='.$viewserver;
if (@get_headers($server_url))	{ 
	$xml1 = simplexml_load_file($server_url, "SimpleXMLElement", LIBXML_NOCDATA) or die("An entered url could not be read.");
}
$html_text = '<body><div style="border-collapse:collapse; line-height:120%">
<table style="font-family:Helvetica, Arial, sans-serif; font-size: 1rem; table-layout: fixed; width:1200px; overflow-wrap: break-word">
<tr><th style="width:300px"></th><th style="width:300px"></th><th style="width:600px"></th></tr>';
$html_text .= '<tr style="font-size: .8rem"><td style="font-size: 1.3rem;color:blue;font-weight:bold">Server Header Related Info</td>
<td><form action='.htmlentities($_SERVER['PHP_SELF']).' method="get"><label for="url">Paste a URL and press Enter</label><input type="text" style="width:90%;font-size: 1.2rem" id="url" name="url" value='.$viewserver.'></form></td><td> <a style="font-size: 0.9rem" href="https://github.com/janwillemstegink/hostingtool.nl/issues" target="_blank">issues on GitHub</a> - <a style="font-size: 0.9rem" href="https://webhostingtech.nl/security-setup/set-up-htaccess/" target="_blank">conditional redirect in .htaccess</a> - <a style="font-size: 0.9rem" href="https://janwillemstegink.nl/" target="_blank">janwillemstegink.nl</a></td></tr>';
foreach ($xml1->xpath('//domain') as $item)	{
	simplexml_load_string($item->asXML());
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';	
	$html_text .= '<tr><td colspan="2" style="cursor:pointer;font-size:1.6rem">'.$item->url.'</td><td style="cursor:pointer;font-size:1.6rem">www.'.$item->url.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(21)">HTTP response +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(21)">HTTP response +/-</button></td></tr>';
	$html_text .= '<tr id="211" style="display:table-row;vertical-align:top"><td colspan="2">'.$item->http_code_initial.'</td><td>'.$item->http_code_initial_www.'</td></tr>';
	$html_text .= '<tr id="212" style="display:table-row;vertical-align:top"><td colspan="2">'.$item->http_code_destination.'</td><td>'.$item->http_code_destination_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="3"><button style="cursor:pointer;font-size:1.05rem;font-style: italic" onclick="SwitchDisplay(22)">About redirection from an alias +/-</button></td></tr>';
	$html_text .= '<tr id="221" style="display:none;font-style:italic"><td colspan="2">RFC 1033: "The CNAME record is used for nicknames. The name associated with</td><td>It is common practice for websites to publish content at their registered domain name.</td></tr>';
	$html_text .= '<tr id="222" style="display:none;font-style:italic"><td colspan="2">the RR is the nickname. The data portion is the official name. There must</td><td>The www subdomain has been considered unnecessary. There are some useful aspects.</td></tr>';
	$html_text .= '<tr id="223" style="display:none;font-style:italic"><td colspan="2">not be any other RRs associated with a nickname of the same class."</td><td>If you host elsewhere, such as with www.microsoft.com, email traffic can remain secure.</td></tr>';
	$html_text .= '<tr id="224" style="display:none;font-style:italic"><td colspan="2">RFC 1033 forbids the use of CNAME records at the same node as any other record type.</td><td>For a URL with a subdomain such as www, HSTS can be set more precisely.</td></tr>';
	$html_text .= '<tr id="225" style="display:none;font-style:italic"><td colspan="2">Apex refers to the root/bare/naked domain, or the zone apex, so without a subdomain part.</td><td>See RFC draft for Address-specific DNS Name Redirection: <a style="font-size: 0.9rem" href="https://datatracker.ietf.org/doc/html/draft-ietf-dnsop-aname-01" target="_blank">draft-ietf-dnsop-aname-01</a></td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(31)">CNAME, A, quad A - FCrDNS +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(31)">CNAME, A, quad A - FCrDNS +/-</button></td></tr>';
	$html_text .= '<tr id="311" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_CNAME.'</td><td>'.$item->DNS_CNAME_www.'</td></tr>';
	if ($item->DNS_MX_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(32)">MX +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(32)">MX +/-</button></td>';		
	}
	if ($item->DNS_MX_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(32)">MX +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(32)">MX +/-</button></td></tr>';		
	}
	$html_text .= '<tr id="321" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_MX.'</td><td>'.$item->DNS_MX_www.'</td></tr>';
	if ($item->DNS_TXT_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(33)">TXT +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(33)">TXT +/-</button></td>';
	}
	if ($item->DNS_TXT_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(33)">TXT +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(33)">TXT +/-</button></td></tr>';		
	}
	$html_text .= '<tr id="331" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_TXT.'</td><td>'.$item->DNS_TXT_www.'</td></tr>';
	if ($item->DNS_DMARC_notice == "1" )	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(34)">DMARC +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(34)">DMARC +/-</button></td>';
	}
	if ($item->DNS_DMARC_www_notice == "1" )	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(34)">DMARC +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(34)">DMARC +/-</button></td></tr>';		
	}
	$html_text .= '<tr id="341" style="display:none;vertical-align:top"><td colspan="2">'.$item->DNS_DMARC.'</td><td>'.$item->DNS_DMARC_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="3"><button style="cursor:pointer;font-size:1.05rem;font-style:italic" onclick="SwitchDisplay(40)">About security.txt Content Expiry +/-</button></td></tr>';
	$html_text .= '<tr id="401" style="display:none;font-style:italic"><td colspan="3">RFC 9116: The "Expires" field indicates the date and time after which the data contained in the "security.txt" file is considered stale and should not be used (as per Section 5.3).</td></tr>';
	$html_text .= '<tr id="402" style="display:none;font-style:italic"><td colspan="3">RFC 9116: It is RECOMMENDED that the value of this field be less than a year into the future to avoid staleness.</td></tr>';
	$html_text .= '<tr id="403" style="display:none;font-style:italic;font-style:italic"><td colspan="3">Suggestion 1: The data contained in the "security.txt" file MUST expire on the date and time as in the "Expires" field, due to the desirability of an annual audit cycle.</td></tr>';
	$html_text .= '<tr id="404" style="display:none;font-style:italic"><td colspan="3">Suggestion 2: For the one-off annual cycle check to work, the "Expires" field date and time is maximally 398 (366+31+1) days into the future, equal to the TLS Certificate Lifespan.</td></tr>';
	$html_text .= '<tr id="405" style="display:none;font-style:italic"><td colspan="3">Suggestion 3: Annual audit requires a scheduled date on an office calendar; and customer requests cannot be dealt with if concentrated in one part of the year.</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(41)">legacy security.txt +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(41)">legacy security.txt +/-</button></td></tr>';
	$html_text .= '<tr id="411" style="display:none;vertical-align:top"><td colspan="2"><em>'.$item->security_txt_url_legacy.'</em></td><td><em>'.$item->security_txt_url_www_legacy.'</em></td></tr>';
	$html_text .= '<tr id="412" style="display:none;vertical-align:top"><td colspan="2">'.$item->security_txt_legacy.'</td><td>'.$item->security_txt_www_legacy.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	if ($item->security_txt_notice == "1")	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(42)">
		.well-known/security.txt +/-</button></td>';
	}
	else	{
		$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(42)">.well-known/security.txt +/-</button></td>';
	}
	if ($item->security_txt_www_notice == "1")	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem;background-color:khaki;border-color:khaki" onclick="SwitchDisplay(42)">
		.well-known/security.txt +/-</button></td></tr>';
	}
	else	{
		$html_text .= '<td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(42)">.well-known/security.txt +/-</button></td></tr>';
	}
	$html_text .= '<tr id="421" style="display:none;vertical-align:top"><td colspan="2"><em>'.$item->security_txt_url_relocated.'</em></td><td><em>'.$item->security_txt_url_www_relocated.'</em></td></tr>';
	$html_text .= '<tr id="422" style="display:none;vertical-align:top"><td colspan="2">'.$item->security_txt_relocated.'</td><td>'.$item->security_txt_www_relocated.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;font-style: italic" onclick="SwitchDisplay(50)">About Security Header Requirements +/-</button></td><td></td></tr>';
	$html_text .= '<tr id="501" style="display:none;font-style:italic"><td colspan="3">RFC 6797, 8.1: "If a UA receives more than one STS header field in an HTTP response message over secure transport, then the UA MUST process only the first such header field."</td></tr>';
	$html_text .= '<tr id="502" style="display:none;font-style:italic"><td colspan="3">Strict Transport Security over secure HTTPS is called HSTS. The server header is only compliant, even if it is just a URL redirect, with a functioning HSTS security header.</td></tr>';
	$html_text .= '<tr id="503" style="display:none;font-style:italic"><td colspan="3">Although browsers do not strictly enforce this rule above, the internet.nl tool tests that the URL is also the first URL over HTTPS for a security header to work.</td></tr>';
	$html_text .= '<tr id="504" style="display:none;font-style:italic"><td colspan="3">With multiple HSTS header values - an application can also set a security header - strictly speaking, the first security header applies to the user agent (UA).</td></tr>';
	$html_text .= '<tr id="505" style="display:none;font-style:italic"><td colspan="3">The internet.nl tool does test for an initial header in the initial server header area.</td></tr>';	
	$html_text .= '<tr id="506" style="display:none;font-style:italic"><td colspan="3">Web browser Chrome and the securityheaders.com tool, show values from application to server header level. The first value, starting from server header level, should be set.</td></tr>';
	$html_text .= '<tr id="507" style="display:none;font-style:italic"><td colspan="3"><b>Note:</b> The securityheaders.com tool does not test and report correctly on rewrite to HTTPS and redirection.</td></tr>';
	$html_text .= '<tr id="508" style="display:none;font-style:italic"><td colspan="3"><b>General approach:</b> Comply with proper initial reading of security headers from the server header(s), and note the interpretation of a subsequent value from an identical security header.</td></tr>';
	$html_text .= '<tr id="509" style="display:none;font-style:italic"><td colspan="3">First rewrite the URL to HTTPS using the checkbox in the control panel, secondly set security header values, and finally, if applicable, (conditionally) redirect in the 301 or 302 way.</td></tr>';
	$html_text .= '<tr id="5010" style="display:none;font-style:italic"><td colspan="3">A server header requires sufficient settings before public Internet access can be used safely. And avoid the HSTS preload list without understanding its implications.</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(51)">server header +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(51)">server header +/-</button></td></tr>';
	$html_text .= '<tr id="511" style="display:none;vertical-align:top"><td colspan="2">'.$item->server_header.'</td><td colspan="1">'.$item->server_header_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(61)">transfer information +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(61)">transfer information +/-</button></td></tr>';
	$html_text .= '<tr id="611" style="display:none;vertical-align:top"><td colspan="2">'.$item->transfer_information.'</td><td>'.$item->transfer_information_www.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
}
$html_text .= '</table></div></body></html>';
echo $html_text;
?>