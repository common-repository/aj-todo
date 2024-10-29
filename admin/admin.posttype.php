<?php
function ajtodo_doc_init() {
    $labels = array(
        'name'                  => _x( '프로젝트 문서', 'Post type general name', 'ajtodo' ),
        'singular_name'         => _x( '프로젝트 문서', 'Post type singular name', 'ajtodo' ),
        'menu_name'             => _x( '프로젝트 문서', 'Admin Menu text', 'ajtodo' ),
        'name_admin_bar'        => _x( '프로젝트 문서', 'Add New on Toolbar', 'ajtodo' ),
        'add_new'               => __( '새 문서', 'ajtodo' ),
        'add_new_item'          => __( '새 프로젝트 문서', 'ajtodo' ),
        'new_item'              => __( '새 문서', 'ajtodo' ),
        'edit_item'             => __( '문서 수정', 'ajtodo' ),
        'view_item'             => __( '문서 보기', 'ajtodo' ),
        'all_items'             => __( '모든 프로젝트 문서', 'ajtodo' ),
        'search_items'          => __( '문서 검색', 'ajtodo' ),
        'parent_item_colon'     => __( '상위 문서:', 'ajtodo' ),
        'not_found'             => __( '문서가 없습니다.', 'ajtodo' ),
        'not_found_in_trash'    => __( '휴지통에 문서가 없습니다.', 'ajtodo' ),
        'filter_items_list'     => _x( 'Filter books list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'ajtodo' ),
        'items_list_navigation' => _x( 'Books list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'ajtodo' ),
        'items_list'            => _x( 'Books list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'ajtodo' ),
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'ajtodo/%project%' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'comments' ),
		'taxonomies'         => array('project'),
    );
 
    register_post_type( 'ajtododoc', $args );
    $set = get_option('post_type_rules_flased_authors');
    if ($set !== true){
        flush_rewrite_rules(false);
        update_option('post_type_rules_flased_authors',true);
    }
}
add_action( 'init', 'ajtodo_doc_init' );

function ajtodo_project_post_link($link, $post){
	global $wpdb;
	if ( $post->post_type !== 'ajtododoc' )
        return $link;

	$sql = "SELECT pkey FROM ".AJTODO_DB_PROJECT." as a join ".AJTODO_DB_LINK." as b ";
	$sql .= " on a.id = b.projectid and b.postid = ".$post->ID;
	$pkey = $wpdb->get_var($sql);
	if($pkey){
		$link = str_replace('%project%', $pkey, $link);
	}

    return $link;
}
add_filter('post_type_link', 'ajtodo_project_post_link', 999, 2);
add_filter('post_link', 'ajtodo_project_post_link', 10, 2 );

function ajtodo_taxon_init() {
	$labels = array(
		'name'                       => _x( '프로젝트', '프로젝트', 'ajtodo' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'show_ui'                    => false,
		'query_var' 				 => true,
		'show_in_rest'               => true,
		'rewrite'                    => array('slug' => 'project'),
	);
	register_taxonomy( 'project', 'ajtododoc', $args );
}
add_action( 'init', 'ajtodo_taxon_init' );
