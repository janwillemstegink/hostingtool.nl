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
		var max = 3
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
	else if (type == 34)	{ // regulation
		var pre = '34';
		var max = 5
	}
	else if (type == 35)	{ // security.txt legacy
		var pre = '35';
		var max = 2
	}
	else if (type == 36)	{ // security.txt
		var pre = '36';
		var max = 2
	}
	else if (type == 50)	{ // about server headers
		var pre = '50';
		var max = 9
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
<table style="font-family:Helvetica, Arial, sans-serif; font-size: 1rem; table-layout: fixed; width:1200px">
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
	$html_text .= '<tr><td colspan="3"><button style="cursor:pointer;font-size:1.05rem;font-style: italic" onclick="SwitchDisplay(22)">About how to redirect +/-</button></td></tr>';
	$html_text .= '<tr id="221" style="display:none;font-style:italic"><td colspan="2">An apex domain is a root domain that does not contain a subdomain part.</td><td>The www subdomain has been considered unnecessary. There are some useful aspects.</td></tr>';
	$html_text .= '<tr id="222" style="display:none;font-style:italic"><td colspan="2">CNAME redirection is not allowed from the root domain.</td><td>If you host elsewhere, such as with microsoft.com, email traffic can remain secure.</td></tr>';
	$html_text .= '<tr id="223" style="display:none;font-style:italic"><td colspan="2"></td><td>For a URL with a subdomain such as www, HSTS can be set more precisely.</td></tr>';
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
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="3"><button style="cursor:pointer;font-size:1.05rem;font-style:italic" onclick="SwitchDisplay(34)">About security.txt Content Expiry +/-</button></td></tr>';
	$html_text .= '<tr id="341" style="display:none;font-style:italic"><td colspan="3">RFC 9116: The "Expires" field indicates the date and time after which the data contained in the "security.txt" file is considered stale and should not be used (as per Section 5.3).</td></tr>';
	$html_text .= '<tr id="342" style="display:none;font-style:italic"><td colspan="3">RFC 9116: It is RECOMMENDED that the value of this field be less than a year into the future to avoid staleness.</td></tr>';
	$html_text .= '<tr id="343" style="display:none;font-style:italic;font-style:italic"><td colspan="3">Suggestion 1: The data contained in the "security.txt" file MUST expire on the date and time as in the "Expires" field, due to the desirability of an annual audit cycle.</td></tr>';
	$html_text .= '<tr id="344" style="display:none;font-style:italic"><td colspan="3">Suggestion 2: For the one-off annual cycle check to work, the "Expires" field date and time is maximally 398 (366+31+1) days into the future, equal to the TLS Certificate Lifespan.</td></tr>';
	$html_text .= '<tr id="345" style="display:none;font-style:italic"><td colspan="3">Suggestion 3: Annual audit requires a scheduled date on an office calendar; and customer requests cannot be dealt with if concentrated in one part of the year.</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(35)">legacy security.txt +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(35)">legacy security.txt +/-</button></td></tr>';
	$html_text .= '<tr id="351" style="display:none;vertical-align:top"><td colspan="2"><em>'.$item->security_txt_url_legacy.'</em></td><td><em>'.$item->security_txt_url_www_legacy.'</em></td></tr>';
	$html_text .= '<tr id="352" style="display:none;vertical-align:top"><td colspan="2">'.$item->security_txt_legacy.'</td><td>'.$item->security_txt_www_legacy.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(36)">.well-known/security.txt +/-</button></td><td><button style="cursor:pointer;font-size:1.05rem" onclick="SwitchDisplay(36)">.well-known/security.txt +/-</button></td></tr>';
	$html_text .= '<tr id="361" style="display:none;vertical-align:top"><td colspan="2"><em>'.$item->security_txt_url_relocated.'</em></td><td><em>'.$item->security_txt_url_www_relocated.'</em></td></tr>';
	$html_text .= '<tr id="362" style="display:none;vertical-align:top"><td colspan="2">'.$item->security_txt_relocated.'</td><td>'.$item->security_txt_www_relocated.'</td></tr>';
	$html_text .= '<tr><td><hr></td><td><hr></td><td><hr></td></tr>';
	$html_text .= '<tr><td colspan="2"><button style="cursor:pointer;font-size:1.05rem;font-style: italic" onclick="SwitchDisplay(50)">About Security Header Requirements +/-</button></td><td></td></tr>';
	$html_text .= '<tr id="501" style="display:none;font-style:italic"><td colspan="3">RFC 6797, 8.1: If a UA receives more than one STS header field in an HTTP response message over secure transport, then the UA MUST process only the first such header field.</td></tr>';
	$html_text .= '<tr id="502" style="display:none;font-style:italic"><td colspan="3">Strict Transport Security over secure HTTPS is called HSTS. The server header is only compliant, even if it is just a URL redirect, with a functioning HSTS security header.</td></tr>';
	$html_text .= '<tr id="503" style="display:none;font-style:italic"><td colspan="3">Although browsers do not strictly enforce this rule above, the internet.nl tool tests that the URL is also the first URL over HTTPS for a security header to work.</td></tr>';
	$html_text .= '<tr id="504" style="display:none;font-style:italic"><td colspan="3">With multiple HSTS header values - an application can also set a security header - strictly speaking, the first security header applies to the user agent (UA).</td></tr>';
	$html_text .= '<tr id="505" style="display:none;font-style:italic"><td colspan="3">The internet.nl tool does only test for an initial header in the initial server header area.</td></tr>';	
	$html_text .= '<tr id="506" style="display:none;font-style:italic"><td colspan="3">In practice, the last security header is chosen, as in the securityheaders.com tool. <b>Note:</b> The securityheaders.com tool does not test and report correctly on rewrite to HTTPS and redirection.</td></tr>';
	$html_text .= '<tr id="507" style="display:none;font-style:italic"><td colspan="3"><b>General approach:</b> Comply with proper initial reading of security headers from the server header(s), and note the interpretation of a subsequent value from an identical security header.</td></tr>';
	$html_text .= '<tr id="508" style="display:none;font-style:italic"><td colspan="3">First rewrite the URL to HTTPS using the checkbox in the control panel, secondly set security header values, and finally, if applicable, (conditionally) redirect in the 301 or 302 way.</td></tr>';
	$html_text .= '<tr id="509" style="display:none;font-style:italic"><td colspan="3">A server header requires sufficient settings before public Internet access can be used safely. And avoid the HSTS preload list without understanding its implications.</td></tr>';
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