<?php


$id = get_field('hero_id');
$title = get_field('hero_title');
$text = get_field('hero_text');
$link_text = get_field('hero_link_text');
$link = get_field('hero_link');
$link_type = get_field('hero_link_type');
$image = get_field('hero_image');
$size = get_field('hero_size');
$theme = get_field('hero_theme');
$alignment = get_field('hero_alignment');

$section_classes = "hero ";
$wrapper_style = "";

if($theme){ 	$section_classes .= $theme." "; }
if($size){  	$section_classes .= $size." "; }
if($alignment){	$section_classes .= $alignment; }

$styles = "";

?>

<section id="<?php echo $id; ?>" class="<?php echo $section_classes; ?>">
	<div class="wrapper" style="<?php echo $wrapper_style; ?>">
		<div class="content">
			<div class="image" style="background-image:url(<?php echo $image['url']; ?>);"></div>
			<div class="text">
			<?php 
				if($title){ ?><h1><?php echo $title; ?></h1><?php }
				if($title){ ?><p><?php echo $text; ?><?php }
				if($link){ ?><a href="<?php echo $link; ?>" class="<?php echo $link_type; ?>"><?php echo $link_text; ?></a></p><?php } ?>
			</div>
		</div>
	</div>
	<!--
	<div id="testvars">
	<h2>HERO BLOCK</h2>
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
</div>