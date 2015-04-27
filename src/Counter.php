<?php
/* Based on original Sharrre by Julien Hany 
 * by Brando Meniconi (b.meniconi@fuoricentrostudio.com)
 */

namespace Fuoricentrostudio\SocialShares;

class Counter {
          
    public static $cache_config = array(
        'adapter' => array(
            'name'    => 'apc',
            'options' => array('ttl' => 3600),
        ),
        'plugins' => array(
            // Don't throw exceptions on cache errors
            'exception_handler' => array(
                'throw_exceptions' => false
            ),
        )
    );
    
    public static $stream_context = array(
        'http'=>array(
          //'proxy' => 'tcp://proxy.example.com:5100', //  
          'max_redirects' => 5,
          'user_agent' => 'Social Counter',
          'timeout' => 5,
          'verify_peer' => false,
        )
    );
        
    public static function count($provider, $url){
        
        $count = self::getCache(self::cacheKey($provider, $url));
        if(!$count){
            $count = self::update($provider, $url);
            self::setCache(self::cacheKey($provider, $url), $count);
        }
        
        return $count;
    }
    
    public static function update($provider, $url){
   
        $count = false;
        
        switch($provider){
            case 'googlePlus':
                $response = self::request('https://plusone.google.com/u/0/_/+1/fastbutton?url=' . urlencode($url) . '&count=true');
                $matches = array();
                if(!empty($response) && preg_match( '/window\.__SSR = {c: ([^,]+)/', $response, $matches )){
                    $count = floatval($matches[1]);
                }
                break;

            case 'stumbleUpon':
                $response = self::request("http://www.stumbleupon.com/services/1.01/badge.getinfo?url=".urlencode($url)); 
                if (!empty($response) && ($result = json_decode($response)) && isset($result->result->views))
                {
                    $count = floatval($result->result->views);
                }

            break;
        }   
        
        return $count;
        
    }
    
    public static function request($url, $stream_context=null){
                
        $context = stream_context_create($stream_context?$stream_context:self::$stream_context);
        
        return file_get_contents($url, false, $context);
    }
    
    public static function getCache($key) {
        
        if(!class_exists('\Zend\Cache\StorageFactory') || empty(self::$cache_config)){
            return false;
        }
        
        $cache = \Zend\Cache\StorageFactory::factory(self::$cache_config);
        
        return $cache->getItem($key);
    }
    
    public static function setCache($key, $data) {
        
        if(!class_exists('\Zend\Cache\StorageFactory') || empty(self::$cache_config)){
            return false;
        }
        
        $cache = \Zend\Cache\StorageFactory::factory(self::$cache_config);
         
        return $cache->set($key, $data);
    }    
    
    public static function cacheKey($provider, $url){
        return $provider.'_'.md5($url);
    }
    
}