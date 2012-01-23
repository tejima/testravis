<div class="menuform hide template">
  <div class="menu-margin row">
    <?php echo op_image_tag('MENU_MARGIN.png', array('width' => '324', 'height' => '12')); ?>
  </div>
  <div class="menu-top row">    　
  </div>
  <div class="menu-middle row">
    <div class="span10 offset1 font20" style="text-align: left; margin-bottom: 4px;">
     Home Menu
    </div>
  </div>

  <div class="menu-middle row">
    <div class="span11 offset1">
      <?php include_component('default', 'smtLocalNav'); ?>
    </div>
  </div>
  <div class="menu-middle row">
    <div class="span10 offset1 font20" style="text-align: left; margin-bottom: 4px;">
     Global Menu
    </div>
  </div>
  <div class="menu-middle row">
    <div class="span11 offset1">    
      <?php include_component('default', 'smtGlobalNav'); ?>
    </div>
  </div>
  <div class="menu-bottom row">
    <div class="span12"></div>
  </div>
</div>

