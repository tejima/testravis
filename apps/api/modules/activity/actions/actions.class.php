<?php

class activityActions extends opApiActions
{
  public function executeSearch(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
      ->setViewerId($this->getUser()->getMemberId())
      ->includeSns();

    $query = $builder->buildQuery()
      ->andWhere('in_reply_to_activity_id IS NULL')
      ->andWhere('foreign_table IS NULL')
      ->andWhere('foreign_id IS NULL')
      ->limit(sfConfig::get('op_json_api_limit', 20));

    if (isset($request['keyword']))
    {
      $query->andWhereLike('body', $request['keyword']);
    }

    $this->activityData = $query->execute();

    $this->setTemplate('array');
  }

  public function executeMember(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
      ->setViewerId($this->getUser()->getMemberId());

    if (isset($request['member_id']))
    {
      $builder->includeMember(explode(',', $request['member_id']));
    }
    elseif (isset($request['id']))
    {
      $builder->includeMember(explode(',', $request['id']));
    }
    else
    {
      $builder->includeSelf();
    }

    $query = $builder->buildQuery()
      ->andWhere('in_reply_to_activity_id IS NULL')
      ->andWhere('foreign_table IS NULL')
      ->andWhere('foreign_id IS NULL')
      ->limit(sfConfig::get('op_json_api_limit', 20));

    $this->activityData = $query->execute();

    $this->setTemplate('array');
  }

  public function executeFriends(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
      ->setViewerId($this->getUser()->getMemberId())
      ->includeAllFriends();

    $query = $builder->buildQuery()
      ->andWhere('in_reply_to_activity_id IS NULL')
      ->andWhere('foreign_table IS NULL')
      ->andWhere('foreign_id IS NULL')
      ->limit(sfConfig::get('op_json_api_limit', 20));

    $this->activityData = $query->execute();

    $this->setTemplate('array');
  }

  public function executeCommunity(sfWebRequest $request)
  {
    if (isset($request['community_id']))
    {
      $communityIds = explode(',', $request['community_id']);
    }
    elseif (isset($request['id']))
    {
      $communityIds = explode(',', $request['id']);
    }
    else
    {
      $this->forward400('community_id parameter not specified.');
    }

    $communityMembers = Doctrine::getTable('CommunityMember')->createQuery()
      ->select('DISTINCT member_id')
      ->andWhereIn('community_id', $communityIds)
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);

    $builder = opActivityQueryBuilder::create()
      ->setViewerId($this->getUser()->getMemberId())
      ->includeMember($communityMembers);

    $query = $builder->buildQuery()
      ->andWhere('in_reply_to_activity_id IS NULL')
      ->andWhere('foreign_table IS "community"')
      ->andWhereIn('foreign_id', $communityIds)
      ->limit(sfConfig::get('op_json_api_limit', 20));

    $this->activityData = $query->execute();

    $this->setTemplate('array');
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
}
