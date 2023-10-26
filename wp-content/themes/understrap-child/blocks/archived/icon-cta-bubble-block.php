<?php
$icon_cta_section_header = get_field('icon-cta-bubble-section-heading');

$section_background = get_field('icon-cta-section-background-color');

$heading_color_select = get_field('icon-cta-bubble-section-heading-text-color');

$select_col_count = get_field('select-column-count');

if ($select_col_count === "Yes") {
  $column_count = get_field('grid-column-count');
}

if ($heading_color_select === "White") {
  $heading_color = '#FFF';
} else if ($heading_color_select === "Pine") {
  $heading_color = "#13382e";
}

if ($section_background === "Spruce") {
  $section_background_color = '#276e6b';
} else if ($section_background === "White") {
  $section_background_color = "#FFF";
} else if ($section_background === "Grey") {
  $section_background_color = '#f3f5f5';
}

$count = count(get_field('icon-cta-bubbles'));
if ($count == 2) {
  $grid_class = "two-bubbles";
} else if ($count == 3) {
  $grid_class = "three-bubbles";
} else {
  $grid_class = "full-rows";
}

?>
<section class="icon-cta-bubble-section" style="background-color: <?php echo $section_background_color; ?>;">
  <div class="icon-cta-bubble-container">
    <h2 class="icon-cta-bubble-section-header" style="color: <?php echo $heading_color; ?>;"><?php echo $icon_cta_section_header; ?></h2>
    <div class="icon-cta-bubbles-grid <?php if ($select_col_count === "Yes") { echo $grid_class . " " . $column_count . "-cols"; } else { echo $grid_class; }?>">
      <?php if (have_rows('icon-cta-bubbles')) {
        while (have_rows('icon-cta-bubbles')) {
          the_row();
          $heading = get_sub_field('icon-cta-bubble-heading');
          $icon = get_sub_field('icon-cta-bubble-icon');
          $text = get_sub_field('icon-cta-bubble-text');
          $background_selection = get_sub_field('icon-cta-bubble-background-color');
          if ($background_selection === "Peppermint") {
            $background_color = '#a8efca';
          } else if ($background_selection === "White") {
            $background_color = '#FFF';
          }
          $button_cta = get_sub_field('icon-cta-bubble-button-text');
          $cta_title = $button_cta['title'];
          $target = $button_cta['target'] ? $button_cta['target'] : '_self';
          $link = $button_cta['url'];    
          ?>
          <div class="icon-cta-bubble" style="background-color: <?php echo $background_color; ?>;">
            <div class="icon-cta-bubble-icon-wrapper">
              <?php echo $icon; ?>
            </div>
            <div class="icon-cta-bubble-text-info">
              <h3 class="icon-cta-bubble-heading"><?php echo $heading; ?></h3>
              <p class="icon-cta-bubble-text"><?php echo $text; ?></p>
              <div class="icon-cta-bubble-action-section">
                <a href="<?php echo $link; ?>" target="<?php echo $target; ?>" class="icon-cta-bubble-text-link"><?php echo $cta_title; ?></a>
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-arrow-right-short icon-cta-bubble-action-icon" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
                </svg>
              </div>
            </div>
          </div>
          <?php
        }
      }
?>  
    </div>
  </div>  
</section>  