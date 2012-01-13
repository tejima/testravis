<?php use_helper('Javascript') ?>
<script id="friendListTemplate" type="text/x-jquery-tmpl">
  <div class="span3">
    <div class="row_memberimg row"><a href="${member.profile_url}"><img src="${member.image}" class="rad10" width="57" height="57"></a></div>
    <div class="row_membername font10 row"><a href="${member.profile_url}">${member.name}</a> (${member.count})</div>
  </div>
</script>
<script type="text/javascript">
$(function(){
  $.getJSON( '<?php echo app_url_for('api', 'member/friendList.json', array('id' => $member->getId())); ?>&apiKey=' + openpne.apiKey, function(json) {
    $('#friendListTemplate').tmpl(json.data).appendTo('#memberFriendList');
  });
});
</script>

<?php
op_include_parts('descriptionBox', 'smtProfileTop', array());
foreach ($member->getProfiles(true) as $profile)
{
  $caption = $profile->getProfile()->getCaption();
  if ($profile->getProfile()->isPreset())
  {
    $presetConfig = $profile->getProfile()->getPresetConfig();
    $caption = __($presetConfig['Caption']);
  }

  $profileValue = (string)$profile;
  if ('' === $profileValue)
  {
    continue;
  }

  if ($profile->getFormType() === 'textarea')
  {
    $profileValue = op_auto_link_text(nl2br($profileValue));
  }

  if ($profile->getProfile()->isPreset())
  {
    if ($profile->getFormType() === 'country_select')
    {
      $profileValue = $culture->getCountry($profileValue);
    }
    elseif ('op_preset_birthday' === $profile->getName())
    {
      $profileValue = op_format_date($profileValue, 'XShortDateJa');
    }

    $profileValue = __($profileValue);
  }

  $list[$caption] = $profileValue;
}
?>
<div class="row">
  <div class="gadget_header span12"><?php echo __('Profile') ?></div>
</div>
<div class="row">
<table class="zebra-striped">
<tbody>
<tr><td><?php echo $op_term['nickname'] ?></td><td><?php echo $member->getName(); ?></td></tr>
<?php foreach ($list as $k => $v): ?>
<tr><td><?php echo __($k); ?></td><td><?php echo $v; ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<div class="row">
  <div class="gadget_header span12"><?php echo __('Photo') ?></div>
</div>
<hr class="toumei" />
<div class="row">
  <div class="span8" style="float: center;">
    <hr class="toumei" />
    <?php echo op_image_tag_sf_image($member->getImageFileName(), array('size' => '180x180')) ?>
  </div>
</div>
<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('%friend% List', array('%friend%' => $op_term['friend'])) ?></div>
</div>
<hr class="toumei" />
<div class="row" id="memberFriendList">
</div>
 
