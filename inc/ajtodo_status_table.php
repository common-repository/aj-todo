<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class AJTODO_Status_Table extends WP_List_Table
{	
	public function __construct() {
        parent::__construct(
            array(
                'singular' => 'singular_form',
                'plural'   => 'plural_form',
                'ajax'     => false
            )
        );
    }
	
    public function prepare_items()
    {
        $columns = $this->get_columns();
        //$hidden = $this->get_hidden_columns();
        //$sortable = $this->get_sortable_columns();
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
            'title'          	=> __('상태 이름', 'ajtodo' ),
            'comment'       	=> __('상태 설명', 'ajtodo' ),
            'statustype' 		=> __('상태 종류', 'ajtodo' ),
            'totaltodocount'       	=> __('할일 개수', 'ajtodo' ),
            'isbuiltin'		=> __('기본 여부', 'ajtodo' ),
            'setting'		=> __('관리', 'ajtodo' ),
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
	
	private function setSetting($item) {
		$btns = "<a class='btn btn-sm btn-info' href='?page=ajtodo_admin_setting&tab=status&ajtodo_statusid=".$item->id."&ajtodo_view=edit'>" . __( '수정', 'ajtodo' ) . '</a>';
		if($item->isbuiltin != "Y"){
			$btns .= "<a class='btn btn-sm btn-danger' href='?page=ajtodo_admin_setting&tab=status&ajtodo_statusid=".$item->id."&ajtodo_view=del'>" . __( '삭제', 'ajtodo' ) . '</a>';
		}
		return $btns;
	}
	
	public function get_sortable_columns()
    {
        return array(
			'title' => array('title', false),
			'regdate' => array('regdate', true),
		);
    }
	
	private function table_data()
    {
		global $wpdb;
		$sql = "SELECT * ";
		$sql .= " FROM ".AJTODO_DB_TODOSTATUS." as a ";
		$sql .= " order by statustype desc ";

		$result = $wpdb->get_results($sql);
		foreach($result as $item){
			$todocount = $wpdb->get_var("select count(*) from ".AJTODO_DB_TODO."_".$this->project->id." where statustypeid = ".$item->id);
			$data[] = array(
				'id'	=> $item->id,
				'title'	=>	$item->title,
				'comment'	=>	$item->comment,
				'statustype'	=>	$this->getStatusTypeName($item->statustype),
				'totaltodocount'	=>	$todocount,
				'isbuiltin'	=>	$item->isbuiltin,
				'setting'	=>	$this->setSetting($item)
			);
		}
		return $data;
	}

	private function getStatusTypeName($type){
        switch($type) {
            case 'S':
                return __("할일","ajtodo");
            case 'I':
                return __("진행중","ajtodo");
            case 'D':
                return __("완료","ajtodo");
        }
	}

	public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'title':
            case 'statustype':
            case 'title':
            case 'comment':
            case 'totaltodocount':
            case 'isbuiltin':
            case 'setting':
                return $item[ $column_name ];
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
			$result = strcmp( $a[$orderby], $b[$orderby] );
			if($order === 'asc')
			{
				return $result;
			}
			return -$result;	
		}
    }
}
?>
