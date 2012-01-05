<?php

class communityActions extends opApiActions
{
  public function executeSearch(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Community')->createQuery();

    if (isset($request['keyword']))
    {
      $query->andWhereLike('name', $request['keyword']);
    }

    $this->communities = $query->execute();

    $this->setTemplate('array');
  }
}
