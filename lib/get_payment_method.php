<?php 

	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);

	include_once('db.php');

	$DB = new DB();
	$DB->set_config_path('../config');
	$DB->dead_or_alive();
	$db_datas = $DB->get_datas();

	require_once('../redbean/rb.php');

	//Controllo che sia settato il paramentro
	if ( isset( $_POST['pm_name'] ) ) {
		$group_name = $_POST['pm_name'];

	// Init ReadBean
	R::setup($db_datas['type'] . ':host=' . $db_datas['host']  . ';dbname=' . $db_datas['dbname'] ,$db_datas['user'] ,$db_datas['password'] );

	$records = R::find('paymentmethod'," name like ? ", 
		array( '%' . $group_name . '%' )
		);

	$returnData = array();

	foreach( $records as $r ) { 
		$returnData[] = array("id" => $r['id'],"name" => $r['name']);
		}

	//Mando in output una stringa json
	echo json_encode($returnData);
	exit();

	}

?>
