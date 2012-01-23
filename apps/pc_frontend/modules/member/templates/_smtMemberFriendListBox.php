<?php use_helper('Javascript') ?>
<script id="friendListTemplate" type="text/x-jquery-tmpl">
  <div class="span3">
    <div class="row_memberimg row"><a href="${profile_url}"><img src="${profile_image}" class="rad10" width="57" height="57"></a></div>
    <div class="row_membername font10 row"><a href="${profile_url}">${name}</a> (${friends_count})</div>
  </div>
</script>
<script type="text/javascript">
$(function(){
  $.getJSON( '<?php echo app_url_for('api', 'member/search.json', array('friend' => 1, 'targetid' => $member->getId())); ?>&apiKey=' + openpne.apiKey, function(json) {
    $('#friendListTemplate').tmpl(json.data).appendTo('#memberFriendList');
  });
});
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('%friend% List', array('%friend%' => $op_term['friend'])) ?></div>
</div>
<hr class="toumei" />
<div class="row" id="memberFriendList">

</div> 
