<?php
	$parts = explode('/', $_SERVER["PHP_SELF"]);
	$self_file = $parts[count($parts) - 1];

	if ( "$self_file" == "items.php" ) {
		$style_items=' style="color: white;" ';
	}
	elseif ( "$self_file" == "transactions.php" ) {
		$style_transac=' style="color: white;" ';
	}
?>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="right" valign="top">
			<table cellpadding="5" cellspacing="0" bgcolor="#212121">
				<tr>
					<td><a class="panel" href="items.php" <?php echo $style_items;?>>&nbsp;&nbsp;Items&nbsp;&nbsp;</a></td>
					<td><a class="panel" href="config.php" <?php echo $style_reports;?>><img src="imgs/settings.png" height="20" width="20"></img</a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
