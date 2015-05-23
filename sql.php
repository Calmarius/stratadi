<?php

$rawQuery=get_magic_quotes_gpc() ? stripslashes(@$_POST['query']) : @$_POST['query'];
$rawError=get_magic_quotes_gpc() ? stripslashes(@$_GET['error']) : @$_GET['query'];

require_once('presenterphps.php');
require_once('setupmysql.php');
require_once('utils/gameutils.php');
bounceNoAdmin();


function sqlStuffFunc($param)
{
	jumpTo('sql.php?error='.urlencode($param));
}

$myId=$_SESSION['userId'];

$rows=array();
$columns=array();
$numRows=0;
$affectedRows=0;
if (isset($_POST['query']))
{
	$_SESSION['sqlRawQuery']=$rawQuery;
	$r=doMySqlQuery($rawQuery,'sqlStuffFunc');
	$numRows=@mysql_num_rows($r);
	$affectedRows=mysql_affected_rows();
	while($row=@mysql_fetch_assoc($r))
	{
		$rows[]=$row;
	}
	if (isset($rows[0]))
	{
		$columns=array_keys($rows[0]);
	}
}


?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>SQL querier</title>
		<link rel="stylesheet" href="main.css">
	</head>
	<body>
		<form method="POST" action="sql.php">
			<textarea name="query" rows="25" cols="80" id="queryArea"><?php echo $_SESSION['sqlRawQuery']; ?></textarea>
			<input type="submit">
		</form>
		<p class="negative"><?php echo htmlspecialchars(@$rawError); ?></p>
		<p>Num rows: <?php echo $numRows;?></p>
		<p>Affected rows: <?php echo $affectedRows;?></p>
		<table>
			<tr>
				<?php
					foreach($columns as $key=>$value)
					{
						?>
							<th><?php echo $value?></th>
						<?php
					}
				?>
			</tr>
			<?php
				foreach($rows as $key=>$row)
				{
					?>
						<tr>
							<?php
								foreach($row as $key2=>$value)
								{
									?>
										<td><?php echo $value===NULL ? '<i>NULL</i>':htmlspecialchars($value) ;?></td>
									<?php
								}
							?>
						</tr>
					<?php
				}
			?>
		</table>
		<script>
			function getCaretPosition (ctrl) {
				var CaretPos = 0;	// IE Support
				if (document.selection) {
				ctrl.focus ();
					var Sel = document.selection.createRange ();
					Sel.moveStart ('character', -ctrl.value.length);
					CaretPos = Sel.text.length;
				}
				// Firefox support
				else if (ctrl.selectionStart || ctrl.selectionStart == '0')
					CaretPos = ctrl.selectionStart;
				return (CaretPos);
			}
			function setCaretPosition(ctrl, pos){
				if(ctrl.setSelectionRange)
				{
					ctrl.focus();
					ctrl.setSelectionRange(pos,pos);
				}
				else if (ctrl.createTextRange) {
					var range = ctrl.createTextRange();
					range.collapse(true);
					range.moveEnd('character', pos);
					range.moveStart('character', pos);
					range.select();
				}
			}
			document.getElementById('queryArea').onkeydown=
			function(e)
			{
				var ev=e || window.event;
				var kc=(ev.keyCode==undefined) ? ev.which: ev.keyCode;
				if (kc==9)
				{
					var str=this.value;
					var cp=getCaretPosition(this);
					str=str.substr(0,cp)+'\t'+str.substr(cp);
					this.value=str;
					setCaretPosition(this,cp+1);
					return false;
				}
				return true;
			}
		</script>
	</body>
</html>




