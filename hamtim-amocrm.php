<?php

/*
СуперЛегкий класс (библиотека) для работы с api AmoCRM
http://hamtim.ru/2017/11/07/hamtim-amocrm/
*/

if (!class_exists('HamtimAmocrm')) {
	class HamtimAmocrm{
		var $settings;
		var $subdomain;
		var $auth;
		
		function HamtimAmocrm($login,$api,$subdomain)
		{
			$this->settings = new stdClass();
			$this->settings->amocrm = new stdClass();
			$this->settings->amocrm->api = $api;
			$this->settings->amocrm->login = $login;
			$this->settings->amocrm->subdomain = $subdomain;
			$this->amocrm_auth();
		}
		
		function amocrm_auth()
		{			
			if(isset($this->settings->amocrm))
			{
				if($this->settings->amocrm->api&&$this->settings->amocrm->login&&$this->settings->amocrm->subdomain)
				{
					$subdomain = $this->settings->amocrm->subdomain;
					$this->subdomain = $subdomain;
					$user = array(
						'USER_LOGIN'=>$this->settings->amocrm->login,
						'USER_HASH'=>$this->settings->amocrm->api
					);
				}else{
					return 'Нет данных для авторизации';
				}
			}else{
				return 'Нет данных для авторизации';
			}		
			
			$link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
			$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
			#Устанавливаем необходимые опции для сеанса cURL
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
			curl_setopt($curl,CURLOPT_URL,$link);
			curl_setopt($curl,CURLOPT_POST,true);
			curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($user));
			curl_setopt($curl,CURLOPT_HEADER,false);
			curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
			curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

			$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
			$code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
			curl_close($curl); #Заверашем сеанс cURL
			
			$auth = json_decode($out);
			if($out) $this->auth = $auth->response->auth;
			
			return $out;
		}
		
		function q($path, $fields, $ifModifiedSince=false)
		{
			return $this->amocrm_query($path, $fields, $ifModifiedSince);
		}
		
		function l($l){ echo '<pre>'; var_dump($l); echo '</pre>'; }
		
		function amocrm_query($path, $fields, $ifModifiedSince=false)
		{
			$link='https://'.$this->subdomain.'.amocrm.ru'.$path;

			$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
			#Устанавливаем необходимые опции для сеанса cURL
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
			curl_setopt($curl,CURLOPT_URL,$link);
			if($ifModifiedSince)
			{
				$httpHeader = array('IF-MODIFIED-SINCE: '.$ifModifiedSince);
			}else{
				$httpHeader = array();
			}
			if( count($fields) ){
				curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
				curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($fields));
				$httpHeader[] = 'Content-Type: application/json';
			}
			curl_setopt($curl,CURLOPT_HTTPHEADER, $httpHeader);
			curl_setopt($curl,CURLOPT_HEADER,false);
			curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
			curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
			 
			$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
			$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
			//$this->l(curl_getinfo($curl));
			return json_decode( $out );
		}
	}
}