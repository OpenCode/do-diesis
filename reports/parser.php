<?php

	class Parser {
		
		public function init() {

				include_once('../../lib/config.php');
				include_once('../../lib/db.php');

				$DB = new DB();
				$DB->set_config_path('../../config');

				// Check db connection
				$DB->dead_or_alive();

				$db_datas = $DB->get_datas();

				require_once('../../redbean/rb.php');
				require_once('../../lib/functions.php');

				// Init ReadBean
				R::setup($db_datas['type'] . ':host=' . $db_datas['host']  . ';dbname=' . $db_datas['dbname'] ,$db_datas['user'] ,$db_datas['password'] );

			} // init()

		public function default_stylesheet() {
				return '
				<link rel="stylesheet" href="../print.css" type="text/css" />
				';
			} // default_stylesheet()
			
		public function on_load_page() {
				if ( __DEV_MODE_ACTIVE__ ) {
					return '';
					}
				$return = 'onload="window.print();window.close();"';
				return $return;
			} // on_load_page()

		} // Class

?>
