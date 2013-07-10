
function confirm_delete() {
	return confirm('Do you want really delete this record?');
	}

function confirm_set_null() {
	return confirm('All the record from other table with reference to this one will be set to null.\n\nDo you want really delete this record?');
	}

function printReport(page) {
	console.log(page);
	printWindow = window.open( page, "mywindow");
	printWindow.print();
	printWindow.close();
	}

function fill_edit_form(vals) {
	$('#read_modal').modal('hide');
	$('#insert_modal').modal();
	for (key in vals) {
		$('#' + key).val(vals[key]);
		}
	}

function fill_read_section(vals) {
	$('#read_modal').modal();
	for (key in vals) {
		$('#read_' + key).text(vals[key]);
		}
	}
