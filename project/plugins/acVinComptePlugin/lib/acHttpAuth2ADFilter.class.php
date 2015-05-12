<?php
/**
 * HTTP Authentication connected to AD filter
 * @author Tangui Morlier <tmorlier@actualys.com>
 * Inspired by James McGlinn <james@mcglinn.org>
 *
 */
class acHttpAuth2ADFilter extends sfFilter
{
  /**
   * Execute filter
   *
   * @param sfFilterChain $filterChain
   */
  public function execute ($filterChain)
  {
    $context = $this->getContext(); 	
    $user = $context->getUser();
    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] == 'logout') {
      acHttpAuth2ADFilter::logout();
    }

    if ($user->isAuthenticated() && isset($_SERVER['PHP_AUTH_USER']) && $user->getAttribute('AUTH_USER') == $_SERVER['PHP_AUTH_USER'] && !isset($_GET['forcead'])) {
      return $filterChain->execute();
    }
    $user->clearCredentials();
    $user->setAuthenticated(true);

    $ad = new acActiveDirectory();
    try {
      if (!isset($_SERVER['PHP_AUTH_USER'])) {
	throw new Exception('phpauth');
      }
      $rights = $ad->getDescription($_SERVER['PHP_AUTH_USER']);
    }catch(Exception $e) {
      if (!sfConfig::get('app_ad_basebn')) {          
	      $rights = sfConfig::get('app_no_ad_rights', 'admin');
	      $user->addCredential('noAD');
      }
    }
    if (isset($_SERVER['PHP_AUTH_USER'])) {
           $user->setAttribute('AUTH_USER', $_SERVER['PHP_AUTH_USER']);
    }else{
           $user->setAttribute('AUTH_USER',  sfConfig::get('app_no_ad_rights', 'admin'));
    }

    $user->setAttribute('AUTH_DESC', $rights);
    $user->addCredential($rights);
    if ($rights == 'admin') {
      $user->addCredential('transactions');
    }
    if ($rights) {
      $user->addCredential('contacts');
    }
    $filterChain->execute();
  }
 

  /**
   * Sends HTTP Auth headers and exits
   *
   */
  public static function logout ($dest = null)
  {
    if (!sfConfig::get('app_ad_basebn')) {
      return ;
    }
    header('HTTP/1.0 401 Unauthorized');
    $extra = '';
    if (isset($_SERVER['PHP_AUTH_USER'])) {
      $extra = ' (not '.$_SERVER['PHP_AUTH_USER'].')';
    }
    header('WWW-Authenticate: Basic realm="Fake user'.$extra.'"');
    if ($dest) {
      header('Location: '.$dest."\n");
    }
    exit;
  }
}
