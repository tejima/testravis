<script type="text/javascript">
$(document).ready(function(){
  $("#postbutton").click(function(){
    $(".postform").toggle();
  });
});
$(document).ready(function(){
  $("#menubutton").click(function(){
    $(".menuform.template").toggle();
  }); 
});

</script>

<div id="tosaka" class="row">
  <div class="span4"><?php echo link_to(op_image_tag('LOGO.png', array('height' => '32')), '@homepage'); ?></div>
  <div class="span4 center"><?php echo op_image_tag('NOTIFY_CENTER.png', array('height' => '32')) ?></div>
  <div class="span3 offset1 center"><?php echo op_image_tag('POST.png', array('height' => '32')) ?></div>
</div>
