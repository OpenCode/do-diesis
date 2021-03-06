<?php

	/************************************************************************

	Copyright 2013 Francesco OpenCode Apruzzese for e-ware.org 
	<info@e-ware.org, cescoap@gmail.com>

	This file is part of Do-Diesis

	Nome-Programma is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Nome-Programma is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Nome-Programma.  If not, see <http://www.gnu.org/licenses/>.

	************************************************************************/

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
			$main = R::load(__MAIN_TABLE__, $_POST['line_id']);
			}
		else {
			$main = R::dispense(__MAIN_TABLE__);
			}
		$main->description = $_POST['description'];
		$main->date = date_to_datetime($_POST['date']);
		$main->in = $_POST['in'];
		$main->out = $_POST['out'];
		$main->group = R::load(__GROUP_TABLE__, extract_relation_id($_POST['group_id']) );
		$main->partner = R::load(__PARTNER_TABLE__, extract_relation_id($_POST['partner_id']) );
		$main->paymentmethod = R::load(__PAYMENT_METHOD_TABLE__, extract_relation_id($_POST['payment_method_id']) );
		R::store($main);
	} // if

	// Delete record passed to page
	if ( $_GET && isset($_GET['unlink']) ) {
		$main = R::load(__MAIN_TABLE__, $_GET['unlink']);
		R::trash( $main );
	} // if

	// Get the limit
	$limit = '50';
	if ( $_GET && isset($_GET['limit']) )
		$limit = $_GET['limit'];

	// Get the order
	$order = 'date DESC';
	if ( $_GET && isset($_GET['order']) )
		$order = $_GET['order'];

	// Get the filter
	$filter = '';
	$filter_description = '';
	$filter_date_from = '';
	$filter_date_to = '';
	$filter_partner_id = '';
	$filter_group_id = '';
	$filter_payment_method_id = '';
	if ( $_GET && isset($_GET['filter_description']) || isset($_GET['filter_date']) ) {
		// Filter Description
		if ( isset($_GET['filter_description']) && $_GET['filter_description'] ) {
			$filter .= " description LIKE '%" . $_GET['filter_description'] . "%' AND ";
			$filter_description = $_GET['filter_description'];
			}
		// Filter Date From
		if ( isset($_GET['filter_date_from']) && $_GET['filter_date_from'] ) {
			$filter .= " date >= '" . date_to_datetime($_GET['filter_date_from']) . "' AND ";
			$filter_date_from = $_GET['filter_date_from'];
			}
		// Filter Date To
		if ( isset($_GET['filter_date_to']) && $_GET['filter_date_to'] ) {
			$filter .= " date <= '" . date_to_datetime($_GET['filter_date_to']) . "' AND ";
			$filter_date_to = $_GET['filter_date_to'];
			}
		// Filter Partner
		if ( isset($_GET['filter_partner_id']) && $_GET['filter_partner_id'] ) {
			$filter .= " partner_id = " . extract_relation_id($_GET['filter_partner_id']) . " AND ";
			$filter_partner_id = $_GET['filter_partner_id'];
			}
		// Filter Group
		if ( isset($_GET['filter_group_id']) && $_GET['filter_group_id'] ) {
			$filter .= " group_id = " . extract_relation_id($_GET['filter_group_id']) . " AND ";
			$filter_group_id = $_GET['filter_group_id'];
			}
		// Filter Payment Method
		if ( isset($_GET['filter_payment_method_id']) && $_GET['filter_payment_method_id'] ) {
			$filter .= " paymentmethod_id = " . extract_relation_id($_GET['filter_payment_method_id']) . " AND ";
			$filter_payment_method_id = $_GET['filter_payment_method_id'];
			}
		// Clear last chars from filter string
		$filter = rtrim($filter,'AND ');
		}

	// Extract all the main record
	if ( !$filter)
		$records = R::findAll(__MAIN_TABLE__, ' ORDER BY ' . $order . ' LIMIT ' . $limit);
	else
		$records = R::find(__MAIN_TABLE__, ' ' . $filter . ' ORDER BY ' . $order . ' ' . ' LIMIT ' . $limit);

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
					<?php echo $Template->get_sidebar_nav('main'); ?>
				</div>

				<div class="span10 main-content">
					
					<div class="row-fluid">
						<div class="btn-group reports-buttons">
							<a onClick="$('#insert_modal').modal();" href="#" role="button" class="btn"><i class="icon-file"></i></a>
						</div>
					
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
							echo '<div id="reports" class="btn-group reports-buttons">';
										foreach ( $reports as $r ) {
											echo $r;
											}
							echo '	</div>';
						}
					?>
					
					</div>

					<form id="search_form" action="<?php echo __MAIN_PAGE__; ?> " method="get">
						<div class="controls controls-row span12">
							<input class="span6" id="filter_description" name="filter_description" type="text" placeholder="Filter Description" value="<?php echo $filter_description; ?>">
							<input class="span6" id="filter_partner_id" name="filter_partner_id" type="text" placeholder="Filter Partner" autocomplete="off" value="<?php echo $filter_partner_id; ?>">
						</div>
						<div class="controls controls-row">
							<input class="span2" id="filter_date_from" name="filter_date_from" type="text" placeholder="Filter Date From" value="<?php echo $filter_date_from; ?>" autocomplete="off">
							<input class="span2" id="filter_date_to" name="filter_date_to" type="text" placeholder="Filter Date To" value="<?php echo $filter_date_to; ?>" autocomplete="off">
							<input class="span3" id="filter_group_id" name="filter_group_id" type="text" placeholder="Filter Group" autocomplete="off" value="<?php echo $filter_group_id; ?>">
							<input class="span4" id="filter_payment_method_id" name="filter_payment_method_id" type="text" placeholder="Filter Payment Method" autocomplete="off" value="<?php echo $filter_payment_method_id; ?>">
							<button class="span1 btn btn-primary" type="submit" value=""><i class="icon-search icon-white"></i></button>
						</div>
					</form>

					<?php
						// if there are lines in the database do your work!
						if ( count($records) ) {
							echo '<table class="table table-striped table-bordered table-hover table-condensed">
									<tr>
										<td></td>
										<td><b><i class="icon-chevron-'.get_order_icon($order, 'description').'"></i> <a href="?order='.invert_order($order, 'description').'">DESCRIPTION</a></b></td>
										<td><b><i class="icon-chevron-'.get_order_icon($order, 'date').'"></i> <a href="?order='.invert_order($order, 'date').'">DATE</a></b></td>
										<td><b>PARTNER</b></td>
										<td><b>GROUP</b></td>
										<td><b>PAYMENT</b></td>
										<td><b>IN</b></td>
										<td><b>OUT</b></td>
										<td><b>SUBTOTAL</b></td>
									</tr>';
							$total_in = 0.00;
							$total_out = 0.00;
							$total_sub = 0.00;
							foreach( $records as $r ) {
								// Draw a table row
								echo '<tr>
										<td>
											<!-- DELETE -->
											<a onclick="return confirm_delete()" href="?unlink=' . $r->id . '">
												<button class="btn btn-danger btn-mini del_line" ><i class="icon-remove-circle"></i></button>
											</a>
											<!-- EDIT -->
											<a onclick=\'fill_edit_form(
												{"line_id" : ' . $r->id . ',
												"description" : "' . $r->description . '",
												"partner_id" : "[' . $r->partner->id . '] ' . $r->partner->name . '",
												"group_id" : "[' . $r->group->id . '] ' . $r->group->name . '",
												"payment_method_id" : "[' . $r->paymentmethod->id . '] ' . $r->paymentmethod->name . '",
												"date" : "' . datetime_to_date($r->date) . '",
												"in" : "' . $r->in . '",
												"out" : "' . $r->out . '",
												})\'>
												<button class="btn btn-mini edit_line" ><i class="icon-edit"></i></button>
											</a>
										</td>
										<td>' . $r->description . '</td>
										<td>' . datetime_to_date($r->date) . '</td>
										<td>' . $r->partner->name . '</td>
										<td>' . $r->group->name . '</td>
										<td>' . $r->paymentmethod->name  . '</td>
										<td>' . $r->in . '</td>
										<td>' . $r->out . '</td>
										<td>' . ($r->in - $r->out) . '</td>
									</tr>';
								// Subtotal
								$total_in += $r->in;
								$total_out += $r->out;
								$total_sub += ($r->in - $r->out);
								} // foreach
							// Draw the last row with the total values
							echo '<tr>
								<td colspan="6"></td>
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

				</div><!-- .span10 -->
			</div><!-- .row -->
				
			<div class="row-fluid">
				<?php echo $Template->footer(); ?>
			</div><!-- .row -->

		<!-- New Modal -->
		<div id="insert_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>Insert record</h3>
			</div>
			<form id="insert_form" action="<?php echo __MAIN_PAGE__; ?> " method="post">
				<div class="modal-body">
					<input id="line_id" class="span12" name="line_id" type="hidden" value="0">
					<table class="table table-unbordered table-condensed">
						<tr>
							<td colspan="6"><b>DESCRIPTION</b></td>
						</tr>
						<tr>
							<td colspan="6"><input id="description" name="description" type="text" placeholder="Description" required></td>
						</tr>
						<tr>
							<td colspan="3" width="50%"><b>PARTNER</b></td>
							<td colspan="3" width="50%"><b>DATE</b></td>
						</tr>
						<tr>
							<td colspan="3"><input id="partner_id" name="partner_id" type="text" placeholder="Partner" autocomplete="off" required></td>
							<td colspan="3"><input id="date" name="date" type="text" placeholder="Date" required readonly value="<?php  echo date("d/m/Y"); ?>"></td>
						</tr>
						<tr>
							<td colspan="3"><b>GROUP</b></td>
							<td colspan="3"><b>PAYMENT METHOD</b></td>
						</tr>
						<tr>
							<td colspan="3"><input id="group_id" name="group_id" type="text" placeholder="Group" autocomplete="off" required onFocus="$('.datepicker').css('display', 'none');"></td>
							<td colspan="3"><input id="payment_method_id" name="payment_method_id" type="text" placeholder="Payment Method" autocomplete="off" required></td>
						</tr>
						<tr>
							<td colspan="3"><b>IN</b></td>
							<td colspan="3"><b>OUT</b></td>
						</tr>
						<tr>
							<td colspan="3"><input name="in" id="in" type="text" placeholder="In"></td>
							<td colspan="3"><input name="out" id="out" type="text" placeholder="Out"></td>
						</tr>
					</table>
				</div>
				<div class="modal-footer">
					<input class="span1 btn btn-primary" type="submit" value="+">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				</div>
			</form>
		</div>

		</div><!-- .container -->

	</body>

	<script type="text/javascript">

		$('#date').datepicker({ 
			format : "dd/mm/yyyy",
			weekStart : 1,
			})

		$('#filter_date_from').datepicker({ 
			format : "dd/mm/yyyy",
			weekStart : 1,
			})

		$('#filter_date_to').datepicker({ 
			format : "dd/mm/yyyy",
			weekStart : 1,
			})

		$("#group_id").typeahead({
			source: function(query, process) {
				$.post("lib/<?php echo __GET_GROUP_PAGE__; ?>", { 'group_name': query }, function(data) {
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

		$("#filter_group_id").typeahead({
			source: function(query, process) {
				$.post("lib/<?php echo __GET_GROUP_PAGE__; ?>", { 'group_name': query }, function(data) {
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
				$.post("lib/<?php echo __GET_PARTNER_PAGE__; ?>", { 'partner_name': query }, function(data) {
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

		$("#filter_partner_id").typeahead({
			source: function(query, process) {
				$.post("lib/<?php echo __GET_PARTNER_PAGE__; ?>", { 'partner_name': query }, function(data) {
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

		$("#payment_method_id").typeahead({
			source: function(query, process) {
				$.post("lib/<?php echo __GET_PAYMENT_METHOD_PAGE__; ?>", { 'pm_name': query }, function(data) {
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

		$("#filter_payment_method_id").typeahead({
			source: function(query, process) {
				$.post("lib/<?php echo __GET_PAYMENT_METHOD_PAGE__; ?>", { 'pm_name': query }, function(data) {
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

		<?php echo $Template->common_script(); ?>

	</script>

</html>
