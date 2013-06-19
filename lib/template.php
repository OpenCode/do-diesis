<?php

	class Template {

		public function get_header() {
			return '
			<h1><img src="img/logo.png" width="38px"> Do Diesis</h1>
			<hr />';
			}

		public function get_head() {
			return '
			<!-- Do-Diesis include -->
			<script src="js/jquery.js" type="text/javascript"></script>
			<script src="js/generic.js" type="text/javascript"></script>
			<!-- Bootstrap include -->
			<link rel="stylesheet" href="bootstrap/css/bootstrap.css" type="text/css"/>
			<script src="bootstrap/js/bootstrap.js" type="text/javascript"></script>
			<!-- Datapicker include -->
			<link rel="stylesheet" href="datepicker/css/datepicker.css" type="text/css"/>
			<script src="datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
			<!-- Do-Diesis include -->
			<link rel="stylesheet" href="css/generic.css" type="text/css"/>
			';
			}

		public function get_sidebar_nav() {
			return '
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li class="nav-header">Main</li>
					<li id="menu_index"><a href="index.php"><i class="icon-list-alt"></i> Main</a></li>
					<li class="nav-header">Configuration</li>
					<li id="menu_partners"><a href="partners.php"><i class="icon-user"></i> Partners</a></li>
					<li id="menu_groups"><a href="groups.php"><i class="icon-th-list"></i> Groups</a></li>
					<li id="menu_payment_methods"><a href="payment_methods.php"><i class="icon-briefcase"></i> P. Methods</a></li>
				</ul>
			</div>
			';
			}

		public function footer() {
			return '
			<hr />
			<p>Developed by <a href="http://www.e-ware.org" target="blank"><img src="img/e-ware.png" width="32px"/></a></p>
			';
			}
			
		public function common_script() {
			return '
				var id_menu = window.location.href.substr(window.location.href.lastIndexOf("/")+1);
				id_menu = "#menu_" + id_menu.replace(".php", "");
				$(id_menu).addClass("active");
			';
			}

		} // class


?>
