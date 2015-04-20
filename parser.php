<?php
/* Based on original Sharrre by Julien Hany 
 * by Brando Meniconi (b.meniconi@fuoricentrostudio.com)
 */

header('content-type: application/json');

$json = array(
    'url'=>filter_input(INPUT_GET, 'url', FILTER_VALIDATE_URL),
    'count'=>0
    );

if(empty($json['url'])){
   return json_encode($json);
}

$context = stream_context_create(array(
  'http'=>array(
    //'proxy' => 'tcp://proxy.example.com:5100', //  
    'max_redirects' => 5,
    'user_agent' => 'Sharrre',
    'timeout' => 5,
    'verify_peer' => false,
  )
));


switch(filter_input(INPUT_GET, 'method')){
   case 'googlePlus':
    $response = file_get_contents('https://plusone.google.com/u/0/_/+1/fastbutton?url=' . urlencode($json['url']) . '&count=true', false, $context);

    $matches = array();
    if(!empty($response) && preg_match( '/window\.__SSR = {c: ([^,]+)/', $response, $matches )){
      $json['count'] = floatval($matches[1]);
    }
    break;

   case 'stumbleUpon':
    $response = file_get_contents("http://www.stumbleupon.com/services/1.01/badge.getinfo?url=".urlencode($json['url']), false, $context); 
    if (!empty($response) && ($result = json_decode($response)) && isset($result->result->views))
    {
        $json['count'] = floatval((int)$result->result->views);
    }

    break;
   }

  echo json_encode($json);  