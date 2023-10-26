<?php
// get main field groups

$block = get_field('block') ?: false; 	// block attributes group	
$text = get_field('text'); 				// text fields group	
$images = get_field('images') ?: false; // images fields group	
	$image = $images['image'];
	$background = $images['background'];
	$overlay = $images['overlay'];
$cta = get_field('cta') ?: false;		// cta fields group
$cta2 = get_field('cta2') ?: false;		// cta2 fields group


// write main classes & styles

$block_classes = "cta-block ";
	$block_classes .= $block['type']." ";
	$block_classes .= $block['theme']." ";
	$block_classes .= $block['alignment']." ";
	$block_classes .= $block['mobile_text_placement']." ";
	if($background['dot_overlay']){ $block_classes .= "dot_overlay "; }
	if($image['image_overflows']){  $block_classes .= "image_overflows "; }


if($text['title_icon']){	
	$icon_class = "icon circle-icon-sm "; 
} else { 
	$icon_class = ""; 
}

$main_image_style;
if($image['main_image']){
	$main_image_style  = "background-image: url(".$image['main_image'].");";
	$main_image_style .= "background-size:".$image['main_image_size'].";";
}	
if($image['main_image_position']){
	$main_image_classes = "bg-".$image['main_image_position']." ";
}


$background_image_style;
if($background['background_image']){
	$background_image_style  = "background-image: url(".$background['background_image'].");";
	$background_image_style .= "background-size:".$background['background_image_size'].";";
}	
if($background['background_image_position']){
	$block_classes .= "bg-".$background['background_image_position']." ";
}


if($background['dot_overlay'] && $background['background_image']){
	$block_classes .= "bring_bg_to_front ";
	$overlay_image_style = $background_image_style."background-repeat:no-repeat;";
	$background_image_style = "";	
}


$overlay_image_style;
if($overlay['overlay_image']){
	$overlay_image_style = "background-image: url(".$overlay['overlay_image'].");";
	$overlay_image_style .= "background-size:".$overlay['overlay_image_size'].";";
}	
$overlay_classes = "bg-".$overlay['overlay_image_position'];

$cta_classes;
if($cta['cta_text']){
	$cta_classes = $cta['cta_type']." ";
	$cta_classes .= $cta['cta_theme']." ";
	if($cta['cta_rounded']){ $cta_classes .= "round "; }
}

$cta2_classes;
if($cta2['cta_text']){
	$cta2_classes = $cta2['cta_type']." ";
	$cta2_classes .= $cta2['cta_theme']." ";
	if($cta2['cta_rounded']){ $cta2_classes .= "round "; }
}	
if($cta['is_external_link']){ $cta['cta_url'] = $cta['cta_external_url']; }
if($cta2['is_external_link']){ $cta2['cta_url'] = $cta2['cta_external_url']; }
?>
<section id="<?php echo $block['id']; ?>" class="<?php echo $block_classes; ?>" style="<?php echo $background_image_style; ?>">
	<div class="bg_overlay" style="<?php echo $overlay_image_style; ?>">
		<div class="cta-content grid">
			<div class="cta-text">
				<div class="title-block">
					<?php if($text['sub_title']){?>
						<p class="subtitle <?php echo $icon_class; ?>"><?php echo $text['sub_title']; ?></p>
					<?php }?>
					<<?php echo $text['title_tag']; ?>><?php echo $text['title']; ?></<?php echo $text['title_tag']; ?>>
					<?php if($text['text_content']){?>
						<p><?php echo $text['text_content']; ?></p>
						
					<?php } ?>
					<div class="cta">
					<?php  
						if($cta['cta_text']){  ?>
							<p class="cta-text-container"><a href="<?php echo $cta['cta_url']; ?>" class="<?php echo $cta_classes; ?>"><?php echo $cta['cta_text']; ?></a></p>
						<?php } 
						if( !empty( $cta2['cta_text']) ){ 
						 
						 ?>
							<p class="cta-link-container"><a href="<?php echo $cta2['cta_url']; ?>" class="<?php echo $cta2_classes; ?>"><?php echo $cta2['cta_text']; ?></a></p>
						<?php } ?>
					</div>
				</div> 
			</div>
			<div class="cta-image <?php echo $main_image_classes; ?>" style="<?php echo $main_image_style; ?>"><?php 
			if($images['has_overlay']){?><div class="cta-overlay <?php echo $overlay_classes; ?>" style="<?php echo $overlay_image_style; ?>"></div><?php } ?></div>
		</div>
	</div>
</section>  	