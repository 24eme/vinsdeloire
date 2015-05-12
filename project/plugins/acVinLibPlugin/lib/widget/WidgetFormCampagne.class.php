<?php

class WidgetFormCampagne extends sfWidgetForm
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * format:       The campagne format string (%start_year%-%end_year% by default)
   *  * start_years:  An array of years for the start year select tag (optional)
   *                  Be careful that the keys must be the years, and the values what will be displayed to the user
   *  * end_years:    An array of years for the end year select tag (optional)
   *                  Be careful that the keys must be the years, and the values what will be displayed to the user
   *  * can_be_empty: Whether the widget accept an empty value (true by default)
   *  * empty_values: An array of values to use for the empty value (empty string for year, month, and day by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('format', '%start_year%-%end_year%');
    $years = range(date('Y') + 1, date('Y') - 10);
    $this->addOption('start_years', array_combine($years, $years));
    $this->addOption('end_years', array_combine($years, $years));

    $this->addOption('can_be_empty', true);
    $this->addOption('empty_values', array('start_year' => '', 'end_year' => ''));
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The campagne displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // convert value to an array
    $default = array('start_year' => null, 'end_year' => null);
    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }
    else
    {
    	$explodeValue = explode('-', $value);
    	if (count($explodeValue) > 1) {
    		$value = array('start_year' => $explodeValue[0], 'end_year' => $explodeValue[1]);
    	} elseif(count($explodeValue) > 0) {
    		$value = array('start_year' => $explodeValue[0], 'end_year' => null);
    	} else {
    		$value = $default;
    	}
    }

    $campagne = array();
    $emptyValues = $this->getOption('empty_values');

   	$campagne['%start_year%'] = $this->renderYearWidget($name.'[start_year]', $value['start_year'], array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['start_year']) + $this->getOption('start_years') : $this->getOption('start_years'), 'id_format' => $this->getOption('id_format')), array_merge($this->attributes, $attributes));
   	$campagne['%end_year%'] = $this->renderYearWidget($name.'[end_year]', $value['end_year'], array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['end_year']) + $this->getOption('end_years') : $this->getOption('end_years'), 'id_format' => $this->getOption('id_format')), array_merge($this->attributes, $attributes));

    return strtr($this->getOption('format'), $campagne);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderYearWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormSelect($options, $attributes);
    return $widget->render($name, $value);
  }
}
