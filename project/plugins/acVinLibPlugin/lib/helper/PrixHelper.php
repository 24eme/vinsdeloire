<?php
   function ttc($p) {
        return $p + $p * 0.196;
    }
    
function echoTtc($prixHt) 
{
  if (is_null($prixHt))
		return null;
  echo sprintf("%01.02f", round(($prixHt + $prixHt * 0.196), 2));
}
    