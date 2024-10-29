<?php
function ajtodo_activation(){
	global $table_prefix, $wpdb, $ajtodoDBVersion;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = array();

    $installed_db_ver = get_option("ajtodo_db_version");
	$ajtodo_projectkeys = $table_prefix . 'ajtodo_projectkeys';
	$ajtodo_plan = $table_prefix . 'ajtodo_plan';
	$ajtodo_project = $table_prefix . 'ajtodo_project';
	$ajtodo_issue = $table_prefix . 'ajtodo_issue';
	$ajtodo_link = $table_prefix . 'ajtodo_link';

    if($installed_db_ver !== $ajtodoDBVersion) {
        $sql[] = "CREATE TABLE $ajtodo_project (
            id int(11) NOT NULL AUTO_INCREMENT,
            pkey varchar(100) NOT NULL,
            title varchar(100) NOT NULL,
            comment varchar(255) NOT NULL,
            projecttype varchar(20) DEFAULT 0,
            authorid int DEFAULT 0,
            autoassign char(1) DEFAULT 'N',
            projectstatus varchar(1),
            statuses longtext NULL,
            roles longtext NULL,
            roleperm longtext NULL,
            category longtext NULL,
            todotype longtext NULL,
            updated datetime DEFAULT CURRENT_TIMESTAMP NULL,
            regdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            donedate datetime NULL,
            PRIMARY KEY  (id)
            ) $charset_collate;";

        $sql[] = "CREATE TABLE $ajtodo_projectkeys (
            projectid int(11) NOT NULL,
            todonewindex int(11) NOT NULL
            ) $charset_collate;";

        $sql[] = "CREATE TABLE $ajtodo_link (
            id int(11) NOT NULL AUTO_INCREMENT,
            projectid int(11) NULL,
            planid int(11) NULL,
            todoid int(11) NULL,
            posttype varchar(100) NULL,
            postid int(11) NULL,
            regdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
            ) $charset_collate;";

        $sql[] = "CREATE TABLE $ajtodo_plan (
            id int(11) NOT NULL AUTO_INCREMENT,
            projectid int(11) NULL,
            plantitle varchar(255) NOT NULL,
            plancomment longtext NULL,
            todoorderset longtext NULL,
            updated datetime DEFAULT CURRENT_TIMESTAMP NULL,
            regdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            ising char(1) DEFAULT 'N',
            startdate datetime NULL,
            finishdate datetime NULL,
            realstartdate datetime NULL,
            donedate datetime NULL,
            PRIMARY KEY  (id)
            ) $charset_collate;";
    }
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	dbDelta($sql);

    update_option('ajtodo_db_version', $ajtodoDBVersion);
}
function ajtodo_uninstall(){
	global $table_prefix, $wpdb, $ajtodoDBVersion;

	$ajtodo_projectkeys = $table_prefix . 'ajtodo_projectkeys';
	$ajtodo_plan = $table_prefix . 'ajtodo_plan';
	$ajtodo_project = $table_prefix . 'ajtodo_project';
	$ajtodo_issue = $table_prefix . 'ajtodo_issue';

	$wpdb->query("DROP TABLE IF EXISTS $table_prefix.ajtodo_projectkeys");
	$wpdb->query("DROP TABLE IF EXISTS $table_prefix.ajtodo_plan");
	$wpdb->query("DROP TABLE IF EXISTS $table_prefix.ajtodo_project");
	$wpdb->query("DROP TABLE IF EXISTS $table_prefix.ajtodo_issue");
}
