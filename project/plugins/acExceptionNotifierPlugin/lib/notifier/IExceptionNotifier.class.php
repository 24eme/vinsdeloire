<?php
/* This file is part of the acExceptionNotifier package.
 * (c) Actualys
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * IExceptionNotifier 
 *
 * @package    acExceptionNotifier
 * @subpackage lib
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @version    0.1
 */
interface IExceptionNotifier
{  

  /**
   * Handles the notification for the given exception 
   *
   * @param sfEvent $event
   * @access public
   * @static
   */
	public static function exceptionHandler(sfEvent $event);
} 