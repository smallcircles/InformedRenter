<section class="find-a-partner">
  <div class="find-a-partner-container">
    <div class="find-a-partner-body">
      <div class="header-radio-buttons">
        <h2 class="header-radio-buttons-heading">Find a Partner</h2>
        <div class="header-radio-buttons-filters">
          <button class="header-radio-button-filter partner-radio-button active-filter">All</button>
          <?php
          //Get all the Parter Categories
          $partner_terms = get_terms([
            'taxonomy' => 'partner_categories',
            'hide_empty' => true,
          ]);

          foreach ($partner_terms as $partner_term) {
            echo '<button class="header-radio-button-filter partner-radio-button">' . $partner_term->name . '</button>';
          }
          ?>
        </div>
      </div>
      <div class="partners-posts">
<?php
        $args = array(
          'post_type' => 'partner',
          'post_status' => 'publish',
          'nopaging' => true
        );

        $posts = get_posts($args);

        foreach ($posts as $post) {
          setup_postdata($post);
          $heading = get_field('partner-heading', $post->ID);
          $image = get_field('partner-image', $post->ID);
          $level = get_field('partner-level', $post->ID);
          $contact = get_field('partner-contact', $post->ID);
          $subheading = get_field('partner-subheading', $post->ID);
          $partner_website = get_field('partner-link', $post->ID);
          $no_level = $level == "None" ? true : false;


?>
        <div class="partner-post">
          <div class="partner-post-image">
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
          </div>
          <div class="partner-post-content">
            <div class="partner-post-content-heading-row">
              <h4 class="partner-post-heading"><?php echo $heading; ?></h4>
<?php 
              if (!$no_level) {
                echo '<span class="partner-post-level ' . $level . '">' . $level . ' level partner</span>';
              }
?>
            </div>
            <a class="partner-post-contact" href="tel:<?php echo $contact ?>">
              <?php echo $contact; ?>
            </a>
            <p class="partner-post-subheading"><?php echo $subheading; ?></p>
            <div class="partner-post-tags">
<?php

            $partner_categories = get_the_terms($post->ID, 'partner_categories');
            if ($partner_categories){
              foreach ($partner_categories as $category) {
?>
                <div class="partner-post-tag">
                  <div class="partner-post-tag-icon">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M14.9795 1.5L5.97953 10.5L1.01953 5.54" stroke="#13382E" stroke-width="1.5" stroke-miterlimit="10"/>
                    </svg>            
                  </div>
                  <span class="partner-post-tag-name"><?php echo $category->name; ?></span>
                </div>
<?php
              }
            }
?>
            </div>
            <div class="partner-post-link-content">
              <a class="partner-post-link" target="_blank" href="<?php echo $partner_website; ?>">Visit website</a>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.8281 13.6699H2.32812V3.16992H5.49813V1.66992H0.828125V15.1699H14.3281V10.4999H12.8281V13.6699Z" fill="currentColor"/>
                <path d="M6.99875 0.25V1.75H13.1887L6.46875 8.47L7.52875 9.53L14.2488 2.81V9H15.7488V0.25H6.99875Z" fill="currentColor"/>
              </svg>
            </div>
          </div>
        </div>
<?php
      }
      wp_reset_postdata();
?>
      </div>
    </div>
  </div>
</section>