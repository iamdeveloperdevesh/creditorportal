<?php

ini_set('pcre.backtrack_limit', '23001337');
ini_set('pcre.recursion_limit', '23001337');
set_time_limit(0);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

header('Content-Type: text/text; charset=utf-8;');

function everything_in_tags($string, $tagname, $container = FALSE) {
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    preg_match($pattern, $string, $matches);

    if (isset($matches[1])) {
        if ($container) {
            $matches[1] = '<root>' . $matches[1] . '</root>';
        }

        return $matches[1];
    } else {
        return FALSE;
    }
}

function xml_to_php($xml = '', $match = '') {
    $php_data = array();
    $match = strtoupper($match);
    if (stripos(html_entity_decode($xml), 'ErrorMessag') !== true) {
        preg_match_all("/<$match.*?>(.*?)<\/$match>/si", html_entity_decode($xml), $php_data);
        if (isset($php_data[1][0])) {
            $string = $php_data[1][0];
            $xml_d = preg_replace('/<(\w+)[^>]*>/', '<$1>', "<$match>" . html_entity_decode($string) . "</$match>");
            $xml_d = str_replace(' & ', ' and ', $xml_d);
            $xml_data = simplexml_load_string(html_entity_decode($xml_d, ENT_QUOTES, "utf-8"));
            $json = json_encode($xml_data);
            $php_data = json_decode($json, true);
            if (is_array($php_data[key($php_data)])) {
                $php_data = $php_data[key($php_data)];
            }

            return $php_data;
        }
    } else {
        return false;
    }
}

function getTextBetweenTags($tag, $html, $strict = 0) {
    $dom = new domDocument;
    if ($strict == 1) {
        $dom->loadXML($html);
    } else {
        $dom->loadHTML($html);
    }

    $dom->preserveWhiteSpace = false;
    $content = $dom->getElementsByTagname($tag);
    $out = array();
    foreach ($content as $item) {
        $out[] = $item->nodeValue;
    }

    return $out;
}

function get_ws_data($url = '', $input = array(), $request_type, $additional_data = array()) {

    $CI = & get_instance();
    $CI->load->library('Curlphp');
    $curl = new Curlphp();
    $curl->setUrl($url);

    if (parse_url($url, PHP_URL_SCHEME) == 'https') {
        $curl->setSSL(false);
    }
    $curl->setRequestTimeout(300);
    $str = '';

    switch ($request_type) {
        case 'lead':
            $str = json_encode($input);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));

            break;

        case 'cron':
            $curl->setData($input);

            break;

        case 'hdfc_ergo':
            if(is_array($input)) {
                if(isset($additional_data['root_tag'])){
                if (count($input) > 1) {
                    $str = $input[0] . '=' . (string)Array2XML::createXML($additional_data['root_tag'], $input[1]);
                }
                else {
                    $str = Array2XML::createXML($additional_data['root_tag'], $input);
					$str = str_replace('#replace', $str, $additional_data['container']);
					$curl->http_header('Content-Type', $additional_data['content_type']);
                }
             }else{
            $str = json_encode($input);
            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('MerchantKey', $additional_data['merchantkey']);
            $curl->http_header('Content-Length', strlen($str));
            $curl->http_header('SecretToken', $additional_data['secret_token']);
             }
            }
            else{
                $str = urldecode($input);
                $curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
                $curl->http_header('Content-Length', strlen($str));
            }
            $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);

            $curl->setData($str);
           // print_pre($str);
            break;

        case 'star':
            $str = json_encode($input);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));

            break;

        case 'bajaj_allianz':
            if ((isset($additional_data['request_data']['request_type'])) || (isset($additional_data['request_data']) && $additional_data['request_data']['section'] == 'health'))
            {
				if($additional_data['request_data']['method']=='Generate PDF'){		
					
					$curl->http_header('Content-Type', 'application/json');
				    $curl->http_header('auth', $additional_data['request_data']['auth']);					
					
				}else if($additional_data['request_data']['method']=='Generate token'){
					$str = urldecode($input);					
					$curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
					$curl->http_header('Content-Length', strlen($str));
					$curl->setData($str);
					
				}else{
					$str = json_encode($input);
					$curl->setData($str);
					$curl->http_header('Content-Type', 'application/json');
					$curl->http_header('Content-Length', strlen($str));
				}
            }
            else
            {
                if(isset($additional_data['request_data']['method']) && $additional_data['request_data']['method'] == 'Generate Policy Motor'){
                    $str = json_encode($input);
					$curl->setData($str);
					$curl->http_header('Content-Type', 'application/json');
					$curl->http_header('Content-Length', strlen($str));
                }else{
                    $str = Array2XML::createXML($additional_data['root_tag'], $input);
                    $str = str_replace('#replace', $str, $additional_data['container']);
                    $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);

                    $curl->setData($str);
                    $curl->http_header('Content-Type', 'text/xml');
                    $curl->http_header('Content-Length', strlen($str));
                }
            }
            break;

        case 'religare':
            if (isset($additional_data['root_tag'])) {
                $str = Array2XML::createXML($additional_data['root_tag'], $input);
                $str = str_replace('#replace', $str, $additional_data['container']);
                $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);

                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/soap+xml');
                $curl->http_header('Content-Length', strlen($str));
                if (isset($additional_data['authorization'])) {
                    $curl->http_login($additional_data['authorization'][0], $additional_data['authorization'][1]);
                }
            } else {
                $str = json_encode($input);
                $curl->setData($str);

                $curl->http_header('appId', $additional_data['request_data']['appId']);
                $curl->http_header('signature', $additional_data['request_data']['signature']);
                $curl->http_header('timestamp', $additional_data['request_data']['timestamp']);
                $curl->http_header('agentId', $additional_data['request_data']['agentId']);
                $curl->http_header('Content-Type', 'application/json');
            }

            break;

        case 'sompo':
            $str = Array2XML::createXML($additional_data['root_tag'], $input);

            if ((isset($additional_data['request_data']['section']) && ($additional_data['request_data']['section'] == "car") || ($additional_data['request_data']['section'] == "bike")))
            {
                $str = htmlentities(preg_replace('/<\\?xml .*\\?>/i', '', $str));
            }
            else
            {
                $str = preg_replace("/<\\?xml .*\\?>/i", "", $str);
            }            
            
            if((isset($additional_data['request_data']['section']) && ($additional_data['request_data']['section'] == "travel")))
            {
                $str = str_replace("#replace", $str, $additional_data['container']);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'text/xml; charset=utf-8');
                $curl->http_header('Content-Length', strlen($str));
                $curl->http_header('SOAPAction', "\"http://tempuri.org/IService1/" . $additional_data['soap_action'] . "\"");
            }
            else if ((isset($additional_data['request_data']['section']) && ($additional_data['request_data']['section'] == "car") || ($additional_data['request_data']['section'] == "bike")))
            {
                 $str = str_replace("#replace", $str, $additional_data['container']);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'text/xml');
            $curl->http_header('Content-Length', strlen($str));
            $curl->http_header('SOAPAction', "\"http://tempuri.org/IService1/" . $additional_data['soap_action'] . "\"");
            $curl->http_header('Accept', 'text/xml');

                
            }
            else if((isset($additional_data['request_data']['section']) && ($additional_data['request_data']['section'] == "health_corona_kavach")))
            {
                $str = json_encode($input);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));
            }
            else
            {
                $str = str_replace("#replace", htmlentities($str), $additional_data['container']);
            
                $curl->setData($str);
                $curl->http_header('Content-Type', 'text/xml; charset=utf-8');
                $curl->http_header('Content-Length', strlen($str));
                $curl->http_header('SOAPAction', "\"http://tempuri.org/IService/" . $additional_data['soap_action'] . "\"");
            }

            break;

        case 'bharti_axa':	
			if (isset($additional_data['request_data']['directXml']))
			{
				$str = $input;
			}
			else
			{
				$str = Array2XML::createXML($additional_data['root_tag'], $input);
			}
			
            $str = str_replace('#replace', $str, $additional_data['container']);
            $str = preg_replace("/<\\?xml .*\\?>/i", "", $str);

            $curl->setData($str); 
            $curl->http_header('Content-Type', 'text/xml');
            $curl->http_header('Content-Length', strlen($str));
		

            break;

        case 'apollo_munich':
            $str = Array2XML::createXML($additional_data['root_tag'], $input);
            $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);
            $str = str_replace("#replace", $str, $additional_data['container']);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'text/xml');
            $curl->http_header('Content-Length', strlen($str));
            $curl->http_header('SOAPAction', "\"http://www.apollomunichinsurance.com/B2BService/" . $additional_data['soap_action'] . "\"");
            $curl->http_header('Accept', 'text/xml');

            break;

        case 'bajaj_life':
            if ($additional_data['request_data']['method'] != 'upload doc') {
                $str = json_encode($input);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('Content-Length', strlen($str));
            } else {
                $str = json_encode($input);
                //$str = json_encode($additional_data['request_withoute_decode']);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('Username', $additional_data['authorization'][0]);
                $curl->http_header('Password', $additional_data['authorization'][1]);               
                $curl->http_login($additional_data['authorization'][0], $additional_data['authorization'][1]);
                $curl->http_header('Content-Length', strlen($str));
            }

            break;

        case 'pnb_metlife':
            if ($additional_data['request_data']['method'] == 'Token Generation') {
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('X-IBM-Client-Id', $additional_data['request_data']['X-IBM-Client-Id']);
                $curl->http_header('X-IBM-Client-Secret', $additional_data['request_data']['X-IBM-Client-Secret']);
                $curl->http_header('subject', $additional_data['request_data']['subject']);
                $curl->http_header('mettype', $additional_data['request_data']['mettype']);
            } else if($additional_data['request_data']['method'] == 'Quote Generation') {
                $str = json_encode($input);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('Authorization', $additional_data['request_data']['accessToken']);
                $curl->http_header('X-IBM-Client-Id', $additional_data['request_data']['X-IBM-Client-Id']);
                $curl->http_header('X-IBM-Client-Secret', $additional_data['request_data']['X-IBM-Client-Secret']);
                $curl->http_header('Content-Length', strlen($str));
            } else if($additional_data['request_data']['method'] == 'Create Lead') {
                $str = json_encode($input);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('Authorization', $additional_data['request_data']['accessToken']);
                $curl->http_header('X-IBM-Client-Id', $additional_data['request_data']['X-IBM-Client-Id']);
                $curl->http_header('X-IBM-Client-Secret', $additional_data['request_data']['X-IBM-Client-Secret']);
                $curl->http_header('utmSource', $additional_data['request_data']['utmSource']);
                $curl->http_header('utmTerm', $additional_data['request_data']['utmTerm']);
                $curl->http_header('utmMedium', $additional_data['request_data']['utmMedium']);
                $curl->http_header('utmCampaign', $additional_data['request_data']['utmCampaign']);
                $curl->http_header('utmContent', $additional_data['request_data']['utmContent']);
                $curl->http_header('Content-Length', strlen($str));
            }

            break;

        case 'new_india':
            $str = Array2XML::createXML($additional_data['root_tag'], $input);
            $str = str_replace('#replace', $str, $additional_data['container']);
            $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'text/xml');
            $curl->http_header('Content-Length', strlen($str));
            $curl->http_login($additional_data['authorization'][0], $additional_data['authorization'][1]);

            break;

        case 'future_generali':

            $str = Array2XML::createXML($additional_data['root_tag'], $input);
            $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);
            if (!isset($additional_data['param'])) {
                $str = htmlentities($str);
            }
            $str = str_replace("#replace", $str, $additional_data['container']);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'text/xml');
            if (isset($additional_data['soap_action'])) {
                if($additional_data['soap_action'] != "GetPDF")
                {
                    $curl->http_header('SOAPAction', "\"http://tempuri.org/IService/" . $additional_data['soap_action'] . "\"");
                }
                else
                {
                  $curl->http_header('SOAPAction', "\"http://tempuri.org/" . $additional_data['soap_action'] . "\"");  
                }       
            }
            
            $curl->http_header('Content-Length', strlen($str));

            break;

        case 'reliance':
            $str = Array2XML::createXML($additional_data['root_tag'], $input);
            $str = preg_replace("/<\\?xml .*\\?>/i", '', $str);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'text/xml');
            $curl->http_header('Content-Length', strlen($str));

            break;

        case 'liberty_videocon':
            $str = json_encode($input);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));

            break;

        case 'hdfc_life':
            $str = Array2XML::createXML($additional_data['root_tag'], $input);
            $str = str_replace('#replace', $str, $additional_data['container']);
            $str = preg_replace("/<\\?xml .*\\?>/i", "", $str);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'text/xml');
            $curl->http_header('Content-Length', strlen($str));

            break;

        case 'lnt':
            $str = Array2XML::createXML($additional_data['root_tag'], $input);
            $str = str_replace('#replace', $str, $additional_data['container']);
            $str = preg_replace("/<\\?xml .*\\?>/i", "", $str);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'text/xml');
            $curl->http_header('Content-Length', strlen($str));
            $curl->http_header('SOAPAction', "\"http://tempuri.org/" . $additional_data['soap_action'] . "\"");
            $curl->http_header('Accept', 'text/xml');

            break;

        case 'tata_aig':
            $str = urldecode(http_build_query($input));
            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
            $curl->http_header('Content-Length', strlen($str));
            break;

        case 'royal_sundaram':
            if (isset($additional_data['root_tag'])) {
                $str = Array2XML::createXML($additional_data['root_tag'], $input);
                $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);

                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/xml');
                $curl->http_header('Content-Length', strlen($str));
            } else {
                if ($additional_data['request_data']['method'] == 'Pdf Generation') {
                    $str = json_encode($input);
                    $curl->http_header('Content-Type', 'application/json');
                    $curl->http_header('Content-Length', strlen($str));
                    $curl->setData($str);
                } else {
                    $str = urldecode(http_build_query($input));
                    $curl->http_header('Content-Type', 'application/xml');
                    $curl->setData($str);
                }
            }

            break;

        case 'godigit':
		 if ((isset($additional_data['request_data']['request_type'])) || (isset($additional_data['request_data']) && $additional_data['request_data']['section'] == 'motor'))
            {
		
            $str = json_encode($input);
            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));
            if (isset($additional_data['authorization']))
            {
                $curl->http_header('Authorization', $additional_data['authorization']);
            }
			else if(isset($additional_data['webUserId']))
			{
				$curl->http_login($additional_data['webUserId'], $additional_data['password'], 'BASIC');
			} }
			else{
				 if (is_array($input)) {
                $str = json_encode($input);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('Content-Length', strlen($str));
            }
            else {
                $str = urldecode($input);
                $curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
            }
            if (isset($additional_data['authorization'])) {
                $curl->http_header('Authorization', $additional_data['authorization']);
            } else {
                $curl->http_login($additional_data['webUserId'], $additional_data['password'], 'BASIC');
            }
            if (isset($additional_data['webUserId'])) {
                $curl->http_login($additional_data['webUserId'], $additional_data['password'], 'BASIC');
            }
			}
        case 'icici_lombard':

            if (is_array($input)) {
                $str = json_encode($input);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('Content-Length', strlen($str));
                if (isset($additional_data['request_data']['authorization'])) {
                    $token = "Bearer " . $additional_data['request_data']['authorization'];
                    $curl->http_header('Authorization', $token);
                } else if (isset($additional_data['authorization_trans_id'])) {
                    $curl->http_login(urldecode($additional_data['authorization_trans_id'][0]), urldecode($additional_data['authorization_trans_id'][1]), 'BASIC');
                    $curl->http_header('Content-Type', 'application/json');
                }
            } else {
                if (isset($additional_data['transaction_id_for_pg'])) {
                    $curl->http_login(urldecode($additional_data['transaction_id_for_pg'][0]), urldecode($additional_data['transaction_id_for_pg'][1]), 'BASIC');
                    $curl->http_header('Content-Type', 'application/json');
                } else if (isset($additional_data['pdf_generation'])) {
                    //PDF Generation
                    $token_for_pdf = "Bearer " . $additional_data['pdf_generation'][0];
                    $curl->http_header('Authorization', $token_for_pdf);
                } else {
                    $str = urldecode($input);

                    $curl->setData($str);
                    $curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
                    $curl->http_header('Content-Length', strlen($str));
                }
            }
             break;
        case 'iffco_tokio':
				if(isset($additional_data['root_tag']))
				{
					$str = Array2XML::createXML($additional_data['root_tag'],$input);
					$str = str_replace('#replace',$str,$additional_data['container']);
					$str = preg_replace("/<\\?xml .*\\?>/i", "", $str);
					
					$curl->setData($str);
					$curl->http_header('Content-Type', 'text/xml; charset=utf-8');
					$curl->http_header('SOAPAction', "''");
					$curl->http_header('Content-Length', strlen($str));
				}else{
					//print_pre($str = json_encode($input));
					$str = json_encode($input);
					$curl->setData($str);
					$curl->http_header('Content-Type', 'application/json');
					$curl->http_login($additional_data['UserName'], $additional_data['Password'], 'BASIC');
					$curl->http_header('Content-Length', strlen($str));
				}
        break;
            
            case 'cigna':
            if(is_array($input))
            {
                $str = json_encode($input,JSON_UNESCAPED_SLASHES);
                $curl->setData($str);
                $curl->http_header('Content-Type', $additional_data['content_type']);
                $curl->http_header('app_key', $additional_data['app_key']);
                $curl->http_header('app_id', $additional_data['app_id']);
                if(isset($additional_data['Action-Type'])){
                    $curl->http_header('Action-Type', $additional_data['Action-Type']);
                }
				
            }
            else
            {			   
                $curl->http_header('app_key', $additional_data['app_key']);
                $curl->http_header('app_id', $additional_data['app_id']);
            }
            break;

        case 'kotak':
                if (!isset($additional_data['request_method'])) {
                    $str = json_encode($input);
    
                    $curl->setData($str);
                    $curl->http_header('Content-Type', 'application/json');
                    if (isset($additional_data['token'])) {
                        $curl->http_header('vTokenCode', $additional_data['token']);
                    } elseif (isset($additional_data['Key'])) {
                        $curl->http_header('vRanKey', $additional_data['Key']);
                    }
                } else {
                    $curl->http_header('vTokenCode', $additional_data['TokenCode']);
                    $curl->http_header('Content-Type', 'application/json');
                }
                $curl->http_header('Content-Length', strlen($str));
    
            break;
            
        case 'icici_pru':
          
            if (isset($additional_data['root_tag']))
            {
                if (isset($additional_data['container']))
                {
					if ($additional_data['request_data']['method'] == 'upload doc') {
						$str = Array2XML::createXML($additional_data['root_tag'], $additional_data['request_withoute_decode']);
						$str = str_replace('#replace', $str, $additional_data['container']);
						$str = preg_replace("/<\\?xml .*\\?>/i", "", $str);
						
						$str1 = Array2XML::createXML($additional_data['root_tag'], $input);
						$str1 = str_replace('#replace', $str1, $additional_data['container']);
						$str1 = preg_replace("/<\\?xml .*\\?>/i", "", $str1);
						$curl->http_header('Content-Type', 'text/xml');
						$curl->http_header('Content-Length', strlen($str1));
						$curl->setData($str1);
					}else{
						$str = Array2XML::createXML($additional_data['root_tag'], $input);
						$str = str_replace('#replace', $str, $additional_data['container']);
						$str = preg_replace("/<\\?xml .*\\?>/i", "", $str);
						$curl->http_header('Content-Type', 'text/xml');
						$curl->http_header('Content-Length', strlen($str));
						$curl->setData($str);
					}
                }
                else
                {
                    $str = $input[0] . '=' . (string) Array2XML::createXML($additional_data['root_tag'], $input[1]);
                    $str = preg_replace('/<\\?xml .*\\?>/i', '', $str);
                    $curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
					$curl->http_header('Content-Length', strlen($str));
					$curl->setData($str);
                }
            }
            else
            {
                $str = json_encode($input);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json');
                $curl->http_header('Content-Length', strlen($str));
            }
            break;   
			
      case 'aegon_life':
			 if (isset($additional_data['request_data']['x-api-key'])) {
				$curl->http_header('x-api-key', $additional_data['request_data']['x-api-key']);
				$curl->http_header('Content-Type', 'application/json');
				$curl->http_header('Authorization', $additional_data['request_data']['authorization']);
				$str = json_encode($input);
				$curl->setData($str);
				$curl->http_header('Content-Length', strlen($str));
			 }else{
				$str = urldecode($input);
				$curl->setData($str);
				$curl->http_header('Authorization', $additional_data['request_data']['authorization']);
				$curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
				$curl->http_header('Content-Length', strlen($str)); 
			 }
            break;
		case 'LDAP':
			$str = json_encode($input);
			$curl->setData($str);	
			$curl->http_header('request_token', $additional_data['authorization']['request_token']);
			$curl->http_header('x-ibm-client-id', $additional_data['authorization']['x-ibm-client-id']);
			$curl->http_header('Content-Type', 'application/json');
			$curl->http_header('Content-Length', strlen($str)); 
			break;	

		 case 'max_life':
            $str = json_encode($input);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));
			
			break;	
      case 'sbi':          
            if (is_array($input)) {
               if (isset($additional_data['root_tag'])) {
                   $str = Array2XML::createXML($additional_data['root_tag'], $input);
                   $str = str_replace('#replace', $str, $additional_data['container']);
                   $str = preg_replace("/<\\?xml .*\\?>/i", "", $str);
                   $str = preg_replace("/&amp;/i", "&", $str);

                   $curl->setData($str);
                   $curl->http_header('Content-Type', 'text/xml');
                   $curl->http_header('Content-Length', strlen($str));
               } else {
                   $str = json_encode($input);
                   $curl->setData($str);                  
                   if (isset($additional_data['request_data']['authorization'])) {                      
                       $token = "Bearer " . $additional_data['request_data']['authorization'];
                       $curl->http_header('Authorization', $token);
                   }
                   $curl->http_header('Content-Type', 'application/json');
		           $curl->http_header('X-IBM-Client-Id', $additional_data['request_data']['Client_Id']);
                   $curl->http_header('X-IBM-Client-Secret', $additional_data['request_data']['Client_Secret']);
                   $curl->http_header('Content-Length', strlen($str));
                   
               }
           } else {
			    
               if($additional_data['request_data']['method'] != 'Generate PDF TOKEN'){
                   
                   $curl->http_header('Content-Type', 'application/json');
                   $curl->http_header('X-IBM-Client-Id', $additional_data['request_data']['Client_Id']);
                   $curl->http_header('X-IBM-Client-Secret', $additional_data['request_data']['Client_Secret']);
                    if (isset($additional_data['request_data']['authorization'])) {                      
                       $token = "Bearer " . $additional_data['request_data']['authorization'];
                       $curl->http_header('Authorization', $token);
                   }
               }else{
                   $curl->http_header('X-IBM-Client-Id', $additional_data['request_data']['Client_Id']);
                   $curl->http_header('X-IBM-Client-Secret', $additional_data['request_data']['Client_Secret']);
               }
           }
            break;
		    case 'aditya_birla':		
		
				$str = json_encode($input);

				$curl->setData($str);
				$curl->http_header('Content-Type', 'application/json');
				$curl->http_header('Content-Length', strlen($str));

				if(isset($additional_data['token'])){
					$curl->http_header('Authorization','Bearer '.$additional_data['token']);
				}
				if(isset($additional_data['request_data']['Authorization'])){
					$curl->http_header('Authorization',$additional_data['request_data']['Authorization']);
				}
				if(isset($additional_data['request_data']['username'])){
					$curl->http_header('username',$additional_data['request_data']['username']);
				}
				if(isset($additional_data['request_data']['password'])){
					$curl->http_header('password',$additional_data['request_data']['password']);
				}

            break;
		 case 'cc_enquiry':
            $str = json_encode($input);

            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));

            break;
        case 'magma':
            if (is_array($input)) {
                $str = json_encode($input);
                $curl->setData($str);
                $curl->http_header('Content-Type', 'application/json; charset=utf-8');
			    if (isset($additional_data['authorization'])) {
                    $token = "Bearer " . $additional_data['authorization'];
                    $curl->http_header('Authorization', $token);
                }
                $curl->http_header('Content-Length', strlen($str));
            } else {
                    $str = urldecode($input);

                    $curl->setData($str);
                    $curl->http_header('Content-Type', 'application/x-www-form-urlencoded');
                    $curl->http_header('Content-Length', strlen($str));
            }
            break;

    }

    $curl->setMethod((isset($additional_data['request_method'])) ? strtolower($additional_data['request_method']) : 'post');

    $start_time = date('Y-m-d H:i:s');
    $response = $curl->executeCurl();
    $end_time = date('Y-m-d H:i:s');
    $curl_info = $curl->getCurlInfo();

    unset($curl);

    /*if (isset($additional_data['request_data']))
    {
        $request_data = $additional_data['request_data'];

        $some_data = [
            'proposal_id'   => $request_data['proposal_id'],
            'company'       => $request_type,
            'section'       => $request_data['section'],
            'method'        => $request_data['method'],
            'request'       => $str,
            'response'      => $response['response'],
            'response_code' => $response['http_code'],
            'response_time' => $curl_info['total_time'],
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $CI->db->reconnect();

        $CI->db->insert('webservice_request_response_data', $some_data);
    }
    if (isset($additional_data['quote']))
    {
        $some_data = [
            'quote'         => $additional_data['quote'],
            'company'       => $additional_data['company'],
            'product'       => $additional_data['product'],
            'section'       => $additional_data['section'],
            'method'        => $additional_data['method'],
            'request'       => $str,
            'response'      => $response['response'],
            'response_code' => $response['http_code'],
            'ip_address'    => $CI->input->ip_address(),
            'start_time'    => $start_time,
            'end_time'      => $end_time,
            'response_time' => $curl_info['total_time'],
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        $CI->db->reconnect();

        $CI->db->insert('quote_temp_data', $some_data);
    } */
    
    if(isset($additional_data['quote'])) {
        if($additional_data['section'] == 'health')
        {
            $section = 'mediclaim';
        }else
        {
            $section = $additional_data['section'];
        }
            
        $trace_id = file_get_contents(APPPATH . 'logs/'.$section.'_quotes/' . $additional_data['quote'] . '/'.$additional_data['section'].'_trace_id.txt');
        $transaction_stage = 'quote';
    }
    else {
        $request_data = $additional_data['request_data'];
        $trace_id = $CI->session->userdata((($request_data['section'] =='motor')?'car':$request_data['section']).'_trace_id');
        $transaction_stage = 'proposal';
    }
    
    if(isset($trace_id) && $trace_id !== NULL) {
		$some_data = [
				'trace_id'      => $trace_id,
				'trans_type'    => (isset($additional_data['quote']) ? 'quote':'proposal'),
				'product'       => (isset($additional_data['product']))?$additional_data['product']:'',
				'company'       => (isset($additional_data['company']))?$additional_data['company']:(isset($request_data['company'])?$request_data['company']:''),
				'company_alias' => $request_type,
				'section'       => (isset($additional_data['section']))?$additional_data['section']:(($request_data['section'] =='motor')?'car':$request_data['section']),
				'method'        => (isset($additional_data['method']))?$additional_data['method']:$request_data['method'],
				'request'       => $str,
				'response'      => $response['response'],
				'request_method'=> ((isset($additional_data['request_method'])) ? strtolower($additional_data['request_method']) : 'post'),
				'status_code'   => $response['http_code'],
				'endpoint_url'  => $url,
				'ip_address'    => $CI->input->ip_address(),
				'start_time'    => $start_time,
				'end_time'      => $end_time,
				'response_time' => $curl_info['total_time'],
				'created_at'    => date('Y-m-d H:i:s'),
		];   
		$CI->db->reconnect();	
		insert_common_log_data($some_data);	
        $web_data = [
            'proposal_id'   => (isset($request_data['proposal_id']))?$request_data['proposal_id']:'',
            'company'       => $request_type,
            'section'       => (isset($request_data['section']))?$request_data['section']:'',
            'method'        => (isset($request_data['method']))?$request_data['method']:'',
            'request'       => $str,
            'response'      =>  $response['response'],
            'response_time' => $curl_info['total_time'],
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $CI->db->insert('webservice_request_response_data', $web_data);
	}
	else if (isset($additional_data['request_data'])) {
        $request_data = $additional_data['request_data'];

        $some_data = [
            'proposal_id'   => $request_data['proposal_id'],
            'company'       => $request_type,
            'section'       => $request_data['section'],
            'method'        => $request_data['method'],
            'request'       => $str,
            'response'      =>  $response['response'],
            //'response_code' => $response['http_code'],
            'response_time' => $curl_info['total_time'],
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $CI->db->reconnect();

        $CI->db->insert('webservice_request_response_data', $some_data);
    }
    return $response['status'] ? $response['response'] : $response['status'];
}

/**
 * Array2XML: A class to convert array in PHP to XML
 * It also takes into account attributes names unlike SimpleXML in PHP
 * It returns the XML in form of DOMDocument class for further manipulation.
 * It throws exception if the tag name or attribute name has illegal chars.
 *
 * Usage:
 *       $xml = Array2XML::createXML('root_node_name', $php_array);
 *       echo $xml->saveXML();
 */
class Array2XML {

    private static $xml = null;
    private static $encoding = 'UTF-8';

    /**
     * Convert an Array to XML
     *
     * @param string $node_name - name of the root node to be converted
     * @param array  $arr       - aray to be converterd
     *
     * @return DomDocument
     */
    public static function createXML($node_name, $arr = array()) {
        $xml = self::getXMLRoot();
        $xml->appendChild(self::convert($node_name, $arr));

        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $xml->saveXML();
    }

    private static function getXMLRoot() {
        if (empty(self::$xml)) {
            self::init();
        }

        return self::$xml;
    }

    /**
     * Initialize the root XML node [optional]
     *
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DomDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */

    /**
     * Convert an Array to XML
     *
     * @param string $node_name - name of the root node to be converted
     * @param array  $arr       - aray to be converterd
     *
     * @return DOMNode
     */
    private static function &convert($node_name, $arr = array()) {

        //print_arr($node_name);
        $xml = self::getXMLRoot();
        $node = $xml->createElement($node_name);

        if (is_array($arr)) {
            // get the attributes first.;
            if (isset($arr['@attributes'])) {
                foreach ($arr['@attributes'] as $key => $value) {
                    if (!self::isValidTagName($key)) {
                        throw new Exception('[Array2XML] Illegal character in attribute name. attribute: ' . $key . ' in node: ' . $node_name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.
            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if (isset($arr['@value'])) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']);    //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } else {
                if (isset($arr['@cdata'])) {
                    $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                    unset($arr['@cdata']);    //remove the key from the array once done.
                    //return from recursion, as a note with cdata cannot have child nodes.
                    return $node;
                }
            }
        }

        //create subnodes using recursion
        if (is_array($arr)) {
            // recurse to get the node for that key
            foreach ($arr as $key => $value) {
                if (!self::isValidTagName($key)) {
                    throw new Exception('[Array2XML] Illegal character in tag name. tag: ' . $key . ' in node: ' . $node_name);
                }
                if (is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND;
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach ($value as $k => $v) {
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]); //remove the key from the array once done.
            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if (!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }

        return $node;
    }

    /*
     * Get string representation of boolean value
     */

    private static function isValidTagName($tag) {
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';

        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }

    /*
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn
     */

    private static function bool2str($v) {
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;

        return $v;
    }

}

/**
 * XML2Array: A class to convert XML to array in PHP
 * It returns the array which can be converted back to XML using the Array2XML script
 * It takes an XML string or a DOMDocument object as an input.
 *
 * Usage:
 *       $array = XML2Array::createArray($xml);
 */
class XML2Array {

    private static $xml = null;
    private static $encoding = 'UTF-8';

    /**
     * Convert an XML to Array
     *
     * @param string $node_name - name of the root node to be converted
     * @param array  $arr       - aray to be converterd
     *
     * @return DOMDocument
     */
    public static function &createArray($input_xml) {
        $xml = self::getXMLRoot();
        libxml_use_internal_errors(TRUE);
        if (is_string($input_xml)) {
            $parsed = $xml->loadXML($input_xml);
            if (!$parsed) {
                throw new Exception('[XML2Array] Error parsing the XML string.');
            }
        } else {
            if (get_class($input_xml) != 'DOMDocument') {
                throw new Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
            }
            $xml = self::$xml = $input_xml;
        }
        $array = self::convert($xml->documentElement);
        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $array;
    }

    private static function getXMLRoot() {
        if (empty(self::$xml)) {
            self::init();
        }

        return self::$xml;
    }

    /**
     * Initialize the root XML node [optional]
     *
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DOMDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */

    /**
     * Convert an Array to XML
     *
     * @param mixed $node - XML as a string or as an object of DOMDocument
     *
     * @return mixed
     */
    private static function &convert($node) {
        $output = array();

        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
                $output['@cdata'] = trim($node->textContent);
                break;

            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:

                // for each child node, call the covert function recursively
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::convert($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;

                        // assume more nodes of same kind are coming
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if ($v !== '') {
                            $output = $v;
                        }
                    }
                }

                if (is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1) {
                            $output[$t] = $v[0];
                        }
                    }
                    if (empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }

                // loop through the attributes and collect them
                if ($node->attributes->length) {
                    $a = array();
                    foreach ($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if (!is_array($output)) {
                        $output = array('@value' => $output);
                    }
                    $output['@attributes'] = $a;
                }
                break;
        }

        return $output;
    }
}
