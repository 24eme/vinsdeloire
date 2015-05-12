<?php

interface acCouchdbFormDocableInterface 
{
	public function embedForm($name, sfForm $form, $decorator = null);
	public function bind(array $taintedValues = null, array $taintedFiles = null);
	public function getDocable();
        public function disabledRevisionVerification();
}