<?php

$block_heading = get_field('feature-block-heading');
$image = get_field('feature-block-image');
$block_type = get_field('feature-block-type');
$background_color = get_field('background-option');

$no_image_class = "";
if(!$image){ $no_image_class ="no-image"; }
?>

<section class="feature-detail-block <?php echo $background_color; ?>">

  <div class="feature-detail-container">

  <div class="feature-detail-body <?php echo $no_image_class; ?>">

  <div class="feature-detail-block-content-section">

    <h2 class="feature-detail-block-heading"><?php echo $block_heading; ?></h2>

  <?php 
  switch($block_type) {
    case 'Feature List': 
      $columns = get_field('feature-block-list-columns');
      $text = get_field('feature-block-description');

      ?>
      <div class="feature-text">
        <p><?php echo $text; ?></p>
      </div>
  <?php
      if ($columns == '2') { ?>
      <div class="feature-list two-cols">
  <?php 
      } else { ?>
      <div class="feature-list">
  <?php
        }
  ?>

  <?php    
        if (have_rows('feature-block-list-items')) {
          while(have_rows('feature-block-list-items')){
            the_row();
            $item = get_sub_field('field-block-list-item'); ?>
            <div class="feature-list-item">
              <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
              <p><?php echo $item; ?></p>
            </div>
  <?php
          }
        } ?>
      </div>
  <?php
      break;
    case 'Feature Accordion': 
      $cta_button = get_field('feature-block-cta-button-text');
      $cta_title = $cta_button['title'];
      $cta_url = $cta_button['url'];
      $link_target = $cta_button['target'] ? $cta_button['target'] : '_self';
  ?>
      <div class="feature-accordion">
  <?php
      if (have_rows('feature-block-accordion-items')) {
        while(have_rows('feature-block-accordion-items')) {
          the_row();
          $accordion_item_heading = get_sub_field('feature-block-accordion-item-heading');
          $accordion_item_text = get_sub_field('feature-block-accordion-item-description');
  ?>
        <h3 class="accordion-item-heading">
          <?php echo $accordion_item_heading; ?>
            <div class="icon-wrapper" aria-hidden="true">
              <svg class="plus" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M28 12.5H15.5V0H12.5V12.5H0V15.5H12.5V28H15.5V15.5H28V12.5Z" fill="#13382E"/>
              </svg>
              <svg class="minus" width="28" height="4" viewBox="0 0 28 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M28 0.5H0V3.5H28V0.5Z" fill="#13382E"/>
              </svg>
            </div>
        </h3>
        <div class="accordion-item-text">
          <p><?php echo $accordion_item_text; ?></p>
        </div>
  <?php
        }
      }
  ?>
      </div>
      <a href="<?php echo $cta_url; ?>" class="cta-button" target="<?php echo $link_target; ?>"><?php echo $cta_title; ?></a>
  <?php
      break;
    case 'Feature Stats': 
      $text = get_field('feature-block-description');
      $stats_heading_1 = get_field('feature-block-stats-heading-1');
      $stats_heading_2 = get_field('feature-block-stats-heading-2');
      $stats_text_1 = get_field('feature-block-stats-description-1');
      $stats_text_2 = get_field('feature-block-stats-description-2');
?>
      <div class="feature-stats">
        <div class="feature-text">
          <p><?php echo $text; ?></p>
        </div>
        <div class="stats-section-wrapper">
          <div class="stats-section stats-section-1">
            <h1 class="stats-heading"><?php echo $stats_heading_1;?></h1>
            <p class="stats-text"><?php echo $stats_text_1; ?></p>
          </div>
<?php
        if (!empty($stats_heading_2) && !empty($stats_text_2)) { 
?>
          <div class="stats-section stats-section-2">
            <h1 class="stats-heading"><?php echo $stats_heading_2;?></h1>
            <p class="stats-text"><?php echo $stats_text_2; ?></p>
          </div>
<?php   } ?>  
        </div>
      </div>
  <?php 
      break;
    case 'Premium Service': 
      $cta_button = get_field('feature-block-cta-button-text');
      $cta_title = $cta_button['title'];
      $cta_url = $cta_button['url'];
      $link_target = $cta_button['target'] ? $cta_button['target'] : '_self';
?>
      <div class="premium-service">
        <div class="feature-list">
<?php
        if (have_rows('feature-block-list-items')) {
          while(have_rows('feature-block-list-items')){
            the_row();
            $item = get_sub_field('field-block-list-item'); ?>
            <div class="feature-list-item">
              <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
              <p><?php echo $item; ?></p>
            </div>
  <?php
          }
        } ?>
        </div>
        <a href="<?php echo $cta_url; ?>" target="<?php echo $link_target; ?>" class="cta-button"><?php echo $cta_title; ?></a>
      </div>
  <?php
      break;
  }
  ?>
  </div>
  <?php if($image) { ?>
  <div class="feature-detail-block-image-section">
     <div class="image-wrapper<?php if ($block_type === "Feature Accordion") { echo " accordion-image"; } ?>">
      <img src="<?php echo $image['url']; ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
    </div>
  </div>
  <?php } ?>	
  </div>
  </div>
</section>