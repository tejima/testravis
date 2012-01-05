<?php

function op_api_member($member)
{
  $viewMemberId = sfContext::getInstance()->getUser()->getMemberId();

  $memberImageFileName = $member->getImageFileName();
  if (!$memberImageFileName)
  {
    $memberImage = op_image_path('no_image.gif', true);
  }
  else
  {
    $memberImage = sf_image_path($memberImageFileName, array('size' => '48x48'), true);
  }

  $relation = null;
  if ((string)$viewMemberId !== (string)$member->getId())
  {
    $relation = Doctrine::getTable('MemberRelationship')->retrieveByFromAndTo($viewMemberId, $member->getId());
  }

  return array(
    'id' => $member->getId(),
    'profile_image' => $memberImage,
    'screen_name' => $member->getConfig('op_screen_name', $member->getName()),
    'name' => $member->getName(),
    'profile_url' => op_api_member_profile_url($member->getId()),
    'friend' => $relation ? $relation->isFriend() : false,
    'blocking' => $relation ? $relation->isAccessBlocked() : false,
    'self' => $viewMemberId === $member->getId(),
    'friends_count' => $member->countFriends(),
    'self_introduction' => (string)$member->getProfile('op_preset_self_introduction', true) ?: null,
  );
}

function op_api_member_profile_url($member_id)
{
  return app_url_for('pc_frontend', array('sf_route' => 'obj_member_profile', 'id' => $member_id), true);
}

function op_api_activity($activity)
{
  $viewMemberId = sfContext::getInstance()->getUser()->getMemberId();
  $member = $activity->getMember();

  return array(
    'id' => $activity->getId(),
    'member' => op_api_member($member),
    'body' => $activity->getBody(),
    'body_html' => op_activity_linkification(op_api_force_escape($activity->getBody())),
    'uri' => $activity->getUri(),
    'source' => $activity->getSource(),
    'source_uri' => $activity->getSourceUri(),
    'created_at' => date('r', strtotime($activity->getCreatedAt())),
  );
}

function op_activity_linkification($body, $options = array())
{
  return preg_replace_callback('/(@+)([-._0-9A-Za-z]+)/', function ($matches)
  {
    $at = $matches[1];
    $screenName = $matches[2];
    $screenNameConfig = Doctrine::getTable('MemberConfig')->createQuery()
      ->select('member_id')
      ->addWhere('name = "op_screen_name"')
      ->addWhere('value = ?', $screenName)
      ->fetchOne(array(), Doctrine::HYDRATE_NONE);

    if ($screenNameConfig)
    {
      $memberId = $screenNameConfig[0];
      return link_to($at.$screenName, op_api_member_profile_url($memberId), array('target' => '_blank'));
    }

    return $matches[0];
  }, $body);
}

function op_api_force_escape($text)
{
  if (!sfConfig::get('sf_escaping_strategy'))
  {
    // escape body even if escaping method is disabled.
    $text = sfOutputEscaper::escape(sfConfig::get('sf_escaping_method'), $text);
  }

  return $text;
}

function op_api_community($community)
{
  $viewMemberId = sfContext::getInstance()->getUser()->getMemberId();

  $communityUrl = app_url_for('pc_frontend', array('sf_route' => 'community_home', 'id' => $community->getId()), true);

  $communityImageFileName = $community->getImageFileName();
  if (!$communityImageFileName)
  {
    $communityImage = op_image_path('no_image.gif', true);
  }
  else
  {
    $communityImage = sf_image_path($communityImageFileName, array('size' => '48x48'), true);
  }

  $communityMember = Doctrine::getTable('CommunityMember')
    ->retrieveByMemberIdAndCommunityId($viewMemberId, $community->getId());

  return array(
    'id' => $community->getId(),
    'name' => $community->getName(),
    'category' => (string)$community->getCommunityCategory() ?: null,
    'community_url' => $communityUrl,
    'community_image_url' => $communityImage,
    'joining' => $communityMember ? !$communityMember->getIsPre() : false,
    'admin' => $communityMember ? $communityMember->hasPosition('admin') : false,
    'sub_admin' => $communityMember ? $communityMember->hasPosition('sub_admin') : false,
    'created_at' => op_api_date($community->getCreatedAt()),
    'admin_member' => op_api_member($community->getAdminMember()),
    'member_count' => $community->countCommunityMembers(),
    'public_flag' => $community->getConfig('public_flag'),
    'register_policy' => $community->getConfig('register_policy'),
    'description' => $community->getConfig('description'),
  );
}

function op_api_date($date)
{
  return gmdate('r', strtotime($date));
}
