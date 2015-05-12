<?php

class acCouchdbToolsJson {

	public static function json2FlatenArray($json, $prefix = null, $decorator = "/")  {
	    $flat_array = array();

	    $i = 0;
	    foreach($json as $key => $value) {
	      if($value instanceof stdClass) {
	        $flat_array = array_merge($flat_array, self::json2FlatenArray($value, $prefix.$decorator.$key));
	      } elseif(is_array($value))  {
	        $flat_array = array_merge($flat_array, self::json2FlatenArray($value, $prefix.$decorator.$key.$decorator.'{array}'));
	      } else {
	        $flat_array[$prefix.$decorator.$key] = $value;
	      }
	      $i++;
	    }

	    if($i == 0) {
	       $flat_array[$prefix.$decorator] = null;
	    }

	    return $flat_array;
  	}

    public static function flatArray2Json($flat_array, $decorator = "/") {
	    $stdClass = new stdClass();

	    foreach($flat_array as $hash => $value) {
	      	$keys = explode($decorator, $hash);
	      	$stdClassPos = $stdClass;
	      	foreach($keys as $i => $key) {
        		if($key === "") {
	          		continue;
	        	}

		        if($key === '{array}') {
		          continue;
		        }

		        if(count($keys) - 1 == $i) {
		          $stdClassPos->{$key} = $value;
		          break;
		        }

		        if(!isset($stdClassPos->{$key})) {
		          $stdClassPos->{$key} = new stdClass();
		        }

	        	$stdClassPos = $stdClassPos->{$key};
	      	}
	    }

	    foreach($flat_array as $hash => $value) {
	      	$keys = explode($decorator, $hash);
	      	$stdClassPos = $stdClass;
	      	foreach($keys as $i => $key) {
		        if($key === "") {
		          	continue;
		        }

		        if($key === '{array}') {
		          	continue;
		        }

		        if($i < (count($keys) - 1) && $keys[$i + 1] === '{array}') {
		          	$array = array();
		          	if($stdClassPos instanceof stdClass && !is_array($stdClassPos->{$key})) {
			            foreach($stdClassPos->{$key} as $value) {
			              	$array[] = $value;
			            }
			            $stdClassPos->{$key} = $array;
		          	}
		          	if(is_array($stdClassPos) && !is_array($stdClassPos[$key])) {
			            foreach($stdClassPos[$key] as $value) {
			              	$array[] = $value;
			            }
			            $stdClassPos[$key] = $array;
		          	}
	        	}

		        if($stdClassPos instanceof stdClass) {
		          	$stdClassPos = $stdClassPos->{$key};
		          	continue;
		        }

		        if(is_array($stdClassPos)) {
		          	$stdClassPos = $stdClassPos[$key];
		          	continue;
		        }
	      	}
	    }

	    return $stdClass;
  	}
}