<?php
header("Content-type: text/css; charset: UTF-8");

$path="../../../media/images_tpl/com_backup/";

?>
<style type="text/css">
/* Basic  ---------------------------------------- */

.clear { clear: both; }

body {
	background: white;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #646464;
	text-align: center;
}

#wrapper {
	text-align: left;
	width: 930px;
	margin: 0 auto;
}

/* Logo  ---------------------------------------- */

h1 {
	margin: 15px 0 10px 5px;
	width: 225px;
	height: 28px;
	/* background: url("<?php echo $path ?>transdmin-light.png") no-repeat left top; */
}

h1 a {
	display: block;
	width: 225px;
	height: 28px;
}

h1 span { display: none; }

/* Main Navigation ---------------------------------------- */

#mainNav {
	width: 918px;
	height: 37px;
	padding: 6px;
	margin: 0 0 30px 0;
	background: url("<?php echo $path ?>top-menu-bg.gif") no-repeat left top; 
	/* We used a background image making the thick border and the thin one to keep the html structure simple */
}

#mainNav li {
	float: left;
	height: 37px;
	border-right: 1px solid #ddd;
}

#mainNav li a {
	display: block;
	float: left;
	height: 37px;
	font-weight: bold;
	line-height: 37px;
	text-decoration: none;
	color: #646464;
	padding: 0 20px;
}

#mainNav li.logout {
	float: right;
	border-left: 1px solid #ddd;
	border-right: none;
}

#mainNav li a:hover { background: #fdfcf6; }

#mainNav li a.active, #mainNav li a.active {
	background: #f0f0f0 url("<?php echo $path ?>top-menu-item-bg.gif") repeat-x left top;
	color: #454545;
}

/* Container ---------------------------------------- */

#containerHolder {
	background: #eee;
	padding: 5px;
}


#container {
	background: #fff url("<?php echo $path ?>content.gif") repeat-y left top;
	border: 1px solid #ddd;
	width: 918px;
}

/* Sidebar ---------------------------------------- */

#sidebar {
	width: 179px;
	float: left;
}

#sidebar .sideNav { width: 179px; }

#sidebar .sideNav li { border-bottom: 1px solid #ddd; width: 179px; }

#sidebar .sideNav li a {
	display: block;
	color: #646464;
	background: #f6f6f6;
	text-decoration: none;
	height: 29px;
	line-height: 29px;
	padding: 0 19px;
	width: 141px;
}

#sidebar .sideNav li a:hover { background: #fdfcf6; }

#sidebar .sideNav li a.active, #sidebar .sideNav li a.active:hover {
	background: #f0f7fa;
	color: #c66653;
}

/* Breadcrumb ---------------------------------------- */

h2 {
	width: 718px;
	float: right;
	color: #646464;
	font-size: 16px;
	line-height: 16px;
	font-weight: bold;
	margin: 20px 0 0 0;
	padding: 0 0 10px 0;
	border-bottom: 1px solid #ddd;
}

h2 a {
	color: #646464;
	text-decoration: none;
}

h2 a.active { color: #c66653; }

h2 a:hover { text-decoration: underline; }

/* Content ---------------------------------------- */

#main {
	width: 700px;
	float: right;
	padding: 0 19px 0 0;
}

h3 {
	font-size: 14px;
	line-height: 14px;
	font-weight: bold;
	color: #5494af;
	padding: 0 0 0 10px;
	margin: 20px 0 10px;
}

#main table {
	border-top: 1px solid #ddd;
	width: 700px;
}

#main table tr td {
	background: #f6f6f6;
	padding: 0px 20px;
	height: 29px;
	line-height: 29px;
	border-bottom: 1px solid #ddd;
}

#main table tr.odd td {
	background: #fbfbfb;
}

#main table tr:hover td { background: #fdfcf6; }

#main table .action {
	text-align: right;
	padding: 0 20px 0 10px;
}

#main table tr .action a { margin: 0 0 0 10px; text-decoration: none; color: #9b9b9b; }
#main table tr:hover .action .edit { color: #c5a059; }
#main table tr:hover .action .delete { color: #a02b2b; }
#main table tr:hover .action .view { color: #55a34a; }

#main table tr:hover .action a:hover { text-decoration: underline; }

#main fieldset {
	border: 1px solid #ddd;
	padding: 19px;
	margin: 0 0 20px 0;
	background: #fbfbfb;
}

#main fieldset p { margin: 0 0 14px 0; float: left; width: 100%;  }

#main fieldset label {
	display: block;
	width: 100%;
	margin: 0 0 7px 0;
	line-height: 12px;
}

#main fieldset .text-long {
	border: 1px solid #ddd;
	background: #fff url("<?php echo $path ?>input-shaddow.gif") no-repeat left top;
	font: 11px Arial, Helvetica, sans-serif;
	color: #646464;
	padding: 5px 7px;
	width: 264px;
	float: left;
	margin: 0 10px 0 0;
}

#main fieldset .text-medium {
	border: 1px solid #ddd;
	background: #fff url("<?php echo $path ?>input-shaddow.gif") no-repeat left top;
	font: 11px Arial, Helvetica, sans-serif;
	color: #646464;
	padding: 5px 7px;
	width: 124px;
	float: left;
	margin: 0 10px 0 0;
}

#main fieldset .text-small {
	border: 1px solid #ddd;
	background: #fff url("<?php echo $path ?>input-shaddow.gif") no-repeat left top;
	font: 11px Arial, Helvetica, sans-serif;
	color: #646464;
	padding: 5px 7px;
	width: 44px;
	float: left;
	margin: 0 10px 0 0;
}

#main fieldset .text-long:focus, 
#main fieldset .text-medium:focus,
#main fieldset .text-small:focus,
#main fieldset textarea:focus { background: #fdfcf6 url("<?php echo $path ?>input-shaddow-hover.gif") no-repeat left top; }

#main fieldset textarea {
	border: 1px solid #ddd;
	background: #fff url("<?php echo $path ?>input-shaddow.gif") no-repeat left top;
	font: 11px Arial, Helvetica, sans-serif;
	color: #646464;
	padding: 5px 7px;
	width: 364px;
	float: left;
	margin: 0 10px 0 0;
	height: 148px;
	overflow: hidden;
}

.button-submit {
	font: 11px Arial, Helvetica, sans-serif;
	color: #646464;
	width: 94px;
	height: 29px;
	cursor: pointer;
	border: none;
	background: url("<?php echo $path ?>button-submit.gif") no-repeat left top;
}

/* Footer ---------------------------------------- */

#footer {
	margin: 10px 0 30px 0;
	font-size: 11px;
	line-height: 11px;
	color: #9B9B9B;
	padding: 0 0 0 5px;
}

#footer a { color: #9B9B9B; }

#footer a:hover { text-decoration: none; }
</style>