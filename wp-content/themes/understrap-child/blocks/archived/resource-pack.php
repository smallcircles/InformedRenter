<?php
  $post_type_field = get_field('resource-pack-post-type');
  $cta = get_field('resource-pack-cta');
  $cta_target = $cta['target'] ? $cta['target'] : '_self';
  $should_have_no_top_padding = get_field('resource-pack-no-top-padding');
  $should_have_no_bottom_padding = get_field('resource-pack-no-bottom-padding');
  $container_spacing_classes = "";
  $has_cta = get_field('include-cta');

  if ($should_have_no_top_padding) {
    $container_spacing_classes .= "no-top-padding";
  }

  if ($should_have_no_bottom_padding) {
    $container_spacing_classes .= "no-bottom-padding";
  }

  $post_type = $post_type_field[0];

  $args = array( 
    'post_type' => $post_type,
    'posts_per_page' => '3'
  );

  $posts_query = new WP_Query( $args );

  $posts = "";

  if ( $posts_query->have_posts() ) {
    while ( $posts_query->have_posts() ) {
      $posts_query->the_post();
      $title = get_the_title();
      $permalink = get_permalink();
      $link_text = "Read " . strtolower($post_type);
      $post_thumbnail_url = get_the_post_thumbnail_url();
      $thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
      $thumbnail_alt = get_post_meta ( $thumbnail_id, '_wp_attachment_image_alt', true );
      
      # Slick slider does some weird inline styling on the first child; DO NOT DELETE wrapping <div>
      $posts .=  '<div>';
      $posts .=    '<article class="post-card">';
      $posts .=      '<figure>';
      $posts .=        '<img class="featured-image" src="' . $post_thumbnail_url . '" alt="' . $thumbnail_alt . '" />';
      $posts .=      '</figure>';
      $posts .=      '<header>';
      $posts .=        '<span class="post-type" aria-label="Post Card Post Type">';
      $posts .=          $post_type;
      $posts .=        '</span>';
      $posts .=        '<h2 class="heading">' . $title . '</h2>';
      $posts .=        '<a class="post-link" href="' . $permalink . '">';
      $posts .=          $link_text;
      $posts .=          '<svg class="arrow-right" width="18" height="14" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.6 0.379883L7.29 1.69988L10.67 5.06988H0V6.92988H10.67L7.29 10.2999L8.6 11.6199L14.22 5.99988L8.6 0.379883Z" fill="currentColor"/></svg>';
      $posts .=        '</a>';
      $posts .=      '</header>';
      $posts .=    '</article>';
      $posts .=  '</div>';
    }
  } else {
    $posts =`<p>There are no posts of post type: {$post_type}</p>`;
  }
  wp_reset_postdata();

  $heading = get_field('resource-pack-heading');
?>

<section class="resource-pack">
  <div class="content-container <?php echo $container_spacing_classes; ?>">
    <?php if ($heading) { ?>
      <div class="resource-pack-header">
        <h3 class="heading"><?php echo $heading; ?></h3>
      </div>

    <?php } ?>
    <div class="post-card-wrapper"><?php echo $posts; ?></div>
    <div class="post-card-carousel"><?php echo $posts; ?></div>
    <div class="actions-wrapper">
<?php if ($has_cta === "Yes") { ?>
      <a class="cta-button dark" href="<?php echo $cta['url']; ?>" target="<?php echo $cta_target; ?>">
        <?php echo $cta['title']; ?>
      </a>
<?php
}
?>

      <div class="carousel-actions">
        <button
          type="button"
          class="carousel-prev">
          <svg
            width="1rem"
            height="0.75rem"
            viewBox="0 0 15 12"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M6.4 11.6201L7.71 10.3001L4.33 6.93012L15 6.93012L15 5.07012L4.33 5.07012L7.71 1.70012L6.4 0.380117L0.780001 6.00012L6.4 11.6201Z"
              fill="#13382E"
            />
          </svg>
        </button>
        <button
          type="button"
          class="carousel-next">
          <svg
            width="1rem"
            height="0.75rem"
            viewBox="0 0 15 12"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M8.6 0.379883L7.29 1.69988L10.67 5.06988H0V6.92988H10.67L7.29 10.2999L8.6 11.6199L14.22 5.99988L8.6 0.379883Z"
              fill="#13382E"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</section>