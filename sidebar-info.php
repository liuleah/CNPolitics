<?php
	$post_author = get_the_author_meta( 'user_login' );
	$query = new WP_Query(
		array(
			'author_name' => $post_author,
			'post__not_in' => array($post->ID),	
			'showposts' => 3, // 显示相关文章数量
			//'orderby' => 'date', // 按时间排序
			'caller_get_posts' => 1
			)
	);
	$posts = $query->posts;
?>
	<div class="post-sidebar-info">
		<div class="post-info-box">
			<img class="expand-observer-info" src="<?php bloginfo('template_directory'); ?>/images/arrow-expand.png">
			<img class="collapse-observer-info" src="<?php bloginfo('template_directory'); ?>/images/arrow-collapse.png">
			观察员：<a href="<?php echo get_author_posts_url(get_the_author_meta('ID'));?>"><?php the_author();?></a>
			<div class="observer-info">
				<p style="margin-top:20px;">个人简介：</p>
				<p style="color:#777;"><?php echo get_the_author_meta('description');?></p>
				<p style="margin-top:20px;">最新发表：</p>
				<p>
				<ul>
				<?php foreach($posts as $k => $p): //文章输出 ?>
					<li><a href="<?php echo get_permalink($p->ID); ?>"><?php echo $p->post_title ?></a></li>
				<?php endforeach; ?>
				</ul>
				</p>
				<p style="margin-bottom:20px;margin-top:0px;">
					<a href="<?php echo get_author_posts_url(get_the_author_meta('ID'));?>" style="color:#b9b9b9;font-size:12px;float:right;">浏览更多 »</a>
				</p>
			</div>
		</div>
<?php
		$r_array = get_rschs_bypostid($post->ID);
		foreach ($r_array as $r) :
			$rid = $r->id;
			$rname = $r->name;
			$rintro = $r->intro;
?>
		<div class="post-info-box">
			<img class="expand-researcher-info" src="<?php bloginfo('template_directory'); ?>/images/arrow-expand.png">
			<img class="collapse-researcher-info" src="<?php bloginfo('template_directory'); ?>/images/arrow-collapse.png">
<?php
			echo '	
			研究者：<a href="'.get_bloginfo('url')."/researcher/?rsch_id=".$rid.'">'.$rname. '</a>
			<div class="researcher-info">
				<p style="margin-top:20px;">个人简介：</p> 
				<p style="color:#777;">'.$rintro.'</p>
				<p style="margin-top:20px;">相关文章：</p>
				<p>
				<ul>';
			$post_id_array = get_postid_byrschid($rid);
			$count = 0;
			foreach ( (array)$post_id_array as $post_id ) :
				if ( $post_id!=$post->ID ) {
					$related_post = get_post($post_id); $count = $count+1;
					echo '<li><a href="'.$related_post->guid.'">'.$related_post->post_title.'</a></li>';
					if ($count>=4) break;
				}
			endforeach;
			echo '
				</ul>
				</p>
				<p style="margin-bottom:20px;margin-top:0px;">
					<a href="'.get_bloginfo('url')."/researcher/?rsch_id=".$rid.'" style="color:#b9b9b9;font-size:12px;float:right;">浏览更多 »</a>
				</p>
			</div>
		</div>';
		endforeach;

		$t_array = get_topics_bypostid($post->ID);
		foreach ($t_array as $t) :
			$tid = $t->id;
			$tsubject = $t->subject;
			$tintro = $t->intro;
?>
		<div class="post-info-box">
			<img class="expand-theme-info" src="<?php bloginfo('template_directory'); ?>/images/arrow-expand.png">
			<img class="collapse-theme-info" src="<?php bloginfo('template_directory'); ?>/images/arrow-collapse.png">
<?php
			echo '	
			主题名：<a href="'.get_bloginfo('url')."/topic/?topic_id=".$tid.'">'.$tsubject.'</a>
			<div class="theme-info">
				<p style="margin-top:20px;">主题简介：</p>
				<p style="color:#777;">'.$tintro.'</p>
				<p style="margin-top:20px;">相关文章：</p>
				<p>
				<ul>';
			$post_id_array = get_postid_bytopicid($tid);
			$count = 0;
			foreach ( (array)$post_id_array as $post_id ) :
				if ( $post_id!=$post->ID ) {
					$related_post = get_post($post_id); $count = $count+1;
					echo '<li><a href="'.$related_post->guid.'">'.$related_post->post_title.'</a></li>';
					if ($count>=4)	break;
				}
			endforeach;
			echo '
				</ul>
				</p>
				<p style="margin-bottom:20px;margin-top:0px;">
					<a href="'.get_bloginfo('url')."/topic/?topic_id=".$tid.'" style="color:#b9b9b9;font-size:12px;float:right;">浏览更多 »</a>
				</p>
			</div>
		</div>';
		endforeach;
?>
		<p><a href="<?php echo get_site_url();?>"><b>政见 CNPolitics.org</b></a> 是一个独立团队，向你介绍世界上最聪明的脑袋是怎样分析中国的。我们致力于发掘海内外学者和智库的智慧成果，引进思想资源。｜<a href="http://cnpolitics.org/static/?static_page=about.php">更多关于我们 »</a></p>
		<p style="color:#777777;margin-bottom:5px;">关注政见动向：</p>
		<div class="post-sidebar-social">
			<ul>
			<li><a href="#" class="fav" style="margin-right:14px;"><img class="img-hover" src="<?php bloginfo('template_directory'); ?>/images/post-fav.png" onmouseover="this.title='添加至收藏夹'"></a></li>
			<li><a href="http://cnpolitics.org/feed/" class="rss" style="margin-right:14px;"><img class="img-hover" src="<?php bloginfo('template_directory'); ?>/images/post-rss.png" onmouseover="this.title='订阅RSS'"></a></li>
			<li><a href="http://weibo.com/cnpolitics" class="sina" style="margin-right:14px;"><img class="img-hover" src="<?php bloginfo('template_directory'); ?>/images/post-sina.png" onmouseover="this.title='访问新浪微博'"></a></li>
			<li><a href="http://t.qq.com/chinapolitics" class="tecent" style="margin-right:14px;"><img class="img-hover" src="<?php bloginfo('template_directory'); ?>/images/post-tencent.png" onmouseover="this.title='访问腾讯微博'"></a></li>
			<li><a href="http://t.163.com/cnpolitics" class="a163" style="margin-right:14px;"><img class="img-hover" src="<?php bloginfo('template_directory'); ?>/images/post-163.png" onmouseover="this.title='访问网易微博'"></a></li>
			</ul>
		</div>
		<p style="color:#777777;margin-bottom:10px;float:left;">订阅最新文章，自动推送至你的邮箱：</p>
		<!--以下是QQ邮件列表订阅嵌入代码-->
		<script >var nId = "384aadf45363c5d64f96fe9a5b020d0b8acee304fd40cdee"</script>
		<script src="http://list.qq.com/zh_CN/htmledition/js/qf/page/qfcode.js" charset="gb18030"></script>

		<form style="display:inline;"  action="http://list.qq.com/cgi-bin/qf_compose_send" target='_blank' method="post">
			<input type="hidden" name="t" value="qf_booked_feedback">
			<input type="hidden" name="id" value="384aadf45363c5d64f96fe9a5b020d0b8acee304fd40cdee">
			<input type="text" id="to" name="to" value="输入你的Email地址" onblur="if (this.value==''){this.value='输入你的Email地址'}" onfocus="if (this.value=='输入你的Email地址') {this.value=''}" class="post-sidebar-emailbox-input"/>
			<input type="image" class="emailbox_img sidebar-emailbox-img" src="<?php bloginfo('template_directory'); ?>/images/sidebar_input.png">
		</form>
		<div class="post-sidebar-emailbox-sample"></div>
		<img class="emailbox_shadow post-sidebar" src="<?php bloginfo('template_directory'); ?>/images/emailbox_shadow_post.png">
</div>
<script type="text/javascript" src="<?php bloginfo('template_directory');?>/js/info_box.js"></script>
