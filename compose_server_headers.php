<?php
//$_GET['url'] = 'fryslan.frl';
//$_GET['url'] = 'janwillemstegink.nl';
//$_GET['url'] = 'top.nic.br';

if (!empty($_GET['url']))	{
	if (strlen($_GET['url']))	{
		$url = trim($_GET['url']);
		$url = clean_url($url);
		$_GET['url'] = rawurlencode($url);
		echo write_file($url);
		die();
	}
	else	{	
		//die("No URL has been entered as input.");	
	}
}
else	{	
	die("No input has been entered.");
}

function puny_code($input)	{
	return idn_to_ascii($input, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
}

function puny_decode($input)	{
	if (mb_substr($input, 0, 7) == 'http://')	{
		return 'http://' . idn_to_utf8(mb_substr($input, 7), IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);		
	}
	elseif (mb_substr($input, 0, 8) == 'https://')	{
		return 'https://' . idn_to_utf8(mb_substr($input, 8), IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);		
	}
	return idn_to_utf8($input, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
}

function write_file($inputurl)	{
	
//$php_version = (float)phpversion();
$own_ip = $_SERVER['SERVER_ADDR'];

$same_server = false;
$cname_limited = false;
$matches_server = false;
$DNS_CNAME = '';
$CNAMED = '';	
$DNS_CNAME_notice = 0;	
if (!str_contains($inputurl, '_'))	{
	$DNS_CNAME = get_cname_target($inputurl);	
	if (strlen($DNS_CNAME))	{		
		$CNAMED = $DNS_CNAME;
		$DNS_CNAME = $inputurl.' '.$DNS_CNAME.' points to';
		$cname_limited = true;
	}	
	$AS_A = '';	
	$AS_AAAA = '';
	$AS_A_www = '';	
	$AS_AAAA_www = '';
	$ttl = '';
	$array = dns_get_record(puny_code($inputurl), DNS_A);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ttl') {
				$ttl = $value2;
			}
			if ($key2 == 'ip')	{
				$DNS_CNAME .= '<br /><b>IPv4</b> TTL '.$ttl.', A '.$value2;
				$AS_A .= get_as_info($value2);
				$rDNS = get_host($value2);
				$rDNS_FC = '';
				if ($rDNS != $value2)	{
					$array2 = dns_get_record(puny_code($rDNS), DNS_A);
					foreach($array2 as $k1 => $v1) {
						foreach($v1 as $k2 => $v2) {
							if ($k2 == 'ttl') {
								$ttl = $v2;
							}										
							if ($k2 == 'ip')	{
								$rDNS_FC .= '<br />FCrDNS TTL '.$ttl.', A '.$v2;
							}
						}		
					}
				}	
				$DNS_CNAME .= ' points to<br />rDNS: '.$rDNS. ' points to'.$rDNS_FC;	
				if ($value2 == $own_ip)	$same_server = true;
				if ($rDNS == $inputurl) $matches_server = true;
				if ($rDNS == $value2)	{
					$DNS_CNAME .= '<br />(A reverse DNS does not exist)';
				}
				elseif (str_contains($rDNS_FC, $value2))	{
				}
				else	{
					$DNS_CNAME_notice = 1;
					$DNS_CNAME .= '<br />(No matching forward-confirmed)';
				}
			}	
		}
	}
	$array = dns_get_record(puny_code($inputurl), DNS_AAAA);	
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ttl') {
				$ttl = $value2;
			}
			if ($key2 == 'ipv6') {
				$DNS_CNAME .= '<br /><b>IPv6</b> TTL '.$ttl.', AAAA '.$value2;
				$AS_AAAA .= get_as_info($value2);
				$rDNS = get_host($value2);
				$rDNS_FC = '';
				if ($rDNS != $value2)	{
					$array2 = dns_get_record(puny_code($rDNS), DNS_AAAA);
					foreach($array2 as $k1 => $v1) {
						foreach($v1 as $k2 => $v2) {
							if ($k2 == 'ttl') {
								$ttl = $v2;
							}
							if ($k2 == 'ipv6')	{
								$rDNS_FC .= '<br />FCrDNS TTL '.$ttl.', AAAA '.$v2;
							}	
						}
					}		
				}
				$DNS_CNAME .= ' points to<br />rDNS: '.$rDNS. ' points to'.$rDNS_FC;
				if ($value2 == $own_ip)	$same_server = true;
				if ($rDNS == $inputurl) $matches_server = true;
				if ($rDNS == $value2)	{
					$DNS_CNAME .= '<br />(A reverse DNS does not exist)';
				}
				elseif (str_contains($rDNS_FC, $value2))	{
				}
				else	{
					$DNS_CNAME_notice = 1;
					$DNS_CNAME .= '<br />(No matching forward-confirmed)';
				}
			}
		}	
	}
	if (!$cname_limited and strlen($DNS_CNAME))	{
		$DNS_CNAME = $inputurl.' works with A and/or quad A' . $DNS_CNAME;
	}
}	
if (strlen($DNS_CNAME))	{
	if (!str_contains($DNS_CNAME, 'IPv4'))	{
		$DNS_CNAME .= '<br />(IPv4 is not supported)';
	}	
	elseif (!str_contains($DNS_CNAME, 'IPv6'))	{
		$DNS_CNAME .= '<br />(IPv6 is not supported)';
	}
}
$same_server_www = false;
$cname_limited_www = false;
$matches_server_www = false;
$DNS_CNAME_www = '';
$CNAMED_www = '';	
$DNS_CNAME_www_notice = 0;	
if (!str_contains('www.'.$inputurl, '_'))	{	
	$DNS_CNAME_www = '';	
	$DNS_CNAME_www = get_cname_target('www.'.$inputurl);
	if (strlen($DNS_CNAME_www))	{
		$CNAMED_www = $DNS_CNAME_www;
		$DNS_CNAME_www = 'www.'.$inputurl.' '.$DNS_CNAME_www.' points to';
		$cname_limited_www = true;
	}
	$array = dns_get_record(puny_code('www.'.$inputurl), DNS_A);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ttl') {
				$ttl = $value2;
			}
			elseif ($key2 == 'ip') {
				$DNS_CNAME_www .= '<br /><b>IPv4</b> TTL '.$ttl.', A '.$value2;
				$AS_A_www .= get_as_info($value2);
				$rDNS = get_host($value2);
				$rDNS_FC = '';
				if ($rDNS != $value2)	{
					$array2 = dns_get_record(puny_code($rDNS), DNS_A);
					foreach($array2 as $k1 => $v1) {
						foreach($v1 as $k2 => $v2) {
							if ($k2 == 'ttl') {
								$ttl = $v2;
							}
							if ($k2 == 'ip')	{
								$rDNS_FC .= '<br />FCrDNS TTL '.$ttl.', A '.$v2;
							}	
						}
					}		
				}
				$DNS_CNAME_www .= ' points to<br />rDNS: '.$rDNS. ' points to'.$rDNS_FC;
				if ($value2 == $own_ip)	$same_server_www = true;
				if ($rDNS == 'www.'.$inputurl) $matches_server_www = true;
				if ($rDNS == $value2)	{
					$DNS_CNAME_www .= '<br />(A reverse DNS does not exist)';
				}
				elseif (str_contains($rDNS_FC, $value2))	{
				}
				else	{
					$DNS_CNAME_www_notice = 1;
					$DNS_CNAME_www .= '<br />(No matching forward-confirmed)';
				}
			}
		}
	}
	$array = dns_get_record(puny_code('www.'.$inputurl), DNS_AAAA);	
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ttl') {
				$ttl = $value2;
			}
			elseif ($key2 == 'ipv6') {
				$DNS_CNAME_www .= '<br /><b>IPv6</b> TTL '.$ttl.', AAAA '.$value2;
				$AS_AAAA_www .= get_as_info($value2);
				$rDNS = get_host($value2);
				$rDNS_FC = '';
				if ($rDNS != $value2)	{
					$array2 = dns_get_record(puny_code($rDNS), DNS_AAAA);
					foreach($array2 as $k1 => $v1) {
						foreach($v1 as $k2 => $v2) {
							if ($k2 == 'ttl') {
								$ttl = $v2;
							}
							if ($k2 == 'ipv6')	{
								$rDNS_FC .= '<br />FCrDNS TTL '.$ttl.', AAAA '.$v2;
							}	
						}
					}		
				}
				$DNS_CNAME_www .= ' points to<br />rDNS: '.$rDNS. ' points to'.$rDNS_FC;
				if ($value2 == $own_ip)	$same_server_www = true;
				if ($rDNS == 'www.'.$inputurl) $matches_server_www = true;
				if ($rDNS == $value2)	{
					$DNS_CNAME_www .= '<br />(A reverse DNS does not exist)';
				}
				elseif (str_contains($rDNS_FC, $value2))	{
				}
				else	{
					$DNS_CNAME_www_notice = 1;
					$DNS_CNAME_www .= '<br />(No matching forward-confirmed)';
				}
			}
		}
	}
	if (!$cname_limited_www and strlen($DNS_CNAME_www))	{
		$DNS_CNAME_www = 'www.'.$inputurl.' works with A and/or quad A' . $DNS_CNAME_www;
	}
}	
if (strlen($DNS_CNAME_www))	{
	if (!str_contains($DNS_CNAME_www, 'IPv4'))	{
		$DNS_CNAME_www .= '<br />(IPv4 is not supported)';
	}	
	elseif (!str_contains($DNS_CNAME_www, 'IPv6'))	{
		$DNS_CNAME_www .= '<br />(IPv6 is not supported)';
	}
}
$DNS_CAA = '';
$array = dns_get_record(puny_code($inputurl), DNS_CAA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'host') {
			$DNS_CAA .= $value2 . ' ';
		}
		elseif ($key2 == 'ttl') {
			$DNS_CAA .= 'TTL '.$value2.', CAA ';
		}
		elseif ($key2 == 'flags') {
			$DNS_CAA .= $value2 . ' ';
		}
		elseif ($key2 == 'tag') {
			$DNS_CAA .= $value2 . ' ';
		}
		elseif ($key2 == 'value') {
			$DNS_CAA .= $value2 . '<br />';
		}	
	}
}
$DNS_CAA_www = '';
$array = dns_get_record(puny_code('www.'.$inputurl), DNS_CAA);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'host') {
			$DNS_CAA_www .= $value2 . ' ';
		}
		elseif ($key2 == 'ttl') {
			$DNS_CAA_www .= 'TTL '.$value2.', CAA ';
		}
		elseif ($key2 == 'flags') {
			$DNS_CAA_www .= $value2 . ' ';
		}
		elseif ($key2 == 'tag') {
			$DNS_CAA_www .= $value2 . ' ';
		}
		elseif ($key2 == 'value') {
			$DNS_CAA_www .= $value2 . '<br />';
		}
	}
}	
$DNS_MX = '';
$DNS_MX_notice = 0;	
$array = dns_get_record(puny_code($inputurl), DNS_MX);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'host') {
			$DNS_MX .= $value2 . ' ';
		}
		elseif ($key2 == 'ttl') {
			$DNS_MX .= 'TTL '.$value2.', MX ';
		}
		elseif ($key2 == 'pri') {
			$DNS_MX .= $value2 . ' ';
		}	
		elseif ($key2 == 'target') {
			$DNS_MX .= $value2 . '.<br />';
			$DNS_MX .= get_mx_ips($value2);
		}	
	}
}
if (strlen($DNS_MX))	{
	if (str_contains($DNS_MX, 'IPv6 on demand'))	{
		$DNS_MX_notice = 1;		
	}
	elseif (str_contains($DNS_MX, 'reverse DNS'))	{
		//$DNS_MX_notice = 1;		
	}	
}
else	{	
	if ($cname_limited)	{
		$DNS_MX .= '(Null MX would combine with future ANAME, flattened CNAME)<br />';
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
$array = dns_get_record(puny_code('www.'.$inputurl), DNS_MX);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'host') {
			$DNS_MX_www .= $value2 . ' ';
		}
		elseif ($key2 == 'ttl') {
			$DNS_MX_www .= 'TTL '.$value2.', MX ';
		}
		elseif ($key2 == 'pri') {
			$DNS_MX_www .= $value2 . ' ';
		}	
		elseif ($key2 == 'target') {
			$DNS_MX_www .= $value2 . '.<br />';
			$DNS_MX_www .= get_mx_ips($value2);
		}
	}
}
if (strlen($DNS_MX_www))	{
	if (str_contains($DNS_MX_www, 'IPv6 on demand'))	{
		$DNS_MX_www_notice = 1;
	}
	elseif (str_contains($DNS_MX_www, 'reverse DNS'))	{
		//$DNS_MX_www_notice = 1;		
	}
}
else	{
	if ($cname_limited_www)	{
		$DNS_MX_www .= '(Null MX would combine with future ANAME, flattened CNAME)<br />';
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
$temp1 = '';
$temp2 = '';	
$temp3 = '';	
$array = dns_get_record(puny_code($inputurl), DNS_TXT);
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'host') {
			$temp1 = $value2;
		}
		elseif ($key2 == 'ttl') {
			$temp2 = $value2;
		}
       	elseif ($key2 == 'txt') {
			$temp3 = $value2;
		}	
    }
	$DNS_TXT .= $temp1.' TTL '.$temp2.', TXT '.$temp3.'<br />';
}
if (!str_contains($inputurl, '_'))	{	
	if (str_contains(strtolower($DNS_TXT), 'v=spf1'))	{
		$counter = substr_count(strtolower($DNS_TXT), 'v=spf1');
		if ($counter > 1)	{
			$DNS_TXT_notice = 1;
			$DNS_TXT .= '(no working SPF authorization: '.  $counter . 'x v=spf1)<br />';
		}
	}
	else	{	
		if ($cname_limited)	{
			$DNS_TXT .= '("v=spf1 -all" would combine with future ANAME, flattened CNAME)<br />';
		}
		elseif (!strlen($DNS_CNAME))	{
			$DNS_TXT .= 'not applicable';		
		}
		elseif ($matches_server)	{
			$DNS_TXT_notice = 1;
			$DNS_TXT .= '("v=spf1 +a ~all" would secure email)<br />';
		}
		elseif (str_contains($DNS_MX, '0 .'))	{
			$DNS_TXT_notice = 1;
			$DNS_TXT .= '("v=spf1 -all" plus "reject" in DMARC would block email)<br />';
		}
		elseif (strlen($DNS_MX))	{
			$DNS_TXT_notice = 1;
			$DNS_TXT .= '("some "v=spf1" setting might be applicable)<br />';
		}
		else	{
			$DNS_TXT_notice = 1;
			$DNS_TXT .= '("v=spf1 -all" plus "reject" in DMARC would block email)<br />';
		}	
	}
}
$DNS_TXT_www = '';
$DNS_TXT_www_notice = 0;	
$temp1 = '';
$temp2 = '';
$temp3 = '';	
$array = dns_get_record(puny_code('www.'.$inputurl), DNS_TXT);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key2 == 'host') {
			$temp1 = $value2;
		}	
		elseif ($key2 == 'ttl') {
			$temp2 = $value2;
		}
       	elseif ($key2 == 'txt') {
			$temp3 = $value2;
		}	
    }
	$DNS_TXT_www .= $temp1.' TTL '.$temp2.', TXT '.$temp3.'<br />';
}
if (!str_contains('www.'.$inputurl, '_'))	{
	if (str_contains(strtolower($DNS_TXT_www), 'v=spf1'))	{
		$counter = substr_count(strtolower($DNS_TXT_www), 'v=spf1');
		if ($counter > 1)	{
			$DNS_TXT_www_notice = 1;
			$DNS_TXT_www .= '(no working SPF authorization: '.  $counter . 'x v=spf1)<br />';
		}
	}
	else	{
		if ($cname_limited_www)	{
			$DNS_TXT_www .= '("v=spf1 -all" would combine with future ANAME, flattened CNAME)<br />';
		}
		elseif (!strlen($DNS_CNAME_www))	{
			$DNS_TXT_www .= 'not applicable';		
		}
		elseif ($matches_server_www)	{
			$DNS_TXT_www_notice = 1;
			$DNS_TXT_www .= '("v=spf1 +a ~all" would secure email)<br />';
		}
		elseif (str_contains($DNS_MX_www, '0 .'))	{
			$DNS_TXT_www_notice = 1;
			$DNS_TXT_www .= '("v=spf1 -all" plus "reject" in DMARC would block email)<br />';
		}
		elseif (strlen($DNS_MX_www))	{
			$DNS_TXT_www_notice = 1;
			$DNS_TXT_www .= '("some "v=spf1" setting might be applicable)<br />';
		}
		else	{
			$DNS_TXT_www_notice = 1;
			$DNS_TXT_www .= '("v=spf1 -all" plus "reject" in DMARC would block email)<br />';
		}	
	}
}	
$DNS_DMARC = dmarc_list($inputurl);
$DNS_DMARC_notice = 0;	
if (!strlen($DNS_DMARC))	{
	if (!strlen($DNS_CNAME))	{
		$DNS_DMARC .= 'not applicable';		
	}
	else	{
		$DNS_DMARC_notice = 1;
		$DNS_DMARC .= '(DMARC misses in email settings)<br />';	
	}	
}
else	{
	if (str_contains($DNS_DMARC, 'p=reject'))	{	
	}
	elseif (str_contains($DNS_DMARC, 'p=quarantine'))	{
	}
	elseif (str_contains($DNS_DMARC, 'p=none') and !str_contains($DNS_DMARC, '@'))	{
		$DNS_DMARC_notice = 1;
		$DNS_DMARC .= '(DMARC with p=none and no email address is not secure)<br />';
	}
	if (str_contains($DNS_DMARC, 'underscore'))	{
		$DNS_DMARC_notice = 1;
		$DNS_DMARC .= '(Non-server URLs are uniquely grouped in DNS settings if they start with an underscore)<br />';
	}	
}	
$DNS_DMARC_www = dmarc_list('www.'.$inputurl);
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
else	{
	if (str_contains($DNS_DMARC_www, 'sp=reject'))	{	
	}
	elseif (str_contains($DNS_DMARC_www, 'p=reject') and !str_contains($DNS_DMARC_www, 'sp=none') and !str_contains($DNS_DMARC_www, 'sp=quarantine'))	{	
	}
	else	{
		$DNS_DMARC_www_notice = 1;
		$DNS_DMARC_www .= '(DMARC can be stronger with "reject" for subdomains that exist or not)<br />';
	}
	if (str_contains($DNS_DMARC_www, 'underscore'))	{
		$DNS_DMARC_www_notice = 1;
		$DNS_DMARC_www .= '(Non-server URLs are uniquely grouped in DNS settings if they start with an underscore)<br />';
	}
}
$DNS_SOA = '';
$array = dns_get_record(puny_code($inputurl), DNS_SOA);	
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		$DNS_SOA .= $key2 . ': ' . $value2 . '<br />';
    }
}
if (strlen($DNS_SOA) and $cname_limited)	{	
	$DNS_SOA = '(CNAME determines fetched data)<br />'.$DNS_SOA;
}
elseif (strlen($DNS_SOA))	{
	$DNS_SOA = '(this can be one of a registrant, second-level or top-level domain)<br />'.$DNS_SOA;
}	
else	{	
	$DNS_SOA = '(this is not one of a registrant, second-level or top-level domain)<br />';	
}	
$DNS_SOA_www = '';
$array = dns_get_record(puny_code('www.').puny_code($inputurl), DNS_SOA);		
foreach($array as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		$DNS_SOA_www .= $key2 . ': ' . $value2 . '<br />';
    }
}
if (strlen($DNS_SOA_www) and $cname_limited_www)	{	
	$DNS_SOA_www = '(CNAME determines fetched data)<br />'.$DNS_SOA_www;	
}
elseif (strlen($DNS_SOA_www))	{	
	$DNS_SOA_www = '(this domain would fit an excluded status)<br />'.$DNS_SOA_www;	
}
else	{
	$DNS_SOA_www = '(no registrant domain)<br />';	
}	
//$DNSSEC_A = 0;
//$output = shell_exec('dig @9.9.9.9 +dnssec '.$inputurl.' A');
//if (str_contains($output,'RRSIG'))	{
//	$DNSSEC_A = 1;
//}
//$DNSSEC_AAAA = 0;
//$output = shell_exec('dig @9.9.9.9 +dnssec '.$inputurl.' AAAA');
//if (str_contains($output,'RRSIG'))	{
//	$DNSSEC_AAAA = 1;
//}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);	
curl_setopt($ch, CURLOPT_NOBODY, 1);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
//curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // CURL_IPRESOLVE_V6, by default CURL_IPRESOLVE_WHATEVER
	
//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36');
	
$curl_error = '';	
$http_code_initial = 'initial: not applicable';
$http_code_notice = 0;
if (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'http://'.$inputurl);
	$curl_server_header = curl_exec($ch);
	$http_code_initial = 'initial: ';
	if (!curl_errno($ch)) {
		$initial_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);		
		$http_code_initial .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. puny_decode($initial_url);		
		if (strlen($redirect_url))	{
			if	(puny_decode(str_replace(':443','', clean_redirect_url($redirect_url))) == puny_decode(str_replace('http://', 'https://', $initial_url)))	{
				$http_code_initial .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}	
			elseif (puny_decode(str_replace(':443','', clean_redirect_url($redirect_url)) . '/') == puny_decode(str_replace('http://', 'https://', $initial_url)))	{
				$http_code_initial .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			elseif (puny_decode(str_replace(':443','', clean_redirect_url($redirect_url))) == puny_decode(str_replace('http://', 'https://', $initial_url . '/')))	{
				$http_code_initial .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			elseif (str_contains(puny_decode($redirect_url), 'https://'))	{
				$http_code_notice = 1;	
				$http_code_initial .= '<br />(unsafe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			else	{
				$http_code_notice = 1;
				$http_code_initial .= '<br />(unsafe from ' . puny_decode($initial_url) . ' to HTTP ' . puny_decode($redirect_url) . ')';
			}
		}	
	}
	elseif (curl_errno($ch) == 7)	{
		$http_code_notice = 1;
		$http_code_initial .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);	
	}	
	else	{
		$http_code_notice = 1;
		$http_code_initial .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		$curl_error = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}	
}
$curl_error_www = '';	
$http_code_initial_www = 'initial: not applicable';
$http_code_www_notice = 0;
if (strlen($DNS_CNAME_www))	{
	curl_setopt($ch, CURLOPT_URL, 'http://www.'.$inputurl);
	$curl_server_header_www = curl_exec($ch);
	$http_code_initial_www = 'initial: ';
	if (!curl_errno($ch)) {
		$initial_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
		$http_code_initial_www .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. puny_decode($initial_url);
		if (strlen($redirect_url))	{
			if	(puny_decode(str_replace(':443','', clean_redirect_url($redirect_url))) == puny_decode(str_replace('http://', 'https://', $initial_url)))	{
				$http_code_initial_www .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}	
			elseif (puny_decode(str_replace(':443','', clean_redirect_url($redirect_url)) . '/') == puny_decode(str_replace('http://', 'https://', $initial_url)))	{
				$http_code_initial_www .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			elseif (puny_decode(str_replace(':443','', clean_redirect_url($redirect_url))) == puny_decode(str_replace('http://', 'https://', $initial_url . '/')))	{
				$http_code_initial_www .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			elseif (str_contains(puny_decode($redirect_url), 'https://'))	{
				$http_code_www_notice = 1;
				$http_code_initial_www .= '<br />(unsafe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			else	{
				$http_code_www_notice = 1;
				$http_code_initial_www .= '<br />(unsafe from ' . puny_decode($initial_url) . ' to HTTP ' . puny_decode($redirect_url) . ')';
			}
		}
	}
	elseif (curl_errno($ch) == 7)	{
		$http_code_www_notice = 1;
		$http_code_initial_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);	
	}
	else	{
		$http_code_www_notice = 1;
		$http_code_initial_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		$curl_error_www = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
}
$meta_tags = 'not applicable';
if (strlen($DNS_CNAME) and strlen($curl_error))	{
	$meta_tags = $curl_error;
}
elseif (strlen($DNS_CNAME))	{
	$meta_tags = '';
	$array = get_meta_tags('https://'.$inputurl);
	foreach($array as $key1 => $value1) {
		$meta_tags .= $key1 . ': ' . $value1 . "\n";
    }
}
$meta_tags_www = 'not applicable';	
if (strlen($DNS_CNAME_www) and strlen($curl_error_www))	{
	$meta_tags_www = $curl_error_www;	
}
elseif (strlen($DNS_CNAME_www))	{
	$meta_tags_www = '';
	$array = get_meta_tags('https://www.'.$inputurl);
	foreach($array as $key1 => $value1) {
		$meta_tags_www .= $key1 . ': ' . $value1 . "\n";
    }
}	
$https_code_initial = 'initial: not applicable';
$https_code_notice = 0;		
$server_header = 'not applicable';
$server_header_code = '';	
$hsts_header = 'not applicable';
$hsts_header_notice = 0;
$transfer_information = 'not applicable';	
if (strlen($DNS_CNAME) and strlen($curl_error))	{
	$https_code_initial = 'initial: '. $curl_error;
	$server_header = $curl_error;	
	$hsts_header = $curl_error;
	$transfer_information = $curl_error;	
}
elseif (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputurl);	
	$curl_server_header = curl_exec($ch);
	$https_code_initial = 'initial: ';
	if (!curl_errno($ch)) {
		$initial_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
		$server_header_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$https_code_initial .= $server_header_code . ' - '. puny_decode($initial_url);
		if (strlen($redirect_url))	{
			if (str_contains(puny_decode($redirect_url), 'https://'))	{
				$https_code_initial .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			else	{
				$https_code_notice = 1;
				$https_code_initial .= '<br />(unsafe from ' . puny_decode($initial_url) . ' to HTTP ' . puny_decode($redirect_url) . ')';
			}	
		}
	}
	else	{
		$https_code_initial .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
	$arr_server_header = explode (",", $curl_server_header);
	$server_header = '';
	$hsts_header = 'The server header';
	foreach($arr_server_header as $key1 => $value1) {
		$server_header .= $key1 . "\n" . $value1 . "\n";	
		if (str_contains(strtolower($value1), 'strict-transport-security'))	{
			$hsts_header .= ' contains HSTS';
			if (str_contains($value1, 'includeSubDomains'))	{
				$hsts_header .= ', and for its subdomains';
			}
			else	{
				$hsts_header .= ', not for its subdomains';
			}
			if (str_contains($value1, 'preload'))	{
				$hsts_header .= ', with preload.' . "\n";
			}
			else	{
				$hsts_header .= ', without preload.' . "\n";
			}													 
		}	
	}
	if (intval($server_header_code) == 500)	{
		$hsts_header .= ' does not show HSTS with response 500.';
	}
	elseif (intval($server_header_code) == 403)	{
		$hsts_header .= ' does not show HSTS with response 403.';
	}
	elseif (str_contains($hsts_header, 'HSTS'))	{
	}
	else	{
		$hsts_header_notice = 1;
		$hsts_header .= ' does not contain HSTS.';	
	}	
	$arr_transfer_information = curl_getinfo($ch);
	$transfer_information = '';	
	foreach($arr_transfer_information as $key1 => $value1) {
		$transfer_information .= $key1 . ': ' . $value1 . '<br />';	
	}
}	
$https_code_initial_www = 'initial: not applicable';
$https_code_www_notice = 0;	
$server_header_www = 'not applicable';
$server_header_code_www = '';	
$hsts_header_www = 'not applicable';
$hsts_header_www_notice = 0;	
$transfer_information_www = 'not applicable';
if (strlen($DNS_CNAME_www) and strlen($curl_error_www))	{
	$https_code_initial_www = 'initial: ' . $curl_error_www;
	$server_header_www = $curl_error_www;
	$hsts_header_www = $curl_error_www;
	$transfer_information_www = $curl_error_www;	
}
elseif (strlen($DNS_CNAME_www))	{	
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputurl);
	$curl_server_header_www = curl_exec($ch);
	$https_code_initial_www = 'initial: ';
	if (!curl_errno($ch)) {
		$initial_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
		$server_header_code_www = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$https_code_initial_www .= $server_header_code_www . ' - '. puny_decode($initial_url);
		if (strlen($redirect_url))	{
			if (str_contains(puny_decode($redirect_url), 'https://'))	{
				$https_code_initial_www .= '<br />(safe from ' . puny_decode($initial_url) . ' to ' . puny_decode($redirect_url) . ')';
			}
			else	{
				$https_code_www_notice = 1;
				$https_code_initial_www .= '<br />(unsafe from ' . puny_decode($initial_url) . ' to HTTP ' . puny_decode($redirect_url) . ')';
			}	
		}
	}
	else	{
		$https_code_www_notice = 1;
		$https_code_initial_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}	
	$arr_server_header_www = explode (",", $curl_server_header_www);
	$server_header_www = '';
	$hsts_header_www = 'The server header';
	foreach($arr_server_header_www as $key1 => $value1) {
		$server_header_www .= $key1 . "\n" . $value1 . "\n";
		if (str_contains(strtolower($value1), 'strict-transport-security'))	{				
			$hsts_header_www .= ' contains HSTS';
			if (str_contains($value1, 'includeSubDomains'))	{
				$hsts_header_www .= ', and for its subdomains';
			}
			else	{
				$hsts_header_www .= ', not for its subdomains';
			}
			if (str_contains($value1, 'preload'))	{
					$hsts_header_www .= ', with preload.' . "\n";
			}
			else	{
				$hsts_header_www .= ', without preload.' . "\n";	
			}
		}	
	}
	if (intval($server_header_code) == 500)	{
		$hsts_header_www .= ' does not show HSTS with response 500.';
	}
	elseif (intval($server_header_code) == 403)	{
		$hsts_header_www .= ' does not show HSTS with response 403.';
	}
	elseif (str_contains($hsts_header_www, 'HSTS'))	{
	}
	else	{
		$hsts_header_www_notice = 1;
		$hsts_header_www .= ' does not contain HSTS.';
	}
	$arr_transfer_information_www = curl_getinfo($ch);
	$transfer_information_www = '';	
	foreach($arr_transfer_information_www as $key1 => $value1) {
		$transfer_information_www .= $key1 . ': ' . $value1 . '<br />';
	}
}
$security_txt_legacy = '';	
$security_txt_url_legacy = 'not applicable';
$security_txt_legacy_plain = 0;
$security_txt_legacy_notice = 0;
$security_txt_relocated = '';
$security_txt_url_relocated = 'not applicable';	
$security_txt_relocated_plain = 0;
$security_txt_relocated_notice = 0;
	
curl_setopt($ch, CURLOPT_HEADER, 0);		
curl_setopt($ch, CURLOPT_NOBODY, 0);	
	
if (strlen($DNS_CNAME) and strlen($curl_error))	{
	$security_txt_legacy = '';	
	$security_txt_url_legacy = $curl_error;	
	$security_txt_relocated = '';
	$security_txt_url_relocated = $curl_error;		
}	
elseif (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputurl.'/security.txt');	
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$security_txt_legacy = curl_exec($ch);
	$security_txt_url_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (!curl_errno($ch)) {
		if ($effective_url_legacy == $security_txt_url_legacy)	{
		}
		else	{
			$security_txt_url_legacy .= '<br />'.$effective_url_legacy;
		}
		$received_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (intval($received_http_code) == 200)	{
			if (str_contains($effective_url_legacy, '/security.txt'))	{
				if (str_contains(curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 'text/plain'))	{
					$security_txt_legacy = $effective;
					$security_txt_legacy_plain = 1;
				}
				else	{
					$security_txt_legacy = 'No text/plain content type received.';
				}
			}	
			else	{
				$security_txt_legacy = 'HTTP 200 OK received without a security.txt file.';
			}
		}
		elseif (intval($received_http_code) == 500)	{
			$security_txt_legacy = 'HTTP code 500 received (without security.txt).';
		}
		elseif (intval($received_http_code) == 403)	{
			$security_txt_legacy = 'HTTP code 403 received (without security.txt).';
		}
		elseif ($matches_server)	{
			$security_txt_legacy = 'HTTP code '. $received_http_code . ' received ('. $inputurl. ' is the server name).';
		}
		else	{
			$security_txt_legacy = 'HTTP code '. $received_http_code . ' received.';
		}	
	}	
	else	{
		$security_txt_legacy = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputurl.'/.well-known/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);		
	$security_txt_relocated = curl_exec($ch);
	$security_txt_url_relocated = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url_relocated = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (!curl_errno($ch)) {
		if ($effective_url_relocated == $security_txt_url_relocated)	{
		}
		else	{
			$security_txt_url_relocated .= '<br />'.$effective_url_relocated;
		}		
		$received_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (intval($received_http_code) == 200)	{
			if (str_contains($effective_url_relocated, '/security.txt'))	{
				if (str_contains(curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 'text/plain'))	{
					$security_txt_relocated = $effective;
					$security_txt_relocated_plain = 1;
				}
				else	{
					$security_txt_relocated_notice = 1;
					$security_txt_relocated = 'No text/plain content type received.';
				}	
			}
			else	{
				$security_txt_relocated_notice = 1;
				$security_txt_relocated = 'HTTP 200 OK received without a security.txt file.';
			}
		}
		elseif (intval($received_http_code) == 500)	{
			$security_txt_relocated_notice = 1;
			$security_txt_relocated = 'HTTP code 500 received (without security.txt).';
		}
		elseif (intval($received_http_code) == 403)	{
			$security_txt_relocated_notice = 1;
			$security_txt_relocated = 'HTTP code 403 received (without security.txt).';
		}
		elseif ($matches_server)	{
			$security_txt_relocated_notice = 1;
			$security_txt_relocated = 'HTTP code '. $received_http_code . ' received ('. $inputurl. ' is the server name).';
		}
		else	{
			$security_txt_relocated_notice = 1;
			$security_txt_relocated = 'HTTP code '. $received_http_code . ' received.';
		}		
	}	
	else	{
		$security_txt_relocated_notice = 1;
		$security_txt_relocated = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
}
$security_txt_www_legacy = '';
$security_txt_url_www_legacy = 'not applicable';
$security_txt_www_legacy_plain = 0;
$security_txt_www_legacy_notice = 0;
$security_txt_www_relocated = '';
$security_txt_url_www_relocated = 'not applicable';
$security_txt_www_relocated_plain = 0;	
$security_txt_www_relocated_notice = 0;
if (strlen($DNS_CNAME_www) and strlen($curl_error_www))	{
	$security_txt_www_legacy = '';
	$security_txt_url_www_legacy = $curl_error_www;	
	$security_txt_www_relocated = '';
	$security_txt_url_www_relocated = $curl_error_www;
}	
elseif (strlen($DNS_CNAME_www))	{
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputurl.'/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$security_txt_www_legacy = curl_exec($ch);
	$security_txt_url_www_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url_www_legacy = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (!curl_errno($ch)) {
		if ($effective_url_www_legacy == $security_txt_url_www_legacy)	{
		}
		else	{
			$security_txt_url_www_legacy .= '<br />'.$effective_url_www_legacy;
		}
		$received_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (intval($received_http_code) == 200)	{
			if (str_contains($effective_url_www_legacy, '/security.txt'))	{
				if (str_contains(curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 'text/plain'))	{
					$security_txt_www_legacy = $effective;
					$security_txt_www_legacy_plain = 1;
				}
				else	{
					$security_txt_www_legacy = 'No text/plain content type received.';
				}
			}	
			else	{
				$security_txt_www_legacy = 'HTTP 200 OK received without a security.txt file.';
			}
		}
		elseif (intval($received_http_code) == 500)	{
			$security_txt_www_legacy = 'HTTP code 500 received (without security.txt).';
		}
		elseif (intval($received_http_code) == 403)	{
			$security_txt_www_legacy = 'HTTP code 403 received (without security.txt).';
		}
		elseif ($matches_server_www)	{
			$security_txt_www_legacy = 'HTTP code '. $received_http_code . ' received (www.'. $inputurl. ' is the server name).';
		}
		else	{
			$security_txt_www_legacy = 'HTTP code '. $received_http_code . ' received.';
		}	
	}	
	else	{
		$security_txt_www_legacy = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputurl.'/.well-known/security.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);	
	$security_txt_www_relocated = curl_exec($ch);
	$security_txt_url_www_relocated = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url_www_relocated = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (!curl_errno($ch)) {
		if ($effective_url_www_relocated == $security_txt_url_www_relocated)	{
		}
		else	{
			$security_txt_url_www_relocated .= '<br />'.$effective_url_www_relocated;
		}
		$received_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (intval($received_http_code) == 200)	{
			if (str_contains($effective_url_www_relocated, '/security.txt'))	{
				if (str_contains(curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 'text/plain'))	{
					$security_txt_www_relocated = $effective;
					$security_txt_www_relocated_plain = 1;
				}
				else	{
					$security_txt_www_relocated_notice = 1;
					$security_txt_www_relocated = 'No text/plain content type received.';
				}	
			}
			else	{
				$security_txt_www_relocated_notice = 1;
				$security_txt_www_relocated = 'HTTP 200 OK received without a security.txt file.';
			}
		}
		elseif (intval($received_http_code) == 500)	{
			$security_txt_www_relocated_notice = 1;
			$security_txt_www_relocated = 'HTTP code 500 received (without security.txt).';
		}
		elseif (intval($received_http_code) == 403)	{
			$security_txt_www_relocated_notice = 1;
			$security_txt_www_relocated = 'HTTP code 403 received (without security.txt).';
		}
		elseif ($matches_server_www)	{
			$security_txt_www_relocated_notice = 1;
			$security_txt_www_relocated = 'HTTP code '. $received_http_code . ' received (www.'. $inputurl. ' is the server name).';
		}
		else	{
			$security_txt_www_relocated_notice = 1;
			$security_txt_www_relocated = 'HTTP code '. $received_http_code . ' received.';
		}
	}	
	else	{
		$security_txt_www_relocated_notice = 1;
		$security_txt_www_relocated = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
}
if ($effective_url_legacy != $effective_url_relocated and $security_txt_legacy_plain and $security_txt_relocated_plain)	{
	$security_txt_legacy_notice = 1;
	$security_txt_legacy .= "\r\n" . '(the legacy file url differs from the .well-known file url)';
}
if ($effective_url_www_legacy != $effective_url_www_relocated and $security_txt_www_legacy_plain and $security_txt_www_relocated_plain)	{
	$security_txt_www_legacy_notice = 1;
	$security_txt_www_legacy .= "\r\n" . '(the legacy file url differs from the .well-known file url)';
}
$robots_txt = 'not applicable';
$robots_txt_notice = 0;
if (strlen($DNS_CNAME) and strlen($curl_error))	{
	$robots_txt = $curl_error;
}	
elseif (strlen($DNS_CNAME))	{
	curl_setopt($ch, CURLOPT_URL, 'https://'.$inputurl.'/robots.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);	
	$robots_txt = curl_exec($ch);
	$robots_txt_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (!curl_errno($ch)) {
		if ($effective_url == $robots_txt_url)	{
		}
		else	{
			$robots_txt_url .= '<br />'.$effective_url;
		}
		$received_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);	
		if (intval($received_http_code) == 200)	{
			if (str_contains($effective_url, '/robots.txt'))	{
				$robots_txt = $effective;
			}	
			else	{
				$robots_txt_notice = 1;
				$robots_txt = 'HTTP 200 OK received without a robots.txt file';
			}
		}
		elseif (intval($received_http_code) == 500)	{
			$robots_txt = 'HTTP code 500 received (without robots.txt).';
		}
		elseif (intval($received_http_code) == 403)	{
			$robots_txt = 'HTTP code 403 received (without robots.txt).';
		}
		elseif ($matches_server)	{
			$robots_txt_notice = 1;
			$robots_txt = 'HTTP code '. $received_http_code . ' received ('. $inputurl. ' is the server name).';
		}
		else	{
			$robots_txt_notice = 1;
			$robots_txt = 'HTTP code '. $received_http_code . ' received.';
		}
	}	
	else	{
		$robots_txt_notice = 1;
		$robots_txt = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
}
$robots_txt_www = 'not applicable';
$robots_txt_www_notice = 0;
if (strlen($DNS_CNAME_www) and strlen($curl_error_www))	{
	$robots_txt_www = $curl_error_www;
}	
elseif (strlen($DNS_CNAME_www))	{
	curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputurl.'/robots.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);	
	$robots_txt_www = curl_exec($ch);
	$robots_txt_url_www = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$effective = curl_exec($ch);
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	if (!curl_errno($ch)) {
		if ($effective_url == $robots_txt_url_www)	{
		}
		else	{
			$robots_txt_url_www .= '<br />'.$effective_url;
		}
		$received_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (intval($received_http_code) == 200)	{
			if (str_contains($effective_url, '/robots.txt'))	{
				$robots_txt_www = $effective;
			}	
			else	{
				$robots_txt_www_notice = 1;
				$robots_txt_www = 'HTTP 200 OK received without a robots.txt file';
			}
		}
		elseif (intval($received_http_code) == 500)	{
			$robots_txt_www = 'HTTP code 500 received (without robots.txt).';
		}
		elseif (intval($received_http_code) == 403)	{
			$robots_txt_www = 'HTTP code 403 received (without robots.txt).';
		}
		elseif ($matches_server_www)	{
			$robots_txt_www_notice = 1;
			$robots_txt_www = 'HTTP code '. $received_http_code . ' received (www.'. $inputurl. ' is the server name).';
		}
		else	{
			$robots_txt_www_notice = 1;
			$robots_txt_www = 'HTTP code '. $received_http_code . ' received.';
		}	
	}	
	else	{
		$robots_txt_www_notice = 1;
		$robots_txt_www = 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
	}
}
$http_code_destination = 'destination: not applicable';
if (strlen($DNS_CNAME) and strlen($curl_error))	{
	$http_code_destination = 'destination: ' . $curl_error;	
}	
elseif (strlen($DNS_CNAME))	{
	$http_code_destination = 'destination: ';
	if (!$same_server)	{
		curl_setopt($ch, CURLOPT_URL, 'http://'.$inputurl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);				
		$target = curl_exec($ch);
		if (!curl_errno($ch)) {
			$destination_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			$http_code_destination .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. $destination_url;
			if (strlen($destination_url))	{
				if (str_contains($destination_url, 'https://'))	{
				}
				elseif (intval(curl_getinfo($ch, CURLINFO_HTTP_CODE)) == 403)	{
				}	
				else	{
					$http_code_notice = 1;
					$http_code_destination .= '<br />(HTTPS misses in the destination url: ' . $destination_url . ')';					
				}
			}	
			else	{
				$http_code_notice = 1;
				$http_code_destination .= '<br />(No destination url)';				
			}	
		}
		elseif (curl_errno($ch) == 7)	{
			$http_code_notice = 1;
			$http_code_destination .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);	
		}
		else	{
			$http_code_notice = 1;
			$http_code_destination .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}	
	}
	else	{
		$http_code_destination .= '(No cURL on the same server)';
	}	
}
$http_code_destination_www = 'destination: not applicable';	
if (strlen($DNS_CNAME_www) and strlen($curl_error_www))	{
	$http_code_destination_www = 'destination: ' . $curl_error_www;	
}	
elseif (strlen($DNS_CNAME_www))	{
	$http_code_destination_www = 'destination: ';
	if (!$same_server_www)	{
		curl_setopt($ch, CURLOPT_URL, 'http://www.'.$inputurl);	
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);	
		$target = curl_exec($ch);
		if (!curl_errno($ch)) {	
			$destination_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			$http_code_destination_www .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. $destination_url;
			if (strlen($destination_url))	{
				if (str_contains($destination_url, 'https://'))	{
				}
				elseif (intval(curl_getinfo($ch, CURLINFO_HTTP_CODE)) == 403)	{
				}
				else	{
					$http_code_www_notice = 1;
					$http_code_destination_www .= '<br />(HTTPS misses in the destination url: ' . $destination_url . ')';					
				}
			}	
			else	{
				$http_code_www_notice = 1;
				$http_code_destination_www .= '<br />(No destination url)';				
			}
		}
		elseif (curl_errno($ch) == 7)	{
			$http_code_www_notice = 1;
			$http_code_destination_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);	
		}
		else	{
			$http_code_www_notice = 1;
			$http_code_destination_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);
		}
	}
	else	{
		$http_code_destination_www .= '(No cURL on the same server)';	
	}
}	
$https_code_destination = 'destination: not applicable';
if (strlen($DNS_CNAME) and strlen($curl_error))	{
	$https_code_destination = 'destination: ' . $curl_error;
}	
elseif (strlen($DNS_CNAME))	{
	$https_code_destination = 'destination: ';
	if (!$same_server)	{
		curl_setopt($ch, CURLOPT_URL, 'https://'.$inputurl);	
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);			
		$target = curl_exec($ch);
		if (!curl_errno($ch)) {	
			$destination_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			$https_code_destination .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. $destination_url;
			if (strlen($destination_url))	{
				if (str_contains($destination_url, 'https://'))	{
				}
				else	{
					$https_code_notice = 1;
					$https_code_destination .= '<br />(HTTPS misses in the destination url: ' . $destination_url . ')';					
				}
				if (strlen($CNAMED))	{
					$destination_url = clean_url($destination_url);
					if ($CNAMED == 'www.'.$inputurl)	{
						$DNS_CNAME_notice = 1;	
						$DNS_CNAME .= '<br />(The destination does not require CNAME; A/AAAA, MX and TXT can work)';
					}	
				}
			}	
			else	{
				$https_code_notice = 1;
				$https_code_destination .= '<br />(No destination url)';				
			}
		}
		else	{
			$https_code_notice = 1;
			$https_code_destination .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);	
		}	
	}
	else	{
		$https_code_destination .= '(No cURL on the same server)';		
	}
}
$https_code_destination_www = 'destination: not applicable';
if (strlen($DNS_CNAME_www) and strlen($curl_error_www))	{
	$https_code_destination_www = 'destination: ' . $curl_error_www;	
}	
elseif (strlen($DNS_CNAME_www))	{
	$https_code_destination_www = 'destination: ';
	if (!$same_server_www)	{
		curl_setopt($ch, CURLOPT_URL, 'https://www.'.$inputurl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);			
		$target = curl_exec($ch);
		if (!curl_errno($ch)) {	
			$destination_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			$https_code_destination_www .= curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' - '. $destination_url;
			if (strlen($destination_url))	{
				if (str_contains($destination_url, 'https://'))	{
					}
				else	{
					$https_code_www_notice = 1;
					$https_code_destination_www .= '<br />(HTTPS misses in the destination url: ' . $destination_url . ')';			
				}
				if (strlen($CNAMED_www))	{
					$destination_url = clean_url($destination_url);
					if ($CNAMED_www == $inputurl)	{
						$DNS_CNAME_www_notice = 1;
						$DNS_CNAME_www .= '<br />(The destination does not require CNAME; A/AAAA, MX and TXT can work)';
					}	
				}	
			}	
			else	{
				$https_code_www_notice = 1;
				$https_code_destination_www .= '<br />(No destination url)';				
			}
		}
		else	{
			$https_code_www_notice = 1;
			$https_code_destination_www .= 'cURL error '.curl_errno($ch).' - '.curl_error($ch);	
		}
	}
	else	{
		$https_code_destination_www .= '(No cURL on the same server)';	
	}
}
//die();
	
//curl_close($ch); //not necessary from PHP 8
	
$doc = new DOMDocument("1.0", "UTF-8");
$doc->xmlStandalone = true;	
$doc->formatOutput = true;		
	
$domains = $doc->createElement("domains");
$doc->appendChild($domains);
	
$domain = $doc->createElement("domain");	
$domains->appendChild($domain);
	
$domain->setAttribute("item", $inputurl);	
	
$domain_url = $doc->createElement("url");
$domain_url->appendChild($doc->createCDATASection(htmlentities($inputurl)));		
$domain->appendChild($domain_url);		

$domain_DNS_CNAME = $doc->createElement("DNS_CNAME");
$domain_DNS_CNAME->appendChild($doc->createCDATASection($DNS_CNAME));		
$domain->appendChild($domain_DNS_CNAME);	
	
$domain_DNS_CNAME_www = $doc->createElement("DNS_CNAME_www");
$domain_DNS_CNAME_www->appendChild($doc->createCDATASection($DNS_CNAME_www));		
$domain->appendChild($domain_DNS_CNAME_www);

$domain_DNS_CNAME_notice = $doc->createElement("DNS_CNAME_notice");
$domain_DNS_CNAME_notice->appendChild($doc->createCDATASection($DNS_CNAME_notice));		
$domain->appendChild($domain_DNS_CNAME_notice);	
	
$domain_DNS_CNAME_www_notice = $doc->createElement("DNS_CNAME_www_notice");
$domain_DNS_CNAME_www_notice->appendChild($doc->createCDATASection($DNS_CNAME_www_notice));		
$domain->appendChild($domain_DNS_CNAME_www_notice);	
	
$domain_DNS_CAA = $doc->createElement("DNS_CAA");
$domain_DNS_CAA->appendChild($doc->createCDATASection($DNS_CAA));		
$domain->appendChild($domain_DNS_CAA);	
	
$domain_DNS_CAA_www = $doc->createElement("DNS_CAA_www");
$domain_DNS_CAA_www->appendChild($doc->createCDATASection($DNS_CAA_www));		
$domain->appendChild($domain_DNS_CAA_www);	
	
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
	
$domain_AS_A = $doc->createElement("AS_A");
$domain_AS_A->appendChild($doc->createCDATASection(nl2br(htmlentities($AS_A))));
$domain->appendChild($domain_AS_A);	
		
$domain_AS_AAAA = $doc->createElement("AS_AAAA");
$domain_AS_AAAA->appendChild($doc->createCDATASection(nl2br(htmlentities($AS_AAAA))));
$domain->appendChild($domain_AS_AAAA);
		
$domain_AS_A_www = $doc->createElement("AS_A_www");
$domain_AS_A_www->appendChild($doc->createCDATASection(nl2br(htmlentities($AS_A_www))));
$domain->appendChild($domain_AS_A_www);	
		
$domain_AS_AAAA_www = $doc->createElement("AS_AAAA_www");
$domain_AS_AAAA_www->appendChild($doc->createCDATASection(nl2br(htmlentities($AS_AAAA_www))));
$domain->appendChild($domain_AS_AAAA_www);	
	
$domain_DNS_SOA = $doc->createElement("DNS_SOA");
$domain_DNS_SOA->appendChild($doc->createCDATASection($DNS_SOA));
$domain->appendChild($domain_DNS_SOA);
	
$domain_DNS_SOA_www = $doc->createElement("DNS_SOA_www");
$domain_DNS_SOA_www->appendChild($doc->createCDATASection($DNS_SOA_www));
$domain->appendChild($domain_DNS_SOA_www);	
	
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
	
$domain_security_txt_legacy_notice = $doc->createElement("security_txt_legacy_notice");
$domain_security_txt_legacy_notice->appendChild($doc->createCDATASection($security_txt_legacy_notice));
$domain->appendChild($domain_security_txt_legacy_notice);	
	
$domain_security_txt_www_legacy_notice = $doc->createElement("security_txt_www_legacy_notice");
$domain_security_txt_www_legacy_notice->appendChild($doc->createCDATASection($security_txt_www_legacy_notice));
$domain->appendChild($domain_security_txt_www_legacy_notice);
	
$domain_security_txt_relocated_notice = $doc->createElement("security_txt_relocated_notice");
$domain_security_txt_relocated_notice->appendChild($doc->createCDATASection($security_txt_relocated_notice));
$domain->appendChild($domain_security_txt_relocated_notice);	
	
$domain_security_txt_www_relocated_notice = $doc->createElement("security_txt_www_relocated_notice");
$domain_security_txt_www_relocated_notice->appendChild($doc->createCDATASection($security_txt_www_relocated_notice));
$domain->appendChild($domain_security_txt_www_relocated_notice);	
	
$domain_robots_txt_url = $doc->createElement("robots_txt_url");
$domain_robots_txt_url->appendChild($doc->createCDATASection($robots_txt_url));		
$domain->appendChild($domain_robots_txt_url);	
	
$domain_robots_txt_url_www = $doc->createElement("robots_txt_url_www");
$domain_robots_txt_url_www->appendChild($doc->createCDATASection($robots_txt_url_www));		
$domain->appendChild($domain_robots_txt_url_www);	
	
$domain_robots_txt = $doc->createElement("robots_txt");
$domain_robots_txt->appendChild($doc->createCDATASection(nl2br(htmlentities($robots_txt))));
$domain->appendChild($domain_robots_txt);	
	
$domain_robots_txt_www = $doc->createElement("robots_txt_www");
$domain_robots_txt_www->appendChild($doc->createCDATASection(nl2br(htmlentities($robots_txt_www))));
$domain->appendChild($domain_robots_txt_www);	
	
$domain_robots_txt_notice = $doc->createElement("robots_txt_notice");
$domain_robots_txt_notice->appendChild($doc->createCDATASection($robots_txt_notice));
$domain->appendChild($domain_robots_txt_notice);	
	
$domain_robots_txt_www_notice = $doc->createElement("robots_txt_www_notice");
$domain_robots_txt_www_notice->appendChild($doc->createCDATASection($robots_txt_www_notice));
$domain->appendChild($domain_robots_txt_www_notice);
	
$domain_meta_tags = $doc->createElement("meta_tags");
$domain_meta_tags->appendChild($doc->createCDATASection(nl2br(htmlentities($meta_tags))));
$domain->appendChild($domain_meta_tags);	
	
$domain_meta_tags_www = $doc->createElement("meta_tags_www");
$domain_meta_tags_www->appendChild($doc->createCDATASection(nl2br(htmlentities($meta_tags_www))));
$domain->appendChild($domain_meta_tags_www);	
	
$domain_http_code_initial = $doc->createElement("http_code_initial");
$domain_http_code_initial->appendChild($doc->createCDATASection($http_code_initial));
$domain->appendChild($domain_http_code_initial);

$domain_http_code_destination = $doc->createElement("http_code_destination");
$domain_http_code_destination->appendChild($doc->createCDATASection($http_code_destination));
$domain->appendChild($domain_http_code_destination);
	
$domain_https_code_initial = $doc->createElement("https_code_initial");
$domain_https_code_initial->appendChild($doc->createCDATASection($https_code_initial));
$domain->appendChild($domain_https_code_initial);

$domain_https_code_destination = $doc->createElement("https_code_destination");
$domain_https_code_destination->appendChild($doc->createCDATASection($https_code_destination));
$domain->appendChild($domain_https_code_destination);
	
$domain_http_code_notice = $doc->createElement("http_code_notice");
$domain_http_code_notice->appendChild($doc->createCDATASection($http_code_notice));
$domain->appendChild($domain_http_code_notice);

$domain_http_code_www_notice = $doc->createElement("http_code_www_notice");
$domain_http_code_www_notice->appendChild($doc->createCDATASection($http_code_www_notice));
$domain->appendChild($domain_http_code_www_notice);
	
$domain_https_code_notice = $doc->createElement("https_code_notice");
$domain_https_code_notice->appendChild($doc->createCDATASection($https_code_notice));
$domain->appendChild($domain_https_code_notice);

$domain_https_code_www_notice = $doc->createElement("https_code_www_notice");
$domain_https_code_www_notice->appendChild($doc->createCDATASection($https_code_www_notice));
$domain->appendChild($domain_https_code_www_notice);	
	
$domain_server_header = $doc->createElement("server_header");
$domain_server_header->appendChild($doc->createCDATASection(nl2br(htmlentities($server_header))));		
$domain->appendChild($domain_server_header);
	
$domain_hsts_header = $doc->createElement("hsts_header");
$domain_hsts_header->appendChild($doc->createCDATASection(nl2br(htmlentities($hsts_header))));
$domain->appendChild($domain_hsts_header);
	
$domain_hsts_header_notice = $doc->createElement("hsts_header_notice");
$domain_hsts_header_notice->appendChild($doc->createCDATASection(nl2br(htmlentities($hsts_header_notice))));
$domain->appendChild($domain_hsts_header_notice);		
	
$domain_transfer_information = $doc->createElement("transfer_information");
$domain_transfer_information->appendChild($doc->createCDATASection($transfer_information));
$domain->appendChild($domain_transfer_information);	
	
$domain_http_code_initial_www = $doc->createElement("http_code_initial_www");
$domain_http_code_initial_www->appendChild($doc->createCDATASection($http_code_initial_www));
$domain->appendChild($domain_http_code_initial_www);
	
$domain_http_code_destination_www = $doc->createElement("http_code_destination_www");
$domain_http_code_destination_www->appendChild($doc->createCDATASection($http_code_destination_www));
$domain->appendChild($domain_http_code_destination_www);

$domain_https_code_initial_www = $doc->createElement("https_code_initial_www");
$domain_https_code_initial_www->appendChild($doc->createCDATASection($https_code_initial_www));
$domain->appendChild($domain_https_code_initial_www);
	
$domain_https_code_destination_www = $doc->createElement("https_code_destination_www");
$domain_https_code_destination_www->appendChild($doc->createCDATASection($https_code_destination_www));
$domain->appendChild($domain_https_code_destination_www);	

$domain_server_header_www = $doc->createElement("server_header_www");
$domain_server_header_www->appendChild($doc->createCDATASection(nl2br(htmlentities($server_header_www))));		
$domain->appendChild($domain_server_header_www);

$domain_hsts_header_www = $doc->createElement("hsts_header_www");
$domain_hsts_header_www->appendChild($doc->createCDATASection(nl2br(htmlentities($hsts_header_www))));
$domain->appendChild($domain_hsts_header_www);
	
$domain_hsts_header_www_notice = $doc->createElement("hsts_header_www_notice");
$domain_hsts_header_www_notice->appendChild($doc->createCDATASection(nl2br(htmlentities($hsts_header_www_notice))));
$domain->appendChild($domain_hsts_header_www_notice);	
	
$domain_transfer_information_www = $doc->createElement("transfer_information_www");
$domain_transfer_information_www->appendChild($doc->createCDATASection($transfer_information_www));
$domain->appendChild($domain_transfer_information_www);	
	
$domains->appendChild($domain);
$doc->appendChild($domains);
	
//return $doc->saveXML(NULL, LIBXML_NOEMPTYTAG);
return $doc->saveXML();
}

function get_cname_target($inputurl)	{
	$output = '';
	$array = dns_get_record(puny_code($inputurl), DNS_CNAME);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ttl') {
				$output .= 'TTL '.$value2.', CNAME ';
			}
			elseif ($key2 == 'target') {
				$output .= $value2;
			}	
		}
	}
	return $output;
}

function get_mx_ips($inputurl)	{
	$output = '';
	$array = dns_get_record(puny_code($inputurl), DNS_A);
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ip')	{
				$output .= 'IPv4: '.$value2.'<br />';
				$rDNS = get_host($value2);
				$rDNS_FC = '';
				if ($rDNS != $value2)	{
					$array2 = dns_get_record(puny_code($rDNS), DNS_A);
					foreach($array2 as $k1 => $v1) {
						foreach($v1 as $k2 => $v2) {
							if ($k2 == 'ip')	{
								$rDNS_FC .= $v2.' ';
							}	
						}
					}		
				}
				if ($value2 == $own_ip)	$same_server = true;
				if ($rDNS == $inputurl) $matches_server = true;
				if (str_contains($rDNS_FC, $value2))	{
				}
				elseif ($rDNS == $value2)	{
					$output .= 'No reverse DNS<br />';
				}
				else	{
					$output .= '(The reverse DNS is not forward-confirmed)<br />';
				}
			}
		}	
	}
	$array = dns_get_record(puny_code($inputurl), DNS_AAAA); 
	foreach($array as $key1 => $value1) {
		foreach($value1 as $key2 => $value2) {
			if ($key2 == 'ipv6')	{
				$output .= 'IPv6: '.$value2.'<br />';
				$rDNS = get_host($value2);
				$rDNS_FC = '';
				if ($rDNS != $value2)	{
					$array2 = dns_get_record(puny_code($rDNS), DNS_AAAA);
					foreach($array2 as $k1 => $v1) {
						foreach($v1 as $k2 => $v2) {
							if ($k2 == 'ipv6')	{
								$rDNS_FC .= $v2.' ';
							}	
						}
					}		
				}
				if ($value2 == $own_ip)	$same_server = true;
				if ($rDNS == $inputurl) $matches_server = true;
				if (str_contains($rDNS_FC, $value2))	{
				}
				elseif ($rDNS == $value2)	{
					$output .= 'No reverse DNS<br />';
				}
				else	{
					$output .= '(The reverse DNS is not forward-confirmed)<br />';
				}				
			}
		}
	}
	if (str_contains($inputurl, 'mail.protection.outlook.com'))	{
		if (str_contains($output, 'IPv6'))	{
		}
		else	{	
			$output .= '(IPv6 on demand; Microsoft is gradually enabling it by default starting October 1, 2024)<br />';
		}		
	}	
	return $output;
}

function remove_subdomain($inputurl)	{
	$strpos = mb_strpos($inputurl, '.');
	$inputurl = mb_substr($inputurl, $strpos + 1);
	return $inputurl;
}

function dmarc_list($inputurl)	{
	$output = '';
	$strpos = 1;
	if (mb_substr($inputurl, 0, 1) == '_')	{
		return 'not applicable';
	}
	while ($strpos)	{
		$underscore = '';
		$array = dns_get_record(puny_code('_dmarc.'.$inputurl), DNS_TXT);
		$cname_value = get_cname_target('_dmarc.'.$inputurl);
		foreach($cname_value as $key1 => $value1) {
			foreach($cname_value as $key2 => $value2) {
				$inputurl = $cname_value;
				$array = dns_get_record(puny_code($inputurl), DNS_TXT);
			}
		}
		$temp1 = '';
		$temp2 = '';
		$temp3  = '';
		foreach($array as $key1 => $value1) {
			foreach($value1 as $key2 => $value2) {
				if ($key2 == 'host') {
					$temp1 = $value2;
					if (str_contains($value2, '_'))	{
						if (mb_substr($value2, 0, 1) != '_')	{
							$underscore = ' (no underscore first)';
						}
					}
					else	{
						$underscore = ' (without underscore)';
					}	
				}
				elseif ($key2 == 'ttl') {
					$temp2 = $value2;
				}
				elseif ($key2 == 'txt') {
					$temp3 = $value2;
				}				
			}
		}
		if (strlen($temp1) and str_contains(str_replace(' ', '', $temp3), 'v=DMARC1;'))	{
			$output .= $temp1 . $underscore.' TTL '.$temp2.', TXT '.$temp3.'<br />';
		}
		if (str_contains($output, 'v=DMARC1;'))	{
			break;
		}
		$inputurl = remove_subdomain($inputurl);
		if (!strpos($inputurl, '.'))	{
			break;	
		}		
	}
	return $output;
}

function get_ip_info($inputip)	{
	//$inputip = '2a01:4f8:161:520a::2';
	$url = 'https://ipinfo.io/'.$inputip.'/json';
	$details = json_decode(file_get_contents($url));
	return('IP block: '.$details->org.' ('.$details->country.')');
}

function get_as_info($inputip)	{
	//$inputip = '2a01:4f8:161:520a::2';
	//$inputip = '136.144.238.43';
	$url = 'http://ip-api.com/json/'.$inputip.'?fields=countryCode,regionName,city,isp,org,as,asname,reverse,query';
	$as = json_decode(file_get_contents($url), true);
	$output = '';
	foreach($as as $key1 => $value1) {
		$output .= $key1 . ': ' . $value1 .  "\n";
	}
	return($output);
}

function get_ip_type($inputip) {
    if (filter_var($inputip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))	{
        return "IPv4";
    }
	elseif (filter_var($inputip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))	{
        return "IPv6";
    }
	else {
		return "Invalid IP";
    }
}

function get_host($inputip)	{
	//$time_start = microtime(true);
	if (get_ip_type($inputip) == 'IPv4') {
		$reverse_ipv4 = implode('.', array_reverse(explode('.', $inputip))) . '.in-addr.arpa';
		$result = dns_get_record($reverse_ipv4, DNS_PTR);
		if ($result) {
			//echo $inputip . ' -> ' . $result[0]['target'] . ' (' . strval(round(microtime(true) - $time_start, 1)) . ' seconds)<br />';
    		return $result[0]['target'];	
		}	
	}
	elseif (get_ip_type($inputip) == 'IPv6') {
		$expanded = inet_ntop(inet_pton($inputip));
    	$nibbles = array_reverse(str_split(bin2hex(inet_pton($expanded))));
    	$reverse_ipv6 = implode('.', $nibbles) . '.ip6.arpa';
		$result = dns_get_record($reverse_ipv6, DNS_PTR);
		if ($result) {
			//echo $inputip . ' -> ' . $result[0]['target'] . ' (' . strval(round(microtime(true) - $time_start, 1)) . ' seconds)<br />';
			return $result[0]['target'];
		}
	}
	//echo $inputip . ' -> ... (' . strval(round(microtime(true) - $time_start, 1)) . ' seconds)<br />';
	return $inputip;

	//$fp = @fsockopen($inputip, 443, $errno, $errstr, 5); // 5-second timeout
	//echo 'Ping on '.$inputip.': '.$fp.'<br />';
	//if ($fp) {
    	//fclose($fp);
		//echo 'reverse host name: '.  gethostbyaddr($inputip).'<br />';
		//return gethostbyaddr($inputip);
	//}
	//else {
		//echo $inputip . ': ' . "Ping failed: $errstr ($errno)".'<br />';
		//return "skipped: $errstr ($errno)";
	//}	
}

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

function clean_redirect_url($inputurl)	{
		$output = clean_url($inputurl);
		if (str_contains($inputurl, 'https://www.'))	{
			$output = 'https://www.' . $output;
		} 		
		elseif (str_contains($inputurl, 'https://'))	{
			$output = 'https://' . $output;
		}
		elseif (str_contains($inputurl, 'http://www.'))	{
			$output = 'http://www.' . $output;
		}
		elseif (str_contains($inputurl, 'http://'))	{
			$output = 'http://' . $output;
		}
		return $output;
}							
?>