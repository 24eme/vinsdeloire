<?php
/* This file is part of the acExceptionNotifier package.
 * (c) Actualys
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * acException allows you to render the application exception
 *
 * @package    acExceptionNotifier
 * @subpackage exception
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @version    0.1
 */
class acException extends sfException
{
	protected $exception;
	protected $format;
	
  /**
   * Construct
   * 
   * @param Exception $exception  An Exception implementation instance
   * @param string    $format     The trace format (txt or html)
   * @access public
   */
	public function __construct($exception, $format = 'html')
	{
		$this->setException($exception);
		$this->setFormat($format);
	}
	
  /**
   * Set the exception object
   * 
   * @param Exception $exception  An Exception implementation instance
   * @access public
   */
	public function setException($exception)
	{
		$this->exception = $exception;
	}
	
  /**
   * Get the exception
   * 
   * @access public
   * @return Exception Exception implementation instance
   */
	public function getException()
	{
		return $this->exception;
	}
	
  /**
   * Set the trace format
   * 
   * @param string $format (txt or html)
   * @access public
   */
	public function setFormat($format)
	{
		$this->format = $format;
	}
	
  /**
   * Get the trace format
   * 
   * @access public
   * @return string the trace format
   */
	public function getFormat()
	{
		return $this->format;
	}

  /**
   * Returns an array of exception informations header.
   * 
   * @access public
   * @return array An array of exception informations header
   */
	public function getExceptionInformations()
	{
		$exception = $this->getException();
		$message = (null === $this->getException()->getMessage()) ? 'n/a' : $exception->getMessage();
		$informations = array();
   		$informations[] = '<strong>500 | Internal Server Error | '.get_class($exception).'</strong>';
		$informations[] = '<span style="display: block; background-color: #EEEEEE; border-radius: 10px 10px 10px 10px; margin: 10px 0px; padding: 10px;">'.$message.'</span>';
   		return $informations;
	}

  /**
   * Returns an array of exception traces.
   * 
   * @access public
   * @return array An array of traces
   */
	public function getExceptionTraces()
	{
		return self::getTraces($this->getException(), $this->getFormat());
	}

  /**
   * Returns an array of main objects values
   * 
   * @access public
   * @return array An array of traces
   */
	public function getDebugTraces()
	{
		$traces = array();
	    if (class_exists('sfContext', false) && sfContext::hasInstance())
	    {
	      $context = sfContext::getInstance();
	      $traces[] = '<strong>Symfony settings :</strong>';
	      $traces[] = $settingsTable = self::formatArrayAsHtml(sfDebug::settingsAsArray());
	      $traces[] = '<strong>Request :</strong>';
	      $traces[] = $requestTable  = self::formatArrayAsHtml(sfDebug::requestAsArray($context->getRequest()));
	      $traces[] = '<strong>Response :</strong>';
	      $traces[] = $responseTable = self::formatArrayAsHtml(sfDebug::responseAsArray($context->getResponse()));
	      $traces[] = '<strong>User :</strong>';
	      $traces[] = $userTable     = self::formatArrayAsHtml(sfDebug::userAsArray($context->getUser()));
	      $traces[] = '<strong>Global vars :</strong>';
	      $traces[] = $globalsTable  = self::formatArrayAsHtml(sfDebug::globalsAsArray());
	    }
	    return $traces;
	}

  /**
   * Returns an array of exception traces.
   *
   * @param Exception $exception  An Exception implementation instance
   * @param string    $format     The trace format (txt or html)
   * @access protected
   * @static
   * @return array An array of traces
   */
	static protected function getTraces($exception, $format = 'html')
  	{
	    $traceData = $exception->getTrace();
	    array_unshift($traceData, array(
	      'function' => '',
	      'file'     => $exception->getFile() != null ? $exception->getFile() : null,
	      'line'     => $exception->getLine() != null ? $exception->getLine() : null,
	      'args'     => array(),
	    ));
	
	    $traces = array();
	    if ($format == 'html') {
	      $lineFormat = 'at <strong>%s%s%s</strong>(%s)<br />in <em>%s</em> line %s<br /><ul class="code" id="%s" style="display: %s">%s</ul>';
	    } else {
	      $lineFormat = 'at %s%s%s(%s) in %s line %s';
	    }
	    for ($i = 0, $count = count($traceData); $i < $count; $i++) {
	      $line = isset($traceData[$i]['line']) ? $traceData[$i]['line'] : null;
	      $file = isset($traceData[$i]['file']) ? $traceData[$i]['file'] : null;
	      $args = isset($traceData[$i]['args']) ? $traceData[$i]['args'] : array();
	      $traces[] = sprintf($lineFormat,
	        (isset($traceData[$i]['class']) ? $traceData[$i]['class'] : ''),
	        (isset($traceData[$i]['type']) ? $traceData[$i]['type'] : ''),
	        $traceData[$i]['function'],
	        self::formatArgs($args, false, $format),
	        self::formatFile($file, $line, $format, null === $file ? 'n/a' : sfDebug::shortenFilePath($file)),
	        null === $line ? 'n/a' : $line,
	        'trace_'.$i,
	        'block',
	        self::fileExcerpt($file, $line)
	      );
	    }
	    return $traces;
  	}
}