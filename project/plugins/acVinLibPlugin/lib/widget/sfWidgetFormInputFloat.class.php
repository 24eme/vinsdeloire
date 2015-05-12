<?php

class sfWidgetFormInputFloat extends sfWidgetFormInputText
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('float_format');
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfApplicationConfiguration::getActive()->loadHelpers('Float');
    $floatFormat = $this->getOption('float_format');
    $value = ($floatFormat)? sprintFloat($value, $floatFormat) : sprintFloat($value);
    $attributes['autocomplete'] = 'off';
    if (!isset($attributes['class']))
      $attributes['class'] = '';
    if (!($value*1)) {
        $attributes['class'] .= ' num_light';
        if(!strstr($name, 'noeud_droits_cvo')){
           $attributes['class'] .= ' num_float';
        }
    }

    return parent::render($name, $value, $attributes, $errors);
  }

  protected function attributesToHtmlCallback($k, $v)
  {
    return false === $v || ('' === $v && 'value' != $k) ? '' : sprintf(' %s="%s"', $k, $this->escapeOnce($v));
  }
}