<?php
class AJTODO_User{
	public function __construct(){
	}

	public static function getUsersByRoles($roles){
        $ret = array();
        $users = array();
		if(!is_array($roles))
			return array();

        foreach($roles as $r){
            $users = array_merge($users, $r["users"]);
        }
        $users = array_unique($users);
        foreach($users as $u){
            $user = get_userdata($u);
            $adduser = array(
                "id" => $u,
                "avatar" => get_avatar($u, 28),
                "name" => $user->display_name,
            );
            array_push($ret, $adduser);
        }
        return $ret;
    }

	public static function getUsers($prj){
        $ret = array();
        $users = array();
        foreach($prj->roles as $r){
            $users = array_merge($users, $r["users"]);
        }
        $users = array_unique($users);
        foreach($users as $u){
            $user = get_userdata($u);
            $adduser = array(
                "id" => $u,
                "avatar" => get_avatar($u, 28),
                "name" => $user->display_name,
            );
            array_push($ret, $adduser);
        }
        return $ret;
    }

	public static function getRoles($prj){
        $ret = array();
		foreach($prj->roles as $key => $val){
            if(in_array(wp_get_current_user()->ID, $val["users"])){
                $ret[] = $val["key"];
            }
        }
        return $ret;
    }
    
	public static function getPerms($prj){
        $ret = array();
        $userroles = AJTODO_User::getRoles($prj);
        foreach($prj->roleperm as $key => $val){
            if(in_array($key, $userroles)){
                $ret = array_merge($ret, $val);
            }
        }
        return array_unique($ret);
    }
    
	public static function getStatusRole($prj){
        $ret = array();
        $userroles = AJTODO_User::getRoles($prj);
        foreach($prj->statuses as $status){
            $arr = array("key" => $status["key"], "action" => array());
            foreach($status["rules"] as $s){
                if(array_intersect($s["roles"], $userroles)){
                    $arr["action"][] = $s["to"];
                }
            }
            $ret[] = $arr;
        }
        return $ret;
	}
}
