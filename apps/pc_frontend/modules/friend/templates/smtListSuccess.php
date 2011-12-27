<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js"></script>
<script id="friendListTemplate" type="text/x-jquery-tmpl">
  <div class="span3">
    <div class="row_memberimg row"><a href="${member.profile_url}"><img src="${member.image}" class="rad10" width="57" height="57"></a></div>
    <div class="row_membername font10 row"><a href="${member.profile_url}">${member.name}</a> (${member.friends})</div>
  </div>
</script>
<script type="text/javascript">
$(function(){
  $.getJSON( "<?php echo app_url_for('api', 'member/friendList.json', array('id' => $member->getId())) ?>&apiKey=" + openpne.apiKey, function(json) {
    $('#friendListTemplate').tmpl(json.data).appendTo('#memberFriendList');
  });
});
</script>

<div class="row">
  <div class="gadget_header span12"><?php echo __('%1%\'s %friend% List', array('%1%' => $member->getName(), '%friend%' => $op_term['friend']->titleize())); ?></div>
</div>
<hr class="toumei" />
<div class="row" id="memberFirendList">

</div>
