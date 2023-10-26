<?php
$stats_bar = get_field('stats-bar');
$background = get_field('stats-bar-background');
$dual_bg = get_field('stats-bar-dual-background');
$bubble_color = get_field('stats-bar-bubble-color');

if (!function_exists('stats_bar_select_color')) {
  function stats_bar_select_color($selection) {
    switch($selection) {
      case 'White':
        $color = 'FFF';
        break;
      case 'Grey':
        $color = '#f3f5f5';
        break;
      case 'Pine':
        $color = '#13382e';
        break;
      case 'Spruce':
        $color = '#276e6b';
        break;
      case 'Peppermint':
        $color = '#a8efca';
        break;
    }
    return $color;
  }
}


if ($bubble_color === "White") {
  $border_color = 'border-white';
} else if ($bubble_color === "Grey") {
  $border_color = 'border-grey';
}


$background = stats_bar_select_color($background);

if ($dual_bg === "Yes") {
  $background_2 = get_field('stats-bar-background-2');
  $background_2 = stats_bar_select_color($background_2);
}

?>

<section class="stats-bar-block" style="
<?php if ($dual_bg === "Yes") { 
  echo 'background: linear-gradient(' . 
  'to bottom,' . 
  $background . ' 0%,' .
  $background . ' 35%,' .
  $background_2 . ' 35%,' .
  $background_2 . ' 100%' .
  ');';
} else {
  echo 'background-color: ' . $background . ';';
}
?>
">
  <div class="stats-bar-block-container">
    <div class="stats-bar-block-body <?php echo $bubble_color; ?>">
      <div class="stats-bar-block-content">
      <?php if (have_rows('stats-bar')) {
        while (have_rows('stats-bar')) {
          the_row();

          $heading = get_sub_field('stats-bar-heading'); 
          $subheading = get_sub_field('stats-bar-subheading');
?>
          <div class="stats-bar-item <?php echo $border_color; ?>">
            <h3 class="stats-bar-heading"><?php echo $heading ?></h3>
            <p class="stats-bar-subheading"><?php echo $subheading; ?></p>
          </div>
<?php
        }
      }
?>
      </div>
    </div>
  </div>
</section>