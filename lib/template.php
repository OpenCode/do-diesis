<?php

	class Template {

		public function get_header() {
			return '
			<h1><img src="img/logo.png" width="38px"> Do Diesis</h1>

			<hr />';
			}

		public function get_sidebar_nav() {
			return '
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li class="nav-header">Main</li>
					<li id="menu_index"><a href="index.php">Main</a></li>
					<li class="nav-header">Configuration</li>
					<li id="menu_partners"><a href="partners.php">Partners</a></li>
					<li id="menu_groups"><a href="groups.php">Groups</a></li>
				</ul>
			</div>
			';
			}

		public function footer() {
			return '
			<hr />
			<p>Developed by <a href="http://www.e-ware.org"><img src="img/e-ware.png" width="32px"/></a></p>
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
