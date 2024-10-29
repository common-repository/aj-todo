<?php
add_filter( 'init', function( $template ) {
    if ( isset( $_GET['aj-todo'] ) ) {
        $invoice_id = $_GET['aj-todo'];
        include AJTODO_PLUGIN_PATH . 'templates/view_todo.php';
        die;
    }
});

add_action('init', function() {
    add_rewrite_endpoint( 'aj-todo', EP_PERMALINK );
});

add_filter( 'generate_rewrite_rules', function ( $wp_rewrite ){
    $wp_rewrite->rules = array_merge(
        ['aj-todo/([A-Z]+-[0-9]+)/?$' => 'index.php?todo_key=$matches[1]'],
        $wp_rewrite->rules
    );
});
add_filter( 'query_vars', function( $query_vars ){
    $query_vars[] = 'todo_key';
    return $query_vars;
});
add_action( 'template_redirect', function(){
    $todo_key = strval(get_query_var('todo_key'));
    if ($todo_key){
        include AJTODO_PLUGIN_PATH . 'templates/view_todo.php';
        die;
    }
});
