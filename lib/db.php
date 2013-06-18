<?php

	class DB {

		public $connect_to_db = false;
		public $datas = array();
		public $config_path = '';

		private function get_values() {
			// If exist read the value
			if ( file_exists( $this->config_path ) ){
				$config_content = file_get_contents($this->config_path, 'r');
				$config_json = json_decode($config_content,true);
				$this->datas['user'] = $config_json['database'][0]['user'];
				$this->datas['password'] = $config_json['database'][0]['password'];
				$this->datas['type'] = $config_json['database'][0]['type'];
				$this->datas['host'] = $config_json['database'][0]['host'];
				$this->datas['dbname'] = $config_json['database'][0]['dbname'];
				$this->connect_to_db = true;
				} // if
			// Else create an example config file
			else {
				$response = array();
				$database = array();
				$database[] = array(
					'user' => 'DBUser',
					'password' => 'DBPassword',
					'type' => 'mysql',
					'host' => 'localhost',
					'dbname' => 'do_diesis',
					);
				$response['database'] = $database;
				$fp = fopen($this->config_path, 'w');
				fwrite($fp, json_encode($response));
				fclose($fp);
				chmod($this->config_path, 0777);
				$this->connect_to_db = false;
				$this->datas = $database;
				} // else
			} // function

		public function get_datas() {
			$this->get_values();
			return $this->datas;
			}

		public function set_config_path( $config_path ) {
			$this->config_path = $config_path;
			return $config_path;
			}

		public function dead_or_alive() {
			// stop the software if the data for connection arent't correct
			$this->get_values();
			if ( !$this->connect_to_db || $this->datas['user'] == 'DBUser' || $this->datas['password'] == 'DBPassword' ) {
				die('A config file been created in your Do-Diesis folder. Please set the correct datas to connect with your database');
				}
			}

		} // class


?>
