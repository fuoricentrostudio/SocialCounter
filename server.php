<?php 

//search for composer autoloader
if(file_exists( dirname(dirname(dirname(__FILE__))).'/autoload.php' )){
    require dirname(dirname(dirname(__FILE__))).'/autoload.php';
} else {
    require dirname(__FILE__).'/src/Counter.php';
}

$input = filter_input_array(INPUT_GET, array( 
    'url' => FILTER_SANITIZE_URL, 
    'method'=> FILTER_SANITIZE_STRIPPED
    )
        );

Fuoricentrostudio\SocialShares\Counter::$cache_config = null;
    
if(!empty($input['url']) && !empty($input['method']) ){
    echo json_encode(array('count'=>Fuoricentrostudio\SocialShares\Counter::count($input['method'], $input['url'])));
}