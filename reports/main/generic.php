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

	// Include always the Parser
	include_once('../parser.php');
	$parser = new Parser();
	$parser->init();

	// Extract all the main record
	$records = R::findAll(__MAIN_TABLE__, ' ORDER BY date ');

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Main Report</title>
		<?php echo $parser->default_stylesheet(); ?>
	</head>

	<body <?php echo $parser->on_load_page(); ?>>

		<h1>Report: Main</h1>

		<?php
			// if there are lines in the database do your work!
			if ( count($records) ) {
				echo '<table>
						<tr>
							<td><b>DESCRIPTION</b></td>
							<td><b>DATE</b></td>
							<td><b>PARTNER</b></td>
							<td><b>PAYMENT</b></td>
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
					$group = R::load(__GROUP_TABLE__, $r['group_id'] );
					$partner = R::load(__PARTNER_TABLE__, $r['partner_id'] );
					$payment_method = R::load(__PAYMENT_METHOD_TABLE__, $r['payment_method_id'] );
					echo '<tr>
							<td>' . $r['description'] . '</td>
							<td>' . datetime_to_date($r['date']) . '</td>
							<td>' . $partner->name . '</td>
							<td>' . $payment_method->name . '</td>
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
		?>

	</body>

</html>
