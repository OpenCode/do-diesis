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

	// Extract all the main record
	$records = R::findAll(__MAIN_TABLE__, ' ORDER BY date LIMIT 50');

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
			<title>Do Diesis</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php echo $Template->get_head(); ?>
		<!--script src="js/jquery.sparkline.min.js" type="text/javascript"></script-->
		<script src="js/jsapi.js" type="text/javascript"></script>
		<script src="js/chartkick.js" type="text/javascript"></script>
	</head>

	<body style="margin-top:10px;">

		<div class="container-fluid">

			<div class="row-fluid">
				<?php echo $Template->get_header(); ?>
			</div>

			<div class="row-fluid">

				<div class="span2">
					<?php echo $Template->get_sidebar_nav('index'); ?>
				</div>

				<div class="span10 main-content">

					<div class="row-fluid">
						<h5 class="span6">Last Movements Chart</h5>
						<h5 class="span6">In/Out Relation</h5>
					</div>

					<div class="row-fluid">
						<div id="movements_chart" class="chart span6" style="height: 300px;"></div>
						<div id="in_out_chart" class="chart span6" style="height: 300px;"></div>
					</div>

					<?php 
						$notes = R::findAll(__NOTE_TABLE__, ' ORDER BY date DESC LIMIT 4');
						if ( count($notes) ) {
							echo '<h5>Last 4 Notes</h5>
							<div class="row-fluid">';
							foreach ( $notes as $n ) {
								echo '<div class="notes span3">
								<p><b>' .  datetime_to_date($n->date) . '</b></p>
								<p>' . $n->text . '</p>
								</div>';
								}
							echo '</div>';
							}

						// if there are lines in the database do your work!
						$movements_chart_values_in = '{';
						$movements_chart_values_out = '{';
						$total_in = 0.00;
						$total_out = 0.00;
						if ( count($records) ) {
							$total_sub = 0.00;
							foreach( $records as $r ) {
								// Subtotal
								$total_in += $r['in'];
								$total_out += $r['out'];
								$total_sub += ($r['in'] - $r['out']);
								// Chart values
								if ( $r['in'] ) $movements_chart_values_in = $movements_chart_values_in . '"' . $r['date'] . '":' . $r['in'] . ',';
								if ( $r['out'] ) $movements_chart_values_out = $movements_chart_values_out . '"' . $r['date'] . '":-' . $r['out'] . ',';
								} // foreach
							} // if
						$movements_chart_values_in = $movements_chart_values_in . '}';
						$movements_chart_values_out = $movements_chart_values_out . '}';
						$in_out_chart_values = '[' . $total_in . ',' . $total_out . ']';
					?>

				</div><!-- .span10 -->
			</div><!-- .row -->
				
			<div class="row-fluid">
				<?php echo $Template->footer(); ?>
			</div><!-- .row -->

		</div><!-- .container -->

	</body>

	<script>
		data = [
			{ "name":"In", "data":  <?php echo $movements_chart_values_in; ?>},
			{ "name":"In", "data":  <?php echo $movements_chart_values_out; ?>},
		]
		new Chartkick.ColumnChart("movements_chart", data);
		new Chartkick.PieChart("in_out_chart", {"In": <?php echo $total_in; ?>, "Out": <?php echo $total_out; ?>});
	</script>

	<script type="text/javascript">

		$('#date').datepicker({ 
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

		<?php echo $Template->common_script(); ?>

	</script>

</html>
