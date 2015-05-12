<?php
/* This file is part of the acExceptionNotifier package.
 * (c) Actualys
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * acEmailNotifier allows you to send email
 *
 * @package    acExceptionNotifier
 * @subpackage email
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @version    0.1
 */
class acEmailNotifier 
{

  /**
   * Sends the given message.
   *
   * @param Swift_Transport $message         The e-mail message
   * @access public
   * @static
   * @return int|false The number of sent emails
   */
    public static function exceptionEmailNotifier($message, $title) 
    {
    	$emailInformations = sfConfig::get('app_ac_exception_notifier_email');
        $from = array($emailInformations['from'] => $emailInformations['from_name']);
        $to = $emailInformations['to'];
        $subject = str_replace("%title%", $title, $emailInformations['subject']);
        $email = self::getMailer()->compose($from, $to, $subject, $message)->setContentType('text/html');
        return self::getMailer()->send($email);
    }

   /**
    * Retrieves the mailer.
    * 
    * @access private
    * @static
    * @return sfMailer The current sfMailer implementation instance.
    */
    private static function getMailer() 
    {
        return sfContext::getInstance()->getMailer();
    }
}