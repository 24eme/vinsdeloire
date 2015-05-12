<?php

class Search {

	public static function matchTerm($term, $text) {
	    $reg = "/[ ]+/";
	    $text = self::normalize($text);
	    $term = self::normalize($term);

	    $words_text = preg_split($reg, $text);
	    $words_term = preg_split($reg, $term);

	    $text_final = "";

	    foreach($words_term as $key_term => $word_term) {
	      $matcher = "/".self::normalize($word_term)."/i";
	      $find = false;
	      foreach($words_text as $key_text => $word_text) {
	        if(preg_match($matcher, $word_text)) {
                    
	          $find = true;
	          unset($words_text[$key_text]);
	          break;
	        }
	        unset($words_text[$key_text]);
	      }
	      if(!$find) {
	        return false;
	      }
	    }

	    return $find;
  	}
        
        public static function matchTermLight($term, $text) {
	    $reg = "/[ ]+/";
	    $text = self::normalize($text);
	    $term = self::normalize($term);

	    $words_text = preg_split($reg, $text);
	    $words_term = preg_split($reg, $term);

	    $text_final = "";

	    foreach($words_term as $key_term => $word_term) {
	      $find = false;
	      foreach($words_text as $key_text => $word_text) {
	        if($word_term == $word_text) {
                    $find = true;
	          unset($words_text[$key_text]);
	          break;
	        }
	        unset($words_text[$key_text]);
	      }
	      if(!$find) {
	        return false;
	      }
	    }

	    return $find;
  	}

  	public static function normalize($text) {

    	return KeyInflector::unaccent($text);
  	}

    public static function getWords($value) {
        $words = array();
        $expressions = preg_split('/([,; \|()])/', $value);
        $words_mandatories = array("ac");
        foreach($expressions as $exp) {
            if(preg_match('/[\wû]{3,}/', $exp) || in_array(strtolower($exp), $words_mandatories)) {
                $words[] = strtolower($exp);
            }
        }
        return $words;
    }

}