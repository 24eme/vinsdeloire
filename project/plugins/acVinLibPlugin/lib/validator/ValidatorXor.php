<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class ValidatorXor
 * @author mathurin
 */
class ValidatorXor extends sfValidatorSchema {
    
    private $f0;
    private $f1;
    //Obligation d'avoir 2 champs + 2 msg ['BOTH'] ['NONE'] checking + comments

    public function __construct($fields = null, $options = array(), $messages = array())
    {
        $this->addRequiredOption('field0');
        $this->addRequiredOption('field1');
        
        $this->f0 = $options['field0'];        
        $this->f1 = $options['field1'];
        
        $this->addMessage('both',$messages['both']);
        $this->addMessage('none',$messages['none']);
        
        
        parent::__construct($fields,$options, $messages);
        $this->addOption('throw_global_error', false);
        
    }
    
    
    public function configure($options = array(), $messages = array()) 
    {
    }
    
    
    public function setXorParameters($fieldName0,$fieldName1,$bothMsg,$noneMsg) 
    {
        $this->associateByName($fieldName0, $fieldName1);
        $this->associateErrorsMsg($bothMsg, $noneMsg);
    }
    
    public function setFieldsByName($fieldName0,$fieldName1)
    {
        $this->f0 = $fieldName0;        
        $this->f1 = $fieldName1;
        $this->addOption($this->f0.'_field', $this->f0);
        $this->addOption($this->f1.'_field', $this->f1);
        $this->addOption('throw_global_error', false);
    }
    
    public function setErrorsMsg($bothMsg,$noneMsg)
    {
        $this->addMessage('both', $bothMsg);
        $this->addMessage('none', $noneMsg);
    }

    protected function doClean($values) {
        
        if ($this->isValidatorFieldsValid($values)) {
            
            return $values;
        }
        
        if (!empty($values[$this->f0]) && !empty($values[$this->f1])) {
        	$message = $this->messages['both'];
        }
        elseif (empty($values[$this->f0]) && empty($values[$this->f1])) {
        	$message = $this->messages['none'];
        }
        else {
            return $values;
        }
        if ($this->getOption('throw_global_error')) {
            throw new sfValidatorError($this, 'invalid');
        }

        throw new sfValidatorErrorSchema($this, array($this->f0 => new sfValidatorError($this, $message)));
    }
    
    private function isValidatorFieldsValid($values) {
        
        return $values[$this->f0] === null || $values[$this->f1] === null;
    }

}
