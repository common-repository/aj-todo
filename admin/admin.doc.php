<?php
add_action('ajtodo_del_todo', function($pid, $tid){
	AJTODO_ProjectLink::updateLinkedTodo($pid, $tid);
}, 10, 2);

add_action('deleted_post', function($postid){
	AJTODO_ProjectLink::unLinkPostId($postid);
}, 10, 1);