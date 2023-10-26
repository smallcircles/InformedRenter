<section class="integration-cards">
  <div class="integration-cards-container">
    <div class="integration-cards-body">
      <?php 
        if (have_rows('integrations-block')) {
          while(have_rows('integrations-block')) {
            the_row();
            ?>
            <div class="integration-section">
              <h2 class="section-heading"><?php echo get_sub_field('integration-heading') ?></h2>
              <div class="integrations">
                <?php
                  if (have_rows('integration')) {
                    while(have_rows('integration')) {
                      the_row();
                ?>
                  <div class="integration">
                    <div class="image-wrapper">
                      <img src="<?php echo get_sub_field('integration-image')['url']; ?>" alt="<?php echo get_sub_field('integration-image')['alt']; ?>">
                    </div>
                    <div class="integration-content">
                      <h3 class="integration-heading"><?php echo get_sub_field('integration-title'); ?></h3>
                      <p class="integration-copy"><?php echo get_sub_field('integration-description'); ?></p>
                    </div>
                  </div>
                <?php }
                  }
                ?>
              </div>
            </div>
            <?php
          }
        } ?>
    </div>
  </div>
</section>