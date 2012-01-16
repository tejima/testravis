<?php use_helper('Javascript') ?>
<script id="communityMemberJoinListTemplate" type="text/x-jquery-tmpl">
  <div class="span3">
    <div class="row_memberimg row"><a href="${profile_url}"><img src="${profile_image}" class="rad10" width="57" height="57"></a></div>
    <div class="row_membername font10 row"><a href="${profile_url}">${name}</a> (${friends_count})</div>
  </div>
</script>
<script type="text/javascript">
$(function(){
  $.getJSON( '<?php echo app_url_for('api', 'member/search.json', array('community' => 1, 'targetid' => $community->getId())); ?>&apiKey=' + openpne.apiKey, function(json) {
    $('#communityMemberJoinListTemplate').tmpl(json.data).appendTo('#communityMemberJoinList');
  });
});
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('%community% Members', array('%community%' => $op_term['community'])) ?></div>
</div>
<hr class="toumei" />
<div class="row" id="communityMemberJoinList">
</div>
