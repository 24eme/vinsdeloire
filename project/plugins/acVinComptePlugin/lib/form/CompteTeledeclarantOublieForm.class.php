<?php

class CompteTeledeclarantOublieForm extends CompteTeledeclarantForm 
{
    public function configure() 
    {
        parent::configure();
        unset($this['email']);
        $this->getValidator('mdp1')->setOption('required', true);
        $this->getValidator('mdp2')->setOption('required', true);
    }
}