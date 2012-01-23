<?php

class activityActions extends opApiActions
{
  public function executeSearch(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
      ->setViewerId($this->getUser()->getMemberId());

    if (isset($request['target']))
    {
      if ('friend' === $request['target'])
      {
        $builder->includeFriends($request['target_id'] ? $request['target_id'] : null);
      }
      elseif ('community' === $request['target'])
      {
        $this->forward400Unless($request['target_id'], 'target_id parameter not specified.');
        $builder->includeCommunity($request['target_id']);
      }
      else
      {
        $this->forward400('target parameter is invalid.');
      }
    }
    else
    {
      if (isset($request['member_id']))
      {
        $builder->includeMember(explode(',', $request['member_id']));
      }
      else
      {
        $builder->includeSns();
      }
    }

    $query = $builder->buildQuery();

    if (isset($request['keyword']))
    {
      $query->andWhereLike('body', $request['keyword']);
    }

    $globalAPILimit = sfConfig::get('op_json_api_limit', 20);
    if (isset($request['count']) && (int)$request['count'] < $globalAPILimit)
    {
      $query->limit($request['count']);
    }
    else
    {
      $query->limit($globalAPILimit);
    }

    if (isset($request['max_id']))
    {
      $query->addWhere('id <= ?', $request['max_id']);
    }

    if (isset($request['since_id']))
    {
      $query->addWhere('id > ?', $request['since_id']);
    }

    $this->activityData = $query
      ->andWhere('in_reply_to_activity_id IS NULL')
      ->execute();

    $this->setTemplate('array');
  }

  public function executeMember(sfWebRequest $request)
  {
    if ($request['id'])
    {
      $request['member_id'] = $request['id'];
    }

    if (isset($request['target']))
    {
      unset($request['target']);
    }

    $this->forward('activity', 'search');
  }

  public function executeFriends(sfWebRequest $request)
  {
    $request['target'] = 'friend';

    if (isset($request['member_id']))
    {
      $request['target_id'] = $request['member_id'];
      unset($request['member_id']);
    }
    elseif (isset($request['id']))
    {
      $request['target_id'] = $request['id'];
      unset($request['id']);
    }

    $this->forward('activity', 'search');
  }

  public function executeCommunity(sfWebRequest $request)
  {
    $request['target'] = 'community';

    if (isset($request['community_id']))
    {
      $request['target_id'] = $request['community_id'];
      unset($request['community_id']);
    }
    elseif (isset($request['id']))
    {
      $request['target_id'] = $request['id'];
      unset($request['id']);
    }
    else
    {
      $this->forward400('community_id parameter not specified.');
    }

    $this->forward('activity', 'search');
  }

  public function executePost(sfWebRequest $request)
  {
    $this->forward400Unless(isset($request['body']), 'body parameter not specified.');

    $memberId = $this->getUser()->getMemberId();
    $body = $request['body'];
    $options = array();

    if (isset($request['public_flag']))
    {
      $options['public_flag'] = $request['public_flag'];
    }

    if (isset($request['in_reply_to_activity_id']))
    {
      $options['in_reply_to_activity_id'] = $request['in_reply_to_activity_id'];
    }

    if (isset($request['uri']))
    {
      $options['uri'] = $request['uri'];
    }
    elseif (isset($request['url']))
    {
      $options['uri'] = $request['url'];
    }

    if (isset($request['target']) && 'community' === $request['target'])
    {
      if (isset($request['target_id']))
      {
        $this->forward400('target_id parameter not specified.');
      }

      $options['foreign_table'] = 'community';
      $options['foreign_id'] = $request['target_id'];
    }

    $options['source'] = 'API';

    $this->activity = Doctrine::getTable('ActivityData')->updateActivity($memberId, $body, $options);

    $this->setTemplate('object');
  }

  public function executeDelete(sfWebRequest $request)
  {
    if (isset($request['activity_id']))
    {
      $activityId = $request['activity_id'];
    }
    elseif (isset($request['id']))
    {
      $activityId = $request['id'];
    }
    else
    {
      $this->forward400('activity_id parameter not specified.');
    }

    $activity = Doctrine::getTable('ActivityData')->find($activityId);

    $this->forward404Unless($activity, 'Invalid activity id.');

    $this->forward403Unless($activity->getMemberId() === $this->getUser()->getMemberId());

    $activity->delete();

    return $this->renderJSON(array('status' => 'success'));
  }

  public function executeMentions(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
      ->setViewerId($this->getUser()->getMemberId())
      ->includeMentions();

    $query = $builder->buildQuery()
      ->andWhere('in_reply_to_activity_id IS NULL')
      ->andWhere('foreign_table IS NULL')
      ->andWhere('foreign_id IS NULL')
      ->limit(20);

    $this->activityData = $query->execute();

    $this->setTemplate('array');
  }
}
