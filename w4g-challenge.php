<?php
/*
Plugin Name: Challenge
Plugin URI: http://wearablesforgood.com
Description: Run and manage a challenge directly from your website.
Version: 2.0
Author: Eleven Alphabet
Author URI: http://elevenalphabet.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define( 'W4G_CHALLENGE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


//register_activation_hook( __FILE__, 'w4g_challenge_app_create_archive' );
//register_activation_hook( __FILE__, 'w4g_challenge_app_create_application' );
//register_activation_hook( __FILE__, 'w4g_challenge_app_create_dashboard' );

function w4g_install()
{
    w4g_challenge_app_create_archive();
    w4g_challenge_app_create_application();
    w4g_challenge_app_create_dashboard();

    // trigger our function that registers the custom post type
    w4g_setup_post_type();
 
    // clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'w4g_install' );


if ( is_admin() ) {
	require_once( W4G_CHALLENGE__PLUGIN_DIR . 'class.w4g-challenge-admin.php' );
}


/**
* Integrate ACF
*/
// Customize ACF path
add_filter('acf/settings/path', 'w4g_acf_settings_path');
function w4g_acf_settings_path( $path ) {
 
    // update path
    $path = W4G_CHALLENGE__PLUGIN_DIR . '/includes/acf/';
    
    // return
    return $path;
    
}

// Customize ACF dir
add_filter('acf/settings/dir', 'w4g_acf_settings_dir');
function w4g_acf_settings_dir( $dir ) {
 
    // update path
    $dir = W4G_CHALLENGE__PLUGIN_DIR . '/includes/acf/';
    
    // return
    return $dir;
    
}
 
// Include ACF
include_once( W4G_CHALLENGE__PLUGIN_DIR . '/includes/acf/acf.php' );


/**
* Load stylesheet
*/
function load_w4g_challenge_resources() {
    
    wp_register_style( 'w4g-challenge.css', plugin_dir_url( __FILE__ ) . '_inc/w4g-challenge.css', array(), '20160908', 'all' );
    wp_enqueue_style( 'w4g-challenge.css');

}
add_action( 'wp_enqueue_scripts', 'load_w4g_challenge_resources' );


/**
* Create custom post type for Challenge Applications
*/
function w4g_setup_post_type() {

	$labels = array(
	    'name' => _x( 'Applications', 'challenge_app' ),
	    'singular_name' => _x( 'Application', 'challenge_app' ),
	    'add_new' => _x( 'Add New', 'challenge_app' ),
	    'add_new_item' => _x( 'Add New Application', 'challenge_app' ),
	    'edit_item' => _x( 'Edit Application', 'challenge_app' ),
	    'new_item' => _x( 'New Application', 'challenge_app' ),
	    'view_item' => _x( 'View Application', 'challenge_app' ),
	    'search_items' => _x( 'Search Applications', 'challenge_app' ),
	    'not_found' => _x( 'No applications found', 'challenge_app' ),
	    'not_found_in_trash' => _x( 'No applications found in Trash', 'challenge_app' ),
	    'parent_item_colon' => _x( 'Parent Application:', 'challenge_app' ),
	    'menu_name' => _x( 'Applications', 'challenge_app' ),
	);

	$args = array(
	    'labels' => $labels,
	    'hierarchical' => true,
	    'description' => 'Challenge applications',
	    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'revisions' ),
	    'taxonomies' => array( 'genres' ),
	    'public' => true,
	    'show_ui' => true,
	    'show_in_menu' => true,
	    'menu_position' => 5,
	    'menu_icon' => 'dashicons-admin-post',
	    'show_in_nav_menus' => true,
	    //'publicly_queryable' => false,
	    'exclude_from_search' => true,
	    'has_archive' => true,
	    'query_var' => true,
	    'can_export' => true,
	    'rewrite' => array('slug' => 'applications','with_front' => false),
	    'capability_type' => 'post',
	);

	register_post_type( 'challenge_app', $args );
}
add_action( 'init', 'w4g_setup_post_type' );


/**
 * Create Tag taxonomy for challenge_app custom post type
 */
function w4g_setup_post_type_taxonomies() 
{
  // Add new taxonomy, NOT hierarchical (like tags)
  $labels = array(
    'name' => _x( 'Tags', 'taxonomy general name' ),
    'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Tags' ),
    'popular_items' => __( 'Popular Tags' ),
    'all_items' => __( 'All Tags' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Tag' ), 
    'update_item' => __( 'Update Tag' ),
    'add_new_item' => __( 'Add New Tag' ),
    'new_item_name' => __( 'New Tag Name' ),
    'separate_items_with_commas' => __( 'Separate tags with commas' ),
    'add_or_remove_items' => __( 'Add or remove tags' ),
    'choose_from_most_used' => __( 'Choose from the most used tags' ),
    'menu_name' => __( 'Tags' ),
  ); 

  register_taxonomy('tag','challenge_app',array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'tag' ),
  ));
}
add_action( 'init', 'w4g_setup_post_type_taxonomies', 0 );


/**
 * Force new Challenge Applications to be Private
 */
function w4g_prepare_new_post( $current_screen ) {
    
    if( ( 'challenge_app' === $current_screen->post_type ) && ( 'add' === $current_screen->action ) ) {
        
        add_filter( 'wp_insert_post_data',  function( $data ) {
            
            $data['post_status'] = 'private';
            // Do other 'new' Application post stuff here...
             
            return $data;

        }, 99, 2 );
    }
}
add_action( 'current_screen', 'w4g_prepare_new_post' );


/**
* Remove Edit link from Challenge Application single template
*/
function w4g_remove_get_edit_post_link( $link ) {
    global $post;

    if ($post->post_type == 'challenge_app') {
        $link = null;
    }

    return $link;
}
add_filter('get_edit_post_link', 'w4g_remove_get_edit_post_link');


/**
* Remove Private from Post Titles
*/
function w4g_title_trim($title) {
    global $post;

    if ($post->post_type == 'challenge_app') {

        $title = esc_attr($title);

        $findthese = array(
            '#Protected:#',
            '#Private:#'
        );

        $replacewith = array(
            '', // What to replace "Protected:" with
            '' // What to replace "Private:" with
        );

        $title = preg_replace($findthese, $replacewith, $title);
    }
    return $title;
    
}
add_filter('the_title', 'w4g_title_trim');


/**
* Delete Post from front-end
*/
function w4g_delete_post(){

    $permission = check_ajax_referer( 'my_delete_post_nonce', 'nonce', false );
    if( $permission == false ) {
        //echo 'error';
        wp_redirect($redirect . '?delete=error');
    }
    else {
        wp_delete_post( $_REQUEST['id'] );
        //echo 'success';
        wp_redirect( home_url('/dashboard/?delete=success') ); 
        exit;
    }

    die();

}
add_action( 'wp_ajax_my_delete_post', 'w4g_delete_post' );


/**
* Create template for single applications
*/
function w4g_replace_single_template($content) {
    global $post;

    if ($post->post_type == 'challenge_app') {
        $content = '[w4g_single_application]';
    }
    return $content;
}
add_filter( 'the_content', 'w4g_replace_single_template' );


/**
* Insert Applications Archive page
*/
function w4g_challenge_app_create_archive() {
	if ( get_page_by_title( 'Applications Archive' ) == NULL ) {
		global $user_ID;
		$new_post = array(
		    'post_title' => 'Applications Archive',
		    'post_content' => '[w4g_archive]',
		    'post_status' => 'private',
		    'post_author' => $user_ID,
		    'post_type' => 'page',
		);
		$post_id = wp_insert_post($new_post);
	}
}


/**
* Insert Application page
*/
function w4g_challenge_app_create_application() {
	if ( get_page_by_title( 'Application' ) == NULL ) {
		global $user_ID;
		$new_post = array(
		    'post_title' => 'Application',
		    'post_content' => '[w4g_application_form]',
		    'post_status' => 'publish',
		    'post_author' => $user_ID,
		    'post_type' => 'page',
		);
		$post_id = wp_insert_post($new_post);
	}
}


/**
* Insert Dashboard page
*/
function w4g_challenge_app_create_dashboard() {
    if ( get_page_by_title( 'Dashboard' ) == NULL ) {
        global $user_ID;
        $new_post = array(
            'post_title' => 'Dashboard',
            'post_content' => '[w4g_dashboard]',
            'post_status' => 'publish',
            'post_author' => $user_ID,
            'post_type' => 'page',
        );
        $post_id = wp_insert_post($new_post);
    }
}


/**
 * Locate template.
 *
 * Locate the called template.
 *
 * @param   string  $template_name          Template to load.
 * @param   string  $string $template_path  Path to templates.
 * @param   string  $default_path           Default path to template files.
 * @return  string                          Path to the template file.
 */
function w4g_locate_template( $template_name, $template_path = '', $default_path = '' ) {

    // Set variable to search in woocommerce-plugin-templates folder of theme.
    if ( ! $template_path ) :
        $template_path = 'woocommerce-plugin-templates/';
    endif;

    // Set default plugin templates path.
    if ( ! $default_path ) :
        $default_path = plugin_dir_path( __FILE__ ) . 'templates/'; // Path to the template folder
    endif;

    // Get plugins template file.
    $template = $default_path . $template_name;

    return apply_filters( 'w4g_locate_template', $template, $template_name, $template_path, $default_path );

}


/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @see w4g_locate_template()
 *
 * @param string    $template_name          Template to load.
 * @param array     $args                   Args passed for the template file.
 * @param string    $string $template_path  Path to templates.
 * @param string    $default_path           Default path to template files.
 */
function w4g_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {

    if ( is_array( $args ) && isset( $args ) ) :
        extract( $args );
    endif;

    $template_file = w4g_locate_template( $template_name, $tempate_path, $default_path );

    if ( !file_exists( $template_file ) ) :
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
        return;
    endif;

    include $template_file;
}


/**
 * Create template shortcodes.
 *
 * The template shortcodes will output the template
 * files from the templates/folder.
 */
function w4g_application_shortcode() {

    ob_start();
    w4g_get_template( 'content-application.php' );
    $output = ob_get_clean();

    return $output;
}
add_shortcode( 'w4g_application_form', 'w4g_application_shortcode' );

function w4g_dashboard_shortcode() {
    ob_start();
    w4g_get_template( 'content-dashboard.php' );
    $output = ob_get_clean();
    return $output;
}
add_shortcode( 'w4g_dashboard', 'w4g_dashboard_shortcode' );

function w4g_archive_shortcode() {
    ob_start();
    w4g_get_template( 'content-archive.php' );
    $output = ob_get_clean();
    return $output;
}
add_shortcode( 'w4g_archive', 'w4g_archive_shortcode' );

function w4g_single_app_shortcode() {
    ob_start();
    w4g_get_template( 'single-application.php' );
    $output = ob_get_clean();
    return $output;
}
add_shortcode( 'w4g_single_application', 'w4g_single_app_shortcode' );


/**
 * Add required acf_form_head() function to head of page
 * @uses Advanced Custom Fields Pro
 */
function w4g_hook_acf_form_head() {
    if ( is_page('Application') || is_singular( 'challenge_app' ) ) {
        acf_form_head();
    }
}
add_action('get_header','w4g_hook_acf_form_head');


/**
 * Redirect non-admin users from wp-admin to Dashboard 
 */
function w4g_restrict_admin_with_redirect() {
    if ( ! current_user_can( 'manage_options' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
        wp_redirect( home_url('/dashboard/') ); 
        exit;
    }
}
add_action( 'admin_init', 'w4g_restrict_admin_with_redirect', 1 );


/**
*  Hide Admin bar from Non-Admin
*/
function w4g_remove_admin_bar() {
    if (!current_user_can('manage_options') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'w4g_remove_admin_bar');


/**
 * Use Project Name as Post Title when new app is submitted
 */
function w4g_my_pre_save_post( $post_id ) {

    // check if this is to be a new post
    if( $post_id != 'new' ) {
        return $post_id;
    }

    // Application ACF Name Key
    $acfNameKey = 'field_58d15fcc4a4f0';

    // Create a new post
    $post = array(
        'post_status'  => 'private',
        'post_title'   =>  wp_strip_all_tags($_POST['fields'][$acfNameKey]), // Post Title ACF field key
        'post_type'    => 'challenge_app',
    );  

    // insert the post
    $post_id = wp_insert_post( $post ); 

    // return the new ID
    return $post_id;

}
add_filter('acf/pre_save_post' , 'w4g_my_pre_save_post');


/**
* Custom redirect after creating a NEW post
*/
function w4g_custom_redirect_function_name($post_id) {
    if ( !is_singular( 'challenge_app' ) ) {
        $redirect = get_the_permalink($post_id);
        wp_redirect($redirect . '?status=created');
        exit;
    }
}
add_action('acf/save_post', 'w4g_custom_redirect_function_name', 20);


/**
* Change Upload Directory for Application CPT
*/
add_filter('wp_handle_upload_prefilter', 'w4g_handle_upload_prefilter');
add_filter('wp_handle_upload', 'w4g_handle_upload');

function w4g_handle_upload_prefilter( $file )
{
    // detects if file is being uploaded from the frontend application
    if ( wp_doing_ajax() ) {
        add_filter('upload_dir', 'w4g_custom_upload_directory');
        return $file;
    } else {
        return $file;   
    }
}

function w4g_handle_upload( $fileinfo )
{
    // detects if file is being uploaded from the frontend application
    if ( wp_doing_ajax() ) {
        remove_filter('upload_dir', 'w4g_custom_upload_directory');
        return $fileinfo;
    } else {
        return $fileinfo;
    }
}

function w4g_custom_upload_directory($path)
{   
    $customdir = '/applications';

    $path['path']    = str_replace($path['subdir'], '', $path['path']); //remove default subdir (year/month)
    $path['url']     = str_replace($path['subdir'], '', $path['url']);      
    $path['subdir']  = $customdir;
    $path['path']   .= $customdir; 
    $path['url']    .= $customdir;

    return $path;
}


/**
 * Hide Media from other users
 */
function w4g_hide_attachments_wpquery_where( $where ){
    global $current_user;
    if( !current_user_can( 'manage_options' ) ) {
        if( is_user_logged_in() ){
            if( isset( $_POST['action'] ) ){
                // library query
                if( $_POST['action'] == 'query-attachments' ){
                    $where .= ' AND post_author='.$current_user->data->ID;
                }
            }
        }
    }

    return $where;
}
add_filter( 'posts_where', 'w4g_hide_attachments_wpquery_where' );


// Import Application ACF fields
include "w4g-acf-data.php";