<?php

global $language;
global $config

?>

<h1><?php echo $language['reports']; ?></h1>
<p>
	<?php
		if ($this->showhidden) echo '<a href="reports.php">'.$language['hidehiddenreports'].'</a>';
		else echo '<a href="reports.php?showhidden">'.$language['showhiddenreports'].'</a>';
	?>
</p>
<p class="center">
	<?php
		for($i=0;$i<$this->count;$i++)
		{
			?>
				<a href="reports.php?p=<?php echo $i; ?><?php echo $this->showhidden ? '&showhidden':''?>"><?php echo ($i+1);?></a>
			<?php
		}
	?>
</p>
	<script>
		function checkAllWithName(elmName,checkState)
		{
			var elms=document.getElementsByTagName('input');
			for(var i=0;i<elms.length;i++)
			{
				var elm=elms[i];
				if ((elm.getAttribute('type')=='checkbox') && (elm.getAttribute('name')==elmName))
				{
					elm.checked=checkState;
				}
			}
		}
	</script>
<form method="POST" action="dodeletereports.php" >
	<table class="center">
		<tr>
			<th></th>
			<th></th>
			<th><?php echo $language['title'];?></th>
			<th><?php echo $language['receivedat'];?></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<td><input type="checkbox" onclick="checkAllWithName('reports[]',this.checked)"></td><td colspan="6"></td>
		<?php
			foreach($this->reports as $key=>$value)
			{
				$reportTypeDescriptor=$config['reportTypes'][$value['reportType']];
				$langname=$language[$reportTypeDescriptor['langName']];
				?>
					<tr>
						<td><input type="checkbox" name="reports[]" value="<?php echo $value['id'];?>"></td>
						<td><img src="<?php echo $reportTypeDescriptor['image']; ?>" alt="<?php echo $langname; ?>" title="<?php echo $langname; ?>"></td>
						<td><a href="viewreport.php?id=<?php echo $value['id'];?>&amp;token=<?php echo $value['token']?>"><?php echo $value['title'];?></a></td>
						<td><?php echo $value['reportTime'];?></td>
						<td><?php echo $value['isRead'] ? '':$language['new'];?></td>
						<td><?php echo $value['isHidden'] ? $language['hidden']:'';?></td>
						<td><?php echo $value['isPublic'] ? $language['public']:'';?></td>
					</tr>
				<?php
			}
		?>
	</table>
	<p class="center"><input type="submit" value="<?php echo $language['delete']; ?>"></p>
</form>

