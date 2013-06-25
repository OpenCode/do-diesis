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
			$main = R::load(__NOTE_TABLE__, $_POST['line_id']);
			}
		else {
			$main = R::dispense(__NOTE_TABLE__);
			}
		$main->text = $_POST['text'];
		$main->date = date_to_datetime($_POST['date']);
		R::store($main);
	} // if

	// Delete record passed to page
	if ( $_GET && isset($_GET['unlink']) ) {
		$main = R::load(__NOTE_TABLE__, $_GET['unlink']);
		R::trash( $main );
	} // if

	// Get the order
	$order_type = 'text ASC';
	if ( $_GET && isset($_GET['order']) ) {
		$order_type = $_GET['order'];
		}

	// Get the filter
	$filter = '';
	$filter_text = '';
	if ( $_GET && isset($_GET['filter_text']) || isset($_GET['filter_text']) ) {
		// Filter Name
		if ( isset($_GET['filter_text']) && $_GET['filter_text'] ) {
			$filter .= " text LIKE '%" . $_GET['filter_text'] . "%' AND ";
			$filter_text = $_GET['filter_text'];
			}
		// Clear last chars from filter string
		$filter = rtrim($filter,'AND ');
		}
	// Extract all the main record
	if ( !$filter) {
		$records = R::findAll(__NOTE_TABLE__, ' ORDER BY ' . $order_type . ' ');
		}
	else {
		$records = R::find(__NOTE_TABLE__, ' ' . $filter . ' ORDER BY ' . $order_type . ' ');
		}

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
					<?php echo $Template->get_sidebar_nav('notes'); ?>
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

					<form action="<?php echo __NOTE_PAGE__; ?>" method="post">
						<div class="controls controls-row">
							<input class="span3" id="date" name="date" type="text" placeholder="Date" required readonly value="<?php  echo date("d/m/Y"); ?>">
							<input class="span8" id="text" name="text" type="text" placeholder="Text" required onFocus="$('.datepicker').css('display', 'none');">
							<input class="span1 btn btn-primary" type="submit" value="+">
						</div>
						<input id="line_id" class="span12" name="line_id" type="hidden" value="0">
					</form>

					<form action="<?php echo __NOTE_PAGE__; ?>" method="get">
						<div class="controls controls-row">
							<input class="span11" id="filter_text" name="filter_text" type="text" placeholder="Filter Text" value="<?php echo $filter_text; ?>">
							<button class="span1 btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
						</div>
					</form>

					<?php
						// if there are lines in the database do your work!
						if ( count($records) ) {
							echo '<table class="table table-striped table-bordered table-hover table-condensed">
									<tr>
										<td width="10%"></td>
										<td><b><i class="icon-chevron-'.get_order_icon($order_type, 'date').'"></i> <a href="?order='.invert_order($order_type, 'date').'">DATE</a></b></td>
										<td><b><i class="icon-chevron-'.get_order_icon($order_type, 'text').'"></i> <a href="?order='.invert_order($order_type, 'text').'">TEXT</a></b></td>
									</tr>';
							foreach( $records as $r ) {
								echo '<tr>
										<td>
											<!-- DELETE -->
											<a onclick="return confirm_delete()" href="?unlink=' . $r->id . '">
												<button class="btn btn-danger btn-mini del_line" >X</button>
											</a>
											<!-- EDIT -->
											<a onclick=\'fill_edit_form({"line_id" : ' . $r->id . ',"text" : "' . $r->text . '","date" : "' . datetime_to_date($r->date) . '",})\'>
												<button class="btn btn-mini edit_line" ><i class="icon-edit"></i></button>
											</a>
										</td>
										<td>' . datetime_to_date($r->date). '</td>
										<td>' . $r->text . '</td>';
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

		$('#date').datepicker({ 
			format : "dd/mm/yyyy",
			weekStart : 1,
			})
		
		<?php echo $Template->common_script(); ?>

	</script>

</html>
