<?php
if (!class_exists('AmokitAmocrm')) {
	class AmokitAmocrm{
		var $api;
    var $login;
		var $domain;
		var $auth;
		var $code;
		var $out;
    var $cookies;

		function __construct($domain,$login,$api)
		{
			$this->api = $api;
			$this->login = $login;
			$this->domain = $domain;
      $result = $this->auth();
		}

    function auth()
		{
      if($this->login&&$this->domain)
      {
        $user = array(
          'USER_LOGIN'=>$this->login,
          'USER_HASH'=>$this->api
        );
      }else{
        return 'Нет данных для авторизации';
      }

			$link='https://'.$this->domain.'/private/api/auth.php?type=json';
			$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
			#Устанавливаем необходимые опции для сеанса cURL
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_USERAGENT,'AMOKIT.RU');
			curl_setopt($curl,CURLOPT_URL,$link);
			curl_setopt($curl,CURLOPT_POST,true);
			curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query($user,null,'&',PHP_QUERY_RFC1738));
			curl_setopt($curl,CURLOPT_HEADER,false);
			curl_setopt($curl, CURLOPT_COOKIEFILE, "");
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

			$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
			$code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
      $this->cookies = curl_getinfo($curl, CURLINFO_COOKIELIST);
			curl_close($curl); #Заверашем сеанс cURL

			$auth = json_decode($out);
			if($code == 200)
			{
				$this->auth = $auth->response->auth;
			}else{
				$this->auth = false;
      }
			return $out;
		}

    function q($path, $fields=array(), $ifModifiedSince='')
    {
      if($params) $params = '&'.$params;
      $link='https://'.$this->domain.$path;
      $curl=curl_init();
      curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
      curl_setopt($curl,CURLOPT_USERAGENT,'AMOKIT.RU');
      curl_setopt($curl,CURLOPT_URL,$link);
      if( count($fields) ){
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($fields));
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
      }
      if($ifModifiedSince){
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: '.date('D, d M Y H:i:s', $ifModifiedSince).' UTC'));
      }
      curl_setopt($curl,CURLOPT_HEADER,false);
      foreach($this->cookies as $cookie_line)
      {
        curl_setopt($curl, CURLOPT_COOKIELIST, $cookie_line);
      }
      curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
      curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

      $out=curl_exec($curl);
      $this->out = json_decode($out);
			$this->code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
      return $this->out;
    }
  }
}
