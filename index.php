<?php

	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);

	include_once('lib/db.php');

	$DB = new DB();
	$DB->set_config_path('config');

	// Check db connection
	$DB->dead_or_alive();

	$db_datas = $DB->get_datas();

	require_once('redbean/rb.php');
	require_once('lib/functions.php');

	// Init ReadBean
	R::setup($db_datas['type'] . ':host=' . $db_datas['host']  . ';dbname=' . $db_datas['dbname'] ,$db_datas['user'] ,$db_datas['password'] );
	
	// Insert new record passed to page
	if ( $_POST ) {
		$main = R::dispense('main');
		$main->description = $_POST['description'];
		$main->date = date_to_datetime($_POST['date']);
		$main->in = $_POST['in'];
		$main->out = $_POST['out'];
		preg_match("#\[(.+?)\]#m",$_POST['group_id'],$group_match);
		$main->group_id = $group_match[1];
		preg_match("#\[(.+?)\]#m",$_POST['partner_id'],$group_match);
		$main->partner_id = $group_match[1];
		R::store($main);
	} // if
	
	// Delete record passed to page
	if ( $_GET && isset($_GET['unlink']) ) {
		$main = R::load('main', $_GET['unlink']);
		R::trash( $main );
	} // if
	
	// Extract all the main record
	$records = R::findAll('main', ' ORDER BY date ');

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
			<title>Do Diesis</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Do-Diesis include -->
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/generic.js" type="text/javascript"></script>
		<!-- Bootstrap include -->
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" type="text/css"/>
		<script src="bootstrap/js/bootstrap.js" type="text/javascript"></script>
		<!-- Datapicker include -->
		<link rel="stylesheet" href="datepicker/css/datepicker.css" type="text/css"/>
		<script src="datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
	</head>

	<body style="margin-top:10px;">

		<div class="container">

			<h1>Do Diesis</h1>

			<div class="btn-toolbar">
				<div class="btn-group">
					<a href="index.php" class="btn"><i class="icon-home"></i></a>
					<a href="partners.php" class="btn"><i class="icon-user"></i></a>
					<a href="groups.php" class="btn"><i class="icon-th-list"></i></a>
				</div>
			</div>

			<hr />

			<form action="." method="post">
				<div class="controls controls-row">
					<input class="span6" name="description" type="text" placeholder="Description" required>
					<input class="span6" id="partner_id" name="partner_id" type="text" placeholder="Partner" required>
				</div>
				<div class="controls controls-row">
					<input class="span2" id="date" name="date" type="text" placeholder="Date" required readonly value="<?php  echo date("d/m/Y"); ?>">
					<input class="span3" id="group_id" name="group_id" type="text" placeholder="Group" autocomplete="off" required>
					<input class="span3" name="in" id="in" type="text" placeholder="In">
					<input class="span3" name="out" id="out" type="text" placeholder="Out">
					<input class="span1 btn btn-primary" type="submit" value="+">
				</div>
			</form>

			<hr />

			<?php
				// if there are lines in the database do your work!
				if ( count($records) ) {
					echo '<table class="table table-striped table-bordered table-hover table-condensed">
							<tr>
								<td></td>
								<td><b>DESCRIPTION</b></td>
								<td><b>DATE</b></td>
								<td><b>PARTNER</b></td>
								<td><b>GROUP</b></td>
								<td><b>IN</b></td>
								<td><b>OUT</b></td>
								<td><b>SUBTOTAL</b></td>
							</tr>';
					$total_in = 0.00;
					$total_out = 0.00;
					$total_sub = 0.00;
					foreach( $records as $r ) {
						// Draw a table row
						$group = R::load('group', $r['group_id'] );
						$partner = R::load('partner', $r['partner_id'] );
						echo '<tr>
								<td>
									<a onclick="return confirm_delete()" href="?unlink=' . $r['id'] . '">
										<button class="btn btn-danger btn-mini del_line" data-original-title="">X</button>
									</a>
								</td>
								<td>' . $r['description'] . '</td>
								<td>' . datetime_to_date($r['date']) . '</td>
								<td>' . $partner->name . '</td>
								<td>' . $group->name . '</td>
								<td>' . $r['in'] . '</td>
								<td>' . $r['out'] . '</td>
								<td>' . ($r['in'] - $r['out']) . '</td>
							</tr>';
						// Subtotal
						$total_in += $r['in'];
						$total_out += $r['out'];
						$total_sub += ($r['in'] - $r['out']);
						} // foreach
					// Draw the last row with the total values
					echo '<tr>
						<td colspan="5"></td>
						<td><b>' . $total_in . '</b></td>
						<td><b>' . $total_out . '</b></td>
						<td><b>' . $total_sub . '</b></td>
					</tr>';
					echo '</table>';
					} // if
				// else show a simple message (for now)
				else {
					echo '<div class="alert">
							<strong>OPS!</strong> There isn\'t record in this database. Please create a new line!
						</div>';
					} // else
			?>

		</div><!-- .container -->

	</body>

	<script type="text/javascript">

		$('#date').datepicker({ 
			format : "dd/mm/yyyy",
			weekStart : 1,
			})

		$("#group_id").typeahead({
			source: function(query, process) {
				$.post("lib/get_group.php", { 'group_name': query }, function(data) {
					objects = []; // going to browser
					map = {}; // storing for later
					$.each(data, function(i, entity) {
						//map[entity.name] = name;
						objects.push( '[' + entity.id + '] ' + entity.name);
					});
					return process(objects);
				},"json");
			},
		});

		$("#partner_id").typeahead({
			source: function(query, process) {
				$.post("lib/get_partner.php", { 'partner_name': query }, function(data) {
					objects = []; // going to browser
					map = {}; // storing for later
					$.each(data, function(i, entity) {
						//map[entity.name] = name;
						objects.push( '[' + entity.id + '] ' + entity.name);
					});
					return process(objects);
				},"json");
			},
		});

	</script>

</html>
