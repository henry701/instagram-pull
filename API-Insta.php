<?php
function Get_Fotos_Recentes($username)
{
	$curr_dir = realpath(dirname(__FILE__));
	
	if(GetCorrectMTime($curr_dir . '/cache/FotosRecentes.txt') > time() - 60*30) // 1/2 hora
		return unserialize(file_get_contents($curr_dir . '/cache/FotosRecentes.txt',FILE_TEXT));
	
	/*

	$post_fields = Array("indice" => 'valor');
	 
	$post_fields_string="";
	 
	foreach($post_fields as $key=>$value) 
		$post_fields_string .= $key . '=' . urlencode($value) .'&'; 
	 
	rtrim($post_fields_string, '&');

	*/
	 
	$handle = curl_init();
	curl_setopt($handle,CURLOPT_URL,"https://www.instagram.com/$username/media/");
	curl_setopt($handle,CURLOPT_FORBID_REUSE,TRUE);
	curl_setopt($handle,CURLOPT_FRESH_CONNECT,TRUE);
	// curl_setopt($handle,CURLOPT_POST,TRUE); // Habilitar POST
	// curl_setopt($handle,CURLOPT_HTTPHEADER, Array( "Content-Type: application/x-www-form-urlencoded; charset=utf-8") ); // Dados POST
	// curl_setopt($handle,CURLOPT_POSTFIELDS,$post_fields_string); // Dados POST
	curl_setopt($handle,CURLOPT_RETURNTRANSFER,TRUE);
	curl_setopt($handle,CURLOPT_FOLLOWLOCATION,TRUE);
	curl_setopt($handle,CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($handle,CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($handle,CURLOPT_CONNECTTIMEOUT,13);
	curl_setopt($handle,CURLOPT_DNS_CACHE_TIMEOUT,0);
	curl_setopt($handle,CURLOPT_TIMEOUT,16);
	curl_setopt($handle,CURLOPT_ENCODING,""); // If an empty string, "", is set, a header containing all supported encoding types is sent.
	curl_setopt($handle,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');


	$result = curl_exec($handle);

	if($result === FALSE)
		goto error;
	 
	$decoded_data = json_decode($result, TRUE);
	$erro_json = json_last_error();
	 
	if($erro_json !== JSON_ERROR_NONE || $decoded_data['status'] !== 'ok')
	{
		error:
		echo '<!--' . json_encode
		( 
			Array
			(
				"Erro não espeficicado, favor contatar o suporte em webmaster@{$_SERVER['SERVER_NAME']}<br>",
				curl_error($handle),
				$result,
				$erro_json
			)
		) . '-->';
		curl_close($handle);
		return FALSE;
	}
	
	curl_close($handle);
	
	$FotosRecentes = Array();
	
	foreach($decoded_data['items'] as $ft_rec)
	{
		$tpar = Array();
		$tpar['texto'] = $ft_rec['caption']['text'];
		$tpar['link'] = $ft_rec['link'];
		$tpar['images'] = Array();
		$tpar['images']['thumb'] = $ft_rec['images']['thumbnail'];
		$tpar['images']['lowres'] = $ft_rec['images']['low_resolution'];
		$tpar['images']['original'] = $ft_rec['images']['standard_resolution'];
		array_push($FotosRecentes, $tpar);
	}

	$decoded_data = $FotosRecentes;
	
	file_put_contents($curr_dir . '/cache/FotosRecentes.txt',serialize($decoded_data),LOCK_EX|FILE_TEXT);
	
	return $decoded_data;
}

function GetCorrectMTime($filePath) 
{ 
    $time = filemtime($filePath); 

    $isDST = (date('I', $time) == 1); 
    $systemDST = (date('I') == 1); 

    $adjustment = 0; 

    if($isDST == false && $systemDST == true) 
        $adjustment = 3600; 
    
    else if($isDST == true && $systemDST == false) 
        $adjustment = -3600; 

    else 
        $adjustment = 0; 

    return ($time + $adjustment); 
}
