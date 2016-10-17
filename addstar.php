<?php
if(!defined(ABSPATH)){
    $pagePath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
}

$user_id = get_current_user_id();

// Create post object
$new_post = array(
  'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
  'post_content'  => $_POST['post_content'],
  'post_status'   => 'publish',
  'post_author'   => $user_id,
  'post_type'     =>'stars'
);
 
// Insert the post into the database
wp_insert_post( $new_post );

