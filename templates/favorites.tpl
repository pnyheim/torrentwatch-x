<div class="dialog_window" id="favorites">
  <div class="favorite">
   <ul class="favorite">
    <li><a href="#favorite_new">New Favorite</a></li>
    <?php if(isset($config_values['Favorites'])): ?>
      <?php foreach($config_values['Favorites'] as $key => $item): ?>
        <li id="fav_<?php echo $key ?>"><a href="#favorite_<?php echo $key; ?>"><?php echo $item['Name']; ?></a></li>
      <?php endforeach; ?>
    <?php endif; ?>
   </ul> 
  </div>
  <div class=favinfo>
  <?php display_favorites_info(array('Name' => '',
                                     'Filter' => '',
                                     'Not' => '',
                                     'Save In' => 'Default',
                                     'Episodes' => '',
                                     'Feed' => '',
                                     'Quality' => 'All'), "new"); ?>
  <?php if(isset($config_values['Favorites']))
          array_walk($config_values['Favorites'], 'display_favorites_info'); ?>
      <div id="favClose" class="buttonContainer">
          <a class="toggleDialog button" id="Close" href="#">Close</a>
      </div>
  </div>
</div>

