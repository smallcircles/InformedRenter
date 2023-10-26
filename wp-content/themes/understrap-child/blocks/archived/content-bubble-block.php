<section class="content-bubble-block">
  <div class="content-bubble-container">
    <div class="content-bubble">
      <?php
        if (have_rows('content')) {
          while(have_rows('content')) {
            the_row();

            $content_type = get_sub_field('type');

            echo '<div class="policy">';

            switch($content_type) {
              case 'Heading':
                $heading_text = get_sub_field( 'heading' );
                echo '<h2 class="policy-heading">' . $heading_text . '</h2>';
                break;
              case 'Sub Heading':
                $sub_heading_text = get_sub_field( 'sub_heading' );;
                echo '<h3 class="policy-subheading">' . $sub_heading_text .'</h3>';
                break;
              case 'Copy':
                $copy_text = get_sub_field( 'copy' );
                echo '<p class="policy-copy">' . $copy_text . '</p>';
                break;
              case 'CTA Button':
                $cta = get_sub_field( 'cta-button' );
                $target = $cta['target'] ? $cta['target'] : '_self'; 
                echo '<a target="' . $target . '" href="' . $cta['url'] . '" class="policy-cta">' . $cta['title'] . '</a>';
                break;
            }

            echo '</div>';
          }
        }
      ?>
    </div>
  </div>
</section>