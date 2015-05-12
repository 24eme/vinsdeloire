<?php
class error401Exception extends sfException
{
  /**
   * Forwards to the 403 action.
   */
  public function printStackTrace()
  {
    $exception = null === $this->wrappedException ? $this : $this->wrappedException;

    if (sfConfig::get('sf_debug'))
    {
      $response = sfContext::getInstance()->getResponse();
      if (null === $response)
      {
        $response = new sfWebResponse(sfContext::getInstance()->getEventDispatcher());
        sfContext::getInstance()->setResponse($response);
      }

      $response->setStatusCode(401);

      return parent::printStackTrace();
    }
    else
    {
      header('HTTP/1.0 401 Unauthorized');
      exit;
    }
  }
}