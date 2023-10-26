<?php 
$main_heading = get_field('security-details-heading');
?>
<section class="security-details-block">
  <div class="security-details-block-container">
    <div class="security-details-block-body">
      <h3 class="security-details-heading"><?php echo $main_heading; ?></h3>
      <div class="security-details">
<?php
      if (have_rows('security-details')) {
        while(have_rows('security-details')) {
          the_row();
?>
          <div class="security-detail">
            <h4 class="detail-heading"><?php echo get_sub_field('heading'); ?></h4>
            <p class="detail-copy"><?php echo get_sub_field('copy'); ?></p>
          </div>
<?php
        }
      }
?>
      </div>
    </div>
  </div>
</section>