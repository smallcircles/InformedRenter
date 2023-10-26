<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme     = wp_get_theme();
	$theme_version = $the_theme->get( 'Version' );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";
	
	$css_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_styles );

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $css_version );
	wp_enqueue_script( 'jquery' );
	
	$js_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_scripts );
	
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $js_version, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );



function acf_template_block() {
	
	// check function exists
	if( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'template-block',
			'title'				=> __('Template Block'),
			'description'		=> __('Template block'),
			'render_template'	=> 'blocks/block-template.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'template' ),
		));
	}
}

add_action('acf/init', 'acf_template_block');

function acf_cta_block() {
	
	// check function exists
	if( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'cta-block',
			'title'				=> __('CTA Block'),
			'description'		=> __('CTA block'),
			'render_template'	=> 'blocks/cta-block.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'hero' ),
		));
	}
}

add_action('acf/init', 'acf_cta_block');


function acf_hero_block() {
	
	// check function exists
	if( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'hero-block',
			'title'				=> __('Hero Block'),
			'description'		=> __('Hero block'),
			'render_template'	=> 'blocks/hero-block.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'hero' ),
		));
	}
}

add_action('acf/init', 'acf_hero_block');
function acf_props_block() {
	
	// check function exists
	if( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'props-block',
			'title'				=> __('Value Propositions Block'),
			'description'		=> __('Value propositions block'),
			'render_template'	=> 'blocks/props-block.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'hero' ),
		));
	}
}

add_action('acf/init', 'acf_props_block');


function acf_quote_block() {
	
	// check function exists
	if( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'quote-block',
			'title'				=> __('Quote Block'),
			'description'		=> __('Quote block'),
			'render_template'	=> 'blocks/quote-block.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'hero' ),
		));
	}
}

add_action('acf/init', 'acf_quote_block');


function acf_review_form_block() {
	
	// check function exists
	if( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'review-form-block',
			'title'				=> __('Review Form Block'),
			'description'		=> __('Review Form block'),
			'render_template'	=> 'blocks/review-form-block.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'review' ),
		));
	}
}

add_action('acf/init', 'acf_review_form_block');


function acf_review_map_block() {
	
	// check function exists
	if( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'review-map-block',
			'title'				=> __('Review Map Block'),
			'description'		=> __('Review Map block'),
			'render_template'	=> 'blocks/review-map-block.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'review' ),
		));
	}
}

add_action('acf/init', 'acf_review_map_block');


add_action('acf/save_post', 'my_save_post');

function my_save_post( $post_id ) {
    
    // bail early if not a contact_form post
    if( get_post_type($post_id) !== 'contact_form' ) { return; }
    
    // bail early if editing in admin
    if( is_admin() ) { return; }
    
    // vars
    $post = get_post( $post_id );
    
    // get custom fields (field group exists for content_form)
    $name = get_field('name', $post_id);
    $email = get_field('email', $post_id);
    
    // email data
    $to = 'contact@website.com';
    $headers = 'From: ' . $name . ' <' . $email . '>' . "\r\n";
    $subject = $post->post_title;
    $body = $post->post_content;
    
    // send email
    wp_mail($to, $subject, $body, $headers );
}


/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function tabor_setup() {
	  // Add support for editor styles.
	  add_theme_support( 'editor-styles' );
  
	// Enqueue editor styles.
	add_editor_style( 'css/child-theme.css' );
}
add_action( 'after_setup_theme', 'tabor_setup' );

function my_acf_google_map_api( $api ){
    $api['key'] = 'AIzaSyAqQgaRRPPEX57kTznmU5-p684uFMlOfqY';
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');