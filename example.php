<?php

/*
СуперЛегкий класс (библиотека) для работы с api AmoCRM
http://hamtim.ru/2017/11/07/hamtim-amocrm/
*/

require_once('hamtim-amocrm.php');
$amo = new HamtimAmocrm('test@email.com'/*логин*/, 'API'/*api ключ*/, 'SUBDOMAIN'/*субдомен*/);

if(!$amo->auth) die('Нет соединения с amoCRM');

//все примеры запросов на https://developers.amocrm.ru/rest_api/


//получаем список сделок в работе
$path = '/private/api/v2/json/leads/list';

//формируем дату -1  день	
$ifModifiedSince = date('D, d M Y H:i:s', (time()-1*24*3600));

//если передается пустой массив fields, то данные post не передаются в заголовке запроса
$fields = array();

//делаем запрос
$leads = $amo->q($path, $fields, $ifModifiedSince);
	
if(!$leads) die('Сделок в работе не найдено');

//выводим дамп с сделками из ответа
echo '<pre>';	
print_r($leads);
echo '</pre>';


//создаем новую сделку
$path = '/private/api/v2/json/leads/set';
$fields['request']['leads']['add']=array(
	array(
		'name'=>'Название сделки',
		'status_id'=>12345,#id статуса, обязательное поле
		//'responsible_user_id'=>12345,#id Отвественного
		'tags' => 'создано с помощью hamtim.ru', #Теги
	)
);
$leadAnswer = $amo->q($path, $fields);
