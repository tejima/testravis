<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * csrfError action.
 *
 * @package    OpenPNE
 * @subpackage default
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class csrfErrorAction extends sfAction
{
  public function execute($request)
  {
    if ($request->isSmartPhone())
    {
      $this->setLayout('smtLayoutSns');
      $this->setTemplate('smtCsrf');
    }
  }
}
