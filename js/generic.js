
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

function show_form(form_type) {
	if ( $('#' + form_type + '_form').is(":visible") ) {
		$('#' + form_type + '_form').hide();
		}
	else {
		$('#' + form_type + '_form').show();
		}
	}

function fill_edit_form(vals) {
	$('#insert_form').show();
	for (key in vals) {
		$('#' + key).val(vals[key]);
		}
	}
