<?php

abstract class _Compte extends acVinCompte {
	public function getGecos() 
    {
      return $this->login.', '.$this->nom;
    }
}