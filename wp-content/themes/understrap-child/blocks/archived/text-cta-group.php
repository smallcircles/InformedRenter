<?php
$bg_color = get_field('text_cta_group_bg_color');
$block_heading = get_field('text_cta_group_heading');


if( have_rows('text_cta_group') ) {

  echo '<div class="text-cta-group ' . $bg_color . '">';
  echo '  <div class="content-wrapper">';
  if($block_heading){
	echo '    <h2>' . $block_heading . '</h2>';
  }
  echo '    <div class="text-ctas">';

  while( have_rows('text_cta_group') ) : the_row();
      $icon = get_sub_field('text_cta_group_icon');
      $heading = get_sub_field('text_cta_group_heading');
      $copy = get_sub_field('text_cta_group_copy');
      $cta = get_sub_field('text_cta_group_cta');
      $cta_link = $cta['url'] ?? null;
      $cta_target = $cta['target'] ? $cta['target'] : '_self';
      $cta_title = $cta['title'] ? $cta['title'] : null;
      $arrow_right  = '<svg class="arrow-right" width="18" height="14" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.6 0.379883L7.29 1.69988L10.67 5.06988H0V6.92988H10.67L7.29 10.2999L8.6 11.6199L14.22 5.99988L8.6 0.379883Z" fill="currentColor" />';

		
      $text_cta = '<div class="text-cta">';
      if($icon) { $text_cta .= $icon; }
      if($heading) { $text_cta .= '<h3>' . $heading . '</h3>'; }
      if($copy) { $text_cta .= '<p>' . $copy . '</p>'; }
      if($cta_link) { $text_cta .= '<a class="post-link" href="' . $cta_link . '" target="' . $cta_target . '">' . $cta_title . $arrow_right . '</a>';}
      $text_cta .= '</div>';

      echo $text_cta;
  endwhile;

  echo '    </div>';
  echo '  </div>';
  echo '</div>';
}