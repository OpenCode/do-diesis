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
		$main = R::dispense(__PARTNER_TABLE__);
		$main->name = $_POST['name'];
		R::store($main);
	} // if
	
	// Delete record passed to page
	if ( $_GET && isset($_GET['unlink']) ) {
		$main = R::load(__PARTNER_TABLE__, $_GET['unlink']);
		R::trash( $main );
		// Set all the record in main table with relation = Null
		R::exec('UPDATE ' . __MAIN_TABLE__ . ' SET partner_id = null WHERE partner_id = ? ',array($_GET['unlink']));
	} // if
	
	// Extract all the main record
	$records = R::findAll(__PARTNER_TABLE__, ' ORDER BY name ');

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
			<title>Do Diesis</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php echo $Template->get_head(); ?>
	</head>

	<body style="margin-top:10px;">

		<div class="container-fluid">

			<div class="row-fluid">
				<?php echo $Template->get_header(); ?>
			</div>
			
			<div class="row-fluid">

				<div class="span2">
					<?php echo $Template->get_sidebar_nav('partners'); ?>
				</div>

				<div class="span10">
					<form action="<?php echo __PARTNER_PAGE__; ?>" method="post">
						<div class="controls controls-row">
							<input class="span11" name="name" type="text" placeholder="Name" required>
							<input class="span1 btn btn-primary" type="submit" value="+">
						</div>
					</form>

					<?php
						// if there are lines in the database do your work!
						if ( count($records) ) {
							$total_line = R::count(__MAIN_TABLE__);
							echo '<table class="table table-striped table-bordered table-hover table-condensed">
									<tr>
										<td></td>
										<td><b>NAME</b></td>
										<td><b>RELATION</b></td>
										<td><b>RELATION %</b></td>
									</tr>';
							foreach( $records as $r ) {
								$relation_records = R::count(__MAIN_TABLE__,' partner_id = ?',array($r['id']));
								if ( R::findOne(__MAIN_TABLE__,' partner_id = ? ',array($r['id'])) ) {
									$js_on_button = 'confirm_set_null()';
									}
								else {
									$js_on_button = 'confirm_delete()';
									}
								echo '<tr>
										<td>
											<a onclick="return ' . $js_on_button . '" href="?unlink=' . $r['id'] . '">
												<button class="btn btn-danger btn-mini del_line" data-original-title="">X</button>
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
