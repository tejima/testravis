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
      'member' => false,
    );

    return $this;
  }

  public function includeSelf()
  {
    $this->include['self'] = true;
    return $this;
  }

  public function includeAllFriends()
  {
    $this->include['friend'] = true;
    return $this;
  }

  public function includeSns()
  {
    $this->include['sns'] = true;
    return $this;
  }

  public function includeMember($member_id)
  {
    $this->include['member'] = $member_id;
    return $this;
  }

  public function buildQuery()
  {
    $query = $this->table->createQuery('a')
      ->leftJoin('a.Member');

    $subQuery = array();

    if ($this->include['self'])
    {
      $subQuery[] = $this->buildSelfQuery($query->createSubquery());
    }

    if ($this->include['friend'])
    {
      $subQuery[] = $this->buildFriendQuery($query->createSubquery());
    }

    if ($this->include['sns'])
    {
      $subQuery[] = $this->buildAllMemberQuery($query->createSubquery());
    }

    if ($this->include['member'])
    {
      $subQuery[] = $this->buildMemberQueryWithCheckRel($query->createSubquery(), $this->include['member']);
    }

    $subQuery = array_map(array($this, 'trimSubqueryWhere'), $subQuery);

    $query->andWhere(implode(' OR ', $subQuery))
      ->orderBy('id DESC')
      ->limit(20);

    return $query;
  }

  protected function buildSelfQuery($query)
  {
    return $this->buildMemberQuery($query, $this->viewerId, ActivityDataTable::PUBLIC_FLAG_PRIVATE);
  }

  protected function buildFriendQuery($query)
  {
    $friendsQuery = $query->createSubquery()
      ->select('r.member_id_to')
      ->from('MemberRelationship r')
      ->addWhere('r.member_id_from = ?', $this->viewerId)
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

    $subQuery = array_map(array($this, 'trimSubqueryWhere'), $subQuery);
    $query->andWhere(implode(' OR ', $subQuery));

    return $query;
  }

  protected function trimSubqueryWhere($subquery)
  {
    return '('.implode(' ', $subquery->getDqlPart('where')).')';
  }
}
