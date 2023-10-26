<?php
$accordion_heading = get_field('faq-accordion-heading');
$accordion_bg_color = get_field('faq-accordion-bg-color');

?>

<section class="faq-accordion-block <?php echo $accordion_bg_color; ?>">
  <div class="faq-accordion-container">
    <div class="accordion-heading-section">
      <h2 class="faq-accordion-heading"><?php echo $accordion_heading; ?></h2>
    </div>
    <div class="faq-accordion-section">
      <div class="faq-accordion">
        <?php
            if(have_rows('faq-accordion-menu')) {
              while(have_rows('faq-accordion-menu')) {
                the_row();
                $heading = get_sub_field('faq-accordion-menu-item-header');
                $text = get_sub_field('faq-accordion-menu-item-text');?>
                <h3 class="accordion-menu-header">
                  <?php echo $heading; ?>
                  <div class="icon-wrapper" aria-hidden="true">
                    <svg class="plus" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M28 12.5H15.5V0H12.5V12.5H0V15.5H12.5V28H15.5V15.5H28V12.5Z" fill="#13382E"/>
                    </svg>
                    <svg class="minus" width="28" height="4" viewBox="0 0 28 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M28 0.5H0V3.5H28V0.5Z" fill="#13382E"/>
                    </svg>
                  </div>
                </h3>
                <div class="faq-accordion-text-section">
                  <p class="accordion-menu-text"><?php echo $text; ?></p>
                </div>
                <p class="faq-border" aria-hidden="true"></p>
        <?php } 
            }
        ?>
      </div>
    </div>
  </div>
</section>