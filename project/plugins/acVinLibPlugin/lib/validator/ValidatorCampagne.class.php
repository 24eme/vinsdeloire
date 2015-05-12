<?php
class ValidatorCampagne extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * campagne_format:         A regular expression that campagnes must match
   *  * campagne_format_error:   The campagne format to use when displaying an error for a bad_format error (use campagne_format if not provided)
   *
   * Available error codes:
   *
   *  * bad_format
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  
  const REGEXP_CAMPAGNE = '#^[0-9]{4}-[0-9]{4}$#';
  
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('bad_format', '"%value%" does not match the camapgne format (%campagne_format%).');

    $this->addOption('campagne_format', null);
    $this->addOption('date_format_error');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
  	if (!is_array($value)) {
  		$year = $value;
  		$value = array('start_year' => $year - 1, 'end_year' => $year);
  	}
  	
    // check campagne format
    if (is_string($value) && $regex = $this->getOption('campagne_format'))
    {
      if (!preg_match($regex, $value, $match))
      {
        throw new sfValidatorError($this, 'bad_format', array('value' => $value, 'campagne_format' => $this->getOption('campagne_format_error') ? $this->getOption('campagne_format_error') : $this->getOption('campagne_format')));
      }

      $value = $match;
    }

    // convert array to date string
    if (is_array($value))
    {
      $value = $this->convertCampagneArrayToString($value);
    }

    return $value;
  }

  /**
   * Converts an array representing a campagne to a string.
   *
   *
   * @param  array $value  An array of campagne elements
   *
   * @return string A string
   */
  protected function convertCampagneArrayToString($value)
  {
    foreach (array('start_year', 'end_year') as $key)
    {
      if (isset($value[$key]) && !preg_match('#^[0-9]{4}$#', $value[$key]) && !empty($value[$key]))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }
    if (isset($value['start_year']) && !empty($value['start_year']) && isset($value['end_year']) && !empty($value['end_year'])) {
	    if ($value['start_year'] != ($value['end_year'] - 1)) {
	    	throw new sfValidatorError($this, 'invalid', array('value' => $value));
	    }
    }
    if ((!isset($value['start_year']) || empty($value['start_year'])) && (isset($value['end_year']) && !empty($value['end_year']))) {
    	throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    if ((isset($value['start_year']) && !empty($value['start_year'])) && (!isset($value['end_year']) || empty($value['end_year']))) {
    	throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    if ((isset($value['start_year']) && empty($value['start_year'])) && (!isset($value['end_year']) || empty($value['end_year']))) {
    	return '';
    }
    return $value['start_year'].'-'.$value['end_year'];    
  }
}
