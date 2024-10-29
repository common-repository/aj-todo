<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class AJTODO_Doc_Table extends WP_List_Table
{	
    public $project;
    private $pid = "";
	public function __construct($pid) {
        $this->pid = $pid;
        parent::__construct(
            array(
                'singular' => 'singular_form',
                'plural'   => 'plural_form',
                'ajax'     => false
            )
        );
    }

	protected function get_views() { 
        $link = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=doc&pid=".$this->pid;
		$qry = array(
			array(
				'key'     => 'ajtodo_pid',
				'value'   => $this->pid,
				'compare' => '=',
			)
		);
        $post_status = ajtodo_get("post_status","");
        $a_cnt = count( get_posts( array( 'post_type' => 'ajtododoc', 'meta_query' => $qry, 'nopaging'  => true )));
        $p_cnt = count( get_posts( array( 'post_type' => 'ajtododoc', 'meta_query' => $qry, 'post_status' => 'publish', 'nopaging'  => true )));
        $d_cnt = count( get_posts( array( 'post_type' => 'ajtododoc', 'meta_query' => $qry, 'post_status' => 'draft', 'nopaging'  => true )));
        $t_cnt = count( get_posts( array( 'post_type' => 'ajtododoc', 'meta_query' => $qry, 'post_status' => 'trash', 'nopaging'  => true )));
        $status_links = array(
            "all"       => "<a href='".$link."' ".(($post_status == "") ? "class='current'" : "").">".__("전체","ajtodo")."(".$a_cnt.")</a>",
            "published" => "<a href='".$link."&post_status=publish' ".(($post_status == "publish") ? "class='current'" : "").">".__("발행됨","ajtodo")."(".$p_cnt.")</a>",
            "drafted" => "<a href='".$link."&post_status=draft' ".(($post_status == "draft") ? "class='current'" : "").">".__("임시글","ajtodo")."(".$d_cnt.")</a>",
            "trashed"   => "<a href='".$link."&post_status=trash' ".(($post_status == "trash") ? "class='current'" : "").">".__("휴지통","ajtodo")."(".$t_cnt.")</a>"
        );
        return $status_links;
    }
    
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();

        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns);
        $this->items = $data;
    }
	
	public function get_columns()
    {
        $columns = array(
            'post_title'          	=> __('문서 제목', 'ajtodo' ),
            'link_item'         	=> __('관련 항목', 'ajtodo' ),
            'post_author'       	=> __('작성자', 'ajtodo' ),
            'post_date'      		=> __('발행일', 'ajtodo' ),
        );
        return $columns;
    }
	
	public function filter_table_data( $table_data, $search_key ) {
		$filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
			foreach( $row as $row_val ) {
				if( stripos( $row_val, $search_key ) !== false ) {
					return true;
				}
			}
		} ) );
		return $filtered_table_data;
    }
    
	function column_post_title($post) {
        $restore_link = wp_nonce_url(
            "post.php?action=untrash&post=$post->ID",
            "untrash-post_$post->ID"
        );
        $del_link = wp_nonce_url(
            "post.php?action=delete&post=$post->ID",
            "delete-post_$post->ID"
        );
        if($post->post_status == 'trash') {
            if($this->project->hasPerm("tp_doc_create")){
                $actionLinks  = '<div class="row-actions"><span class="untrash"><a href="'.$restore_link.'">'.__('복원', 'ajtodo').'</a> | </span>';
                $actionLinks .= '<span class="view"><a href="'.get_permalink($post->ID).'">'.__('보기', 'ajtodo').'</a> | </span>';
                $actionLinks .= '<span class="trash"><a href="'.$del_link.'" class="submitdelete">'.__('영구 삭제', 'ajtodo').'</a></span>';
            }else{
                $actionLinks .= '<span class="view"><a href="'.get_permalink($post->ID).'">'.__('보기', 'ajtodo').'</a></span>';
            }
        } else {
            if($this->project->hasPerm("tp_doc_create")){
                $actionLinks  = '<div class="row-actions"><span class="view"><a href="'.get_permalink($post->ID).'">'.__('보기', 'ajtodo').'</a> | </span>';
                $actionLinks .= '<span class="edit"><a href="'.get_admin_url().'post.php?post='.$post->ID.'&action=edit&pid='.$this->pid.'">'.__('수정', 'ajtodo').'</a> | </span>';
                $actionLinks .= '<span class="trash"><a href="'.get_delete_post_link($post->ID).'" class="submitdelete">'._x('휴지통', 'verb (ie. trash this post)', 'ajtodo').'</a></span>';
            }else{
                $actionLinks  = '<div class="row-actions"><span class="view"><a href="'.get_permalink($post->ID).'">'.__('보기', 'ajtodo').'</a></span>';
            }
        }
        return $post->post_title.$actionLinks;
    }

	function column_post_author($post) {
		$user = get_userdata($post->post_author);
		return "<a href='".get_author_posts_url($post->post_author)."' target='_blank'>".$user->display_name."</a>";
    }

	function column_link_item($post) {
        $ret = "";
        $pid = ajtodo_get("pid", "");
        $ajtodo_link = new AJTODO_ProjectLink();
        $data = $ajtodo_link->getLink('ajtododoc', $post->ID);
        if($data->todoid){
            $todo = AJTODO_Todo::getTodo($pid, $data->todoid);
            $ret = "<a href='".get_permalink($post->ID)."' target='_blank'>[".$todo->tkey."]".$todo->title."</a><div>".__("플랜","ajtodo")."</div>";
        }else if($data->planid){
            $plan = AJTODO_ProjectPlan::getPlan($data->planid);
            $ret = $plan->plantitle."<div>".__("플랜","ajtodo")."</div>";
        }else {
            $project = AJTODO_Project::getProject($pid);
            $ret = $project->title."<div>".__("프로젝트","ajtodo")."</div>";
        }
		return $ret;
    }

	public function get_sortable_columns()
    {
        return array(
			'post_title' => array('post_title', false),
			'post_date' => array('post_date', true),
		);
    }
    	
	private function table_data()
    {
        $post_status = ajtodo_get("post_status","");
		$qry = array(
			array(
				'key'     => 'ajtodo_pid',
				'value'   => $this->pid,
				'compare' => '=',
			)
		);
		$args = array(
            'post_type'   => 'ajtododoc',
			'meta_query' => $qry, 
        );
        if($post_status){
            $args["post_status"] = $post_status;
        }
		return get_posts($args);
	}

	public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'post_title':
            case 'post_author':
            case 'post_date':
            case 'link_item' :
                return $item->$column_name;
            default:
                return print_r( $item, true ) ;
        }
    }
	
	private function sort_data( $a, $b )
    {
		if(isset($_GET['orderby']) && isset($_GET['order'])){
			if(!empty($_GET['orderby']))
			{
				$orderby = sanitize_text_field( $_GET['orderby'] );
			}
			if(!empty($_GET['order']))
			{
				$order = sanitize_text_field( $_GET['order'] );
			}
			$result = strcmp( $a->$orderby, $b->$orderby );
			if($order === 'asc')
			{
				return $result;
			}
			return -$result;	
		}
    }
}
?>
