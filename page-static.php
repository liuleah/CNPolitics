<?php
/*
Template Name: Static
*/
?>
<?php  get_header();?>
<?php
	global $cnpolitics_dir;
	if ( isset($_GET['static_page']) ) :
		//echo 	$_GET['static_page'];
		include_once($cnpolitics_dir."/inc/".$_GET['static_page']);
	endif;
?>
<?php get_footer(); ?>
<script>
	$(document).ready(function(){
		$('.language-toggle').click(function()	{
			if (!$(this).hasClass('active') & $(this).hasClass('english')) {
				$('#copyright-cn').css('display','none');
				$('#copyright-en').css('display','block');
			}

			else if (!$(this).hasClass('active') & $(this).hasClass('chinese')) {
				$('#copyright-cn').css('display','block');
				$('#copyright-en').css('display','none');
			}
		})
		$(".join-nav-obs").click(function() {
			var heightObs = $("#join-observer").offset().top-110;
			$('html,body').animate({
				scrollTop: heightObs
			},300,function() {});
			window.location.hash='observer';
		})

		$(".join-nav-gra").click(function() {
			var heightGra = $("#join-graphic").offset().top-110;
			$('html,body').animate({
				scrollTop: heightGra
			},300,function() {});
			window.location.hash='graphic';
		})

		$(".join-nav-ops").click(function() {
			var heightOps = $("#join-operation").offset().top-110;
			$('html,body').animate({
				scrollTop: heightOps
			},300,function() {});		
			window.location.hash='operation';
		})

		$(".join-nav-des").click(function() {
			var heightDes = $("#join-design").offset().top-110;
			$('html,body').animate({
				scrollTop: heightDes
			},300,function() {});		
			window.location.hash='design';
		})

		$(".join-nav-eng").click(function() {
			var heightEng = $("#join-engineer").offset().top-110;
			$('html,body').animate({
				scrollTop: heightEng
			},300,function() {});		
			window.location.hash='engineer';
		})

		$(".team-nav-obs").click(function() {
			var heightEng = $("#join-observer").offset().top-200;
			$('html,body').animate({
				scrollTop: heightEng
			},300,function() {});		
			window.location.hash='observer';
		})
	})
</script>

<style type="text/css">
.clear1	{clear: both;height:0;} 

#join-nav {
	margin:0 auto; text-align: center; padding-bottom: 10px;
	background-image:url(images/background_pattern.png);
}

#fixed-top {
	/*position:fixed;z-index:1000;*/
    position:relative;
    text-align: center;
    width: 960px;
}

#join-nav p { font-size:24pt;margin:0 auto; text-align:center; padding:38px 0;font-weight:bold;}
#join-nav ul {margin:0px; text-align:center;padding: 0px;}
#join-nav ul li {display: inline;color:#b9b9b9;}
#join-nav ul li a {font-size:14px; color: b9b9b9;}
#join-head p { font-size:16px; color: 3b3b3b;}
#join-head a { color: #b42800; font-weight: bold;}

.join-position p { 
	padding-top: 80px;
	margin: 0 auto;
	text-align: center; 
	font-size: 18px; 
	font-weight:bold; 
}

.join-body p { font-size: 16px; color: #3b3b3b; padding-bottom: 10px; padding-top: 10px;}
.join-body a { color: #b42800;}

.join-contact {
	width: 340px;
	margin-top: 30px;
	margin-left: auto;
	margin-right: auto;
	padding-top: 20px;
	padding-bottom: 20px;
/*	border-style:solid;*/
}

.join-contact p { 
	text-align: center; 
	font-size:15px; 
	color:#777777;
}

.join-contact a {color: #b42800;}


/*_______COPYRIGHT*/


.copyright-content {padding-bottom: 10px;}

.copyright-content p {
	color:#3b3b3b;
	font-size: 16px;
	line-height: 28px;
	margin-bottom: 15px;
}

.copyright-content p.about-title {
	color:#000000;
	font-size: 18px;
	font-weight: bold;
	text-align: center;
	margin:20px auto;
	
}

#copyright-en, #copyright-cn {
	padding:0 22;
}
.copyright-item {
	margin-top: 20px;
	//border: 1px solid red;
	height: auto;
}

.copyright-item span {font-weight: bold;}

.img-item {
	width:36px;
	height: auto;
	float: left;
}

.content-item {
	width:520px;
	margin-left: 20px;
	padding-top: 5px;
	float: left;
	font-size: 16px;
}

.copyright-contact {margin-top: 30px;}
.copyright-contact {font-size: 16px;}

#copyright-head .head {
	color:#000000;
	font-size:24px;
	margin-top:15px;
	font-weight: bold;
}

#copyright-head .subhead {
	font-size:16px; 
	font-weight:bold;
	line-height: 30px;
}

#copyright-head li {
	display: inline; 
	font-size:13px;
}

a {
	color: #b42800;
}

#copyright-en p {
	font-size: 15px;
	color:#3b3b3b;
	line-height: 24px;
	margin-bottom: 10px;

}

#copyright-en ul, #copyright-cn ul {
	margin:30px auto; padding-left: 0px;
}


li.language-toggle {
	font-size: 14px; 
	color:#b9b9b9;
}

li.language-toggle.active {
	color: #777777; font-weight: bold;
}

li.language-toggle.inactive {
	cursor: pointer;
}

.contribute {
	margin-top: 40px;
}

#copyright-cn .contribute p {
	font-size: 15px;
	color:#777777;
	text-align: center;
	margin-bottom: 2px;
}

#copyright-en .contribute p {
	font-size: 14px;
	color:#777777;
	text-align: center;
	margin-bottom: 2px;
}

#join-main {
	padding:0 ;
	margin:0 auto;
	width: 620px;
}


#team-main {
	padding:0 ;
	margin:0 auto;
	width: 980px;
	text-align: center;
}

.team-first-group p {
    padding-top: 0px;
    margin:0 auto;
    text-align: center;
    font-size: 18px;
    font-weight:bold;

}

.team-group p {
    padding-top: 80px;
    margin:0 auto;
    text-align: center;
    font-size: 18px;
    font-weight:bold;

}

.team-unit {
	width:20%;
	margin: 30px 20px;
	float: left;
	height:220px;
}

.team-logo img {
	border-radius:50%;
	width: 80px;
	height: 80px;
	-webkit-box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.5);
	-moz-box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.5);
	box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.5);
	behavior: url(PIE.htc);
}

.team-name {margin-top:12px; margin-bottom:12px;}
.team-name a {
	font-weight: bold;
	font-size: 14px;
}

.team-intro {
	padding:0;
}
.team-intro p {
	text-align: left;
	font-size:14px;
	color:#777777;
    line-height: 24px;
    padding-bottom: 8px;
}

#team-more {
	margin-top: 80px;
}

#invited-members {
    width:42%;
    text-align: left;
    margin-left: 10px;
    padding: 25px 20px;
    float: left;
    height:130px;
    border:#b9b9b9 dashed 1px;
}

#invited-members p {
    font-size:15px;
    font-weight: bold;
    font-color:#000000;
    padding-bottom: 5px;
}

#invited-members ul {
    padding-left: 0px;
}

#invited-members ul li {
    list-style: none;
    font-size: 14px;
    color:#3B3B3B;
    width:23%;
    height:30px;
    float:left;
}

#contributors {
    width:42%;
    padding:25px 20px;
    margin-left: 20px;
    text-align: left;
    float: left;
    height:130px;
    border:#b9b9b9 dashed 1px;
}

#contributors p {
    font-size:15px;
    font-weight: bold;
    font-color:#000000;
    padding-bottom: 5px;
}

#contributors ul {
    padding-left: 0px;
}

#contributors ul li {
    list-style: none;
    font-size: 14px;
    color:#3B3B3B;
    width:23%;
    height:30px;
    float:left;
}
</style>
