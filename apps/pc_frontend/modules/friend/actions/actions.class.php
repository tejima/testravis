<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * friend actions.
 *
 * @package    OpenPNE
 * @subpackage friend
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class friendActions extends opFriendAction
{
  public function preExecute()
  {
    parent::preExecute();

    if ($this->id == $this->getUser()->getMemberId())
    {
      sfConfig::set('sf_nav_type', 'default');
    }
  }

  public function executeList(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartPhone(), 'friend', 'smtList');

    $this->size = 50;

    return parent::executeList($request);
  }

  public function executeSmtList(opWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($this->id);
    $this->getResponse()->setDisplayMember($this->member);

    return sfView::SUCCESS;
  }
}
