<?php
	//To avoid the browser from showing the cached page, it should always load a fresh page
	//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past


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
	$company_name = mysql_result(mysql_query('SELECT value from stock_config where name="company_name"'),0);
	// If form (new group) is posted, insert  values in database
	if ( isset($_POST['ngname']) ) {
		$ngname=$_POST['ngname'];
		$sql_q="INSERT INTO stock_itemgroups VALUES ('', '$ngname');";
		$result = mysql_query($sql_q);
		if ( $result )
			echo '<script type="text/javascript">window.alert("Group '.$ngname.' added Successfully")</script>';
		else
			echo '<script type="text/javascript">window.alert("ERROR!! Failed to add new group. SQL='.$sql_q.'")</script>';

		header ("Location: ".$_SERVER['PHP_SELF']);
	}

	// If form (add_form) is posted, insert values in database
	if ( isset($_POST['pgid']) && isset($_POST['piname']) && isset($_POST['pidetails'])  ){


			$pgid=$_POST['pgid'];
			$piname=$_POST['piname'];
			$pidetails=$_POST['pidetails'];

			$sql_q="INSERT INTO stock_items VALUES ('', '$pgid', '$piname', '$pidetails');";
			$result = mysql_query($sql_q);

			if ( $result )
				//echo '<script type="text/javascript">window.alert("Item added Successfully")</script>';
				echo '';
			else
				echo '<script type="text/javascript">window.alert("ERROR!! Failed to add new item. SQL='.$sql_q.'")</script>';
			header ("Location: ".$_SERVER['PHP_SELF']);
		}
?>
<html>
<head>
	<title><?php echo $company_name;?> Stock :: Items</title>
	<script type="text/javascript" src="calendar/calendar.js"></script>
	<script type="text/javascript">
		function validateForm() {
			var itn=document.addform.piname.value;
			var sid=document.getElementById("pgid");
			var itg=sid.options[sid.selectedIndex].text;
			var itd=document.addform.pidetails.value;
			if ( itn == "" ){
				alert("Item name is empty!");
				return false;
			}
			//if (!confirm("Adding the following Item:\n\nName: " + itn + "\nGroup: " + itg + "\nDetails: " + itd )){
			//	return false;
			//}
			document.addform.submit();
		}
		function add_group() {
			if (document.addform.pgid.value == "new_group") {
				var new_group_name=prompt("Enter the new group name","");
				if ( new_group_name ) {
					var myForm = document.createElement("form");
					myForm.method="post";
					myForm.action="<?php echo $_SERVER['PHP_SELF'] ?>";
					var myInput = document.createElement("input");
					myInput.setAttribute("name", "ngname");
					myInput.setAttribute("value", new_group_name);
					myForm.appendChild(myInput);
					document.body.appendChild(myForm);
					myForm.submit();
					document.body.removeChild(myForm);
				}
				else {
					alert("Group name is empty!");
					document.addform.pgid.selectedIndex=addform.pgid.options.length-2;
				}
			}



		}
	</script>

	<link href="css/table_box.css" rel="stylesheet" type="text/css">
	<link href="calendar/calendar.css" rel="stylesheet" type="text/css">
	<link href="css/panel.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		html,body {margin:0;padding:0;}
	</style>
<style>
div{height:25px;background-color:#fed;border:1px solid #f85;padding:6px }
#div1{-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;position:relative;margin:90px;}
</style>

</head>

<body bgcolor="silver">
<?php include_once('panel.php'); ?>

<center>
	<table border="0" id="table_box">
		<thead>		
			<tr>
				<th style="">Item Name</th>
				<th style="">Group</th>
				<th style="">Details</th>
				<th style="">Add</th>
			</tr>

			<form name="addform" method="post"  action="<?php echo $_SERVER['PHP_SELF']; ?>">		
				<tr>
					<th style="">
						<input name="piname" id="add-form" type="text" size="20" />
					</th>
					<th style="">
						<select name="pgid" id="pgid" onchange="add_group();">
						<?php
							$res_table = mysql_query('SELECT group_id, group_name FROM stock_itemgroups WHERE group_id != 1');
							while($row = mysql_fetch_array($res_table)){
								echo '<option value="'.$row['group_id'].'">'.$row['group_name'].'</option>';
							}
							echo '<option selected="selected" name="pgid" value="1"></option>';
							echo '<option value="new_group">New Group</option>';
						?>
						</select>

					</th>
					<th style="">
						<textarea name="pidetails" id="add-form" rows=1 cols=24 ></textarea>
					</th>
					<th style="">
						<a href="javascript:;"><img id="button" height=35 width=35 src="imgs/add.png" onclick="validateForm();"></img></a>
					</th>
				</tr>
			</form>
		</thead>
	</table>
<br>
</center>

	<table border="1" id="table_box" width="80%">
		<?php
			$res_groups = mysql_query ("SELECT group_id, group_name FROM stock_itemgroups;");
			while($row = mysql_fetch_array($res_groups)){
				echo '<thead><tr width="100%"><th align="center">';
				echo $row['group_name'];
				echo '</th></tr></thead>';
				echo '<tbody><tr><td style="line-height: 300%; word-wrap:normal;">';
					$res_items = mysql_query ('SELECT item_id, item_name, item_detail FROM stock_items WHERE group_id = "'.$row['group_id'].'";');
					while($row2 = mysql_fetch_array($res_items)){
						echo '<a style="text-decoration:none;" href="transactions.php?iid='.$row2['item_id'].'">';
						echo '<div id="div1" title="'.$row2['item_detail'].'" style="display:inline;margin:auto; white-space: nowrap;">'.$row2['item_name'].'</div></a> ';

					}
				echo '</td></tr></tbody>';
			}





		?>

	</table>

</body>
