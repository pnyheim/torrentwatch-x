 <form action="torrentwatch.php?updateFavorite=1"  
 class="favinfo" id="favorite_<?php echo $key ?>" <?php echo $style; ?>>
  <input type="hidden" name="idx" id="idx" value="<?php echo $key; ?>">
  <div class="favorite_name">
    <label class="item" title="Name of the Favorite">Name:</label>
    <input type="text" class="text" name="name" value="<?php echo $item['Name']; ?>">
  </div>
  <div class="favorite_filter">
    <label class="item" title="Regexp filter, use .* to match all">Filter:</label>
    <input type="text" class="text" name="filter" value="<?php echo $item['Filter']; ?>">
  </div>
  <div class="favorite_not">
    <label class="item" title="Regexp Not Filter">Not:</label>
    <input type="text" class="text" name="not" value="<?php echo $item['Not']; ?>">
  </div>
  <div class="favorite_savein" id="favorite_savein">
    <label class="item" title="Save Directory or Default">Save In:</label>
    <input type="text" class="text" name="savein" value="<?php echo $item['Save In']; ?>">
  </div>
  <div class="favorite_episodes">
    <label class="item" title="Episode filter. ex.: 1x1-3x24 for Season 1 to 3 episode 24. To just set a starting point use: 2x10. You may use s01e12 instead of 1x12." >Episodes:</label>
    <input type="text" class="text" name="episodes" title="Episode filter. ex.: 1x1-3x24 for Season 1 to 3 episode 24. To just set a starting point use: 2x10. You may use s01e12 instead of 1x12." value="<?php echo $item['Episodes'] ?>">
  </div>
  <div class="favorite_feed">
    <label class="item" title="Feed to match against">Feed:</label>
    <select name="feed">
      <?php echo $feed_options; ?>
    </select>
  </div>
  <div class="favorite_quality">
    <label class="item" title="Regexp Filter against full title">Quality:</label>
    <input type="text" class="text" name="quality" value="<?php echo $item['Quality']; ?>">
  </div>
  <div class="favorite_seedratio"><label class="item" title="Maximum seeding ratio, set to -1 to disable">Seed Ratio:</label>
    <input type="text" class="text" name="seedratio" value="<?php echo _isset($item, 'seedRatio', '-1'); ?>">
  </div>
<?php if($config_values['Settings']['Client'] == "folder") { echo '<br><br><br>'; }; ?>
  <div class="buttonContainer">
    <a class="submitForm button" id="Update" href="#">Update</a>
    <a class="submitForm button" id="Delete" href="#">Delete</a>
    <a class="toggleDialog button" id="Close" href="#">Close</a>
  </div>
</form>
