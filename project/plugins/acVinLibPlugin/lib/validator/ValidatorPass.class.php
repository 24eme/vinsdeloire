<?php

class ValidatorPass extends sfValidatorBase
{

  public function clean($value)
  {
    return $this->doClean($value);
  }

  protected function doClean($value)
  {
    return ($value === "")? null : $value;
  }
}
