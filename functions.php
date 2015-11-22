<?php
/**
 * cmb2 functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package cmb2
 */

if ( ! function_exists( 'cmb2_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function cmb2_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on cmb2, use a find and replace
	 * to change 'cmb2' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'cmb2', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'cmb2' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'cmb2_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // cmb2_setup
add_action( 'after_setup_theme', 'cmb2_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function cmb2_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'cmb2_content_width', 640 );
}
add_action( 'after_setup_theme', 'cmb2_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function cmb2_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'cmb2' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'cmb2_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function cmb2_scripts() {
	// wp_enqueue_style( 'cmb2-style', get_stylesheet_uri() );

	// wp_enqueue_script( 'cmb2-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	// wp_enqueue_script( 'cmb2-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	// if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
	// 	wp_enqueue_script( 'comment-reply' );
	// }
}
add_action( 'wp_enqueue_scripts', 'cmb2_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


// ##############################################################################################
// ############### custom post type ############################################################
// #############################################################################################

add_action('init', 'c56_cpt_book');
function c56_cpt_book(){
	$args = array(
		'public'		=> true,
		'label'			=> 'Books', 
		'taxonomies'	=> array('category', 'post_tag'),
		'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
		'menu_position'	=> 5
	);
	register_post_type('book', $args);

	$args = array(
		'public'		=> true,
		'label'			=> 'Music',
		'taxonomies'	=> array('category', 'post_tag'),
		'supports'		=> array('title', 'editor', 'thumbnail'),
		'menu_position'	=> 5	
	);
	register_post_type('music', $args);
}


function c56_manage_columns($columns){
	$columns = array(
		"cb" 		=> "CB",
		"id"		=> "Post ID",
		"length"	=> "No. of Characters",
		"title"		=> "Title",
		"author"	=> "writer",
		"thumb"		=> "Thumbnail"
	);
	return $columns;
}
function c56_id_data($column, $post){
	if ( $column == 'id' ) {
		echo $post;
	}
	if ( $column == "length" ) {
		$_post = get_post( $post );
		echo strlen( $_post->post_content );
	}
	if ( $column == 'thumb' ) {
		echo get_the_post_thumbnail( $post, "thumb" );
	}
}

add_action( 'admin_init', 'c56_columns_init');
function c56_columns_init(){
	add_filter('manage_book_posts_columns', 'c56_manage_columns');
	add_filter('manage_music_posts_columns', 'c56_manage_columns');

	add_action('manage_book_posts_custom_column', 'c56_id_data', 10, 2);
	add_action('manage_music_posts_custom_column', 'c56_id_data', 10, 2);
}

// CMB2 Setup
##########################################################
##########################################################

require_once( dirname(__FILE__) . "/libs/cmb2/init.php");

add_action("cmb2_init", "book_metabox");
function book_metabox(){
	$prefix = "_ab_";

	$cmb = new_cmb2_box(array(
		'id'			=> $prefix.'book_info',
		'title'			=> __( 'Book Info', 'ab' ),
		'object_types'	=> array('book', 'music', 'post'),
		'context'		=> 'normal',
		'priority'		=> 'default'
	));

	$cmb->add_field( array(
		'name'			=> __( 'Purchase Links', 'ab'),
		'id'			=> $prefix.'pl',
		'type'			=> 'text', 
		'repeatable'	=> true
	) );
	// pl means purchase links

	$cmb->add_field( array(
		'name'			=> __( 'ISBN', 'ab'),
		'id'			=> $prefix.'isbn',
		'type'			=> 'text'
	) );

	$cmb->add_field( array(
		'name'			=> __( 'Author', 'ab'),
		'id'			=> $prefix.'author',
		'type'			=> 'text'
	) );

	$cmb->add_field( array(
		'name'			=> __( 'Back Cover', 'ab'),
		'id'			=> $prefix.'bcover',
		'type'			=> 'file'
	) );
	$cmb->add_field( array(
		"name"    => __( "Color", "ab" ),
		"id"      => $prefix . "color",
		"type"    => "colorpicker",
		"default" => "#ff0000"
	) );

}

add_action( 'cmb2_init', 'cmb2_add_page_mb' );
function cmb2_add_page_mb() {

	$prefix = '_ab_';

	$cmb = new_cmb2_box( array(
		'id'           => $prefix . 'pagemb',
		'title'        => __( 'Sample Metabox', 'ab' ),
		'object_types' => array( 'page' ),
		'show_on'      => array( 'key' => 'page-template', 'value' => 'about.php' ),
		'context'      => 'normal',
		'priority'     => 'default',
	) );

	$cmb->add_field( array(
		'name'    => __( 'Some Info', 'ab' ),
		'id'      => $prefix . 'someinfo',
		'type'    => 'text',
		'default' => 'hello',
	) );

	$cmb->add_field( array(
		'name' => __( 'Another Info', 'ab' ),
		'id'   => $prefix . 'another_info',
		'type' => 'text',
	) );

	$cmb->add_field( array(
		'name' => __( 'Gallery', 'ab' ),
		'id'   => $prefix . 'gallery',
		'type' => 'file_list',
	) );

	$cmb->add_field( array(
		'name'    => __( 'A Dropdown', 'ab' ),
		'id'      => $prefix . 'dropdown',
		'type'    => 'pw_multiselect',
		// 'show_option_none'=>true,
		'options' => all_posts()
	) );

}
function all_posts() {
	$_allposts = get_posts( array(
		"posts_per_page" => - 1,
		"post_type"      => "post"
	) );

	$_posts = array();
	foreach ( $_allposts as $ap ) {
		$_posts[ $ap->ID ] = $ap->post_title;
	}

	return $_posts;
}

add_action('cmb2_init', 'team_member_mb');
function team_member_mb() {
	$cmb = new_cmb2_box( array(
		"id"           => "teammembers_metabox",
		'title'        => __( 'Sample Metabox', 'mytheme' ),
		'object_types' => array( 'page' ),
		'show_on'      => array( 'key' => 'page-template', 'value' => 'about.php' ),
		'context'      => 'normal',
		'priority'     => 'default'
	) );

	$cmb->add_field( array(
		"id"   => "team_name",
		"type" => "text",
		"name" => "Team Name"
	) );

	$gr = $cmb->add_field( array(
		"type"    => "group",
		"id"      => "teammembers",
		"options" => array(
			'group_title'   => __( 'Team Member {#}', 'mytheme' ),
			// since version 1.1.4, {#} gets replaced by row number
			'add_button'    => __( 'Add Another member', 'mytheme' ),
			'remove_button' => __( 'Remove Member', 'mytheme' ),
			'sortable'      => true,
		)
	) );

	$cmb->add_group_field( $gr, array(
		"id"   => "tm_photo",
		"type" => "file",
		"name" => "Member Photo"
	) );

	$cmb->add_group_field( $gr, array(
		"id"   => "tm_name",
		"type" => "text",
		"name" => "Member Name"
	) );

	$cmb->add_group_field( $gr, array(
		"id"   => "tm_email",
		"type" => "text",
		"name" => "Member Email"
	) );
}

require_once( dirname(__FILE__)).'/love_porinite.php';
// require_once( dirname(__FILE__) . "/libs/cmb2/init.php");

// class 13 code
################################################
################################################
// function class12_scripts(){
// 	wp_enqueue_script("jquery");
// 	wp_enqueue_style( 'class12-style', get_stylesheet_uri() );

// 	wp_enqueue_script( 'class12-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

// 	wp_enqueue_script( 'class12-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

// 	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
// 		wp_enqueue_script( 'comment-reply' );
// 	}
// 	wp_enqueue_style("bootstrap","//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css");

// 	wp_enqueue_script("imagesloaded","//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/3.2.0/imagesloaded.pkgd.min.js",null,"1.0",true);
// 	wp_enqueue_script("isotope","https://cdn.jsdelivr.net/isotope/2.2.2/isotope.pkgd.min.js",array("jquery","imagesloaded"),"1.0",true);

// 	$jspath = get_template_directory_uri()."/js/scripts.js";
// 	wp_enqueue_script("class12script", $jspath,array("jquery"),"1.0",true);

// 	$data = array(
// 		"ajax_url"=>admin_url("admin-ajax.php")
// 	);

// 	wp_localize_script("class12script","class12",$data);
// }

// add_action('wp_enqueue_scripts', 'class12_scripts');
function love_u_baby(){
	wp_enqueue_script("jquery");
	wp_enqueue_style('bootstrap', "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css");

	wp_enqueue_script('imageloaded', "https://cdn.jsdelivr.net/imagesloaded/3.2.0/imagesloaded.pkgd.min.js", null, "1.0", true);
	wp_enqueue_script('isotope', "https://cdn.jsdelivr.net/isotope/2.2.2/isotope.pkgd.min.js", array("jquery", "imageloaded"), "1.0", true);

	$js_path = get_template_directory_uri()."/js/boob.js";
	wp_enqueue_script("my_sexy_script", $js_path, array("jquery"), "1.0", true);

	wp_localize_script("my_sexy_script", "baby_boobs", $data);
}
add_action('wp_enqueue_scripts', "love_u_baby");

// require_once get_template_directory()."/class13.php";
require_once get_template_directory()."/parts_of_parinite.php";

