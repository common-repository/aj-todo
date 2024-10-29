<?php
class AJTODO_Status{

	public $id = "";
	public $title = "";
	public $comment = "";
	public $statustype = "";
	public $isbuiltin = "";
	public $statusorder = "";

	public function __construct(){
	}
	
	public function setData(){
		global $wpdb;
		if($this->id){
			$data = $wpdb->get_row("select * from ".AJTODO_DB_TODOSTATUS." where id = $this->id");	
			$this->title = $data->title;
			$this->comment = $data->comment;
			$this->statustype = $data->statustype;
			$this->isbuiltin = $data->isbuiltin;
			$this->statusorder = $data->statusorder;
		}
	}

	public function create(){
		global $wpdb;
		$sql = "insert into ".AJTODO_DB_TODOSTATUS;
		$sql .= "(title, comment, statustype, regdate, statusorder)";
		$sql .= "values('$this->title', '$this->comment', '$this->statustype', '".date("Y-m-d H:i:s")."', '$this->statusorder')";
		$wpdb->query($sql);	
	}

	public function del($moveid){
		global $wpdb;
		if($moveid){
			$wpdb->query("update ".AJTODO_DB_TODO." set statustypeid = $moveid where statustypeid = $this->id");	
		}
		$wpdb->query("delete from ".AJTODO_DB_TODOSTATUS." where id = $this->id");	
	}

	public function getTodoCount(){
		global $wpdb;
		$sql = "select count(*) from ".AJTODO_DB_TODO;
		$sql .= " where statustypeid = $this->id";
		return $wpdb->get_var($sql);	
	}

	public function update(){
		global $wpdb;
		$sql = "update ".AJTODO_DB_TODOSTATUS;
		$sql .= " set title = '$this->title', 
			comment = '$this->comment', 
			updated = '".date("Y-m-d H:i:s")."' where id = $this->id";
		$wpdb->query($sql);	
	}

	public function getStatusList(){
		global $wpdb;
		return $wpdb->get_results("select * from ".AJTODO_DB_TODOSTATUS);	
	}
}
