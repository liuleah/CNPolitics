<?php
$page_setting_total_uri = explode( '&', $_SERVER['REQUEST_URI']);
$page_setting_uri = $page_setting_total_uri[0];

$topic_checkbox_contents = get_checkbox_contents("topic");
$rsch_checkbox_contents = get_checkbox_contents("rsch");
$issue_checkbox_contents = get_checkbox_contents("issue");

/**
* Move position of item
* @param string $db_table database table prefix: "topic" or "researchers"
* @param string $category "category" for topic table, "region" for researcher table  
* @param int $cat_id   category id or region id
* @param int $cur_order current item's order number
* @param string $toward direction: top | bottom | up | down
*/
function move_position($db_table, $category, $cat_no, $content_id, $curr_ordering, $toward) {
	global $wpdb;
	if ( $toward=="top" ) {	// bubble sort..
		//echo "Top";
		$sql = "SET @temp_var := 1";	// Leave the first entry empty
		$result = mysql_query($sql);
		if (!$result) {
    		echo "SQL execution error in ". __LINE__ . " in ". __FILE__; 
    		echo "Invalid query: " . mysql_error();
		}
		// reorder ordering when it is less than 1000, starting with 2
		$sql = "UPDATE {$wpdb->prefix}$db_table  
				SET ordering = (@temp_var := @temp_var+1) 
				WHERE $category = $cat_no ORDER BY ordering";
		$result = mysql_query($sql);
		if (!$result) {
    		echo "SQL execution error in ". __LINE__ . " in ". __FILE__; 
    		echo "Invalid query: " . mysql_error();
		}
    	// set record's ordering to 1
    	$sql = "UPDATE {$wpdb->prefix}$db_table 
    			SET ordering = 1 
    			WHERE $category = $cat_no AND id = $content_id";
     	if ( !$wpdb->query($sql) ) {
			echo "SQL execution error in ". __LINE__ . " in ". __FILE__;
		}
	}
	else if ( $toward=="bottom" ){	// bubble sort..
		//echo "Bottom";
		$sql = "SET @temp_var := 0";	// Don't leave the first entry empty
		$result = mysql_query($sql);
		if (!$result) {
    		echo "SQL execution error in ". __LINE__ . " in ". __FILE__; 
    		echo "Invalid query: " . mysql_error();
		}
		$sql = "UPDATE {$wpdb->prefix}$db_table  
				SET ordering = (@temp_var := @temp_var+1) 
				WHERE $category = $cat_no ORDER BY ordering";
		$result = mysql_query($sql);
		if (!$result) {
    		echo "SQL execution error in ". __LINE__ . " in ". __FILE__; 
    		echo "Invalid query: " . mysql_error();
		}
		// get last
  	    $sql = "SELECT ordering FROM {$wpdb->prefix}$db_table 
  	    		WHERE $category = $cat_no AND  id != $content_id";
  	    if ( ($order_obj = $wpdb->get_results($sql))==null ) {
			echo "SQL execution error in ". __LINE__ . " in ". __FILE__;
		}
		$bottom = 0;
		foreach( (array)$order_obj as $obj ) {
			if ( $bottom<$obj->ordering )	$bottom = $obj->ordering;
		}
  	    $bottom = 1 + (int) $bottom;
  	    // set record's ordering to last
    	$sql = "UPDATE {$wpdb->prefix}$db_table 
    			SET ordering = $bottom
    			WHERE $category=$cat_no AND id = $content_id";
     	if ( !$wpdb->query($sql) ) {
			echo "SQL execution error in ". __LINE__ . " in ". __FILE__;
		}
	}
	else if ( $toward=="up" ) {
  	    $sql = "SELECT ordering FROM {$wpdb->prefix}$db_table
  	    		WHERE $category=$cat_no AND  ordering < $curr_ordering ORDER BY ordering DESC LIMIT 1";
  	    if ( ($order_obj = $wpdb->get_results($sql))==null ) {
			//echo "Fail to get ordering of previous record. Error in ". __LINE__ . " in ". __FILE__;
			return;
		}
		$prev_ordering = $order_obj[0]->ordering;

  	    // Set above record to current ordering
  	  	$sql = "UPDATE {$wpdb->prefix}$db_table
  	  			SET ordering = $curr_ordering 
  	  			WHERE $category=$cat_no AND ordering < $curr_ordering ORDER BY ordering DESC LIMIT 1";
  	    if ( !$wpdb->query($sql) ) {
			echo "Exchanging ordering fails. SQL execution error in ". __LINE__ . " in ". __FILE__;
		}

		// Set moving recorder to previous ordering
  	  	$sql = "UPDATE {$wpdb->prefix}$db_table 
  	  			SET ordering = $prev_ordering 
  	  			WHERE $category=$cat_no AND id = $content_id";
  	    if ( !$wpdb->query($sql) ) {
			echo "Exchanging ordering fails. SQL execution error in ". __LINE__ . " in ". __FILE__;
		}
	}
	else if ( $toward=="down" ) {
	    $sql = "SELECT ordering FROM {$wpdb->prefix}$db_table 
	    		WHERE $category=$cat_no AND ordering > $curr_ordering ORDER BY ordering ASC LIMIT 1";
	    if ( ($order_obj = $wpdb->get_results($sql))==null ) {
			//echo "Fail to get ordering of previous record. Error in ". __LINE__ . " in ". __FILE__;
			return;
		}
		$next_ordering = $order_obj[0]->ordering;
        
        $sql = "UPDATE {$wpdb->prefix}$db_table SET ordering = $curr_ordering 
        		WHERE $category=$cat_no AND ordering > $curr_ordering ORDER BY ordering ASC LIMIT 1";
  	    if ( !$wpdb->query($sql) ) {
			echo "Exchanging ordering fails. SQL execution error in ". __LINE__ . " in ". __FILE__;
		}
  	    
  	  	$sql = "UPDATE {$wpdb->prefix}$db_table SET ordering = $next_ordering 
  	  			WHERE $category = $cat_no AND id = $content_id";
		if ( !$wpdb->query($sql) ) {
			echo "Exchanging ordering fails. SQL execution error in ". __LINE__ . " in ". __FILE__;
		}
	}
}

/**
* Get topic table
* @param string $category "topic" or "researchers"
* @param string $key_word "topic" name  
*/
function get_topic_table($category='', $key_word='') {

	global $wpdb;
	$sql = "SELECT id, subject, category, intro, ordering
			FROM {$wpdb->prefix}topics";
	//echo $category;
	if ( $category!='') {
		$sql = $sql . " WHERE category = '$category'";
		//echo "search category";
	}
	else if ( $key_word!='' ) {
		$sql = $sql . " WHERE subject = '$key_word'";
		//echo "search key word";
	}
	$sql = $sql . " ORDER BY ordering";
	//echo $sql;
	$topic_array = $wpdb->get_results($sql);
	if ( !empty($topic_array) ) {
		foreach ( $topic_array as $t ) {
			$sql = "SELECT *
					FROM {$wpdb->prefix}post_info
					WHERE type = '1' AND info_id = '$t->id';";
			$t->post_num = $wpdb->query($sql);
		}
	}
	return $topic_array;
}

/**
* Get editting topic from topic id
* @param int $id "id" of topic"  
*/
function get_edit_topic($id) {
	global $wpdb;
	global $regions;
	global $rsch_checkbox_contents;
	$sql = "SELECT id, subject, category, intro, img_path
			FROM {$wpdb->prefix}topics
			WHERE id = $id";
	$topic_array = $wpdb->get_results($sql);
	if ( empty($topic_array) ) {
		echo __FUNCTION__;
		die("Editing topic is not in DB!");
	}	
	// select from topics_rschs
	$sql = "SELECT rsch_id
			FROM {$wpdb->prefix}topics_rschs
			WHERE topic_id = '$id'";

	$topic_array[0]->checked = $wpdb->get_results($sql, ARRAY_N);
	//var_dump(	$topic_array[0]->checked );
	//var_dump($rsch_array[0]->checkbox_tabs);

	$topic_array[0]->checkbox_tabs = $regions;	// toptopics
	$topic_array[0]->checkbox_contents = $rsch_checkbox_contents;


	return $topic_array[0];
}

/**
* Get editting topic from topic id
* @param string $region "region" of researchers
* @param string $key_word researcher's name
*/
function get_rsch_table($region='', $key_word='') {
	global $wpdb;
	$sql = "SELECT id, name, region, intro, ordering
			FROM {$wpdb->prefix}rschs";
	//echo $region;
	if ( $region!='') {
		$sql = $sql . " WHERE region = '$region'";
	}
	else if ( $key_word!='' ) {
		$sql = $sql . " WHERE name = '$key_word'";
	}
	$sql = $sql . " ORDER BY ordering";
	//echo $sql;
	$rsch_array = $wpdb->get_results($sql);
	if ( !empty($rsch_array) ) {
		foreach ( $rsch_array as $r ) {
			$sql = "SELECT *
					FROM {$wpdb->prefix}post_info
					WHERE type = '2' AND info_id = '$r->id';";	// type 2 means rsch
			$r->post_num = $wpdb->query($sql);
		}
	}
	return $rsch_array;
}

/**
* Get check box contents
* @param string $type "topic" | "rsch" | "issue"
*/
function get_checkbox_contents($type) {
	global $wpdb;
	global $toptopics;
	global $regions;
	$checkbox_contents = NULL;
	if ( $type=="topic" ) {
		foreach ( $toptopics as $key => $value ) {
			//echo $key."\n";
			$sql = "SELECT id, subject
					FROM {$wpdb->prefix}topics
					WHERE category = '$key' ORDER BY ordering";
			$content = $wpdb->get_results($sql);
			$checkbox_contents[$key] = $content;
		}
	}
	else if ( $type=="rsch" ) {
		foreach ( $regions as $key => $value ) {
			//echo $key."\n";
			$sql = "SELECT id, name
					FROM {$wpdb->prefix}rschs
					WHERE region = '$key' ORDER BY ordering";
			$content = $wpdb->get_results($sql);
			$checkbox_contents[$key] = $content;
		}
	}
	else if ( $type=="issue" ) {
		$sql = "SELECT id, name
				FROM {$wpdb->prefix}issues 
				ORDER BY ordering";	
		$content = $wpdb->get_results($sql);
		$checkbox_contents[1] = $content;
	}

	return $checkbox_contents;
}

/**
* Get editting rsch from rsch id
* @param int $id "id" of "rsch"  
*/
function get_edit_rsch($id) {
	global $wpdb;
	global $toptopics;
	global $topic_checkbox_contents;
	//id, name, alias, sex, region, intro, img_pathF
	$sql = "SELECT *
			FROM {$wpdb->prefix}rschs
			WHERE id = $id";
	$rsch_array = $wpdb->get_results($sql);
	if ( empty($rsch_array) )
		die("DB connection fails!");

	// select from topics_rschs
	$sql = "SELECT topic_id
			FROM {$wpdb->prefix}topics_rschs
			WHERE rsch_id = '$id'";
	$rsch_array[0]->checked = $wpdb->get_results($sql, ARRAY_N);
	//var_dump(	$rsch_array[0]->checked );
	$rsch_array[0]->checkbox_tabs = $toptopics;
	$rsch_array[0]->checkbox_contents = $topic_checkbox_contents;
	return $rsch_array[0];
}

/**
* Add topic to database
* @param structure $topic_info 1-D array
*/
function add_topic($topic_info) {
	global $wpdb;
	$topic_name = $topic_info['name'];
	$top_topic = $topic_info['cat'];
	$topic_intro = $topic_info['intro'];
	$topic_img_path = $topic_info['img_path'];
	$sql = "INSERT INTO {$wpdb->prefix}topics
			(id, subject, category, intro, img_path) 
			VALUES(NULL, '$topic_name', '$top_topic', '$topic_intro', '$topic_img_path');";
	if ( !$wpdb->query( $sql ) ) {
		echo __FUNCTION__;
		die("DB connection fails!");
	}
	// get the topic_id
	$sql = "SELECT id
			FROM {$wpdb->prefix}topics
			WHERE subject = '$topic_name' AND category = '$top_topic';";
	$obj_topic = $wpdb->get_results($sql);
	if ( empty($obj_topic) ) {
		echo __FUNCTION__;
		die("DB connection fails!");
	}
	$tid = $obj_topic[0]->id;
	$new_checked = $topic_info['related_rschs'];
	foreach ( $new_checked as $rid) {
		//echo "add".$rid."\n";
		add_topic_rsch($tid, $rid);								
	}
}

/**
* Add rsch to database
* @param structure $rsch_info 1-D array
*/
function add_rsch($rsch_info) {
	global $wpdb;
	$rsch_name = $rsch_info['name'];
	$rsch_alias = $rsch_info['alias'];
	$rsch_sex = $rsch_info['sex'];
	$rsch_birth = $rsch_info['birth'];
	$rsch_region = $rsch_info['region'];
	$rsch_title = $rsch_info['title'];
	$rsch_experience = $rsch_info['experience'];
	$rsch_intro = $rsch_info['intro'];
	$rsch_img_path = $rsch_info['img_path'];
	$sql = "INSERT INTO {$wpdb->prefix}rschs
			(id, name, alias, sex, birth, region, title, experience, intro, img_path) 
			VALUES(NULL, '$rsch_name', '$rsch_alias','$rsch_sex','$rsch_birth', '$rsch_region', '$rsch_title', '$rsch_experience', '$rsch_intro', '$rsch_img_path');";
	if ( !$wpdb->query( $sql ) ) {
		echo __FUNCTION__;
		die("DB connection fails!");
	}
}

/**
* Add issue to database
* @param structure $issue_info 1-D array
*/
function add_issue($issue_info) {
	global $wpdb;
	$issue_name = $issue_info['name'];
	$issue_intro = $issue_info['intro'];
	$sql = "INSERT INTO {$wpdb->prefix}issues
			(id, name, intro) 
			VALUES(NULL, '$issue_name', '$issue_intro');";
	if ( !$wpdb->query( $sql ) ) {
		echo __FUNCTION__;
		die("DB connection fails!");
	}
}

/**
* Delete topic with id
* @param int $id id of topic
*/
function delete_topic($id) {
	global $wpdb;
	$sql = "DELETE FROM {$wpdb->prefix}topics
			WHERE id = $id";
	if ( !$wpdb->query($sql) )
		die("DB connection fails!");

	$sql = "DELETE FROM {$wpdb->prefix}topics_rschs
			WHERE topic_id = $id";
	$wpdb->query($sql);

	$sql = "DELETE FROM {$wpdb->prefix}post_info
			WHERE type '1' = AND info_id = '$id';";
	$wpdb->query($sql);
}

/**
* Delete rsch with id
* @param int $id id of rsch
*/
function delete_rsch($id) {
	global $wpdb;
	$sql = "DELETE FROM {$wpdb->prefix}rschs
			WHERE id = $id";
	if ( !$wpdb->query($sql) )
		die("DB connection fails!");
	$sql = "DELETE FROM {$wpdb->prefix}topics_rschs
			WHERE rsch_id = $id";
	$wpdb->query($sql);

	$sql = "DELETE FROM {$wpdb->prefix}post_info
			WHERE type '2' = AND info_id = '$id';";
	$wpdb->query($sql);
}

/**
* Delete issue with id
* @param int $id id of issue
*/
function delete_issue($id) {
	global $wpdb;
	$sql = "DELETE FROM {$wpdb->prefix}issues
			WHERE id = $id";
	if ( !$wpdb->query($sql) )
		die("DB connection fails!");

	$sql = "DELETE FROM {$wpdb->prefix}post_info
			WHERE type '3' = AND info_id = '$id';";
	$wpdb->query($sql);
}

/**
* Update topic information
* @param sturcture $t "topic" information, 1-D array
*/
function update_topic($t) {
	global $wpdb;
	$id = $t['id'];
	$topic_name = $t['sub'];
	$top_topic = $t['cat'];
	$topic_intro = $t['intro'];
	$img_path = $t['img_path'];
	$sql = "UPDATE {$wpdb->prefix}topics
			SET subject = '$topic_name', category = '$top_topic', intro = '$topic_intro', img_path = '$img_path'
			WHERE id = $id";
	//echo $sql."<br>";
	$wpdb->query($sql);

	$new_checked = $t['related_rschs'];
	// update related rschs
	$sql = "SELECT rsch_id
			FROM {$wpdb->prefix}topics_rschs
			WHERE topic_id = '$id';";
	$array_checked = $wpdb->get_results($sql, ARRAY_N);
	$old_checked = array();
	foreach( $array_checked as $key => $value ) {
		$old_checked[$key] = $value[0];
	}
	//var_dump($old_checked);
	//var_dump($new_checked);
	if ( $old_checked!=$new_checked ) {
		// delete unselected
		foreach ( $old_checked as $rid) {
			if ( !in_array($rid, (array)$new_checked) ) {
				//echo "delete".$rid."\n";
				delete_topic_rsch($id, $rid);
			}
		}
		// add new
		//echo "add new selected checkbox\n";
		foreach ( (array)$new_checked as $rid) {
			if ( !in_array($rid, $old_checked) ) {
				//echo "add".$rid."\n";
				add_topic_rsch($id, $rid);								
			}
		}
	}
	//	die("Editing topic fails!");

	//	die("Editing topic fails!");
}

/**
* Update rsch information
* @param sturcture $r "rsch" information, 1-D array
*/
function update_rsch($r) {
	global $wpdb;
	$id = $r['id'];
	$rsch_name = $r['name'];
	$rsch_alias = $r['alias'];
	$rsch_sex = $r['sex'];
	$rsch_birth = $r['birth'];
	$rsch_region = $r['region'];
	$rsch_title = $r['title'];
	$rsch_experience = $r['experience'];
	$rsch_rep = $r['rep'];
	$rsch_intro = $r['intro'];
	$rsch_img_path = $r['img_path'];

	$sql = "UPDATE {$wpdb->prefix}rschs
			SET name = '$rsch_name', alias = '$rsch_alias', sex = '$rsch_sex', 
				birth = '$rsch_birth', region = '$rsch_region', title = '$rsch_title', 
				experience = '$rsch_experience', intro = '$rsch_intro', rep = '$rsch_rep', 
				img_path = '$rsch_img_path'
			WHERE id = $id";
	//echo $sql."<br>";
	$wpdb->query($sql);
	
	$new_checked = $r['related_topics'];
	// update related topics
	$sql = "SELECT topic_id
			FROM {$wpdb->prefix}topics_rschs
			WHERE rsch_id = '$id';";
	$array_checked = $wpdb->get_results($sql, ARRAY_N);
	$old_checked = array();
	foreach( $array_checked as $key => $value ) {
		$old_checked[$key] = $value[0];
	}
//	var_dump($old_checked);
//	var_dump($new_checked);
	if ( $old_checked!=$new_checked ) {
		// delete unselected
		foreach ( $old_checked as $tid) {
			if ( !in_array($tid, (array)$new_checked) ) {
				//echo "delete".$tid."\n";
				delete_topic_rsch($tid, $id);
			}
		}
		// add new
		//echo "add new selected checkbox\n";
		foreach ( (array)$new_checked as $tid) {
			if ( !in_array($tid, $old_checked) ) {
				//echo "add".$tid."\n";
				add_topic_rsch($tid, $id);								
			}
		}
	}
	//	die("Editing topic fails!");
}

/**
* Update issue information
* @param sturcture $i "issue" information, 1-D array
*/
function update_issue($i) {
	global $wpdb;
	$id = $i['id'];
	$issue_name = $i['name'];
	$issue_intro = $i['intro'];

	$sql = "UPDATE {$wpdb->prefix}issues
			SET name = '$issue_name', intro = '$issue_intro'
			WHERE id = $id";
	//echo $sql."<br>";
	$wpdb->query($sql);

}

/**
* Save check box information to database
* @param int $post_id "post" id
*/
function CNPolitics_save_post($post_id) {
	global $wpdb;
	//global $post;
	//wp_die( 'hey' );
	if ( isset($_POST['topic_checkbox']) )
		$checked_topics = $_POST['topic_checkbox'];
	else 
		$checked_topics = NULL;
	if ( isset($_POST['rsch_checkbox']) )
		$checked_rschs = $_POST['rsch_checkbox'];
	else
		$checked_rschs = NULL;
	if ( isset($_POST['issue_checkbox']) )
		$checked_issues = $_POST['issue_checkbox'];
	else
		$checked_issues = NULL;
	//$post_id = $post->ID;
	//wp_die( var_dump($checked_rschs ) );
	//wp_die( var_dump($post_id) );
	// get old checked topics
	$sql = "SELECT info_id
			FROM {$wpdb->prefix}post_info
			WHERE type = '1' AND post_id = '$post_id';";
	$array_checked = $wpdb->get_results($sql, ARRAY_N);
	$old_checked_topics = array();
	foreach( $array_checked as $key => $value ) {
		$old_checked_topics[$key] = $value[0];
	}
	
	// get old checked rschs
	$sql = "SELECT info_id
			FROM {$wpdb->prefix}post_info
			WHERE type = '2' AND post_id = '$post_id';";
	$array_checked = $wpdb->get_results($sql, ARRAY_N);
	$old_checked_rschs = array();
	foreach( $array_checked as $key => $value ) {
		$old_checked_rschs[$key] = $value[0];
	}
	// get old checked issues
	$sql = "SELECT info_id
			FROM {$wpdb->prefix}post_info
			WHERE type = '3' AND post_id = '$post_id';";
	$array_checked = $wpdb->get_results($sql, ARRAY_N);
	$old_checked_issues = array();
	foreach( $array_checked as $key => $value ) {
		$old_checked_issues[$key] = $value[0];
	}
	// update checked topics
	if ( $old_checked_topics!=$checked_topics ) {
		// delete unselected
		foreach ( $old_checked_topics as $tid) {
			if ( !in_array($tid, $checked_topics) ) {
				//echo "delete".$tid."\n";
				delete_post_info($post_id, "topic", $tid);
			}
		}
		// add new
		//echo "add new selected checkbox\n";
		foreach ( $checked_topics as $tid) {
			if ( !in_array($tid, $old_checked_topics) ) {
				//echo "add".$tid."\n";
				add_post_info($post_id, "topic", $tid);								
			}
		}
	}
	// update checked rschs
	if ( $old_checked_rschs!=$checked_rschs ) {
		// delete unselected
		//wp_die( var_dump($post_id) );
		foreach ( $old_checked_rschs as $rid) {
			if ( !in_array($rid, $checked_rschs) ) {
				//echo "delete".$tid."\n";
				delete_post_info($post_id, "rsch", $rid);
			}
		}
		// add new
		//echo "add new selected checkbox\n";
		foreach ( $checked_rschs as $rid) {
				//wp_die( 'add new' );
			if ( !in_array($rid, $old_checked_rschs) ) {
				//echo "add".$tid."\n";
				add_post_info($post_id, "rsch", $rid);								
			}
		}
	}
	// update checked issues
	if ( $old_checked_issues!=$checked_issues ) {
		// delete unselected
		//wp_die( var_dump($post_id) );
		foreach ( $old_checked_issues as $iid) {
			if ( !in_array($iid, (array)$checked_issues) ) {
				//echo "delete".$tid."\n";
				delete_post_info($post_id, "issue", $iid);
			}
		}
		// add new
		//echo "add new selected checkbox\n";
		foreach ( (array)$checked_issues as $iid) {
				//wp_die( 'add new' );
			if ( !in_array($iid, $old_checked_issues) ) {
				//echo "add".$iid."\n";
				add_post_info($post_id, "issue", $iid);								
			}
		}
	}
}

/**
* Get issue via its id
* @param int $id issue id
*/
function get_issue_byID( $id )
{
	global $wpdb;
	$sql = "SELECT name, intro
			FROM {$wpdb->prefix}issues WHERE id = $id";
	$issue_array = $wpdb->get_results($sql);
	if ( empty($issue_array) )
		return NULL;
	else
		return $issue_array[0];
}

function get_rschs_bypostid( $post_id ) {
	global $wpdb;
	//var_dump( $rsch_regions );
	//$region_id = array_search( $rsch_region, $regions);
	//var_dump($myrsch);
	$sql = "SELECT info_id
			FROM {$wpdb->prefix}post_info
			 WHERE type = '2' AND post_id = '$post_id';";
	$rsch_id_array = $wpdb->get_results($sql);
	$rschs_array = array();
	foreach ($rsch_id_array as $r) {
		$rsch = get_rsch_byID( $r->info_id );
		if ( $rsch!=NULL )
			array_push($rschs_array, $rsch);
	}
	return $rschs_array;
}

function get_rsch_bytopic( $tid ) {
	global $wpdb;
	$rsch = array();
	//var_dump( $rsch_regions );
	//$region_id = array_search( $rsch_region, $regions);
	//var_dump($myrsch);
	$sql = "SELECT rsch_id
			FROM {$wpdb->prefix}topics_rschs
			WHERE topic_id = $tid";// LIMIT 4";
	$rsch_id_array = $wpdb->get_results($sql);
	$key = 0;
	foreach ( $rsch_id_array as $rid ) {
		$rid = $rid->rsch_id;
		$sql = "SELECT name, id
				FROM {$wpdb->prefix}rschs
				WHERE id = $rid";
		$rsch_obj_array = $wpdb->get_results($sql);
		if ( empty($rsch_obj_array) )
			die("Wrong researcher id in get_rsch_bytopic");
		$rsch_obj = $rsch_obj_array[0];
		$rsch[$key] = $rsch_obj;
		$key = $key + 1;
	}
	return $rsch;
}

function get_rsch_byID( $id )
{
	global $wpdb;
	$sql = "SELECT id, name, alias, sex, birth, region, title, experience, rep, intro, img_path
			FROM {$wpdb->prefix}rschs WHERE id = $id";
	$rsch_array = $wpdb->get_results($sql);
	if ( empty($rsch_array) )
		return NULL;
	else
		return $rsch_array[0];
}

function get_rschs( $region_id ) {
	global $wpdb;
	global $regions;
	//var_dump( $rsch_regions );
	//$region_id = array_search( $rsch_region, $regions);
	//var_dump($myrsch);
	$sql = "SELECT name, id
			FROM {$wpdb->prefix}rschs
			WHERE region = $region_id ORDER BY ordering ASC";// LIMIT 4";
	$rsch_array = $wpdb->get_results($sql);
	//if ( empty($rsch_array) )
	//	die("DB connection fails!");
	//var_dump( $rsch_array );
	return $rsch_array;	
}

function get_checked_id($post_id, $type) {
	global $wpdb;
	if ( $type=="topic")
		$type_value = 1;
	else if ( $type=="rsch" )
		$type_value = 2;
	else 
		$type_value = 3;

	$sql = "SELECT info_id
			FROM {$wpdb->prefix}post_info
			WHERE type = '$type_value' AND post_id = '$post_id';";
	$checked_array_array = $wpdb->get_results($sql, ARRAY_N);
	if ( empty($checked_array_array) )
		return NULL;
	else {
		foreach ( $checked_array_array as $key => $checked_array ) {
			$checked[$key] = $checked_array[0]; 
		}
		return $checked;
	}
}

function get_topics_bypostid( $post_id ) {
	global $wpdb;
	$sql = "SELECT info_id
			FROM {$wpdb->prefix}post_info
			 WHERE type = '1' AND post_id = '$post_id';";
	$topic_id_array = $wpdb->get_results($sql);
	
	$topics_array = array();
	foreach ($topic_id_array as $t) {
		$topic = get_topic_byID( $t->info_id );
		if ( $topic!=NULL )
			array_push($topics_array, $topic);
	}
	return $topics_array;
}

function get_topic_byrsch( $rid ) {
	global $wpdb;
	$sql = "SELECT topic_id
			FROM {$wpdb->prefix}topics_rschs
			WHERE rsch_id = $rid";// LIMIT 4";
	$topic_id_array = $wpdb->get_results($sql);
	$key = 0;
	if ( empty($topic_id_array) )
		$topic = '';
	else {
		foreach ( $topic_id_array as $tid ) {
			$tid = $tid->topic_id;
			$sql = "SELECT subject, id
					FROM {$wpdb->prefix}topics
					WHERE id = $tid";
			$topic_obj_array = $wpdb->get_results($sql);
			if ( empty($topic_obj_array) )
				die("Wrong topic id in get_topic_byrsch");
			$topic_obj = $topic_obj_array[0];
			$topic[$key] = $topic_obj;
			$key = $key + 1;
		}
	}
	return $topic;
}

function get_topic_byID( $id ) {
	global $wpdb;
	$sql = "SELECT id, subject, intro, img_path
			FROM {$wpdb->prefix}topics WHERE id = '$id';";
	$topic_array = $wpdb->get_results($sql);
	if ( empty($topic_array) )
		return NULL;
	else
		return $topic_array[0];
}

function get_topics( $category ) {
	global $wpdb;
	global $toptopics;
	//var_dump( $toptopic_cat );
	//$toptopic_id = array_search( $toptopic_cat, $toptopics);
	//var_dump($toptopic_id);
	$sql = "SELECT subject, id
			FROM {$wpdb->prefix}topics
			WHERE category = $category  ORDER BY ordering ASC";// LIMIT 4";
	$topic_array = $wpdb->get_results($sql);
	//if ( empty($rsch_array) )
	//	die("DB connection fails!");
	//var_dump( $rsch_array );
	return $topic_array;
}

function is_topic_exist( $topic_name ) {
	global $wpdb;
	//echo "Search $topic_name in topics<br>";
	$sql = "SELECT *
			FROM {$wpdb->prefix}topics
			WHERE subject = '$topic_name'";
	$results = $wpdb->get_results($sql);
	//var_dump($topic_name);
	//echo "<br>";
	//var_dump($results);
	//var_dump($results[0]->subject);
	//if ( $topic_name==$results[0]->subject )
	//	echo "equal<br>";
	if ( empty($results) )
		return false;
	else
		return true;
}

function is_rsch_exist( $rsch_name ) {
	global $wpdb;
	//echo "Search $rsch_name in rschs<br>";
	$sql = "SELECT *
			FROM {$wpdb->prefix}rschs
			WHERE name = '$rsch_name'";
	$results = $wpdb->get_results($sql);
	if ( empty($results) )
		return false;
	else
		return true;
}

function is_issue_exist( $issue_name ) {
	global $wpdb;
	//echo "Search $issue_name in rschs<br>";
	$sql = "SELECT *
			FROM {$wpdb->prefix}issues
			WHERE name = '$issue_name'";
	$results = $wpdb->get_results($sql);
	if ( empty($results) )
		return false;
	else
		return true;
}

function add_topic_rsch($tid, $rid) {
	global $wpdb;
	$sql = "INSERT INTO {$wpdb->prefix}topics_rschs
			(id, topic_id, rsch_id) 
			VALUES(NULL, '$tid', '$rid');";
	if ( !$wpdb->query( $sql ) ) {
		echo __FUNCTION__;
		die("DB connection fails!");
	}
}

function delete_topic_rsch($tid, $rid) {
	global $wpdb;
	$sql = "DELETE FROM {$wpdb->prefix}topics_rschs
			WHERE topic_id = '$tid' AND rsch_id = '$rid';";
	if ( !$wpdb->query($sql) ) {
		echo __FUNCTION__;
		die("DB connection fails!");
	}
}

function delete_post_info($post_id, $type, $id) {
	global $wpdb;
	if ( $type=="topic" ) {
		$type_value = 1;
	}
	else if ( $type=="rsch" ) {
		$type_value = 2;
	}
	else if ( $type=="issue" ) {
		$type_value = 3;
	}
	$sql = "DELETE FROM {$wpdb->prefix}post_info
			WHERE type = '$type_value' AND post_id = '$post_id' AND info_id = '$id';";
	$wpdb->query($sql);
}

function add_post_info($post_id, $type, $id) {
	global $wpdb;
	//var_dump($type);
	if ( $type=="topic" ) {
		$type_value = 1;
	}
	else if ( $type=="rsch" ) {
		$type_value = 2;
	}
	else if ( $type=="issue" ) {
		$type_value = 3;
	}
	$sql = "INSERT INTO {$wpdb->prefix}post_info
			(id, type, post_id, info_id) 
			VALUES(NULL, '$type_value', '$post_id', '$id');";
	//echo $sql;
	$wpdb->query($sql);
}

function get_postid_byrschid($rsch_id) {
	global $wpdb;
	//echo $rsch_id;
	$sql = "SELECT post_id
			FROM {$wpdb->prefix}post_info WHERE type = '2' AND info_id = '$rsch_id';";
	$pid_obj_array = $wpdb->get_results($sql);
	$pid_array = NULL;
	foreach ( $pid_obj_array as $key => $pid ) {
		$pid_array[$key] = $pid->post_id;
	}
	//var_dump($pid_array);
	return $pid_array;
}

function get_postid_bytopicid($topic_id) {
	global $wpdb;
	//echo $rsch_id;
	$sql = "SELECT post_id
			FROM {$wpdb->prefix}post_info WHERE type = '1' AND info_id = '$topic_id';";
	$pid_obj_array = $wpdb->get_results($sql);
	$pid_array = NULL;
	foreach ( $pid_obj_array as $key => $pid ) {
		$pid_array[$key] = $pid->post_id;
	}
	//var_dump($pid_array);
	return $pid_array;
}

function get_postid_all() {
	global $wpdb;
	$sql = "SELECT ID
			FROM $wpdb->posts
			LEFT JOIN $wpdb->term_relationships ON
			($wpdb->posts.ID = $wpdb->term_relationships.object_id)
			LEFT JOIN $wpdb->term_taxonomy ON
			($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			WHERE $wpdb->posts.post_status = 'publish'
			AND $wpdb->term_taxonomy.taxonomy = 'category'
			ORDER BY post_date DESC";
	$pid_obj_array = $wpdb->get_results($sql);
	$pid_array = NULL;
	foreach ( $pid_obj_array as $key => $pid ) {
		$pid_array[$key] = $pid->ID;
	}
	//var_dump($pid_array);
	return $pid_array;
}

function get_postid_bycatid($cat_id) {
	global $wpdb;
	$sql = "SELECT ID
			FROM $wpdb->posts
			LEFT JOIN $wpdb->term_relationships ON
			($wpdb->posts.ID = $wpdb->term_relationships.object_id)
			LEFT JOIN $wpdb->term_taxonomy ON
			($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			WHERE $wpdb->posts.post_status = 'publish'
			AND $wpdb->term_taxonomy.taxonomy = 'category'
			AND $wpdb->term_taxonomy.term_id = '$cat_id'
			ORDER BY post_date DESC";
	$pid_obj_array = $wpdb->get_results($sql);
	$pid_array = NULL;
	foreach ( $pid_obj_array as $key => $pid ) {
		$pid_array[$key] = $pid->ID;
	}
	//var_dump($pid_array);
	return $pid_array;
}

function get_issues_bypostids($pid_array) {
	global $wpdb;
	$issue_array = array();
	if ( empty($pid_array) ) 	return $issue_array;
	foreach ( $pid_array as $pid ) {
		//$mypost = get_post( $pid );
		$issue = get_issue_bypostid($pid);
		array_push($issue_array, $issue);	
	}
	$issue_array = array_filter($issue_array, 'strlen');
	$issue_array = array_unique($issue_array);
	$issue_array = array_values($issue_array);
	return $issue_array;
}

/**
* @param pid post id
*/
function get_issue_bypostid($pid) {
	global $wpdb;
	$sql = "SELECT info_id
			FROM {$wpdb->prefix}post_info
			 WHERE type = '3' AND post_id = '$pid' LIMIT 1;";
	$issue_id_array = $wpdb->get_results($sql);
	if ( !empty($issue_id_array) ) {
		$issue = get_issue_byID( $issue_id_array[0]->info_id );
		return $issue->name;
	}
	else
		return NULL;
}

function get_authorids_bypostids($pid_array) {
	global $wpdb;
	$authorid_array = array();
	if ( empty($pid_array) ) 	return $authorid_array;
	foreach ( $pid_array as $pid ) {
		$mypost = get_post( $pid );
		if ( is_null($mypost) ) {
			echo "In ". __FUNCTION__ ." the post id: ". $pid . " is deleted. Please clean the post_info table.<br>";
		}
		else 
			array_push($authorid_array, $mypost->post_author);	
	}

	$authorid_array = array_filter($authorid_array, 'strlen');
	$authorid_array = array_unique($authorid_array);
	$authorid_array = array_values($authorid_array);
	return $authorid_array;
}

function get_quarters_bypostids($pid_array) {
	global $wpdb;
	$quarter_array = array();
	if ( empty($pid_array) ) 	return $quarter_array;
	foreach ( $pid_array as $pid ) {
		$mypost = get_post( $pid );
		if ( is_null($mypost) ) {
			echo "In ". __FUNCTION__ ." the post id: ". $pid . " is deleted. Plese clean the post_info table.<br>";
		}
		else
			array_push( $quarter_array,  substr($mypost->post_date, 0, 7) );
	}

	$quarter_array = array_filter($quarter_array, 'strlen');
	$quarter_array = array_unique($quarter_array);
	$quarter_array = array_values($quarter_array);
	arsort($quarter_array);
	foreach ( $quarter_array as $key => $quarter ) {
		$mon = substr($quarter, 5,6);
		if ( $mon>0 && $mon<=3) {
			$quarter_array[$key] = substr($quarter, 0,4)."年春季";
		}
		else if ( $mon>3 && $mon<=6) {
			$quarter_array[$key] = substr($quarter, 0,4)."年夏季";
		}
		else if ( $mon>6 && $mon<=9) {
			$quarter_array[$key] = substr($quarter, 0,4)."年秋季";
		}
		else if ( $mon>9 && $mon<=12) {
			$quarter_array[$key] = substr($quarter, 0,4)."年冬季";
		}
	}
	$quarter_array = array_unique($quarter_array);
	$quarter_array = array_values($quarter_array);
	return $quarter_array;
}

/**
* @param pid_array
* @param issue
* @param authorid
* @param quarter
*/
function pid_filter($pid_array, $issue, $authorid, $quarter) {
	//var_dump($issue);
	//var_dump($authorid);
	//var_dump($quarter);
	//echo strlen($quarter);
	if ( !empty($quarter) ) {
		$year_sel = substr($quarter, 0, 4);
		$season = substr($quarter,7);
		//var_dump($year_sel);
		if ( $season=="春季" ){
			$mon_min = "01";
			$mon_max = "03";		
		}
		else if ( $season=="夏季" ){
			$mon_min = "04";
			$mon_max = "06";		
		}
		else if ( $season=="秋季" ){
			$mon_min = "07";
			$mon_max = "09";
		}
		else if ( $season=="冬季" ){
			$mon_min = "10";
			$mon_max = "12";		
		
		}
		//var_dump($mon_min);
		//var_dump($mon_max);
	}
	foreach ( (array)$pid_array as $key => $pid ) {
		$mypost = get_post( $pid );
		// filter accordomg to issue
		//echo $issue;
		//echo get_issue_bypostid($pid);
		if ( !empty($issue) && $issue!=get_issue_bypostid($pid) ) {
			unset($pid_array[$key]);
			continue;
		}
		// filter according to author id
		if ( !empty($authorid) && $authorid!=$mypost->post_author ) {
			unset($pid_array[$key]);
			continue;
		}
		// filter according to quarter
		if ( !empty($quarter) ) {
			//echo $mypost->post_date."<br>";
			$date = substr($mypost->post_date,0,7);
			$year = substr($date, 0, 4);
			$mon = substr($date, 5, 6);
			if ( $year!=$year_sel || $mon<$mon_min || $mon>$mon_max) {
				unset($pid_array[$key]);
				continue;
			}
		}
	}
	$pid_array = array_values((array)$pid_array);
	//var_dump($pid_array);
	return $pid_array;
}

/**
* @param $region: "region" of researchers
* @param $key_word: researcher's name
*/
function get_issue_table($region='', $key_word='') {
	global $wpdb;
	$sql = "SELECT id, name, intro, ordering
			FROM {$wpdb->prefix}issues";
	//echo $region;
	if ( $key_word!='' ) {
		$sql = $sql . " WHERE name = '$key_word'";
	}
	$sql = $sql . " ORDER BY ordering";
	//echo $sql;
	$issue_array = $wpdb->get_results($sql);
	if ( !empty($issue_array) ) {
		foreach ( $issue_array as $i ) {
			$sql = "SELECT *
					FROM {$wpdb->prefix}post_info
					WHERE type = '3' AND info_id = '$i->id';";	// type 3 means issue
			$i->post_num = $wpdb->query($sql);
		}
	}
	return $issue_array;
}

function get_edit_issue($id) {
	global $wpdb;
	//id, name, alias, sex, region, intro, img_pathF
	$sql = "SELECT *
			FROM {$wpdb->prefix}issues
			WHERE id = $id";
	$issue_array = $wpdb->get_results($sql);
	if ( empty($issue_array) )
		die("DB connection fails!");

	return $issue_array[0];
}
?>