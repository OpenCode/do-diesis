<?php

	class Template {

		public function get_header_buttons() {
			return '
			<h1><img src="img/logo.png" width="38px"> Do Diesis</h1>

			<div class="btn-toolbar">
				<div class="btn-group">
					<a id="go_home" href="index.php" class="btn"><i class="icon-home"></i></a>
					<a id="go_partner" href="partners.php" class="btn"><i class="icon-user"></i></a>
					<a id="go_group" href="groups.php" class="btn"><i class="icon-th-list"></i></a>
				</div>
			</div>

			<hr />';
			}

		public function get_header_buttons_attrs() {
			return '
			$("#go_home").tooltip({placement: "bottom",trigger: "hover",title : "Homepage"});
			$("#go_partner").tooltip({placement: "bottom",trigger: "hover",title : "Manage Partner"});
			$("#go_group").tooltip({placement: "bottom",trigger: "hover",title : "Manage Group"});
			';
			}
			
		public function footer() {
			return '
			<hr />
			<p>Developed by <a href="http://www.e-ware.org"><img src="img/e-ware.png" width="32px"/></a></p>
			';
			}

		} // class


?>
