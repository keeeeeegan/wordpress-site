<?php
/*
Plugin Name: Portfolio
Plugin URI: http://keeganberry.com
Description: Declares a plugin that will create a custom post type for a portfolio.
Version: 1.0
Author: Keegan Berry
Author URI: http://keeganberry.com/
License: MIT
*/

  function custom_post_project() {
    $labels = array(
      'name'               => _x( 'Projects', 'post type general name' ),
      // 'name'               => _x( 'Portfolio Projects', 'post type general name' ),
      'singular_name'      => _x( 'Project', 'post type singular name' ),
      'add_new'            => _x( 'Add New Project', 'book' ),
      'add_new_item'       => __( 'Add New Project' ),
      'edit_item'          => __( 'Edit Project' ),
      'new_item'           => __( 'New Project' ),
      'all_items'          => __( 'Projects' ),
      'view_item'          => __( 'View Project' ),
      'search_items'       => __( 'Search Projects' ),
      'not_found'          => __( 'No projects found' ),
      'not_found_in_trash' => __( 'No projects found in the Trash' ),
      'parent_item_colon'  => '',
      'menu_name'          => 'Portfolio'
    );
    $args = array(
      'labels'        => $labels,
      'description'   => 'Holds projects and project specific data',
      'public'        => true,
      'menu_position' => 5,
      'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
      'menu_icon' => 'dashicons-images-alt2',
      'has_archive'   => true,
    );
    register_post_type( 'projects', $args );
  }
  add_action( 'init', 'custom_post_project' );

  function my_updated_messages( $messages ) {
    global $post, $post_ID;
    $messages['projects'] = array(
      0 => '',
      1 => sprintf( __('Project updated. <a href="%s">View project</a>'), esc_url( get_permalink($post_ID) ) ),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => __('Project updated.'),
      5 => isset($_GET['revision']) ? sprintf( __('Project restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
      6 => sprintf( __('Project published. <a href="%s">View project</a>'), esc_url( get_permalink($post_ID) ) ),
      7 => __('Project saved.'),
      8 => sprintf( __('Project submitted. <a target="_blank" href="%s">Preview project</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
      9 => sprintf( __('Project scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview project</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
      10 => sprintf( __('Project draft updated. <a target="_blank" href="%s">Preview project</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
  }
  add_filter( 'post_updated_messages', 'my_updated_messages' );

  function my_taxonomies_project_client() {
    $labels = array(
      'name'              => _x( 'Project Clients', 'taxonomy general name' ),
      'singular_name'     => _x( 'Clients', 'taxonomy singular name' ),
      'search_items'      => __( 'Search Clients' ),
      'all_items'         => __( 'All Clients' ),
      'parent_item'       => __( 'Parent Project Clients' ),
      'parent_item_colon' => __( 'Parent Project Clients:' ),
      'edit_item'         => __( 'Edit Client' ),
      'update_item'       => __( 'Update Client' ),
      'add_new_item'      => __( 'Add New Client' ),
      'new_item_name'     => __( 'New Project Client' ),
      'menu_name'         => __( 'Clients' ),
    );
    $args = array(
      'labels' => $labels,
      'hierarchical' => true,
    );
    register_taxonomy( 'project_client', 'projects', $args );
  }

  add_action( 'init', 'my_taxonomies_project_client', 0 );

  function project_labels_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'project_labels_box_content_nonce' );
    echo '<label for="project_labels"></label>';
    echo '<input type="text" id="project_labels" name="project_labels" placeholder="enter labels" />';
  }

  add_action( 'add_meta_boxes', 'project_labels' );
  function project_labels() {
      add_meta_box(
          'project_labels',
          __( 'Labels', 'myplugin_textdomain' ),
          'project_labels_box_content',
          'projects',
          'side',
          'core'
      );
  }

  add_action( 'save_post', 'project_labels_box_save' );
  function project_labels_box_save( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    return;

    if ( !wp_verify_nonce( $_POST['project_labels_box_content_nonce'], plugin_basename( __FILE__ ) ) )
    return;

    if ( 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ) )
      return;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ) )
      return;
    }
    $project_labels = $_POST['project_labels'];
    update_post_meta( $post_id, 'project_labels', $project_labels );
  }
?>
