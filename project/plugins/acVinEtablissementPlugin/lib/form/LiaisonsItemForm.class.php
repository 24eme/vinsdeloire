<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class LiaisonsItemForm
 * @author mathurin
 */
class LiaisonsItemForm extends acCouchdbObjectForm {

    public function configure() {
        foreach ($this->getObject() as $key => $object) {                
            $this->embedForm($key, new LiaisonItemForm($object));
        }
    }

    public function bind(array $taintedValues = null, array $taintedFiles = null) {
        foreach ($this->embeddedForms as $key => $form) {
            if (!array_key_exists($key, $taintedValues)) {
                $this->unEmbedForm($key);
            }
        }
        foreach ($taintedValues as $key => $values) {
            if (!is_array($values) || array_key_exists($key, $this->embeddedForms)) {
                continue;
            }
            $this->embedForm($key, new LiaisonItemForm($this->getObject()->add()));
        }
    }

    public function unEmbedForm($key) {
        unset($this->widgetSchema[$key]);
        unset($this->validatorSchema[$key]);
        unset($this->embeddedForms[$key]);
        $this->getObject()->remove($key);
    }

}