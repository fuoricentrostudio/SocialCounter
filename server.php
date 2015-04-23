<?php 

//search for composer autoloader
if(file_exists( dirname(dirname(dirname(__FILE__))).'/autoload.php' )){
    require dirname(dirname(dirname(__FILE__))).'/autoload.php';
} else {
    require dirname(__FILE__).'/src/Counter.php';
}

$input = filter_input_array(INPUT_GET, array( 
    'url' => FILTER_SANITIZE_URL, 
    'type'=> FILTER_SANITIZE_STRIPPED
    )
        );
    
if(!empty($input['url']) && !empty($input['type']) ){
    echo json_encode(Fuoricentrostudio\SocialShares\Counter::count($input['type'], $input['url']));
}