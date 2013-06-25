<?php

	include_once('lib/config.php');

	if ( __DEV_MODE_ACTIVE__ ) {
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', 1);
		}

	include_once('lib/db.php');

	$DB = new DB();
	$DB->set_config_path('config');

	// Check db connection
	$DB->dead_or_alive();

	include_once('lib/template.php');
	$Template = new Template();

	$db_datas = $DB->get_datas();

	require_once('redbean/rb.php');
	require_once('lib/functions.php');

	// Init ReadBean
	R::setup($db_datas['type'] . ':host=' . $db_datas['host']  . ';dbname=' . $db_datas['dbname'] ,$db_datas['user'] ,$db_datas['password'] );

	// Insert new record passed to page
	if ( $_POST ) {
		if ( isset($_POST['line_id']) && $_POST['line_id'] != '0' ) {
			$main = R::load(__PAYMENT_METHOD_TABLE__, $_POST['line_id']);
			}
		else {
			$main = R::dispense(__PAYMENT_METHOD_TABLE__);
			}
		$main->name = $_POST['name'];
		R::store($main);
	} // if

	// Delete record passed to page
	if ( $_GET && isset($_GET['unlink']) ) {
		$main = R::load(__PAYMENT_METHOD_TABLE__, $_GET['unlink']);
		R::trash( $main );
		// Set all the record in main table with relation = Null
		R::exec('UPDATE ' . __MAIN_TABLE__ . ' SET paymentmethod_id = null WHERE paymentmethod_id = ? ',array($_GET['unlink']));
	} // if

	// Get the order
	$order_type = 'name ASC';
	if ( $_GET && isset($_GET['order']) ) {
		$order_type = $_GET['order'];
		}

	// Get the filter
	$filter = '';
	$filter_name = '';
	if ( $_GET && isset($_GET['filter_name']) || isset($_GET['filter_name']) ) {
		// Filter Name
		if ( isset($_GET['filter_name']) && $_GET['filter_name'] ) {
			$filter .= " name LIKE '%" . $_GET['filter_name'] . "%' AND ";
			$filter_name = $_GET['filter_name'];
			}
		// Clear last chars from filter string
		$filter = rtrim($filter,'AND ');
		}

	// Extract all the main record
	if ( !$filter) {
		$records = R::findAll(__PAYMENT_METHOD_TABLE__, ' ORDER BY ' . $order_type . ' ');
		}
	else {
		$records = R::find(__PAYMENT_METHOD_TABLE__, ' ' . $filter . ' ORDER BY ' . $order_type . ' ');
		}

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
			<title>Do Diesis</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Do-Diesis include -->
		<?php echo $Template->get_head(); ?>
	</head>

	<body style="margin-top:10px;">

		<div class="container-fluid">

			<div class="row-fluid">
				<?php echo $Template->get_header(); ?>
			</div>
			
			<div class="row-fluid">

				<div class="span2">
					<?php echo $Template->get_sidebar_nav('payment_methods'); ?>
				</div>

				<div class="span10 main-content">

					<?php 
						$script_name = $_SERVER["SCRIPT_NAME"];
						$break = Explode('/', $script_name);
						$pfile = $break[count($break) - 1]; 
						// Get all the reports for this page
						$reports_dir = opendir('reports/' . str_replace('.php', '', $pfile) . '/');
						$reports = array();
						while($file = readdir($reports_dir)){ 
							if($file == "." || $file == "..") continue; 
							$reports[] = '<a class="btn" target="blank" href="reports/' . str_replace('.php', '', $pfile) . '/' . $file . '"><i class="icon-print"></i> ' . ucfirst(str_replace('.php', '', $file)) . '</a>';
							}
						closedir($reports_dir);
						if ($reports) {
							echo '<div class="row-fluid">
									<div id="reports" class="span12 btn-group reports-buttons">';
								foreach ( $reports as $r ) {
									echo $r;
									}
							echo '	</div>
							</div>';
						}
					?>

					<form action="<?php echo __PAYMENT_METHOD_PAGE__; ?>" method="post">
						<div class="controls controls-row">
							<input class="span11" id="name" name="name" type="text" placeholder="Name" required>
							<input class="span1 btn btn-primary" type="submit" value="+">
						</div>
						<input id="line_id" class="span12" name="line_id" type="hidden" value="0">
					</form>

					<form action="<?php echo __PAYMENT_METHOD_PAGE__; ?>" method="get">
						<div class="controls controls-row">
							<input class="span11" id="filter_name" name="filter_name" type="text" placeholder="Filter Name" value="<?php echo $filter_name; ?>">
							<button class="span1 btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
						</div>
					</form>

					<?php
						// if there are lines in the database do your work!
						if ( count($records) ) {
							$total_line = R::count(__MAIN_TABLE__);
							echo '<table class="table table-striped table-bordered table-hover table-condensed">
									<tr>
										<td width="10%"></td>
										<td><b><i class="icon-chevron-'.get_order_icon($order_type, 'name').'"></i> <a href="?order='.invert_order($order_type, 'name').'">NAME</a></b></td>
										<td width="10%"><b>RELATION</b></td>
										<td width="10%"><b>RELATION %</b></td>
									</tr>';
							foreach( $records as $r ) {
								$relation_records = R::count(__MAIN_TABLE__,' paymentmethod_id = ?',array($r['id']));
								if ( R::findOne(__MAIN_TABLE__,' paymentmethod_id = ? ',array($r['id'])) ) {
									$js_on_button = 'confirm_set_null()';
									}
								else {
									$js_on_button = 'confirm_delete()';
									}
								echo '<tr>
										<td>
											<!-- DELETE -->
											<a onclick="return ' . $js_on_button . '" href="?unlink=' . $r->id . '">
												<button class="btn btn-danger btn-mini del_line" >X</button>
											</a>
											<!-- EDIT -->
											<a onclick=\'fill_edit_form({"line_id" : ' . $r->id . ',"name" : "' . $r->name . '",})\'>
												<button class="btn btn-mini edit_line" ><i class="icon-edit"></i></button>
											</a>
										</td>
										<td>' . $r['name'] . '</td>';
										if ($total_line) {
											echo '<td><span class="badge badge-success">' . $relation_records . '</span></td>
											<td><div class="progress"><div class="bar" style="width: ' . (($relation_records/$total_line)*100) . '%;"></div></div></td>';
											}
										else {
											echo '<td></td><td></td>';
											}
									echo '</tr>';
								} // foreach
							echo '</table>';
							} // if
						// else show a simple message (for now)
						else {
							echo '<div class="alert">
									<strong>OPS!</strong> There isn\'t record in this database. Please create a new line!
								</div>';
							} // else
					?>

				</div><!-- .span10 -->
			</div><!-- .row -->
				
			<div class="row-fluid">
				<?php echo $Template->footer(); ?>
			</div><!-- .row -->

		</div><!-- .container -->

	</body>
	
	<script type="text/javascript">
		<?php echo $Template->common_script(); ?>
	</script>

</html>
