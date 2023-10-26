<?php 
$main_image = get_field('customer-story-image');
$customer_image = get_field('customer-story-customer-image');
$story_background_select = get_field('customer-story-background-color');
$heading = get_field('customer-story-heading');
$text = get_field('customer-story-text');
$cta = get_field('customer-story-cta-text');
$dual_bg = get_field('customer-story-dual-background');
$bg_1_select = get_field('customer-story-background-1');
$should_remove_top_padding = get_field('customer-story-background-remove-top-padding');
$should_remove_bottom_padding = get_field('customer-story-background-remove-bottom-padding');

$section_classes = "";

if ($should_remove_top_padding) {
  $section_classes .= "no-top-padding";
}

if ($should_remove_bottom_padding) {
  $section_classes .= " no-bottom-padding";
}

$background_color = "#F3F5F5";

switch($bg_1_select) {
  case 'Pine':
    $background_color = "#13382E";
    break;
  case 'Spruce': 
    $background_color = "#276E6B";
    break;
  case 'Peppermint':
    $background_color = "#A8EFCA";
    break;
  case 'Grey':
    $background_color = "#F3F5F5";
    break;
  case 'White':
    $background_color = "#FFF";
    break;
  default:
    break;
}

if ($dual_bg == 'Yes') {
  $bg_2_select = get_field('customer-story-background-2');
  $bg_2 = "#F3F5F5";

  switch($bg_2_select) {
    case 'Pine':
      $bg_2 = "#13382E";
      break;
    case 'Spruce': 
      $bg_2 = "#276E6B";
      break;
    case 'Peppermint':
      $bg_2 = "#A8EFCA";
      break;
    case 'Grey':
      $bg_2 = "#F3F5F5";
      break;
    case 'White':
      $bg_2 = "#FFF";
      break;
    default:
      break;
  }
}
?>

<section class="customer-story <?php echo $section_classes; ?>"
<?php 
  if ($dual_bg == 'Yes') {
    echo 'style="background: linear-gradient(' . 
      'to top,' . 
      $bg_2 . ' 0%,' .
      $bg_2 . ' 50%,' .
      $background_color . ' 50%,' .
      $background_color . ' 100%' .
      ');"';
  } else {
    echo 'style="background: ' . $background_color . '"';
  }
?>>
  <div class="customer-story-container "> 
  <div class="customer-story-body <?php echo $story_background_select; ?>">
    <div class="customer-story-image-wrapper">
      <div class="main-image-wrapper">
        <img class="customer-story-image" src="<?php echo esc_url($main_image['url']); ?>" alt="<?php echo esc_attr($main_image['alt']); ?>" />
      </div>
      <div class="image-wrapper">
        <img class="customer-brand-image-mobile" src="<?php echo esc_url($customer_image['url']); ?>" alt="<?php echo esc_attr($customer_image['alt']); ?>" />
      </div> 
    </div>
    <div class="customer-story-content-wrapper">
      <div class="image-wrapper">
        <img class="customer-brand-image-desktop" src="<?php echo esc_url($customer_image['url']); ?>" alt="<?php echo esc_attr($customer_image['alt']); ?>" />
      </div>
      <h2 class="customer-story-heading"><?php echo $heading; ?></h2>
      <p class="customer-story-text"><?php echo $text; ?></p>
      <div class="customer-story-cta">

      <?php 
        if ($cta) {
          $title = $cta['title'];
          $link = $cta['url'];
          $target = $cta['target'] ? $cta['target'] : '_self';      
      ?>
        <a class="btn btn-secondary customer-story-cta-button" target="<?php echo $target; ?>" href="<?php echo $link; ?>"><?php echo $title; ?></a>
      <?php } ?>        

        <svg class="customer-story-cta-icon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-arrow-right-short" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
        </svg>
      </div>
    </div>
  </div>
  </div>
</section>