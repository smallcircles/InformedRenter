<?php  // BLOCK NAME


$id = get_field('cta_id') ?: false;
$title = get_field('cta_title') ?: false;
$text = get_field('cta_text') ?: false;
$link = get_field('cta_link') ?: false;
$link_text = get_field('cta_link_text') ?: false;
$link_type = get_field('cta_link_type') ?: false;
$theme = get_field('cta_theme') ?: false;
$image = get_field('cta_image') ?: false;
$alignment = get_field('cta_alignment') ?: false;



$section_classes = "cta  ";
$wrapper_style = "";
if($theme){ $section_classes .= $theme." "; }

$block_header = false;
if($title || $text){ $block_header = true; }
?>
<section id="<?php echo $id; ?>" class="block <?php echo $section_classes; ?>">
	<div class="wrapper" style="<?php echo $wrapper_style; ?>">
		<div class="content">
			<div class="image"><img src="<?php echo $image['url']; ?>" /></div>
			<div class="cta-link">
				<h2><?php echo $title; ?></h2>
				<p><a href="<?php echo $link; ?>" class="<?php echo $link_type; ?>"><?php echo $link_text; ?></a></p>
			</div>
		</div>

<!-- 
		<div id="testvars">
	<h2>CTA BLOCK</h2>
		$id = <?php echo $id; ?><br />
		$alignment = <?php echo $alignment; ?><br />
		$title = <?php echo $title; ?><br />
		$text = <?php echo $text; ?><br />
		$link = <?php echo $link; ?><br />
		$link_text = <?php echo $link_text; ?><br />
		$link_type = <?php echo $link_type; ?><br />
		$theme = <?php echo $theme; ?><br />
		$image['url'] = <?php echo $image['url']; ?><br />
		$image['title'] = <?php echo $image['title']; ?><br />
		$image['caption'] = <?php echo $image['caption']; ?><br />
		$image['description'] = <?php echo $image['description']; ?><br />
		$image['alt'] = <?php echo $image['alt']; ?><br />
		
	</div>
	-->
		<div class="block_content">
		
		</div>
	</div>
</section>