<?php

/**
* HTML, display the options to be selected (region, toptopic)
* @param  $tag_name name of select
* @param int $selected selected option
* @param int $opt_array options array
*/
function option_select($tag_name, $selected, $opt_array) {
	echo '<select name="'.$tag_name.'" id="'. $tag_name.'" class="postform">';
	if ( $selected=="" )
		echo '<option class="level-0" value="" selected>All</option>';
	foreach ( $opt_array as $key => $value ) {
		if ( $selected==$key) {
			echo '<option class="level-0" value="'.$key.'" selected>'.$value.'</option>';
		}					
		else	
			echo '<option class="level-0" value="'.$key.'">'.$value.'</option>';		
	}
	echo '</select>';
}

/**
* @param $tag_id: name of select
* @param $selected: selected option
* @param $opt_array: option array
*/
function filter_option_select($tag_name, $selected, $opt_array) {
	echo '<select name="'.$tag_name.'" id="'. $tag_name.'" class="postform">
			<option class="level-0" value="" selected>All</option>';
	foreach ( $opt_array as $key => $value ) {
		if ( $selected==$key) {
			echo '<option class="level-0" value="'.$key.'" selected>'.$value.'</option>';
		}					
		else	
			echo '<option class="level-0" value="'.$key.'">'.$value.'</option>';		
	}
	echo '</select>';
}

/**
* Display topic editing page
* @param object $t: topic information
*/
function edit_topic_disp($t) {
	global $page_setting_uri;
?>
<div class="wrap">
	<div id="icon-edit" class=icon32><br></div>
	<h2>Topics</h2>
	<div  align="center">
		<!--img src="<?php echo get_bloginfo('template_directory');?>/images/researcher-avatar.png"-->
		<img src="<?php echo get_bloginfo('template_directory') . $t->img_path;?>" style="width:150px; height=150px; border-radius:50%;">		
	</div>	
	<form name="edittopic" id="edittopic" enctype="multipart/form-data" method="post" action="<?php echo $page_setting_uri;?>" class="validate">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="id", value="<?php echo $t->id;?>">
		<input type="hidden" name="img-path", value="<?php echo $t->img_path;?>">
		<table class="form-table">
		<tbody>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="topic-name">Name</label></th>
				<td><input name="topic-name" id="topic-name" type="text" value="<?php echo $t->subject ?>" size="40" aria-required="true">
				<p class="description">Use 4 Chinese characters.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="top-topic">Top level topic</label></th>
				<td><?php global $toptopics; option_select("top-topic", $t->category, $toptopics);?>
				<p class="description">Currentlly, the top level topics are fixed.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="topic-intro">Description</label></th>
				<td><textarea name="topic-intro" id="topic-intro" rows="5" cols="50" class="large-text"><?php echo $t->intro;?></textarea><br>
				<span class="description">The description is not prominent by default; however, some themes may show it.</span></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-img">Related researchers</label></th>
				<td><?php check_box("rsch", $t->checkbox_tabs, $t->checkbox_contents); ?>
				<p class="description">The description is not prominent by default; however, some themes may show it.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="Image">Image</label></th>
				<td><input name="topic-img" id="topic-img" type="file">
				<p class="description">The description is not prominent by default; however, some themes may show it.</p></td>
			</tr>
		</tbody>
		</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Update">
			</p>
	</form>
</div>
<?php
}

/**
* Display topic setting page
* @param table $topic_table
*/
function topic_setting_disp($topic_table, $filter_cat='') {
	global $page_setting_uri;
	$tmp = explode('=', $page_setting_uri);
	$page_type = $tmp[1];
?>
	<div class="wrap nosubsub">
		<div id="icon-edit" class=icon32><br></div>
		<h2>Topics</h2>
		<!--?php global $toptopics; echo $toptopics['1'];?-->
		<!--?php echo $_SERVER['REQUEST_URI'];?-->
		<!--?php echo $page_setting_uri; ?-->
		<!--?php echo $page_type; ?-->
		<form class="search-form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="get">
			<input type="hidden" name="page" value="<?php echo $page_type;?>">
			<input type="hidden" name="action" value="search">
			<p class="search-box"><label class="screen-reader-text" for="tag-search-input">Search Topics:</label>
			<input type="search" id="tag-search-input" name="s" value="">
			<input type="submit" name="" id="search-submit" class="button" value="Search Topics"></p>
		</form>
		<br class="clear">
		<div id="col-container">
			<div id="col-right">
			<div class="col-wrap">
			<div class="form-wrap">
				<?php right_col_disp("topic", $topic_table, $filter_cat);?>
			</div>
			</div>
			</div>
			<div id="col-left">
			<div class="col-wrap">
			<div class="form-wrap">
				<h3>Add New Subtopic</h3>
				<?php topic_col_left_disp();?>
			</div>
			</div>
			</div>
		</div>
	</div>
<?php
}

/*
* Display left column of topic setting
*/
function topic_col_left_disp() {
	global $page_setting_uri;
	global $regions;
	global $rsch_checkbox_contents;
?>
	<!--?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?-->
	<form id="addsubtopic" enctype="multipart/form-data" method="post" action="<?php echo $page_setting_uri;?>" class="validate">
	<input type="hidden" name="action" value="add">
	<div class="form-field form-required">
		<label for="topic-name">Topic Name</label>
		<input name="topic-name" id="topic-name" type="text" value="" size="40" aria-required="true">
		<p>Use 4 Chinese characters.</p>
	</div>
	<div class="form-field">
		<label for="topic-intro">Introduction</label>
		<textarea name="topic-intro" id="topic-intro" rows="5" cols="40"></textarea>
		<p>The introduction should not exceed 100 words.</p>
	</div>
	<div class="form-field">
		<label for="top-topic">Top level topic</label>
		<?php global $toptopics; option_select("top-topic", 1, $toptopics);?>
	</div>
	<div class="form-field">
		<label for="topic-rsch">Related researchers</label>
		<?php check_box("rsch", $regions, $rsch_checkbox_contents); ?>
		<p>Focused area of the researcher</p>
	</div>
	<div class="form-field">
		<label for="topic-image">Image for this topic</label>
		<input name="topic-img" id="topic-img" type="file">
	</div>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Subtopic">
	</p>
	</form>
<?php
}

/**
* Display right column 
* @param string $type "topic" or "rsch" or "issue"
* @param structure $table information to display
* @param int $filter_cat selected category to filter
*/
function right_col_disp($type, $table, $filter_cat='') {
	global $wpdb;
	global $page_setting_uri;

	/* setting_page_type: topic setting or researcher setting */
	$tmp = explode('=', $page_setting_uri);
	$setting_page_type = $tmp[1];

	/* pagination */
	$paged = 1;
	if ( isset($_GET['paged']) )
		$paged = $_GET['paged'];
	$table_page_size = 10;
	if ( $filter_cat!=NULL )
		$page_uri = $page_setting_uri ."&action=filter&filter-tag=". $filter_cat;
	else
		$page_uri = $page_setting_uri;
	//echo $page_uri . "\n";
	$total_page_num = ceil( count($table) / $table_page_size);
	
	echo '<div class="tablenav top">
			<form action="'. $_SERVER['REQUEST_URI'] .'" method="get"">
			<input type="hidden" name="page" value="'. $setting_page_type. '">
			<input type="hidden" name="action" value="filter">';
	if ( $type=="topic") {	
		global $toptopics;
		filter_option_select("filter-tag", $filter_cat, $toptopics);
	}
	if ($type=="rsch") {
		global $regions;
		filter_option_select("filter-tag", $filter_cat, $regions);
	}
	if ($type!="issue")	{
		echo '	<input type="submit" name="" id="doaction" class="button action" value="Filter">';
	}
	echo'	</form>
		  </div>';
	table_body_disp($type, array_slice($table, ($paged-1)*$table_page_size, $table_page_size), $filter_cat, $paged);
	$visibility_prev_page = $visibility_next_page = '';
	$link_first_page = $page_uri.'&paged=1';
	$link_prev_page = $page_uri.'&paged='.($paged-1);
	$link_next_page = $page_uri.'&paged='.($paged+1);
	$link_last_page = $page_uri.'&paged='.$total_page_num;
	if ( $paged==1 ) {
		$visibility_prev_page = "disabled";
		$link_first_page = $link_prev_page = "";
	}
	if ( $paged==$total_page_num ) {
		$visibility_next_page = "disabled";
		$link_next_page = $link_last_page = "";
	}
	echo '<div class="tablenav bottom">
			<div class="tablenav-pages">
				<span class="displaying-num">' . count($table) . ' items</span>
				<span class="pagination-links">
					<a class="first-page ' . $visibility_prev_page .'" title="Go to the first page" href="'.$link_first_page.'">«</a>
					<a class="prev-page ' . $visibility_prev_page .'" title="Go to the previous page" href="'.$link_prev_page.'">‹</a>
					<span class="paging-input">' . $paged .' of <span class="total-pages">' . $total_page_num. '</span></span>
					<a class="next-page ' . $visibility_next_page . '" title="Go to the next page" href="'.$link_next_page.'">›</a>
					<a class="last-page ' . $visibility_next_page . '" title="Go to the last page" href="'.$link_last_page.'">»</a>	
				</span>
			</div>
		  </div>';
}

/**
* Display information in the table
* @param string $page_type "topic" or "researcher" or "issue"
* @param structure $table table information
*/
function table_body_disp($page_type, $table, $filter_cat, $paged) {
	global $wpdb;
	global $show_cat;
	global $page_setting_uri;
	global $toptopics;
	global $regions;
	if ($page_type=="rsch") {
		$column_2 = "Region";
		$view_dir = "researcher";
		$id_type = "rsch_id";
	}
	else if ($page_type=="topic") {
		$column_2 = "Category";
		$view_dir = "topic";
		$id_type = "topic_id";
	}
	else if ($page_type=="issue") {
		$column_2 = "";
		$id_type = "issue_id";
	}
	$sub_array = $table;
	//var_dump($sub_array);
	$count = 0;
	$page_uri = $page_setting_uri;
	//echo $page_uri;
?>
		<table class="wp-list-table widefat fixed tags" cellspacing="0">
			<thead><tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
					<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
					<input id="cb-select-all-1" type="checkbox">
				</th>
				<th scope="col" id="name" class="manage-column column-name" style>
					<a>
					<span>Name</span>
					<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" id="description" class="manage-column column-description" style>
					<a>
					<span>Description</span>
					<span class="sorting-indicator"></span>
					</a>	
				</th>
				<th scope="col" id="slug" class="manage-column column-slug" style>
					<a>
					<span><?php echo $column_2;?></span>
					<span class="sorting-indicator"></span>
					</a>	
				</th>
				<th scope="col" id="posts" class="manage-column column-posts" style>
					<a>
					<span>Posts</span>
					<span class="sorting-indicator"></span>
					</a>
				</th>
			</tr>
			</thead>
			<tfoot>
				<tr><th colspan=5 scope="col" id="pagenav" class="manage-column" style><br></th>
				</tr>
			</tfoot>
			<tbody id="the-list">
				<?php if ( empty($sub_array) ) 
						echo "<tr><td colspan=5>No items found.<td></tr>";
				?>
				<?php foreach ( $sub_array as $s) {?>
			<tr>
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="cb-select-7">Select 学人自述 </label>
				<input type="checkbox" name="delete_tags[]" value="7" id="cb-select-7">
			</th>
			<td class="description column-name">
				<strong><a class="row-title" href="<?php echo $page_uri.'&action=edit&id='.$s->id; ?>">
<?php 
	if ($page_type=="rsch")	_e($s->name); 
	else if ($page_type=="topic")	_e($s->subject);
	else if ($page_type=="issue")	_e($s->name);
?>
					</a>
				</strong>
				<div class="row-actions">
					<span class="edit"><a href="<?php echo $page_uri.'&action=edit&id='.$s->id; ?>">Edit</a></span>
					<span class="delete"> | <a href="<?php echo $page_uri.'&action=delete&id='.$s->id.'&paged='.$paged; ?>">Delete</a></span>
<?php 
	if ($page_type!="issue") {
		echo '		<span class="view"> | <a href="';
		echo 			site_url()."/".$view_dir."?".$id_type."=".$s->id; 
		echo '		">View</a></span>';
	}
?>
				</div>
				<div class="row-actions">
				<?php 
					$move_order_uri = $page_uri.'&position=move&action=filter&filter-tag='.$filter_cat.'&paged='.$paged.'&order_no='.$s->ordering.'&id='.$s->id;
					if ($page_type=="topic") {
						$move_order_uri = $move_order_uri.'&cat='.$s->category;
					}
					else if ($page_type=="rsch") {
						$move_order_uri = $move_order_uri.'&cat='.$s->region;
					}
				?>
					<span class="top">
					<a href="<?php echo $move_order_uri.'&toward=top';?>">Top</a> | 
					</span>
					<span class="bottom">
					<a href="<?php echo $move_order_uri.'&toward=bottom';?>">Bottom</a> | 
					</span>
					<span class="up">
					<a href="<?php echo $move_order_uri.'&toward=up';?>">Up</a> | 
					</span>
					<span class="down"><a href="<?php echo $move_order_uri.'&toward=down';?>">Down</a>						</span>
				</div>
			</td>
			<td class="description column-description"><?php _e($s->intro);?></td>
			<td class="slug column-slug">
			<?php 
				if ($page_type=="topic") 
					_e($toptopics[$s->category]); 
				else if ($page_type=="rsch") 	
					_e($regions[$s->region]);
				else if ($page_type=="issue")
					;
			?>
			</td>
			<td class="posts column-posts"><a href="#"><?php echo $s->post_num;?></a></td>
			</tr>
<?php
			}	// end of foreach						
	echo '	</tbody>';
	echo '</table>';
}	// end of function

/**
* Display research setting page
* @param structure $rsch_table table information
*/
function rsch_setting_disp($rsch_table, $filter_cat='') {
	global $page_setting_uri;
	$tmp = explode('=', $page_setting_uri);
	$page_type = $tmp[1];
?>
	<div class="wrap nosubsub">
		<div id="icon-edit" class=icon32><br></div>
		<h2>Researchers</h2>
		<form class="search-form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="get">
			<input type="hidden" name="page" value="<?php echo $page_type;?>">
			<input type="hidden" name="action" value="search">
			<p class="search-box"><label class="screen-reader-text" for="tag-search-input">Search Researchers:</label>
			<input type="search" id="tag-search-input" name="s" value="">
			<input type="submit" name="" id="search-submit" class="button" value="Search Researchers"></p>
		</form>
		<br class="clear">
		<div id="col-container">
			<div id="col-right">
			<div class="col-wrap">
			<div class="form-wrap">
				<?php right_col_disp("rsch", $rsch_table, $filter_cat);?>
			</div>
			</div>
			</div>
			<div id="col-left">
			<div class="col-wrap">
			<div class="form-wrap">
				<h3>Add New Researcher</h3>
				<?php rsch_col_left_disp();?>
			</div>
			</div>
			</div>
		</div>
	</div>
<?php
}

/**
* Display left column of research setting page
*/
function rsch_col_left_disp() {
	global $toptopics;
	global $page_setting_uri;
	global $topic_checkbox_contents;
?>
	<form id="addrsch" enctype="multipart/form-data" method="post" action="<?php echo $page_setting_uri;?>" class="validate">
	<input type="hidden" name="action" value="add">
	<div class="form-field form-required">
		<label for="rsch-name">Researcher Name</label>
		<input name="rsch-name" id="rsch-name" type="text" value="" size="40" aria-required="true">
		<p>The name is how it appears on your site.</p>
	</div>
	<div class="form-field">
		<label for="rsch-alias">Alias</label>
		<input name="rsch-alias" id="rsch-alias" type="text" value="" size="40">
		<p>Alias is the original name or nick name of the researcher.</p>
	</div>
	<div class="form-field">
		<label for="rsch-sex">Gender</label>
		<select name="rsch-sex" id="rsch-sex" class="postform">
			<option value="0">Not sure</option>
			<option class="level-0" value="1">Male</option>
			<option class="level-0" value="2">Female</option>		
		</select>
		<p>The default value is not sure.</p>
	</div>
	<div class="form-field">
		<label for="rsch-birth">Date of Birth</label>
		<input name="rsch-birth" id="rsch-birth" type="date" value="" size="40">
		<p>The “Date of Birth” can be left empty.</p>
	</div>
	<div class="form-field">
		<label for="rsch-title">Title</label>
		<input name="rsch-title" id="rsch-title" type="text" value="" size="40">
		<p>The current title.</p>
	</div>
	<div class="form-field">
		<label for="rsch-experience">Experiences</label>
		<!--input name="slug" id="topic-slug" type="text" value="" size="40"-->
		<textarea name="rsch-experience" id="rsch-experience" rows="5" cols="40"></textarea>
		<p>The experiences should be related to his/her research.</p>
	</div>
	<div class="form-field">
		<label for="rsch-rep">Seleted publications</label>
		<!--input name="slug" id="topic-slug" type="text" value="" size="40"-->
		<textarea name="rsch-rep" id="rsch-rep" rows="5" cols="40"></textarea>
		<p>Separated by comma.</p>
	</div>
	<div class="form-field">
		<label for="rsch-intro">Introduction</label>
		<textarea name="rsch-intro" id="rsch-intro" rows="5" cols="40"></textarea>
		<p>The description is not prominent by default; however, some themes may show it.</p>
	</div>
	<div class="form-field">
		<label for="rsch-region">Region</label>
		<?php global $regions; option_select("rsch-region", 1, $regions);?>
	</div>
	<div class="form-field">
		<label for="rsch-topic">Related topics</label>
		<?php check_box("topic", $toptopics, $topic_checkbox_contents); ?>
		<p>Focused area of the researcher</p>
	</div>	
	<div class="form-field">
		<label for="rsch-img">Image</label>
		<input name="rsch-img" id="rsch-img" type="file"  size="40">
		<p>Image for this topic</p>
	</div>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Researcher">
	</p>
	</form>

<?php
}

/**
* Display rsch editing page
* @param structure $r rsch information
*/
function edit_rsch_disp($r) {
	global $page_setting_uri;
	global $regions;
?>
<div class="wrap">
	<div id="icon-edit" class=icon32><br></div>
	<h2>Researcher</h2>
	<div  align="center">
		<img src="<?php echo get_bloginfo('template_directory') . $r->img_path;?>" style="width:150px; height=150px; border-radius:50%;">
	</div>	
	<form name="editrsch" id="editrsch" enctype="multipart/form-data" method="post" action="<?php echo $page_setting_uri;?>" class="validate">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="id", value="<?php echo $r->id;?>">
		<input type="hidden" name="img-path", value="<?php echo $r->img_path;?>">
		<table class="form-table">
		<tbody>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="rsch-name">Name</label></th>
				<td><input name="rsch-name" id="rsch-name" type="text" value="<?php echo $r->name; ?>" size="40" aria-required="true">
				<p class="description">The name is how it appears on your site.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-alias">Alias</label></th>
				<td><input name="rsch-alias" id="rsch-alias" type="text" value="<?php echo $r->alias; ?>" size="40" aria-required="true">
				<p class="description">Alias is the original name or nick name of the researcher.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-sex">Gender</label></th>
				<td><?php global $sexes; option_select("rsch-sex", $r->sex, $sexes);?>
				<p class="description">Gender of researcher.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-birth">Date of Birth</label></th>
				<td><input name="rsch-birth" id="rsch-birth" type="date" value="<?php echo $r->birth;?>" size="40">
				<p>The “Date of Birth” can be left empty.</p>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-title">Title</label></th>
				<td><input name="rsch-title" id="rsch-title" type="text" value="<?php echo $r->title; ?>" size="40">
				<p>The current title.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-experience">Experiences</label></th>
				<td><textarea name="rsch-experience" id="rsch-experience" rows="5" cols="50" class="large-text"><?php echo $r->experience;?></textarea><br>
				<p>The experiences should be related to his/her research.</p></td>
			</tr>

			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-rep">Seleted publications</label></th>
				<td><textarea name="rsch-rep" id="rsch-rep" rows="5" cols="50" class="large-text"><?php echo $r->rep;?></textarea><br>
				<p>Separated by comma.</p></td>
			</tr>

			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-intro">Introduction</label></th>
				<td><textarea name="rsch-intro" id="rsch-intro" rows="5" cols="50" class="large-text"><?php echo $r->intro;?></textarea><br>
				<span class="description">The description is not prominent by default; however, some themes may show it.</span></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-region">Region</label></th>
				<td><?php global $regions; option_select("rsch-region", $r->region, $regions);?>
				<p class="description">Currentlly, the regions are fixed.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-img">Related topics</label></th>
				<td><?php check_box("topic", $r->checkbox_tabs, $r->checkbox_contents); ?>
				<p class="description">The description is not prominent by default; however, some themes may show it.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="rsch-img">Image</label></th>
				<td><input name="rsch-img" id="rsch-img" type="file">
				<p class="description">The description is not prominent by default; however, some themes may show it.</p></td>
			</tr>
		</tbody>
		</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Update">
			</p>
	</form>
</div>
<?php
}

/**
* Display issue setting page
* @param structure $issue_table issue information
*/
function issue_setting_disp($issue_table, $filter_cat='') {
	global $page_setting_uri;
	$tmp = explode('=', $page_setting_uri);
	$page_type = $tmp[1];
?>
	<div class="wrap nosubsub">
		<div id="icon-edit" class=icon32><br></div>
		<h2>Special Issues</h2>
		<form class="search-form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="get">
			<input type="hidden" name="page" value="<?php echo $page_type;?>">
			<input type="hidden" name="action" value="search">
			<p class="search-box"><label class="screen-reader-text" for="tag-search-input">Search Issues:</label>
			<input type="search" id="tag-search-input" name="s" value="">
			<input type="submit" name="" id="search-submit" class="button" value="Search Researchers"></p>
		</form>
		<br class="clear">
		<div id="col-container">
			<div id="col-right">
			<div class="col-wrap">
			<div class="form-wrap">
				<?php right_col_disp("issue", $issue_table, $filter_cat);?>
			</div>
			</div>
			</div>
			<div id="col-left">
			<div class="col-wrap">
			<div class="form-wrap">
				<h3>Add New Issue</h3>
				<?php issue_col_left_disp();?>
			</div>
			</div>
			</div>
		</div>
	</div>
<?php
}

/**
* Display issue left column
*/
function issue_col_left_disp() {
	global $toptopics;
	global $page_setting_uri;
	global $topic_checkbox_contents;
?>
	<form id="addissue" enctype="multipart/form-data" method="post" action="<?php echo $page_setting_uri;?>" class="validate">
	<input type="hidden" name="action" value="add">
	<div class="form-field form-required">
		<label for="issue-name">Name</label>
		<input name="issue-name" id="issue-name" type="text" value="" size="40" aria-required="true">
		<p>The name is how it appears on your site.</p>
	</div>
	<div class="form-field">
		<label for="issue-intro">Introduction</label>
		<!--input name="slug" id="topic-slug" type="text" value="" size="40"-->
		<textarea name="issue-intro" id="issue-intro" rows="5" cols="40"></textarea>
		<p>The description should be related to the issue.</p>
	</div>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Issue">
	</p>
	</form>
<?php
}

/**
* Display issue editing page
* @param structure $i issue information
*/
function edit_issue_disp($i) {
	global $page_setting_uri;
?>
<div class="wrap">
	<div id="icon-edit" class=icon32><br></div>
	<h2>Issue</h2>
	<form name="editissue" id="editissue" enctype="multipart/form-data" method="post" action="<?php echo $page_setting_uri;?>" class="validate">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="id", value="<?php echo $i->id;?>">
		<table class="form-table">
		<tbody>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="rsch-name">Name</label></th>
				<td><input name="issue-name" id="issue-name" type="text" value="<?php echo $i->name; ?>" size="40" aria-required="true">
				<p class="description">The name is how it appears on your site.</p></td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="issue-intro">Introduction</label></th>
				<td><textarea name="issue-intro" id="issue-intro" rows="5" cols="50" class="large-text"><?php echo $i->intro;?></textarea><br>
				<span class="description">The description is not prominent by default; however, some themes may show it.</span></td>
			</tr>
		</tbody>
		</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Update">
			</p>
	</form>
</div>
<?php
}

/**
* Display check box in post editting
* @param string $type "topic" or "rsch"
* @param string $tabs "toptopics" in topics, "regions" in rschs, 1-D array
* @param string $contents 2-D array 
*/
function check_box($type, $tabs, $contents ) {
	global $wpdb;
	//var_dump($contents);
	
	if ( $type=="topic" ) {
		$name = "subject";
		$category = "top-topic";
		$panel = "topic-list";
		$content = "topic-list-content";
	}
	else if ( $type=="rsch" ) {
		$name = "name";
		$category = "region";
		$panel = "rsch-list";
		$content = "rsch-list-content";
	}
	else if ( $type=="issue" ) {
		$name = "name";
		$category = "category";
		$panel = "issue-list";
		$content = "issue-list-content";
	}
	
	echo '<div id="taxonomy-'.$type.'" class="categorydiv">';
	echo '	<ul id="'.$type.'-tabs" class="category-tabs">';
	foreach ( $tabs as $key => $value ) {		
		if ( $key==1 )
			$classtype="tabs";
		else
			$classtype="";
		echo '<li id="'.$category.$key.'" class="'.$classtype.'">';
		echo '	<a href=#'.$type.'-list'.$key.' >';
		echo 	$value;
		echo '	</a>
			</li>';
	}
	echo '	</ul>';
	foreach ( $tabs as $key => $value ) {
		if ( $key==1 )
			$styletype="display: block";
		else
			$styletype="display: none";

		echo '<div id="'.$type.'-list'.$key.'"  class="tabs-panel" style="'.$styletype.'">';
		echo '<ul id="'.$content.$key.'" class="categorychecklist form-no-clear">';
		$arraySub = $contents[$key];
		//var_dump($arraySub);
		foreach ( $arraySub as $s) {
		echo '	<li>';
		echo '	<label><input style="width:auto;" type="checkbox" name="'.$type.'_checkbox[]" id="'.$type.'id-'.$s->id.'" value="'.$s->id .'">'.
$s->$name.'</label>';
		echo '	</li>';
		}
		echo '</ul></div>';
	}
	echo '</div>';
} 

/**
* Select checked topic boxes
*/
function topics_box() {
	global $post;
	global $toptopics;
	global $topic_checkbox_contents;
	check_box("topic", $toptopics, $topic_checkbox_contents);
	//$checked = get_checked_checkbox("topic",);
	$checked = get_checked_id($post->ID, "topic");
	//var_dump($checked);
	//var_dump($post->ID);
	//$checked = array(1,2,3);
	check_checkbox_php("topicid-", $checked);
} 

/**
* Select checked rsch boxes
*/
function researchers_box() {
	global $post;
	global $regions;
	global $rsch_checkbox_contents;
	check_box("rsch", $regions, $rsch_checkbox_contents);
	$checked = get_checked_id($post->ID, "rsch");
	//var_dump($checked);
	//$checked = array(4,5,6);
	check_checkbox_php("rschid-", $checked);
}

/**
* Select checked issue boxes
*/
function issues_box() {
	global $post;
	global $issue_checkbox_contents;
	//var_dump($issue_checkbox_contents);
	$categoris = array( '1' => '所有专题');
	check_box("issue", $categoris, $issue_checkbox_contents);
	$checked = get_checked_id($post->ID, "issue");
	//var_dump($checked);
	//$checked = array(4,5,6);
	check_checkbox_php("issueid-", $checked);
}

/**
* PHP interface to envoke JavaScript
* @param string $prefixID 
* @param 
*/
function check_checkbox_php($prefixID, $checked){

	echo '<script type="text/javascript">
			var arrayID='.json_encode($checked).';					
			check_checkbox("'.$prefixID.'",arrayID);
		  </script>';
}

/**
* HTML, display the extra_profile_fields
*/
function cnpolitics_show_extra_profile_fields( $user ) {
?>
	<table class="form-table">
		<tr>
			<th><label for="Title">Title</label></th>
			<td>
				<input type="text" name="title" id="title" value="<?php echo esc_attr( get_the_author_meta( 'cnpolitics_title', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your Title in CNPolitics.</span>
			</td>
		</tr>
		<tr>
			<th><label for="Weibo">Weibo</label></th>
			<td>
				<input type="text" name="weibo" id="weibo" value="<?php echo esc_attr( get_the_author_meta( 'cnpolitics_weibo', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your weibo url.</span>
			</td>
		</tr>
		<tr>
			<th><label for="Pubemail">Public Email</label></th>
			<td>
				<input type="text" name="pubemail" id="pubemail" value="<?php echo esc_attr( get_the_author_meta( 'cnpolitics_pubemail', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your public email address. You can also leave it blank.</span>
			</td>
		</tr>
	</table>
<?php 
} 
?>