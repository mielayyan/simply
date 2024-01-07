<?php
    /**
     * [dump description]
     * @param  [type] $var [description]
     * @return [type]      [description]
     */
    function dump($var) {
        if(is_bool($var)) {
            echo "<pre>";
            var_dump($var);
            echo "</pre>";
        } else {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
    }
    
    /**
     * [dd description]
     * @param  [type] $var [description]
     * @return [type]      [description]
     */
    function dd($var) {
        dump($var);
        die;
    }

    function array_key_value_equal($array = [], $key1 = "", $value1 = "", $key2 = "", $value2 = "") {
        if(array_key_exists($key1, $array)) {

        } else {
            return true;
        }
    }

    /**
     * [search_array description]
     * @param  [type] $array  [description]
     * @param  string $key1   [description]
     * @param  string $value1 [description]
     * @param  string $key2   [description]
     * @param  string $value2 [description]
     * @return [type]         [description]
     */
    function search_array($array, $key1="", $value1="", $key2="", $value2="") { 
       foreach($array as $key => $item) {
            foreach($item as $item_key => $value) {
                if(($key1 == $item_key && $item[$item_key] == $value1) && ($item[$key2] == $value2)) {
                    return true;
                }
            }
       }
       return false;
    } 

    /**
     * [array_key_first return first element key of an array]
     * @param  array  $arr 
     * @return [string|NULL key]  
     */
    if (!function_exists('array_key_first')) {
        function array_key_first(array $arr) {
            foreach($arr as $key => $unused) {
                return $key;
            }
            return NULL;
        }
    }