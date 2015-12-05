<?php
	// MYSQL Connection and Database Selection
	include_once('db.config.php');
	$dbh = mysql_connect($mysql_host, $mysql_user, $mysql_pass);
	mysql_select_db($mysql_database, $dbh);
	if (!$dbh) {
		die('Could not connect to mysql: '. mysql_errno() . mysql_error());
	}

	// Decimal number cleaner i.e, removing trailing zeros
	// This function should go to a function library if one is built
	function clean_num( $num ){
		$pos = strpos($num, '.');
		if($pos === false) { // it is integer number
			return $num;
		}
		else{ // it is decimal number
			return rtrim(rtrim($num, '0'), '.');
		}
	}

	function price2code ( $price, $code) {
		$price_arr = str_split($price);
		$nums_arr = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0' );
		$code_arr = str_split($code);
		return str_replace( $nums_arr, $code_arr, $price);
	}

	//Including Calendar class
	require_once('calendar/classes/tc_calendar.php');

	//Load Configuration Variables
	$currency = mysql_result(mysql_query('SELECT value from stock_config where name="currency"'),0);
	$company_name = mysql_result(mysql_query('SELECT value from stock_config where name="company_name"'),0);
	$price_code = mysql_result(mysql_query('SELECT value from stock_config where name="price_code"'),0);
	$sql_q = mysql_query('SELECT value from stock_config where name="transaction"');
	while( $res_trans = mysql_fetch_array($sql_q) ){
		$transaction_names[] = $res_trans['value'];
	}
	$sql_q = mysql_query('SELECT value from stock_config where name="trans_type"');
	while( $res_tt = mysql_fetch_array($sql_q) ){
		$trans_types[] = $res_tt['value'];
	}

	// ItemID from GET
	$current_itemid=$_GET['iid'];
	$current_itemname = mysql_result(mysql_query("SELECT item_name from stock_items where item_id='$current_itemid'"),0);

	//If delform is posted, delete transcation from the db
	if ( isset($_POST['del_tid']) ) {
		$del_tid=$_POST['del_tid'];
		$sql_q="DELETE FROM stock_transactions WHERE trans_id='$del_tid'";
		$result = mysql_query($sql_q);
		if ( $result )
			echo '<script type="text/javascript">window.alert("Record deleted successfully")</script>';
		else
			echo '<script type="text/javascript">window.alert("ERROR!! Failed to delete record. SQL='.$sql_q.'")</script>';
		header ("Location: ".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']);
	}


	// If addform is posted, insert values in database
	if ( isset($_POST['sdate']) && isset($_POST['trans']) && isset($_GET['iid']) && isset($_POST['trans_t']) && isset($_POST['sinvoiceno']) && isset($_POST['suprice']) && isset($_POST['sqty']) ){

			$sdate=$_POST['sdate'];
			$trans=$_POST['trans'];
			$trans_t=$_POST['trans_t'];
			$sinvoiceno=$_POST['sinvoiceno'];
			$suprice=$_POST['suprice'];
			$sqty=$_POST['sqty'];
			$scomments=$_POST['scomments'];
			$timestamp=time();
			$sql_q="INSERT INTO stock_transactions VALUES ('', '$sdate', '$current_itemid', '$trans', '$trans_t', '$sinvoiceno', '$suprice', '$sqty', '$scomments', '$timestamp');";
			$result = mysql_query($sql_q);

			if ( $result )
				//echo '<script type="text/javascript">window.alert("'.$trans.' record Added Successfully")</script>';
				echo '';
			else
				echo '<script type="text/javascript">window.alert("ERROR!! Failed to Add '.$trans.' record. SQL='.$sql_q.'")</script>';
			header ("Location: ".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']);
		}
?>
<html>
<head>
	<title><?php echo $company_name;?> Stock :: <?php echo $current_itemname; ?></title>
	<script type="text/javascript" src="calendar/calendar.js"></script>
	<script type="text/javascript">
		function updatetotal() {
		document.addform.stotal.value = (document.addform.sqty.value -0) * (document.addform.suprice.value -0);
		}
		function validateForm() {
			var dt=document.addform.sdate.value
			var trns=document.addform.trans.value
			var trnst=document.addform.trans_t.value
			var invno=document.addform.sinvoiceno.value;
			var unpr=document.addform.suprice.value;
			var qty=document.addform.sqty.value
			var scmnt=document.addform.scomments.value
			if ( invno == "" ){
				alert("Invoice number is empty!");
				return false;
			}
			if ( unpr == "0" ){
				alert("Unit price is zero!");
				return false;
			}
			//if (!confirm("Adding the following entry:\n\nDate: " + dt + "\nTransaction: " + trns + "\nType: " + trnst + "\nInvoice#: " + invno + "\nUnit price: " + unpr + "\nQuantity: " + qty + "\nComments: " + scmnt)){
			//	return false;
			//}
			document.addform.submit();
		}
	</script>

	<link href="css/table_box.css" rel="stylesheet" type="text/css">
	<link href="calendar/calendar.css" rel="stylesheet" type="text/css">
	<link href="css/panel.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		html,body,form {margin:0;padding:0;}
	</style>
</head>

<body bgcolor="silver">
<?php include_once('panel.php'); ?>

<center>
	<table border="1" id="table_box">
		<thead>		
			<tr>
				<th style="font-size:22px;color:black; border:0px" colspan=8 align=center>
					<table width="100%" border=0><tr>
						<th style="border-top:0px; border-bottom:0px; font-size:22px" align=center>
							<?php echo "$current_itemname";?>
						</td>
						<th style="border-top:0px; border-bottom:0px; color:#880000;" align=right>
							<div id="stockdiv">ERROR</div>
						</td>
					</tr></table>
				</th>
			</tr>
			<tr>
				<!--<th>#</th>--!>
				<th>Date</th>
				<th>Transaction</th>
				<th>Type</th>
				<th>Invoice No.</th>
				<th>Unit Price</th>
				<th>Qty</th>
				<th>Total</th>
				<th>Comments</th>
			</tr>

			<form name="addform" method="post"  action="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']; ?>">		
				<tr>
					<!--<td>
						<input type=submit value=Add />
					</td>--!>
					<th>
						
						<?php
							$myCalendar = new tc_calendar("sdate", true, false);
							$myCalendar->setIcon("calendar/images/iconCalendar.gif");
							$myCalendar->setPath("calendar/");
							$myCalendar->setYearInterval(2012, 2020);
							$myCalendar->setAlignment('left', 'bottom');
							$myCalendar->setDate(date('d'), date('m'), date('Y'));
							$myCalendar->writeScript();
						?>
					</th>
					<th>
						<select name="trans">
						<?php
							foreach ($transaction_names as $_trans ){
								echo "<option value=\"$_trans\">$_trans</option>";
							}
						?>
						</select>
					</th>
					<th>
						<select name="trans_t">
						<?php
							foreach ($trans_types as $_transt ){
								echo "<option value=\"$_transt\">$_transt</option>";
							}
						?>
						</select>

					</th>
					<th>
						<input name="sinvoiceno" id="add-form" type="text" size="20" />
					</th>
					<th>
						<input name="suprice" value="0" id="add-form" type="text" size="5" onChange="updatetotal()" /><?php echo $currency?>
					</th>
					<th>
						<input name="sqty" value="1" id="add-form" type="text" size="5" onChange="updatetotal()" />
					</th>
					<th>
						<input name="stotal" disabled="disabled" value="0" id="add-form" type="text" size="5" /><?php echo $currency?>
					</th>
					<th>
						<textarea name="scomments" id="add-form" rows=1 cols=24 ></textarea>
						<a href="javascript:;"><img id="button" height=35 width=35 src="imgs/add.png" onclick="validateForm();"></img></a>
					</th>
				</tr>
			</form>
		</thead>

		<tbody>

				<?php
					// Select Qurey to print the content
					$res_table = mysql_query("SELECT trans_id, date, transaction, trans_type, invoice_no, uprice, qty, comments, timestamp FROM stock_transactions WHERE item_id = $current_itemid ORDER BY trans_id DESC");

					//counters for counting
					$item_stock=0;
					$item_sale=0;
					$item_purchase=0;
					$item_hold=0;

					while($row = mysql_fetch_array($res_table)){

						if ( $row['transaction'] == 'Purchase' )
							$row_style='style="color:#d00000"';
						else
							$row_style='';

						echo '<tr>';
						//echo '<td '.$row_style.'>'.$row['trans_id'].'</td>';
						echo '<td '.$row_style.'>'.$row['date'].'</td>';
						echo '<td '.$row_style.'>'.$row['transaction'].'</td>';
						echo '<td '.$row_style.'>'.$row['trans_type'].'</td>';
						echo '<td '.$row_style.'>'.$row['invoice_no'].'</td>';
						echo '<td '.$row_style.' title="'.clean_num($row['uprice']).'">'.price2code(clean_num($row['uprice']),$price_code).'</td>';
						echo '<td '.$row_style.'>'.clean_num($row['qty']).'</td>';
						echo '<td '.$row_style.' title="'.clean_num($row['uprice'] * $row['qty']).'">'.price2code(clean_num($row['uprice'] * $row['qty']),$price_code).'</td>';
						echo '<td '.$row_style.'><table style="padding:0px; margin:0px" width="100%" border="0"><tr><td style="border-bottom:0px; padding:0px;"><pre style="display:inline;">'.$row['comments'].'</pre></td>';

						//if the entery is fresh, or hold type then show the delete button
						$curr_ts=time();
						$ts_diff=$curr_ts-$row['timestamp'];
						if ( $ts_diff < 180 || $row['transaction'] == "Hold" ) {
							echo '<td style="border-bottom:0px; padding:0px;" align="right"><form style="display:inline" id="delform" name="delform" method="post"  action="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'">';
							echo '<input type="hidden" name="del_tid" value="'.$row['trans_id'].'" />';
							echo '<button style="margin:0;padding:0;" type=submit><img id="delbutton" height=8 width=8 src="imgs/del.png"></img></button>';
							echo '</form></td></tr></table></td>';
						}
						else
							echo '</tr></table></td>';
				
						echo '</tr>';
						if ( $row['transaction'] == "Purchase" ) {
							$item_purchase += $row['qty'];
						}
						elseif ( $row['transaction'] == "Sale" ) {
							$item_sale += $row['qty'];
						}
						elseif ( $row['transaction'] == "Hold" ) {
							$item_hold += $row['qty'];
						}
					}
					$item_stock = $item_purchase - $item_sale - $item_hold;
					echo '<script type="text/javascript">document.getElementById("stockdiv").innerHTML = "[Purchase:'.$item_purchase.' | Sale:'.$item_sale.' | Hold:'.$item_hold.' | Stock:'.$item_stock.']"</script>';
				?>	
		</tbody>
	</table>

</center>
</body>
