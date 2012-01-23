<?php

class opActivityQueryBuilder
{
  protected
    $table,
    $viewerId = null,
    $inactiveIds,
    $include;

  static public function create()
  {
    return new self();
  }

  public function __construct()
  {
    $this->table = Doctrine::getTable('ActivityData');
    $this->inactiveIds = Doctrine::getTable('Member')->getInactiveMemberIds();

    $this->resetInclude();
  }

  public function setViewerId($viewerId)
  {
    $this->viewerId = $viewerId;
    return $this;
  }

  public function resetInclude()
  {
    $this->include = array(
      'self' => false,
      'friend' => false,
      'sns' => false,
      'mention' => false,
      'member' => false,
      'community' => false,
    );

    return $this;
  }

  public function includeSelf()
  {
    $this->include['self'] = true;
    return $this;
  }

  public function includeFriends($target_member_id)
  {
    $this->include['friend'] = $target_member_id ?: $this->viewerId;
    return $this;
  }

  public function includeSns()
  {
    $this->include['sns'] = true;
    return $this;
  }

  public function includeMentions()
  {
    $this->include['mention'] = true;
    return $this;
  }

  public function includeMember($member_id)
  {
    $this->include['member'] = $member_id;
    return $this;
  }

  public function includeCommunity($community_id)
  {
    $this->include['community'] = $community_id;
    return $this;
  }

  public function buildQuery()
  {
    $query = $this->table->createQuery('a')
      ->leftJoin('a.Member');

    $subQuery = array();

    if ($this->include['self'])
    {
      $subQuery[] = $this->buildSelfQuery($query->createSubquery())
        ->addWhere('foreign_table IS NULL OR foreign_table <> "community"');
    }

    if ($this->include['friend'])
    {
      $subQuery[] = $this->buildFriendQuery($query->createSubquery(), $this->include['friend'])
        ->addWhere('foreign_table IS NULL OR foreign_table <> "community"');
    }

    if ($this->include['sns'])
    {
      $subQuery[] = $this->buildAllMemberQuery($query->createSubquery())
        ->addWhere('foreign_table IS NULL OR foreign_table <> "community"');
    }

    if ($this->include['mention'])
    {
      $subQuery[] = $this->buildMentionQuery($query->createSubquery());
    }

    if ($this->include['member'])
    {
      $subQuery[] = $this->buildMemberQueryWithCheckRel($query->createSubquery(), $this->include['member'])
        ->addWhere('foreign_table IS NULL OR foreign_table <> "community"');
    }

    if ($this->include['community'])
    {
      $subQuery[] = $this->buildCommunityQuery($query->createSubquery(), $this->include['community']);
    }

    $subQuery = array_map(array($this, 'trimSubqueryWhere'), $subQuery);

    $query->andWhere(implode(' OR ', $subQuery))
      ->orderBy('id DESC');

    return $query;
  }

  protected function buildSelfQuery($query)
  {
    return $this->buildMemberQuery($query, $this->viewerId, ActivityDataTable::PUBLIC_FLAG_PRIVATE);
  }

  protected function buildFriendQuery($query, $member_id)
  {
    $friendsQuery = $query->createSubquery()
      ->select('r.member_id_to')
      ->from('MemberRelationship r')
      ->addWhere('r.member_id_from = ?', $member_id)
      ->addWhere('r.is_friend = true')
      ->andWhereNotIn('r.member_id_to', $this->inactiveIds);

    return $this->buildMemberQuery($query, $friendsQuery, ActivityDataTable::PUBLIC_FLAG_FRIEND);
  }

  protected function buildAllMemberQuery($query)
  {
    return $this->buildMemberQuery($query, null, ActivityDataTable::PUBLIC_FLAG_SNS);
  }

  protected function buildMemberQuery($query, $member_id = null, $public_flag = ActivityDataTable::PUBLIC_FLAG_SNS)
  {
    if (is_array($member_id))
    {
      $query->andWhereIn('a.member_id', $member_id);
    }
    elseif ($member_id instanceof Doctrine_Query)
    {
      $query->andWhere('a.member_id IN ('.$member_id->getDql().')');
    }
    elseif (is_scalar($member_id))
    {
      $query->andWhere('a.member_id = ?', $member_id);
    }

    $query->andWhereIn('a.public_flag', $this->table->getViewablePublicFlags($public_flag));

    return $query;
  }

  protected function buildMemberQueryWithCheckRel($query, $member_id = null)
  {
    $subQuery = array();

    foreach ((array)$member_id as $id)
    {
      if ($this->viewerId === $id)
      {
        $subQuery[] = $this->buildSelfQuery($query->createSubquery());
      }
      elseif (in_array($id, $this->inactiveIds))
      {
        continue;
      }
      else
      {
        $relation = Doctrine::getTable('MemberRelationship')->retrieveByFromAndTo($this->viewerId, $id);

        if ($relation && $relation->isFriend())
        {
          $subQuery[] = $this->buildMemberQuery($query->createSubquery(), $id, ActivityDataTable::PUBLIC_FLAG_FRIEND);
        }
        else
        {
          $subQuery[] = $this->buildMemberQuery($query->createSubquery(), $id, ActivityDataTable::PUBLIC_FLAG_SNS);
        }
      }
    }

    if (!empty($subQuery))
    {
      $subQuery = array_map(array($this, 'trimSubqueryWhere'), $subQuery);
      $query->andWhere(implode(' OR ', $subQuery));
    }

    return $query;
  }

  protected function buildMentionQuery($query)
  {
    $friendQuery = $this->buildFriendQuery($query->createSubquery())
      ->andWhereLike('a.template_param', '|'.$this->viewerId.'|');

    $snsQuery = $this->buildAllMemberQuery($query->createSubquery())
      ->andWhereLike('a.template_param', '|'.$this->viewerId.'|');

    $subQuery = array_map(array($this, 'trimSubqueryWhere'), array($friendQuery, $snsQuery));
    $query->andWhere(implode(' OR ', $subQuery));

    return $query;
  }

  protected function buildCommunityQuery($query, $community_id)
  {
    $communityMemberIds = Doctrine::getTable('CommunityMember')->createQuery()
      ->select('DISTINCT member_id')
      ->addWhere('community_id = ?', $community_id)
      ->addWhere('is_pre = false')
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);

    return $this->buildMemberQueryWithCheckRel($query, $communityMemberIds)
      ->addWhere('foreign_table = "community"')
      ->addWhere('foreign_id = ?', $community_id);
  }

  protected function trimSubqueryWhere($subquery)
  {
    return '('.implode(' ', $subquery->getDqlPart('where')).')';
  }
}
