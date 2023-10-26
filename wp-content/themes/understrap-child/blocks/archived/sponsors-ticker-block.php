<?php 
  $background_color = get_field('ticker-color');
?>

<section class="sponsors-ticker-block" style="background-color: <?php echo $background_color; ?>;">
  <div class="sponsors-ticker-wrapper">
    <?php 
      if(have_rows('sponsors-images')) {
        while(have_rows('sponsors-images')) {
          the_row();
          $sponsor = get_sub_field('sponsors-logo');
          echo '<img class="sponsor-ticker" src="' . esc_url($sponsor['url']) . '" alt="' .  esc_attr($sponsor['alt']) . '">';
        }
      }
      if(have_rows('sponsors-images')) {
        while(have_rows('sponsors-images')) {
          the_row();
          $sponsor = get_sub_field('sponsors-logo');
          echo '<img class="sponsor-ticker" src="' . esc_url($sponsor['url']) . '" aria-hidden="true">';
        }
      }
    ?>
  </div>
</section>