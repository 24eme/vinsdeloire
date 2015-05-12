<?php

class CompteTeledeclarantCreationForm extends CompteTeledeclarantForm 
{
    public function configure() 
    {
        parent::configure();
        $this->getValidator('mdp1')->setOption('required', true);
        $this->getValidator('mdp2')->setOption('required', true);
    }
}