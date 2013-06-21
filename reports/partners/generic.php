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
	$records = R::findAll(__PARTNER_TABLE__, ' ORDER BY name ASC ');

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Partners Report</title>
		<?php echo $parser->default_stylesheet(); ?>
	</head>

	<body <?php echo $parser->on_load_page(); ?>>

		<h1>Report: Partners</h1>

		<?php
			// if there are lines in the database do your work!
			if ( count($records) ) {
				echo '<table>
						<tr>
							<td><b>NAME</b></td>
						</tr>';
				foreach( $records as $r ) {
					// Draw a table row
					echo '<tr>
							<td>' . $r['name'] . '</td>
						</tr>';
					} // foreach
				echo '</table>';
				} // if
		?>

	</body>

</html>
