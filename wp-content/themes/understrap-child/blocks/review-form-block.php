<?php acf_form_head(); ?>
<section id="<?php echo $id; ?>" class="block">
	<div class="wrapper" style="">
		<div class="content">
		<?php 
		
			acf_form(array(
				'field_groups' => [178],
				'post_id'       => 'new_post',
				'new_post'      => array(
				'post_type'     => 'review',
				'post_status'   => 'publish'
			),
			'submit_value'  => 'Create new event'
		)); ?>
		<?php endwhile; ?>
		</div>
    </div>
</section>