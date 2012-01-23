<?php

class myUser extends sfBasicSecurityUser
{
  protected
    $member = null;

  public function getMemberByApiKey($apiKey)
  {
    if (!$apiKey)
    {
      return null;
    }

    $config = Doctrine::getTable('MemberConfig')->createQuery('c')
      ->leftJoin('c.Member')
      ->where('c.name = \'api_key\'')
      ->where('c.value = ?', $apiKey)
      ->fetchOne();

    if (!$config)
    {
      return null;
    }

    return $config->getMember();
  }

  public function getMember()
  {
    if (null !== $this->member)
    {
      return $this->member;
    }

    $request = sfContext::getInstance()->getRequest();

    $apiKey = $request['apiKey'];
    if (false === $apiKey)
    {
      $exception = new opErrorHttpException('apiKey parameter not specified.');
      throw $exception->setHttpStatusCode(401);
    }

    $member = $this->getMemberByApiKey($apiKey);
    if (null === $member)
    {
      $exception = new opErrorHttpException('Invalid API key.');
      throw $exception->setHttpStatusCode(401);
    }

    $this->member = $member;

    return $member;
  }

  public function getMemberId()
  {
    return $this->getMember()->getId();
  }
}
