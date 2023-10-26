<?php
/**
 * Template Name: Review Form Page Template
 *
 * Template for displaying a review form page.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

acf_form_head();

get_header();

?>
<div class="wrapper" id="fullwidthpage">

	<div  id="content" class="container review-form">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">

				<main class="site-main" id="main" role="main">
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d20839.787058922295!2d-123.0812461514533!3d49.238993858946095!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x548673f143a94fb3%3A0xbb9196ea9b81f38b!2sVancouver%2C%20BC!5e0!3m2!1sen!2sca!4v1698253472213!5m2!1sen!2sca"  style="border:0; WIDTH:100%; height:450px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					<?php
					
						acf_form(array(
							'id'       => 'review_form',
							'post_id'       => 'new_post',
							'post_title'    => false,
							'post_content'  => false,
							'new_post'      => array(
								'post_type'     => 'review',
								'post_status'   => 'publish'
							),
							'return'        => home_url('reviews'),
							'submit_value'  => 'Send'
						));
					
					?>
				</main>
				
    
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>