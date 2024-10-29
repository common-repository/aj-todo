<?php
class AJTODO_Alert{
	public $alerts;
	private $skinPath;
	public function __construct($common){
		include($common->skinPath . "/alert.php");
		$this->alerts = $alerts;
	}

	public function getHtml($alerttype){
		return $this->alerts[$alerttype];
	}
}

class AJTODO_Common{
	private $hasContainer;
	public $skinUrl;
	public $skinPath;
	public $skinName;
	public $js_templates;
	public function __construct(){
		$this->skinName = get_option('ajtodo_skin', "basic");
		$this->skinUrl = AJTODO_PLUGIN_URL."skins/".$this->skinName;
		$this->skinPath = AJTODO_PLUGIN_PATH."skins/".$this->skinName;
		include($this->skinPath . "/js_templates.php");
		$this->js_templates = $js_templates;
	}

	public function loadCommon(){
	}
	public function start($container = ""){
		$this->hasContainer = $container;
		echo "<div class='ajtodo'>";
		echo $this->hasContainer ? "<div class='ajtodo_container'>" : "";
	}
	public function last(){
		echo "</div>";
		echo $this->hasContainer ? "</div>" : "";
	}
}
