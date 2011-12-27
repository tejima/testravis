<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * community actions.
 *
 * @package    OpenPNE
 * @subpackage community
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class communityActions extends opCommunityAction
{
 /**
  * Executes home action
  *
  * @param opWebRequest $request A request object
  */
  public function executeHome(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartPhone(), 'community', 'smtHome');

    return parent::executeHome($request);
  }

 /**
  * Executes smtHome action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtHome(opWebRequest $request)
  {
    $result = parent::executeHome($request);

    $this->getResponse()->setDisplayCommunity($this->community);

    return $result;
  }

 /**
  * Executes edit action
  *
  * @param opWebRequest $request A request object
  */
  public function executeEdit(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartPhone(), 'community', 'smtEdit');

    $this->enableImage = true;
    $result = parent::executeEdit($request);

    if ($this->community->isNew()) {
      sfConfig::set('sf_nav_type', 'default');
    }


    return $result;
  }

 /**
  * Executes smtEdit action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtEdit(opWebRequest $request)
  {
    $result = parent::executeEdit($request);

    if ($this->community->isNew())
    {
      $this->setLayout('smtLayoutHome');
    }
    else
    {
      $this->getResponse()->setDisplayCommunity($this->community);
    }

    return $result;
  }

 /**
  * Executes memberList action
  *
  * @param opWebRequest $request A request object
  */
  public function executeMemberList(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartPhone(), 'community', 'smtMemberList');

    return parent::executeMemberList($request);
  }

 /**
  * Executes smtMemberList action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtMemberList(opWebRequest $request)
  {
    $result = parent::executeMemberList($request);

    $this->getResponse()->setDisplayCommunity($this->community);

    return $result;
  }

 /**
  * Executes joinlist action
  *
  * @param sfRequest $request A request object
  */
  public function executeJoinlist($request)
  {
    sfConfig::set('sf_nav_type', 'default');

    if ($request->hasParameter('id') && $request->getParameter('id') != $this->getUser()->getMemberId())
    {
      sfConfig::set('sf_nav_type', 'friend');
    }

    return parent::executeJoinlist($request);
  }
}
