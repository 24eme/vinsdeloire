<?php

abstract class acCouchdbObjectForm extends sfFormObject implements acCouchdbFormDocableInterface {

    protected $docable = null;

    public function __construct(acCouchdbJson $object, $options = array(), $CSRFSecret = null) {
        try {
            $object->getDocument();
        } catch (Exception $exc) {
            throw new acCouchdbException(sprintf('The form only accepts an object with a associated document'));
        }

        $this->object = $object;
        $this->isNew = false;
        $this->docable = new acCouchdbFormDocable($this, $object->getDocument());
        
        parent::__construct(array(), $options, $CSRFSecret);
        $this->updateDefaultsFromObject();
        $this->docable->init();
    }

    public function getDocable() {

        return $this->docable;
    }
    
    public function disabledRevisionVerification() {
        
        $this->docable->disabledRevisionVerification();
    }

    public function processValues($values) {
        // see if the user has overridden some column setter
        $valuesToProcess = $values;
        foreach ($valuesToProcess as $field => $value) {
            $method = sprintf('update%sField', $this->camelize($field));

            if (method_exists($this, $method)) {
                if (false === $ret = $this->$method($value)) {
                    unset($values[$field]);
                } else {
                    $values[$field] = $ret;
                }
            } else {
                // save files
                if ($this->validatorSchema[$field] instanceof sfValidatorFile) {
                    $values[$field] = $this->processUploadedFile($field, null, $valuesToProcess);
                }
            }
        }

        return $values;
    }

    protected function updateDefaultsFromObject() {
        $defaults = $this->getDefaults();

        // update defaults for the main object
        if ($this->isNew()) {
            $defaults = $defaults + $this->getObject()->toArray(true, false);
        } else {
            $defaults = $this->getObject()->toArray(true, false) + $defaults;
        }

        foreach ($this->embeddedForms as $name => $form) {
            if ($form instanceof acCouchdbObjectForm) {
                $form->updateDefaultsFromObject();
                $defaults[$name] = $form->getDefaults();
            }
        }

        $this->setDefaults($defaults);
    }

    public function embedForm($name, sfForm $form, $decorator = null) {
        $this->docable->beforeEmbedForm($name, $form, $decorator);
        parent::embedForm($name, $form, $decorator);
    }

    public function bind(array $taintedValues = null, array $taintedFiles = null)
    {
        parent::bind($taintedValues, $taintedFiles);
        $this->docable->postBind($taintedValues, $taintedFiles);
    }

    protected function processUploadedFile($field, $filename = null, $values = null) {
        if (!$this->validatorSchema[$field] instanceof sfValidatorFile) {
            throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
        }

        if (null === $values) {
            $values = $this->values;
        }

        if (isset($values[$field . '_delete']) && $values[$field . '_delete']) {
            $this->removeFile($field);

            return '';
        }

        if (!$values[$field]) {
            // this is needed if the form is embedded, in which case
            // the parent form has already changed the value of the field
            $oldValues = $this->getObject()->getModified(true, false);

            return isset($oldValues[$field]) ? $oldValues[$field] : $this->object->$field;
        }

        // we need the base directory
        if (!$this->validatorSchema[$field]->getOption('path')) {
            return $values[$field];
        }

        $this->removeFile($field);

        return $this->saveFile($field, $filename, $values[$field]);
    }

    protected function removeFile($field) {
        if (!$this->validatorSchema[$field] instanceof sfValidatorFile) {
            throw new LogicException(sprintf('You cannot remove the current file for field "%s" as the field is not a file.', $field));
        }

        $directory = $this->validatorSchema[$field]->getOption('path');
        if ($directory && is_file($file = $directory . '/' . $this->getObject()->$field)) {
            unlink($file);
        }
    }

    protected function saveFile($field, $filename = null, sfValidatedFile $file = null) {
        if (!$this->validatorSchema[$field] instanceof sfValidatorFile) {
            throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
        }

        if (null === $file) {
            $file = $this->getValue($field);
        }

        $method = sprintf('generate%sFilename', $this->camelize($field));

        if (null !== $filename) {
            return $file->save($filename);
        } else if (method_exists($this, $method)) {
            return $file->save($this->$method($file));
        } else if (method_exists($this->getObject(), $method)) {
            return $file->save($this->getObject()->$method($file));
        } else if (method_exists($this->getObject(), $method = sprintf('generate%sFilename', $field))) {
            // this non-camelized method name has been deprecated
            return $file->save($this->getObject()->$method($file));
        } else {
            return $file->save();
        }
    }

    protected function doUpdateObject($values) {
        $this->getObject()->fromArray($values);
    }

    protected function doSave($con = null) {
        if (null === $con) {
            $con = $this->getConnection();
        }

        $this->updateObject();
        $this->object->getCouchdbDocument()->save();
    }

    public function save($con = null) {
        if (!$this->isValid()) {
            throw $this->getErrorSchema();
        }

        try {
            $this->doSave($con);
        } catch (Exception $e) {
            throw $e;
        }

        return $this->getObject();
    }

    public function getModelName()
    {

        return null;
    }

    public function saveEmbeddedForms($con = null, $forms = null)
    {

        return ;
    }
    
    public function updateObjectEmbeddedForms($values, $forms = null)
  	{
  		
  		return ;
  	}

    public function getConnection() {

        return null;
    }
}
