<?php
/* This file is part of the acExceptionNotifier package.
 * (c) Actualys
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * acExceptionNotifier allows you to handle the application exception
 *
 * @package    acExceptionNotifier
 * @subpackage lib
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @version    0.1
 */
class acExceptionEmailNotifier implements IExceptionNotifier
{

  /**
   * Handles the notification for the given event 
   *
   * @param sfEvent $event
   * @access public
   * @static
   */
	public static function exceptionHandler(sfEvent $event)
	{	
		if (!sfConfig::get('sf_debug') && is_object($exception = $event->getSubject())) {
			$acException = new acException($exception, sfConfig::get('app_ac_exception_notifier_format'));
			$traces = self::renderTraces($acException);
			self::notify($traces, $exception->getMessage());
		}
	}

  /**
   * Renders exception traces
   *
   * @param acException $acException
   * @access private
   * @static
   * @return string The exception traces
   */
	private static function renderTraces(acException $acException)
	{
		$traces  = implode('<br />', $acException->getExceptionInformations());
		$traces .= implode('<br />', $acException->getExceptionTraces());
		$traces .= '<hr />';
		$traces .= implode('<br />', $acException->getDebugTraces());
		return $traces;
	}

  /**
   * Notify by e-mail the given message
   *
   * @param string $message         A message to notify
   * @access private
   * @static
   */
	private static function notify($message, $title)
	{
		acEmailNotifier::exceptionEmailNotifier($message, $title);
	}
}