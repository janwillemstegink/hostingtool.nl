<?php
//$_GET['domain'] = 'hostingtool.nl';
if (!empty($_GET['domain']))	{
	if (strlen($_GET['domain']))	{
		$domain = trim($_GET['domain']);
		$domain = mb_strtolower($domain);
		$domain = str_replace('http://','', $domain);
		$domain = str_replace('https://','', $domain);
		if (substr_count($domain, '.') > 1)	{
			$domain = str_replace('www.','', $domain);
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
		die("No domain name is filled as input");	
	}
}
else	{	
	die("No domain name variable as input");
}

function write_file($inputdomain)	{
	
$zone_top_level_domain = mb_substr($inputdomain, strrpos($inputdomain, '.') + 1);	
switch ($zone_top_level_domain) {
	case 'nl':
    	$url = 'https://rdap.sidn.nl/domain/';
    	break;		
	//case 'cc': // transparencia.cc
    //	$url = 'https://rdap.godaddy.com/v1/domain/';
    //	break;
	case 'biz':
    	$url = 'https://rdap.nic.biz/domain/';
    	break;	
	case 'com':
    	$url = 'https://rdap.verisign.com/com/v1/domain/';
    	break;	
	case 'net':
    	$url = 'https://rdap.verisign.com/net/v1/domain/';
    	break;
	case 'org':
    	$url = 'https://rdap.publicinterestregistry.org/rdap/domain/';
		break;
	case 'ca':
		$url = 'https://rdap.ca.fury.ca/rdap/domain/';
		break;
	case 'ch':
    	$url = 'https://rdap.nic.ch/domain/';	
    	break;		
	case 'de':
    	$url = 'https://rdap.denic.de/domain/';
    	break;
	case 'fr':
   		$url = 'https://rdap.nic.fr/domain/';
   		break;
	//case 'it':
   	//	$url = 'https://rdap.pubtest.nic.it/domain/';
   	//	break;
	case 'uk':
    	$url = 'https://rdap.nominet.uk/uk/domain/';
    	break;
	case 'amsterdam':
    	$url = 'https://rdap.nic.amsterdam/domain/';
    	break;
	case 'politie':
    	$url = 'https://rdap.nic.politie/domain/';
    	break;		
	default:
   		die("No match with a top level domain.");
}	
$url = $url.$inputdomain;
$obj = json_decode(file_get_contents($url), true);		
$rdap_conformance = $obj['rdapConformance'];
$object_class_name = $obj['objectClassName'];
$zone_notice_0_title = $obj['notices'][0]['title'];
$zone_notice_0_description_0 = $obj['notices'][0]['description'][0];	
$zone_notice_0_description_1 = $obj['notices'][0]['description'][1];
$zone_notice_0_links_0_href = $obj['notices'][0]['links'][0]['href'];
$zone_notice_0_links_0_type = $obj['notices'][0]['links'][0]['type'];
$zone_notice_1_title = $obj['notices'][1]['title'];
$zone_notice_1_description_0 = $obj['notices'][1]['description'][0];
$zone_notice_1_description_1 = $obj['notices'][1]['description'][1];
$zone_notice_1_links_0_href = $obj['notices'][1]['links'][0]['href'];
$zone_notice_1_links_0_type = $obj['notices'][1]['links'][0]['type'];	
$zone_notice_2_title = $obj['notices'][2]['title'];	
$zone_notice_2_description_0 = $obj['notices'][2]['description'][0];
$zone_notice_2_description_1 = $obj['notices'][2]['description'][1];
$zone_notice_2_links_0_href = $obj['notices'][2]['links'][0]['href'];
$zone_notice_2_links_0_type = $obj['notices'][2]['links'][0]['type'];
	
if ($zone_top_level_domain == 'nl')	{
	$zone_registry_web_id = '';
	$zone_registry_full_name = 'SIDN B.V.';
	$zone_menu = 'https://nl.sidn.nl';
	$zone_support = 'support@sidn.nl';
	$registrant_web_id = 'NL88COMM01234567890123456789012345';
}
else	{
	$zone_registry_web_id = '';
	$zone_registry_full_name = '';
	$zone_menu = '';
	$zone_support = '';
	$registrant_web_id = '';
}
$zone_registry_language = $obj['lang'];	
$view_links_0_value = $obj['links'][0]['value'];
$view_links_0_related = $obj['links'][0]['rel'];
$view_links_0_href = $obj['links'][0]['href'];
$view_links_0_href_lang = $obj['links'][0]['hreflang'];
$view_links_0_title = $obj['links'][0]['title'];
$view_links_0_media = $obj['links'][0]['media'];
$view_links_0_type = $obj['links'][0]['type'];
	
$view_links_1_value = $obj['links'][1]['value'];
$view_links_1_related = $obj['links'][1]['rel'];
$view_links_1_href = $obj['links'][1]['href'];
$view_links_href_lang_1 = $obj['links'][1]['hreflang'];
$view_links_1_title = $obj['links'][1]['title'];
$view_links_1_media = $obj['links'][1]['media'];
$view_links_1_type = $obj['links'][1]['type'];
	
$status = '';
$expiration = '(public information in a gTLD zone)';	
$last_changed = '';
$last_transferred = '';
$termination = '';
$registrar_iana_id = $obj['entities'][0]['publicIds'][0]['identifier'];	
$handle = $obj['handle']; 	
$name = $obj['ldhName'];
$name_unicode = $obj['ldhName'];
$name_servers_dnssec = '(not available)';
$name_servers_dnssec_algorithm = '(not applicable)';
if (empty($obj['secureDNS']['delegationSigned']))	{
}	
elseif ($obj['secureDNS']['delegationSigned'] === true)	{
	$name_servers_dnssec = 'yes';
	$algorithm = $obj['secureDNS']['dsData'][0]['algorithm'];
	if (strlen($algorithm))	{
		$name_servers_dnssec_algorithm = $algorithm;
	}
	else	{
		$name_servers_dnssec_algorithm = '(not available)';
	}	
}
elseif ($obj['secureDNS']['delegationSigned'] === false)	{
	$name_servers_dnssec = 'no';	
}
$registrar_protected = 'name,phone,fax,email';
$registrar_handle = '';
$registrant_handle = '';
$admin_handle = '';	
$registrar_name = '';
$reseller_name = '';
$registrant_name = '';
$registrar_kind = '';
$reseller_kind = '';
$registrant_kind = '';
$registrar_country_code = '(public information in a gTLD zone)';
$registrar_language_pref_1 = '';
$registrar_language_pref_2 = '';
$reseller_language_pref_1 = '';
$reseller_language_pref_2 = '';
$registrant_language_pref_1 = '';
$registrant_language_pref_2 = '';
$admin_language_pref_1 = '';
$admin_language_pref_2 = '';
$tech_language_pref_1 = '';
$tech_language_pref_2 = '';
$reseller_full_name = '';
$reseller_street = '';
$reseller_city = '';
$reseller_postal_code = '';
$reseller_country_code = '(public information in a gTLD zone)';
$reseller_protected = 'name,phone,fax,email';
$registrant_full_name = '(no holder name value)';
$registrant_street = '';
$registrant_city = '';
$registrant_postal_code = '';
$registrant_country_code = '(public information in a gTLD zone)';	
$registrant_protected = 'name,phone,fax,email,address';
$admin_handle = '';
$admin_email = '(no email value)';
$admin_country_code = '(public information in a gTLD zone)';	
$admin_protected = 'web_id,full_name,name,phone,fax,address';
$tech_handle = '';
$tech_email	= '(no email value)';
$tech_country_code = '(public information in a gTLD zone)';
$tech_protected = 'web_id,full_name,name,phone,fax,address';
$billing_handle = '';
$billing_email = '(no email value)';	
$billing_country_code = '(public information in a gTLD zone)';	
$billing_protected = 'web_id,full_name,name,phone,fax,address';

$server_name_1 = $obj['nameservers'][0]['ldhName'];
$server_name_2 = $obj['nameservers'][1]['ldhName'];
$server_name_3 = $obj['nameservers'][2]['ldhName'];
$server_name_4 = $obj['nameservers'][3]['ldhName'];
$server_name_5 = $obj['nameservers'][4]['ldhName'];
$server_name_6 = $obj['nameservers'][5]['ldhName'];	
$server_name_unicode_1 = $obj['nameservers'][0]['unicodeName'];
$server_name_unicode_2 = $obj['nameservers'][1]['unicodeName'];
$server_name_unicode_3 = $obj['nameservers'][2]['unicodeName'];
$server_name_unicode_4 = $obj['nameservers'][3]['unicodeName'];
$server_name_unicode_5 = $obj['nameservers'][4]['unicodeName'];
$server_name_unicode_6 = $obj['nameservers'][5]['unicodeName'];	
$server_ipv4_1 = $obj['nameservers'][0]['ipAddresses']['v4'][0];
$server_ipv6_1 = $obj['nameservers'][0]['ipAddresses']['v6'][0];	
$server_ipv4_2 = $obj['nameservers'][1]['ipAddresses']['v4'][0];
$server_ipv6_2 = $obj['nameservers'][1]['ipAddresses']['v6'][0];
$server_ipv4_3 = $obj['nameservers'][2]['ipAddresses']['v4'][0];
$server_ipv6_3 = $obj['nameservers'][2]['ipAddresses']['v6'][0];
$server_ipv4_4 = $obj['nameservers'][3]['ipAddresses']['v4'][0];
$server_ipv6_4 = $obj['nameservers'][3]['ipAddresses']['v6'][0];	
$server_ipv4_5 = $obj['nameservers'][4]['ipAddresses']['v4'][0];
$server_ipv6_5 = $obj['nameservers'][4]['ipAddresses']['v6'][0];
$server_ipv4_6 = $obj['nameservers'][5]['ipAddresses']['v4'][0];
$server_ipv6_6 = $obj['nameservers'][5]['ipAddresses']['v6'][0];	
	
$registrar_abuse_email = '(no point of contact)';
$entity_registrar = -1;	
$entity_abuse = -1;
$entity_reseller = -1;	
$entity_registrant = -1;
$entity_admin = -1;
$entity_tech = -1;
	
$raw_data = '';	
foreach($obj as $key1 => $value1) {
	$raw_data .= $key1 . ': ' . $value1 . '<br />';
    foreach($value1 as $key2 => $value2) {
		$raw_data .= "+". $key2 . ': ' . $value2 . '<br />';
		foreach($value2 as $key3 => $value3) {
			$raw_data .= "++" . $key3 . ': ' . $value3 . '<br />';
			foreach($value3 as $key4 => $value4) {
				$raw_data .= "+++" . $key4 . ': ' . $value4 . '<br />';				
				if ($value4 == 'registrar')	{
					$entity_registrar = $key2;
				}
				elseif ($value4 == 'reseller')	{
					$entity_reseller = $key2;
				}
				elseif ($value4 == 'registrant')	{
					$entity_registrant = $key2;
				}
				elseif ($value4 == 'administrative')	{
					$entity_admin = $key2;	
				}
				elseif ($value4 == 'technical')	{
					$entity_tech = $key2;
				}
				foreach($value4 as $key5 => $value5) {
					$raw_data .= "++++" . $key5 . ': ' . $value5 . '<br />';
					foreach($value5 as $key6 => $value6) {
						$raw_data .= "+++++" . $key6 . ': ' . $value6 . '<br />';
						if ($value6 == 'abuse')	{
							$entity_abuse = $key4;
						}
						foreach($value6 as $key7 => $value7) {
							$raw_data .= "++++++" . $key7 . ': ' . $value7 . '<br />';
							foreach($value7 as $key8 => $value8) {
								$raw_data .= "+++++++" . $key8 . ': ' . $value8 . '<br />';	
							}
						}	
					}	
				}
			}
		}
	}
}
$raw_data = str_replace('Array','', $raw_data);	
foreach($obj as $key1 => $value1) {
	foreach($value1 as $key2 => $value2) {
		if ($key1 == 'status')	{			
			$status .= $value2.'<br />';
		}
		foreach($value2 as $key3 => $value3) {
			if ($key1 == 'events')	{
				if ($value3 == 'registration')	{
					$registration = $value2['eventDate'];
				}
				elseif ($value3 == 'expiration')	{
					$expiration = $value2['eventDate'];
				}
				elseif ($value3 == 'last update of RDAP database')	{
					$last_uploaded = $value2['eventDate'];
				}
				elseif ($value3 == 'deletion')	{
					$termination = $value2['eventDate'];
				}
				elseif ($value3 == 'last changed')	{
					$last_changed = $value2['eventDate'];
				}
				elseif ($value3 == 'transfer')	{
					$last_transferred = $value2['eventDate'];
				}				
			}
			if ($key1 == 'entities' and $key3 == 'handle')	{
				if ($key2 == $entity_registrar)	{
					$registrar_handle = $value3;
				}
				if ($key2 == $entity_reseller)	{
					$reseller_handle = $value3;
				}
				if ($key2 == $entity_registrant)	{
					$registrant_handle = $value3;
				}
				if ($key2 == $entity_admin)	{
					$admin_handle = $value3;
				}
				if ($key2 == $entity_tech)	{
					$tech_handle = $value3;
				}
			}
			foreach($value3 as $key4 => $value4) {
				foreach($value4 as $key5 => $value5) {
					foreach($value5 as $key6 => $value6) {
						if ($key1 == 'entities' and $key3 == 'handle')	{
							die($value3.'_'.$value4.'_'.$value5.'_'.$value6);
							if ($key2 == $entity_registrar)	{
								$registrar_handle = $value4['value'];
							}
							if ($key2 == $entity_reseller)	{
								$reseller_handle = $value4['value'];
							}
							if ($key2 == $entity_registrant)	{
								$registrant_handle = $value4['value'];
							}
							if ($key2 == $entity_admin)	{
								$admin_handle = $value4['value'];
							}
							if ($key2 == $entity_tech)	{
								$tech_handle = $value4['value'];
							}
						}
						if ($key1 == 'entities' and $key3 == 'vcardArray' and $value6 == 'fn')	{
							if ($key2 == $entity_registrar)	{
								$registrar_full_name = $value5[3];
							}
							if ($key2 == $entity_reseller)	{
								$reseller_full_name = $value5[3];
							}
							if ($key2 == $entity_registrant)	{
								$registrant_full_name = $value5[3];
							}
							if ($key2 == $entity_admin)	{
								$admin_full_name = $value5[3];
							}
							if ($key2 == $entity_tech)	{
								$tech_full_name = $value5[3];
							}
						}
						if ($key1 == 'entities' and $key3 == 'vcardArray' and $value6 == 'n')	{
							if ($key2 == $entity_registrar)	{
								$registrar_name = $value5[3][0];	
								if (strlen($value5[3][1]))	{
									$registrar_name .= ', '. $value5[3][1];
								}
							}
							if ($key2 == $entity_reseller)	{
								$reseller_name = $value5[3][0];	
								if (strlen($value5[3][1]))	{
									$reseller_name .= ', '. $value5[3][1];
								}
							}
							if ($key2 == $entity_registrant)	{
								$registrant_name = $value5[3][0];	
								if (strlen($value5[3][1]))	{
									$registrant_name .= ', '. $value5[3][1];
								}
							}
							if ($key2 == $entity_admin)	{
								$admin_name = $value5[3][0];	
								if (strlen($value5[3][1]))	{
									$admin_name .= ', '. $value5[3][1];
								}
							}
							if ($key2 == $entity_tech)	{
								$tech_name = $value5[3][0];	
								if (strlen($value5[3][1]))	{
									$tech_name .= ', '. $value5[3][1];
								}
							}
						}
						if ($key1 == 'entities' and $key3 == 'vcardArray' and $value6 == 'kind')	{
							if ($key2 == $entity_registrar)	{
								$registrar_kind = $value5[3];
							}
							if ($key2 == $entity_reseller)	{
								$reseller_kind = $value5[3];
							}
							if ($key2 == $entity_registrant)	{
								$registrant_kind = $value5[3];
							}
							if ($key2 == $entity_admin)	{
								$admin_kind = $value5[3];
							}
							if ($key2 == $entity_tech)	{
								$tech_kind = $value5[3];
							}							
						}
						if ($key1 == 'entities' and $key3 == 'vcardArray' and $value6 == 'lang')	{
							if ($key2 == $entity_registrar) 		{
								if ($value6['pref'] == 1)	$registrar_language_pref_1 = $value5[3];
								if ($value6['pref'] == 2)	$registrar_language_pref_2 = $value5[3];
							}
							if ($key2 == $entity_reseller)	{
								if ($value6['pref'] == 1)	$reseller_language_pref_1 = $value5[3];
								if ($value6['pref'] == 2)	$reseller_language_pref_2 = $value5[3];
							}
							if ($key2 == $entity_registrant)	{
								if ($value6['pref'] == 1)	$registrant_language_pref_1 = $value5[3];
								if ($value6['pref'] == 2)	$registrant_language_pref_2 = $value5[3];
							}
							if ($key2 == $entity_admin)	{
								if ($value6['pref'] == 1)	$admin_language_pref_1 = $value5[3];
								if ($value6['pref'] == 2)	$admin_language_pref_2 = $value5[3];
							}
							if ($ky2 == $entity_tech)	{
								if ($value6['pref'] == 1)	$tech_language_pref_1 = $value5[3];
								if ($value6['pref'] == 2)	$tech_language_pref_2 = $value5[3];
							}
						}
						if ($key1 == 'entities' and $key3 == 'vcardArray' and $value6 == 'email')	{
							if ($key2 == $entity_admin)	{
								$admin_email = $value5[3];
							}	
							if ($key2 == $entity_tech)	{
								$tech_email = $value5[3];
							}	
						}
						if ($key1 == 'entities' and $key3 == 'vcardArray' and $value5[0] == 'adr' and $key6 == 3)	{
							if ($key2 == $entity_registrar)	{
								if (!is_array($value6[2]))	{
									$registrar_street = $value6[2];
								}
								if (!is_array($value6[3]))	{
									$registrar_city = $value6[3];
								}
								if (!is_array($value6[5]))	{
									$registrar_postal_code = $value6[5];
								}
								if (!is_array($value6[6]))	{
									$registrar_country_code = $value6[6];
								}	
							}	
							if ($key2 == $entity_reseller)	{
								if (!is_array($value6[2]))	{
									$reseller_street = $value6[2];
								}
								if (!is_array($value6[3]))	{
									$reseller_city = $value6[3];
								}
								if (!is_array($value6[5]))	{
									$reseller_postal_code = $value6[5];
								}
								if (!is_array($value6[6]))	{
									$reseller_country_code = $value6[6];
								}	
							}
							if ($key2 == $entity_registrant)	{
								$registrant_country_code = $value6[6];
							}
							if ($key2 == $entity_admin)	{
								$admin_country_code = $value6[6];
							}	
							if ($key2 == $entity_tech)	{
								$tech_country_code = $value6[6];
							}							
						}
						foreach($value6 as $key7 => $value7) {
							foreach($value7 as $key8 => $value8) {
								if ($key1 == 'entities' and $key2 == $entity_registrar and $key3 == 'entities' and $key4 == $entity_abuse and $key5 == 'vcardArray' and $key6 == 1)	{
									if ($value8 == 'tel')	{
										$registrar_abuse_phone = $value7[3];									
									}
									elseif ($value8 == 'email')	{
										$registrar_abuse_email = $value7[3];
									}	
								}
							}	
						}	
					}					
				}	
			}
		}
	}
}
	
$doc = new DOMDocument("1.0", "UTF-8");
$doc->xmlStandalone = true;	
$doc->formatOutput = true;		
	
$domains = $doc->createElement("domains");
$doc->appendChild($domains);
	
$domain = $doc->createElement("domain");	
$domains->appendChild($domain);
	
$domain->setAttribute("item", $inputdomain);
	
$zone = $doc->createElement("zone");	
$domain->appendChild($zone);
	
$domain_zone_top_level_domain = $doc->createElement("zone_top_level_domain");
$domain_zone_top_level_domain->appendChild($doc->createCDATASection($zone_top_level_domain));	
$zone->appendChild($domain_zone_top_level_domain);

$domain_zone_registry_web_id = $doc->createElement("zone_registry_web_id");
$domain_zone_registry_web_id->appendChild($doc->createCDATASection($zone_registry_web_id));	
$zone->appendChild($domain_zone_registry_web_id);	

$domain_zone_registry_full_name = $doc->createElement("zone_registry_full_name");
$domain_zone_registry_full_name->appendChild($doc->createCDATASection($zone_registry_full_name));	
$zone->appendChild($domain_zone_registry_full_name);
	
$domain_zone_registry_language = $doc->createElement("zone_registry_language");
$domain_zone_registry_language->appendChild($doc->createCDATASection($zone_registry_language));	
$zone->appendChild($domain_zone_registry_language);	
	
$domain_zone_menu = $doc->createElement("zone_menu");
$domain_zone_menu->appendChild($doc->createCDATASection($zone_menu));	
$zone->appendChild($domain_zone_menu);	
	
$domain_zone_support = $doc->createElement("zone_support");
$domain_zone_support->appendChild($doc->createCDATASection($zone_support));	
$zone->appendChild($domain_zone_support);	
	
$domain_zone_notice_0_title = $doc->createElement("zone_notice_0_title");
$domain_zone_notice_0_title->appendChild($doc->createCDATASection($zone_notice_0_title));	
$zone->appendChild($domain_zone_notice_0_title);	
	
$domain_zone_notice_0_description_0 = $doc->createElement("zone_notice_0_description_0");
$domain_zone_notice_0_description_0->appendChild($doc->createCDATASection($zone_notice_0_description_0));	
$zone->appendChild($domain_zone_notice_0_description_0);
	
$domain_zone_notice_0_description_1 = $doc->createElement("zone_notice_0_description_1");
$domain_zone_notice_0_description_1->appendChild($doc->createCDATASection($zone_notice_0_description_1));		
$zone->appendChild($domain_zone_notice_0_description_1);
	
$domain_zone_notice_0_links_0_href = $doc->createElement("zone_notice_0_links_0_href");
$domain_zone_notice_0_links_0_href->appendChild($doc->createCDATASection($zone_notice_0_links_0_href));	
$zone->appendChild($domain_zone_notice_0_links_0_href);
	
$domain_zone_notice_0_links_0_type = $doc->createElement("zone_notice_0_links_0_type");
$domain_zone_notice_0_links_0_type->appendChild($doc->createCDATASection($zone_notice_0_links_0_type));	
$zone->appendChild($domain_zone_notice_0_links_0_type);	
	
$domain_zone_notice_1_title = $doc->createElement("zone_notice_1_title");
$domain_zone_notice_1_title->appendChild($doc->createCDATASection($zone_notice_1_title));	
$zone->appendChild($domain_zone_notice_1_title);	

$domain_zone_notice_1_description_0 = $doc->createElement("zone_notice_1_description_0");
$domain_zone_notice_1_description_0->appendChild($doc->createCDATASection($zone_notice_1_description_0));	
$zone->appendChild($domain_zone_notice_1_description_0);
	
$domain_zone_notice_1_description_1 = $doc->createElement("zone_notice_1_description_1");
$domain_zone_notice_1_description_1->appendChild($doc->createCDATASection($zone_notice_1_description_1));		
$zone->appendChild($domain_zone_notice_1_description_1);
	
$domain_zone_notice_1_links_0_href = $doc->createElement("zone_notice_1_links_0_href");
$domain_zone_notice_1_links_0_href->appendChild($doc->createCDATASection($zone_notice_1_links_0_href));	
$zone->appendChild($domain_zone_notice_1_links_0_href);
	
$domain_zone_notice_1_links_0_type = $doc->createElement("zone_notice_1_links_0_type");
$domain_zone_notice_1_links_0_type->appendChild($doc->createCDATASection($zone_notice_1_links_0_type));	
$zone->appendChild($domain_zone_notice_1_links_0_type);	
	
$domain_zone_notice_2_title = $doc->createElement("zone_notice_2_title");
$domain_zone_notice_2_title->appendChild($doc->createCDATASection($zone_notice_2_title));	
$zone->appendChild($domain_zone_notice_2_title);
	
$domain_zone_notice_2_description_0 = $doc->createElement("zone_notice_2_description_0");
$domain_zone_notice_2_description_0->appendChild($doc->createCDATASection($zone_notice_2_description_0));	
$zone->appendChild($domain_zone_notice_2_description_0);
	
$domain_zone_notice_2_description_1 = $doc->createElement("zone_notice_2_description_1");
$domain_zone_notice_2_description_1->appendChild($doc->createCDATASection($zone_notice_2_description_1));	
$zone->appendChild($domain_zone_notice_2_description_1);
	
$domain_zone_notice_2_links_0_href = $doc->createElement("zone_notice_2_links_0_href");
$domain_zone_notice_2_links_0_href->appendChild($doc->createCDATASection($zone_notice_2_links_0_href));	
$zone->appendChild($domain_zone_notice_2_links_0_href);
	
$domain_zone_notice_2_links_0_type = $doc->createElement("zone_notice_2_links_0_type");
$domain_zone_notice_2_links_0_type->appendChild($doc->createCDATASection($zone_notice_2_links_0_type));	
$zone->appendChild($domain_zone_notice_2_links_0_type);	
	
$domain->appendChild($zone);	

$view = $doc->createElement("view");
$domain->appendChild($view);

$domain_view_links_0_value = $doc->createElement("view_links_0_value");
$domain_view_links_0_value->appendChild($doc->createCDATASection($view_links_0_value));	
$view->appendChild($domain_view_links_0_value);
	
$domain_view_links_0_related = $doc->createElement("view_links_0_related");
$domain_view_links_0_related->appendChild($doc->createCDATASection($view_links_0_related));	
$view->appendChild($domain_view_links_0_related);
	
$domain_view_links_0_href = $doc->createElement("view_links_0_href");
$domain_view_links_0_href->appendChild($doc->createCDATASection($view_links_0_href));	
$view->appendChild($domain_view_links_0_href);
	
$domain_view_links_0_href_lang = $doc->createElement("view_links_0_href_lang");
$domain_view_links_0_href_lang->appendChild($doc->createCDATASection($view_links_0_href_lang));	
$view->appendChild($domain_view_links_0_href_lang);
	
$domain_view_links_0_title = $doc->createElement("view_links_0_title");
$domain_view_links_0_title->appendChild($doc->createCDATASection($view_links_0_title));	
$view->appendChild($domain_view_links_0_title);	
	
$domain_view_links_0_media = $doc->createElement("view_links_0_media");
$domain_view_links_0_media->appendChild($doc->createCDATASection($view_links_0_media));	
$view->appendChild($domain_view_links_0_media);

$domain_view_links_0_type = $doc->createElement("view_links_0_type");
$domain_view_links_0_type->appendChild($doc->createCDATASection($view_links_0_type));	
$view->appendChild($domain_view_links_0_type);
	
$domain_view_links_1_value = $doc->createElement("view_links_1_value");
$domain_view_links_1_value->appendChild($doc->createCDATASection($view_links_1_value));	
$view->appendChild($domain_view_links_1_value);
	
$domain_view_links_1_related = $doc->createElement("view_links_1_related");
$domain_view_links_1_related->appendChild($doc->createCDATASection($view_links_1_related));	
$view->appendChild($domain_view_links_1_related);
	
$domain_view_links_1_href = $doc->createElement("view_links_1_href");
$domain_view_links_1_href->appendChild($doc->createCDATASection($view_links_1_href));	
$view->appendChild($domain_view_links_1_href);
	
$domain_view_links_href_lang_1 = $doc->createElement("view_links_href_lang_1");
$domain_view_links_href_lang_1->appendChild($doc->createCDATASection($view_links_href_lang_1));	
$view->appendChild($domain_view_links_href_lang_1);
	
$domain_view_links_1_title = $doc->createElement("view_links_1_title");
$domain_view_links_1_title->appendChild($doc->createCDATASection($view_links_1_title));	
$view->appendChild($domain_view_links_1_title);	
	
$domain_view_links_1_media = $doc->createElement("view_links_1_media");
$domain_view_links_1_media->appendChild($doc->createCDATASection($view_links_1_media));	
$view->appendChild($domain_view_links_1_media);

$domain_view_links_1_type = $doc->createElement("view_links_1_type");
$domain_view_links_1_type->appendChild($doc->createCDATASection($view_links_1_type));	
$view->appendChild($domain_view_links_1_type);	
	
$domain->appendChild($view);
$domain_handle = $doc->createElement("domain_handle");
$domain_handle->appendChild($doc->createCDATASection($handle));	
$domain->appendChild($domain_handle);	
$domain_name = $doc->createElement("domain_name");
$domain_name->appendChild($doc->createCDATASection($name));	
$domain->appendChild($domain_name);
$domain_name_unicode = $doc->createElement("domain_name_unicode");
$domain_name_unicode->appendChild($doc->createCDATASection($name_unicode));	
$domain->appendChild($domain_name_unicode);
$domain_status = $doc->createElement("domain_status");
$domain_status->appendChild($doc->createCDATASection($status));	
$domain->appendChild($domain_status);	
$event_registration = $doc->createElement("event_registration");
$event_registration->appendChild($doc->createCDATASection($registration));	
$domain->appendChild($event_registration);
$event_expiration = $doc->createElement("event_expiration");
$event_expiration->appendChild($doc->createCDATASection($expiration));	
$domain->appendChild($event_expiration);
$event_last_uploaded = $doc->createElement("event_last_uploaded");
$event_last_uploaded->appendChild($doc->createCDATASection($last_uploaded));	
$domain->appendChild($event_last_uploaded);	
$event_termination = $doc->createElement("event_termination");
$event_termination->appendChild($doc->createCDATASection($termination));	
$domain->appendChild($event_termination);
$event_last_changed = $doc->createElement("event_last_changed");
$event_last_changed->appendChild($doc->createCDATASection($last_changed));	
$domain->appendChild($event_last_changed);	
$event_last_transferred = $doc->createElement("event_last_transferred");
$event_last_transferred->appendChild($doc->createCDATASection($last_transferred));	
$domain->appendChild($event_last_transferred);		

$registrar = $doc->createElement("registrar");
$domain->appendChild($registrar);
$domain_registrar_handle = $doc->createElement("registrar_handle");
$domain_registrar_handle->appendChild($doc->createCDATASection($registrar_handle));	
$registrar->appendChild($domain_registrar_handle);	
$domain_registrar_full_name = $doc->createElement("registrar_full_name");
$domain_registrar_full_name->appendChild($doc->createCDATASection($registrar_full_name));	
$registrar->appendChild($domain_registrar_full_name);
$domain_registrar_kind = $doc->createElement("registrar_kind");
$domain_registrar_kind->appendChild($doc->createCDATASection($registrar_kind));	
$registrar->appendChild($domain_registrar_kind);	
$domain_registrar_name = $doc->createElement("registrar_name");
$domain_registrar_name->appendChild($doc->createCDATASection($registrar_name));	
$registrar->appendChild($domain_registrar_name);	
$domain_registrar_iana_id = $doc->createElement("registrar_iana_id");
$domain_registrar_iana_id->appendChild($doc->createCDATASection($registrar_iana_id));	
$registrar->appendChild($domain_registrar_iana_id);	
$domain_registrar_street = $doc->createElement("registrar_street");
$domain_registrar_street->appendChild($doc->createCDATASection($registrar_street));	
$registrar->appendChild($domain_registrar_street);
$domain_registrar_city = $doc->createElement("registrar_city");
$domain_registrar_city->appendChild($doc->createCDATASection($registrar_city));	
$registrar->appendChild($domain_registrar_city);
$domain_registrar_postal_code = $doc->createElement("registrar_postal_code");
$domain_registrar_postal_code->appendChild($doc->createCDATASection($registrar_postal_code));	
$registrar->appendChild($domain_registrar_postal_code);
$domain_registrar_country_code = $doc->createElement("registrar_country_code");
$domain_registrar_country_code->appendChild($doc->createCDATASection($registrar_country_code));	
$registrar->appendChild($domain_registrar_country_code);
$domain_registrar_language_pref_1 = $doc->createElement("registrar_language_pref_1");
$domain_registrar_language_pref_1->appendChild($doc->createCDATASection($registrar_language_pref_1));	
$registrar->appendChild($domain_registrar_language_pref_1);
$domain_registrar_language_pref_2 = $doc->createElement("registrar_language_pref_2");
$domain_registrar_language_pref_2->appendChild($doc->createCDATASection($registrar_language_pref_2));	
$registrar->appendChild($domain_registrar_language_pref_2);	
$domain_registrar_protected = $doc->createElement("registrar_protected");	
$domain_registrar_protected->appendChild($doc->createCDATASection($registrar_protected));	
$registrar->appendChild($domain_registrar_protected);
	
$domain_registrar_abuse_email = $doc->createElement("registrar_abuse_email");
$domain_registrar_abuse_email->appendChild($doc->createCDATASection($registrar_abuse_email));	
$registrar->appendChild($domain_registrar_abuse_email);
$domain_registrar_abuse_phone = $doc->createElement("registrar_abuse_phone");
$domain_registrar_abuse_phone->appendChild($doc->createCDATASection($registrar_abuse_phone));	
$registrar->appendChild($domain_registrar_abuse_phone);		
$domain->appendChild($registrar);	

$reseller = $doc->createElement("reseller");
$domain->appendChild($reseller);
$domain_reseller_handle = $doc->createElement("reseller_handle");
$domain_reseller_handle->appendChild($doc->createCDATASection($reseller_handle));	
$reseller->appendChild($domain_reseller_handle);	
$domain_reseller_full_name = $doc->createElement("reseller_full_name");
$domain_reseller_full_name->appendChild($doc->createCDATASection($reseller_full_name));	
$reseller->appendChild($domain_reseller_full_name);
$domain_reseller_kind = $doc->createElement("reseller_kind");
$domain_reseller_kind->appendChild($doc->createCDATASection($reseller_kind));	
$reseller->appendChild($domain_reseller_kind);		
$domain_reseller_name = $doc->createElement("reseller_name");
$domain_reseller_name->appendChild($doc->createCDATASection($reseller_name));	
$reseller->appendChild($domain_reseller_name);
$domain_reseller_street = $doc->createElement("reseller_street");
$domain_reseller_street->appendChild($doc->createCDATASection($reseller_street));	
$reseller->appendChild($domain_reseller_street);
$domain_reseller_city = $doc->createElement("reseller_city");
$domain_reseller_city->appendChild($doc->createCDATASection($reseller_city));	
$reseller->appendChild($domain_reseller_city);
$domain_reseller_postal_code = $doc->createElement("reseller_postal_code");
$domain_reseller_postal_code->appendChild($doc->createCDATASection($reseller_postal_code));	
$reseller->appendChild($domain_reseller_postal_code);
$domain_reseller_country_code = $doc->createElement("reseller_country_code");
$domain_reseller_country_code->appendChild($doc->createCDATASection($reseller_country_code));	
$reseller->appendChild($domain_reseller_country_code);
$domain_reseller_language_pref_1 = $doc->createElement("reseller_language_pref_1");
$domain_reseller_language_pref_1->appendChild($doc->createCDATASection($reseller_language_pref_1));	
$reseller->appendChild($domain_reseller_language_pref_1);
$domain_reseller_language_pref_2 = $doc->createElement("reseller_language_pref_2");
$domain_reseller_language_pref_2->appendChild($doc->createCDATASection($reseller_language_pref_2));	
$reseller->appendChild($domain_reseller_language_pref_2);	
$domain_reseller_protected = $doc->createElement("reseller_protected");	
$domain_reseller_protected->appendChild($doc->createCDATASection($reseller_protected));	
$reseller->appendChild($domain_reseller_protected);	
$domain->appendChild($reseller);
	
$registrant = $doc->createElement("registrant");
$domain->appendChild($registrant);
$domain_registrant_handle = $doc->createElement("registrant_handle");
$domain_registrant_handle->appendChild($doc->createCDATASection($registrant_handle));	
$registrant->appendChild($domain_registrant_handle);	
$domain_registrant_web_id = $doc->createElement("registrant_web_id");
$domain_registrant_web_id->appendChild($doc->createCDATASection($registrant_web_id));	
$registrant->appendChild($domain_registrant_web_id);
$domain_registrant_full_name = $doc->createElement("registrant_full_name");
$domain_registrant_full_name->appendChild($doc->createCDATASection($registrant_full_name));	
$registrant->appendChild($domain_registrant_full_name);
$domain_registrant_kind = $doc->createElement("registrant_kind");
$domain_registrant_kind->appendChild($doc->createCDATASection($registrant_kind));	
$registrant->appendChild($domain_registrant_kind);	
$domain_registrant_name = $doc->createElement("registrant_name");
$domain_registrant_name->appendChild($doc->createCDATASection($registrant_name));	
$registrant->appendChild($domain_registrant_name);
$domain_registrant_country_code = $doc->createElement("registrant_country_code");
$domain_registrant_country_code->appendChild($doc->createCDATASection($registrant_country_code));
$registrant->appendChild($domain_registrant_country_code);		
$domain_registrant_language_pref_1 = $doc->createElement("registrant_language_pref_1");
$domain_registrant_language_pref_1->appendChild($doc->createCDATASection($registrant_language_pref_1));	
$registrant->appendChild($domain_registrant_language_pref_1);
$domain_registrant_language_pref_2 = $doc->createElement("registrant_language_pref_2");
$domain_registrant_language_pref_2->appendChild($doc->createCDATASection($registrant_language_pref_2));	
$registrant->appendChild($domain_registrant_language_pref_2);	
$domain_registrant_protected = $doc->createElement("registrant_protected");	
$domain_registrant_protected->appendChild($doc->createCDATASection($registrant_protected));	
$registrant->appendChild($domain_registrant_protected);	
$domain->appendChild($registrant);
	
$admin = $doc->createElement("admin");
$domain->appendChild($admin);
$domain_admin_handle = $doc->createElement("admin_handle");
$domain_admin_handle->appendChild($doc->createCDATASection($admin_handle));	
$admin->appendChild($domain_admin_handle);	
$domain_admin_full_name = $doc->createElement("admin_full_name");
$domain_admin_full_name->appendChild($doc->createCDATASection($admin_full_name));	
$admin->appendChild($domain_admin_full_name);	
$domain_admin_email = $doc->createElement("admin_email");
$domain_admin_email->appendChild($doc->createCDATASection($admin_email));	
$admin->appendChild($domain_admin_email);
$domain_admin_country_code = $doc->createElement("admin_country_code");
$domain_admin_country_code->appendChild($doc->createCDATASection($admin_country_code));
$admin->appendChild($domain_admin_country_code);	
$domain_admin_language_pref_1 = $doc->createElement("admin_language_pref_1");
$domain_admin_language_pref_1->appendChild($doc->createCDATASection($admin_language_pref_1));	
$admin->appendChild($domain_admin_language_pref_1);
$domain_admin_language_pref_2 = $doc->createElement("admin_language_pref_2");
$domain_admin_language_pref_2->appendChild($doc->createCDATASection($admin_language_pref_2));	
$admin->appendChild($domain_admin_language_pref_2);	
$domain_admin_protected = $doc->createElement("admin_protected");	
$domain_admin_protected->appendChild($doc->createCDATASection($admin_protected));	
$admin->appendChild($domain_admin_protected);		
$domain->appendChild($admin);	
	
$tech = $doc->createElement("tech");
$domain->appendChild($tech);
$domain_tech_handle = $doc->createElement("tech_handle");
$domain_tech_handle->appendChild($doc->createCDATASection($tech_handle));	
$tech->appendChild($domain_tech_handle);	
$domain_tech_full_name = $doc->createElement("tech_full_name");
$domain_tech_full_name->appendChild($doc->createCDATASection($tech_full_name));	
$tech->appendChild($domain_tech_full_name);	
$domain_tech_email = $doc->createElement("tech_email");
$domain_tech_email->appendChild($doc->createCDATASection($tech_email));	
$tech->appendChild($domain_tech_email);
$domain_tech_country_code = $doc->createElement("tech_country_code");
$domain_tech_country_code->appendChild($doc->createCDATASection($tech_country_code));
$tech->appendChild($domain_tech_country_code);	
$domain_tech_language_pref_1 = $doc->createElement("tech_language_pref_1");
$domain_tech_language_pref_1->appendChild($doc->createCDATASection($tech_language_pref_1));	
$tech->appendChild($domain_tech_language_pref_1);
$domain_tech_language_pref_2 = $doc->createElement("tech_language_pref_2");
$domain_tech_language_pref_2->appendChild($doc->createCDATASection($tech_language_pref_2));	
$tech->appendChild($domain_tech_language_pref_2);	
$domain_tech_protected = $doc->createElement("tech_protected");	
$domain_tech_protected->appendChild($doc->createCDATASection($tech_protected));	
$tech->appendChild($domain_tech_protected);		
$domain->appendChild($tech);
	
$billing = $doc->createElement("billing");
$domain->appendChild($billing);
$domain_billing_handle = $doc->createElement("billing_handle");
$domain_billing_handle->appendChild($doc->createCDATASection($billing_handle));	
$billing->appendChild($domain_billing_handle);	
$domain_billing_full_name = $doc->createElement("billing_full_name");
$domain_billing_full_name->appendChild($doc->createCDATASection($billing_full_name));	
$billing->appendChild($domain_billing_full_name);	
$domain_billing_email = $doc->createElement("billing_email");
$domain_billing_email->appendChild($doc->createCDATASection($billing_email));	
$billing->appendChild($domain_billing_email);
$domain_billing_country_code = $doc->createElement("billing_country_code");
$domain_billing_country_code->appendChild($doc->createCDATASection($billing_country_code));
$billing->appendChild($domain_billing_country_code);	
$domain_billing_language_pref_1 = $doc->createElement("billing_language_pref_1");
$domain_billing_language_pref_1->appendChild($doc->createCDATASection($billing_language_pref_1));	
$billing->appendChild($domain_billing_language_pref_1);
$domain_billing_language_pref_2 = $doc->createElement("billing_language_pref_2");
$domain_billing_language_pref_2->appendChild($doc->createCDATASection($billing_language_pref_2));	
$billing->appendChild($domain_billing_language_pref_2);	
$domain_billing_protected = $doc->createElement("billing_protected");	
$domain_billing_protected->appendChild($doc->createCDATASection($billing_protected));	
$billing->appendChild($domain_billing_protected);		
$domain->appendChild($billing);	
	
$name_servers = $doc->createElement("name_servers");
$domain->appendChild($name_servers);

$server_1 = $doc->createElement("server_1");
$name_servers->appendChild($server_1);	
$domain_server_name_1 = $doc->createElement("server_name_1");
$domain_server_name_1->appendChild($doc->createCDATASection($server_name_1));		
$server_1->appendChild($domain_server_name_1);
$domain_server_name_unicode_1 = $doc->createElement("server_name_unicode_1");
$domain_server_name_unicode_1->appendChild($doc->createCDATASection($server_name_unicode_1));		
$server_1->appendChild($domain_server_name_unicode_1);
$domain_server_ipv4_1 = $doc->createElement("server_ipv4_1");
$domain_server_ipv4_1->appendChild($doc->createCDATASection($server_ipv4_1));		
$server_1->appendChild($domain_server_ipv4_1);
$domain_server_ipv6_1 = $doc->createElement("server_ipv6_1");
$domain_server_ipv6_1->appendChild($doc->createCDATASection($server_ipv6_1));		
$server_1->appendChild($domain_server_ipv6_1);	
$name_servers->appendChild($server_1);
	
$server_2 = $doc->createElement("server_2");
$name_servers->appendChild($server_2);	
$domain_server_name_2 = $doc->createElement("server_name_2");
$domain_server_name_2->appendChild($doc->createCDATASection($server_name_2));		
$server_2->appendChild($domain_server_name_2);
$domain_server_name_unicode_2 = $doc->createElement("server_name_unicode_2");
$domain_server_name_unicode_2->appendChild($doc->createCDATASection($server_name_unicode_2));		
$server_2->appendChild($domain_server_name_unicode_2);
$domain_server_ipv4_2 = $doc->createElement("server_ipv4_2");
$domain_server_ipv4_2->appendChild($doc->createCDATASection($server_ipv4_2));		
$server_2->appendChild($domain_server_ipv4_2);
$domain_server_ipv6_2 = $doc->createElement("server_ipv6_2");
$domain_server_ipv6_2->appendChild($doc->createCDATASection($server_ipv6_2));		
$server_2->appendChild($domain_server_ipv6_2);	
$name_servers->appendChild($server_2);	
	
$server_3 = $doc->createElement("server_3");
$name_servers->appendChild($server_3);	
$domain_server_name_3 = $doc->createElement("server_name_3");
$domain_server_name_3->appendChild($doc->createCDATASection($server_name_3));		
$server_3->appendChild($domain_server_name_3);
$domain_server_name_unicode_3 = $doc->createElement("server_name_unicode_3");
$domain_server_name_unicode_3->appendChild($doc->createCDATASection($server_name_unicode_3));		
$server_3->appendChild($domain_server_name_unicode_3);
$domain_server_ipv4_3 = $doc->createElement("server_ipv4_3");
$domain_server_ipv4_3->appendChild($doc->createCDATASection($server_ipv4_3));		
$server_3->appendChild($domain_server_ipv4_3);
$domain_server_ipv6_3 = $doc->createElement("server_ipv6_3");
$domain_server_ipv6_3->appendChild($doc->createCDATASection($server_ipv6_3));		
$server_3->appendChild($domain_server_ipv6_3);		
$name_servers->appendChild($server_3);
$server_4 = $doc->createElement("server_4");
$name_servers->appendChild($server_4);	
$domain_server_name_4 = $doc->createElement("server_name_4");
$domain_server_name_4->appendChild($doc->createCDATASection($server_name_4));		
$server_4->appendChild($domain_server_name_4);
$domain_server_name_unicode_4 = $doc->createElement("server_name_unicode_4");
$domain_server_name_unicode_4->appendChild($doc->createCDATASection($server_name_unicode_4));		
$server_4->appendChild($domain_server_name_unicode_4);
$domain_server_ipv4_4 = $doc->createElement("server_ipv4_4");
$domain_server_ipv4_4->appendChild($doc->createCDATASection($server_ipv4_4));		
$server_4->appendChild($domain_server_ipv4_4);
$domain_server_ipv6_4 = $doc->createElement("server_ipv6_4");
$domain_server_ipv6_4->appendChild($doc->createCDATASection($server_ipv6_4));		
$server_4->appendChild($domain_server_ipv6_4);		
$name_servers->appendChild($server_4);
$server_5 = $doc->createElement("server_5");
$name_servers->appendChild($server_5);	
$domain_server_name_5 = $doc->createElement("server_name_5");
$domain_server_name_5->appendChild($doc->createCDATASection($server_name_5));		
$server_5->appendChild($domain_server_name_5);
$domain_server_name_unicode_5 = $doc->createElement("server_name_unicode_5");
$domain_server_name_unicode_5->appendChild($doc->createCDATASection($server_name_unicode_5));		
$server_5->appendChild($domain_server_name_unicode_5);
$domain_server_ipv5_5 = $doc->createElement("server_ipv5_5");
$domain_server_ipv5_5->appendChild($doc->createCDATASection($server_ipv5_5));		
$server_5->appendChild($domain_server_ipv5_5);
$domain_server_ipv6_5 = $doc->createElement("server_ipv6_5");
$domain_server_ipv6_5->appendChild($doc->createCDATASection($server_ipv6_5));		
$server_5->appendChild($domain_server_ipv6_5);		
$name_servers->appendChild($server_5);
$server_6 = $doc->createElement("server_6");
$name_servers->appendChild($server_6);	
$domain_server_name_6 = $doc->createElement("server_name_6");
$domain_server_name_6->appendChild($doc->createCDATASection($server_name_6));		
$server_6->appendChild($domain_server_name_6);
$domain_server_name_unicode_6 = $doc->createElement("server_name_unicode_6");
$domain_server_name_unicode_6->appendChild($doc->createCDATASection($server_name_unicode_6));		
$server_6->appendChild($domain_server_name_unicode_6);
$domain_server_ipv6_6 = $doc->createElement("server_ipv6_6");
$domain_server_ipv6_6->appendChild($doc->createCDATASection($server_ipv6_6));		
$server_6->appendChild($domain_server_ipv6_6);
$domain_server_ipv6_6 = $doc->createElement("server_ipv6_6");
$domain_server_ipv6_6->appendChild($doc->createCDATASection($server_ipv6_6));		
$server_6->appendChild($domain_server_ipv6_6);		
$name_servers->appendChild($server_6);	
	
$domain_name_servers_dnssec = $doc->createElement("name_servers_dnssec");
$domain_name_servers_dnssec->appendChild($doc->createCDATASection($name_servers_dnssec));	
$name_servers->appendChild($domain_name_servers_dnssec);
$domain_name_servers_dnssec_algorithm = $doc->createElement("name_servers_dnssec_algorithm");
$domain_name_servers_dnssec_algorithm->appendChild($doc->createCDATASection($name_servers_dnssec_algorithm));	
$name_servers->appendChild($domain_name_servers_dnssec_algorithm);	
	
$domain->appendChild($name_servers);
		
$domain_raw_data = $doc->createElement("raw_data");	
$domain_raw_data->appendChild($doc->createCDATASection($raw_data));		
$domain->appendChild($domain_raw_data);
	
$domains->appendChild($domain);
$doc->appendChild($domains);
//return $doc->saveXML(NULL, LIBXML_NOEMPTYTAG);
return $doc->saveXML();
}
?>