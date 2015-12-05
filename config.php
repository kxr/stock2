<?php
	// MYSQL Connection and Database Selection
	include_once('db.config.php');
	$dbh = mysql_connect($mysql_host, $mysql_user, $mysql_pass);
	mysql_select_db($mysql_database, $dbh);
	if (!$dbh) {
		die('Could not connect to mysql: '. mysql_errno() . mysql_error());
	}

?>
<html>
<head>
	<title><?php echo $company_name;?> Stock :: Configuration</title>
	<link href="css/table_box.css" rel="stylesheet" type="text/css">
	<link href="css/panel.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		html,body {margin:0;padding:0;}
	</style>
</head>

<body bgcolor="silver">
<?php include_once('panel.php'); ?>
<br />
<center>
	<table border="1" id="table_box">
		<thead>		
			<tr>
				<th>Name</th>
				<th>Value</th>
			</tr>
		</thead>

		<tbody>
				<?php
					// Select Qurey to print the content
					$res_table = mysql_query('SELECT * FROM stock_config');

					while($row = mysql_fetch_array($res_table)){
						echo '<tr>';
						echo '<td>'.$row['name'].'</td>';
						echo '<td>'.$row['value'].'</td>';
						echo '</tr>';
					}
				?>
		</tbody>
	</table>



</center>
</body>
