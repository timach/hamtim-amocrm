<?php
if (!class_exists('AmokitAmocrm')) {
	class AmokitAmocrm{
		var $api;
    var $login;
		var $domain;
		var $auth;
		var $code;
		var $out;
		var $server_time;

		function __construct($domain,$login,$api)
		{
			$this->api = $api;
			$this->login = $login;
			$this->domain = $domain;
      $result = $this->q('/private/api/auth.php', 'type=json', array());
      if(isset($result->response->server_time)) $this->server_time = $result->response->server_time;
		}

    function q($path, $params='', $fields=array(), $ifModifiedSince='')
    {
      if($params) $params = '&'.$params;
      $link='https://'.$this->domain.$path.'?USER_LOGIN='.$this->login.'&USER_HASH='.$this->api.$params;
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
      curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
      curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

      $out=curl_exec($curl);
      $this->out = json_decode($out);
			$this->code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
      return $this->out;
    }
  }
}
