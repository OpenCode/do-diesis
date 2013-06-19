<?php

	class Template {

		public function get_header() {
			return '
			<h1><img src="img/logo.png" width="38px"> Do Diesis</h1>
			<hr />';
			}

		public function get_head() {
			return '
			<link rel="shortcut icon" href="img/logo.png" >
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

		public function get_sidebar_nav($sm) {
			return '
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li class="nav-header">Main</li>
					<li class="' . ($sm=='index' ? 'active' : '') . '"><a href="index.php"><img src="img/dashboard.png" width="14px" /> Dashboard</a></li>
					<li class="' . ($sm=='main' ? 'active' : '') . '"><a href="main.php"><i class="icon-list-alt"></i> Main</a></li>
					<li class="nav-header">Configuration</li>
					<li class="' . ($sm=='partners' ? 'active' : '') . '"><a href="partners.php"><i class="icon-user"></i> Partners</a></li>
					<li class="' . ($sm=='groups' ? 'active' : '') . '"><a href="groups.php"><i class="icon-th-list"></i> Groups</a></li>
					<li class="' . ($sm=='payment_methods' ? 'active' : '') . '"><a href="payment_methods.php"><i class="icon-briefcase"></i> P. Methods</a></li>
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
			return '';
			}

		} // class


?>
