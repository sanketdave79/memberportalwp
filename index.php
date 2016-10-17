<?php
/*
Plugin Name: Portal plugin
Description: Plugin for Portal Administration
Author: Sanket Dave
Version: 0.1
*/

// Loading wp-load.php

if(!defined(ABSPATH)){
    $pagePath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
}


// Setting up tasks when installing and uninstalling 

register_activation_hook( __FILE__, 'setup_plugin' );

register_deactivation_hook( __FILE__, 'deactivate_plugin');

// Setting up Custom Post Type

add_action( 'init', 'create_star' );

// Setting up Admin Menu 

add_action('admin_menu', 'admin_plugin_setup_menu');
add_action( 'admin_menu', 'member_menu' );
add_action( 'admin_menu', 'member_menu_add_new_star' );
add_action( 'admin_menu', 'member_menu_edit_previous_star' );
add_action( 'admin_init', 'add_member_caps');


// remove admin bar for front Not part of the Task though .
add_action('init', 'remove_admin_bar');


// Functions for Setting up Plugin and Deactive Plugin

function setup_plugin(){
    
    // Activating plugin will add 'Member Role'.
    
    add_member_role();
    add_action('admin_init','add_role_caps',999);
}

function deactivate_plugin(){
    
    // Deactivating Plugin will remove 'Member' Role.
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();
    if (in_array("Member", $roles))
  {
    $wp_roles->remove_role("member");
  }
}
    
 
function admin_plugin_setup_menu(){
        add_menu_page( 'Admin Plugin Page', 'Walk Thru Portal', 'manage_options', 'adminwalkthru', 'admin_init',$icon_url = plugins_url( 'images/info.png', __FILE__ ),$postion = 3 );
}
 
function admin_init(){
        echo "<h1>Guide on using Portal</h1>";
        echo "<p><ul><li>Prerequisite : You have read ReadMe Doc.Before you place Plugin in wp-content/plugin directory and activate it, Please ensure that there is no role named 'Member' or 'member' in WP system to avoid clashes.</li><li>Step 1</li><li>Create a User</li><li>Step 2</li><li>Assign Member role to newly created User</li><li>Step 3</li><li>Now you can Logout as Administrator</li><li>For Steps 4 to 7 Login as Member</li><li>Step 8</li><li>Click on Stars to view/delete/edit Stars added from all Portal Members.</li></ul></p>";
        member_menu();
       
}

function add_member_caps() {
    // gets the author role
    $role = get_role( 'member' );

    $role->add_cap( 'edit_private_posts' ); 
    $role->add_cap( 'publish_posts' ); 
    
}


// Create Member Role

function add_member_role(){
 /**
 * Create New User Role
 * 
 * */
     

$wp_roles = new WP_Roles();
$roles = $wp_roles->get_names();

if (!in_array("Member", $roles))
  {
    $result = add_role( 'member', __('Member' ),array (
 'publish_star' => true,
 'edit_star' => true,
 'edit_others_star' => true,
 'delete_star' => true,
 'delete_others_star' => true,
 'read_private_star' => true,
 'edit_star' => true,
 'delete_star' => true,
 'read_star' => true,
 // more standard capabilities here
'read' => true,
 
) );

  }

     
/** 
 * Deleting Member role
    $wp_roles = new WP_Roles();
    $wp_roles->remove_role("member");
     echo 'Member role deleted !! ';
 * 
 */


} 

// Function for Creating Custom Post Type Stars

function create_star() {
    
    $capabilities = array(
 'publish_posts' => 'publish_star',
 'edit_posts' => 'edit_star',
 'edit_others_posts' => 'edit_others_star',
 'delete_posts' => 'delete_star',
 'delete_others_posts' => 'delete_others_star',
 'read_private_posts' => 'read_private_star',
 'edit_post' => 'edit_star',
 'delete_post' => 'delete_star',
 'read_post' => 'read_star'
);

    register_post_type( 'stars',
        array(
            'labels' => array(
                'name' => 'Stars',
                'singular_name' => 'Star',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Star',
                'edit' => 'Edit',
                'edit_item' => 'Edit Star',
                'new_item' => 'New Star',
                'view' => 'View',
                'view_item' => 'View Star',
                'search_items' => 'Search Stars',
                'not_found' => 'No Stars found',
                'not_found_in_trash' => 'No Stars found in Trash',
                'parent' => 'Parent Star Review'
            ),
            
            'public' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'show_ui' => true,
            'menu_position' => 16,
            'supports' => array( 'title', 'editor','author', 'comments', 'thumbnail', 'custom-fields'),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
            'has_archive' => true,
            //'capabilities'=>$capabilities,
            'map_meta_cap' => true,
    // as pointed out by iEmanuele, adding map_meta_cap will map the meta correctly 
    
        )
    );
    
    flush_rewrite_rules(false);
 
}

// Function for removing admin Bar 


function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

/// Functions for adding admin menus for members, Listings and Forms

function member_menu(){
    add_menu_page( 'List of Stars', 'Stars', 'read_star', 'List of Stars', 'member_stars_list',$icon_url = plugins_url( 'images/image.png', __FILE__ )); 
    add_menu_page( 'Instruction', 'Walk Thru Portal', 'read_star', 'memberwalkthru', 'member_instructions',$icon_url = plugins_url( 'images/info.png', __FILE__ ),$postion = 2 );
}

function member_instructions(){
    echo "<h1>Guide on using Portal for members</h1>";
        echo "<p><ul><li>Step 4</li><li>Add a new Star information  on Left hand side by clicking on Add New star</li><li>Step 5</li><li>You can edit previous newly added star using form by clicking on Edit Previous star</li><li>Step 6</li><li>You can list all the stars added by you by clicking on Stars</li><li>Step 7</li>Logout as member and Login as Admin to view/edit/delete all the member's stars posts.</li></ul></p>";
}

function member_menu_add_new_star()
{
    add_menu_page( 'Adding New Star', 'Add New Star', 'publish_star', 'Add New Star', 'member_add_new_star',$icon_url = plugins_url( 'images/plus.png', __FILE__ ) );
}

function member_menu_edit_previous_star()
{
   add_menu_page( 'Editing Previous Star', ' Edit Previous Star', 'edit_star', 'Edit Previous Star', 'member_edit_previous_star',$icon_url = plugins_url( 'images/pensil.png', __FILE__ ) );
}

function member_stars_list() {
    
    $user_id = get_current_user_id();
    $args = array('post_type' => 'stars','author' => $user_id,'posts_per_page' => 3);
	if (current_user_can('member')) {
  echo '<div class="wrap">';
	
	
        
        echo '<table border="4">';
   echo' <tr><th>Title</th><th>Content</th><th>Author</th></tr>';
    $myposts = get_posts($args);
 
    if ( $myposts ) {
        foreach ( $myposts as $post ) :
            setup_postdata( $post );
            
            ?>

                
<tr><td><?php echo $post->post_title; ?></td>
    <td><?php echo $post->post_content; ?></td>
    <td><?php echo $post->post_author; ?></td>
</tr>
        <?php
        endforeach; 
    }
    
echo '</table>';
echo '</div>';
}
	
}


// add new member function Form view html in function

function member_add_new_star(){
    if (current_user_can('member')) {
        $url = plugins_url( 'addstar.php', __FILE__ );
  echo '<div class="wrap">';
	echo '<p>'
  . '<form action="'.$url.'" method="post" name="form">
Title <input id="title" type="text" name="post_title" />
Content <input id="content" type="text" name="post_content" />
<input type="submit" value="Submit" /></form> </p>';
	echo '</div>';
}
    
}

// Edit Previous entry Form view html in function

function member_edit_previous_star(){
    $user_id = get_current_user_id();
    $args = array('numberposts' => 1,'post_type'=>'stars','author'=>$user_id);
    
    $recent_post = wp_get_recent_posts( $args, $output);
    
    $title = $recent_post[0]->post_title;
    $content = $recent_post[0]->post_content;
    
    if (current_user_can('member')) {
  echo '<div class="wrap">';
	echo '<p>'
  . '<form action="" method="post" name="form">
Title <input id="title" type="text" name="post_title" value="'.$title.'" />
Content <input id="content" type="text" name="post_content" value="'.$content.'" />
<input type="submit" value="Submit" /></form> </p>';
	echo '</div>';
}
    
}




?>

