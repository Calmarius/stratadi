<?php

global $language;
global $config;

?>

<div style="text-align:center">
	<h1><?php echo date('Y.m.d H:i:s'); ?></h1>
	<div style="position:relative; left:0; top:0; clear:both">
		<div class="thumbdiv">
			<h1><?php echo $language['inbuiltlevelspie']; ?></h1>
			<p class="center"><img src="<?php echo $this->inbuiltpieimg; ?>" alt=""></p>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['goldproductiontop10']; ?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr>
					<th>#</th>
					<th><?php echo $language['name']; ?></th>
					<?php
						if (!$this->hidden)
						{
							?>
								<th><?php echo $language['gold']; ?></th></tr>
							<?php
						}
					?>
				<?php
					$i=1;
					foreach($this->goldTop as $key=>$row)
					{
						?>
							<tr>
								<td><?php echo $i;?></td>
								<td><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><?php echo $row['userName']; ?></a></td>
								<?php
									if (!$this->hidden)
									{
										?>
											<td><?php echo $row['goldProduction']; ?></td>
										<?php
									}
								?>
							</tr>
						<?php
						$i++;
					}
				?>
			</table>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['armytop10']; ?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr>
					<th>#</th>
					<th><?php echo $language['name']; ?></th>
					<?php
						if (!$this->hidden)
						{
							?>
								<th>&nbsp;</th>
							<?php
						}
					?>
				</tr>
				<?php
					$i=1;
					foreach($this->armyTop as $key=>$row)
					{
						?>
							<tr>
								<td><?php echo $i;?></td>
								<td><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><?php echo $row['userName']; ?></a></td>
								<?php
									if (!$this->hidden)
									{
										?>
											<td><?php echo $row['army']; ?></td>
										<?php
									}
								?>
							</tr>
						<?php
						$i++;
					}
				?>
			</table>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['townhalltop10']; ?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr>
					<th>#</th>
					<th><?php echo $language['name']; ?></th>
					<?php
						if (!$this->hidden)
						{
							?>
								<th><?php echo $language['level']; ?></th>
							<?php
						}
					?>
				</tr>
				<?php
					$i=1;
					foreach($this->townHallTop as $key=>$row)
					{
						?>
							<tr>
								<td><?php echo $i;?></td>
								<td><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><?php echo $row['userName']; ?></a></td>
								<?php
									if (!$this->hidden)
									{
										?>
											<td><?php echo $row['thLevel']; ?></td>
										<?php
									}
								?>
							</tr>
						<?php
						$i++;
					}
				?>
			</table>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['scoretop10']; ?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr><th>#</th><th><?php echo $language['name']; ?></th><th><?php echo $language['score']; ?></th></tr>
				<?php
					$i=1;
					foreach($this->playerTop as $key=>$row)
					{
						?>
							<tr>
								<td><?php echo $i;?></td>
								<td><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><?php echo $row['userName']; ?></a></td>
								<td><?php echo nf($row['totalScore']); ?></td>
							</tr>
						<?php
						$i++;
					}
				?>
			</table>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['herotop10']; ?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr><th>#</th><th><?php echo $language['name']; ?></th><th><?php echo $language['hero']; ?></th><th><?php echo $language['level']; ?></th></tr>
				<?php
					$i=1;
					foreach($this->heroTop as $key=>$row)
					{
						?>
							<tr><td><?php echo $i;?></td><td><a href="viewplayer.php?id=<?php echo $row['ownerId']; ?>"><?php echo $row['userName']; ?></a></td><td><a href="viewhero.php?id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td><td><?php echo $row['level']; ?></td></tr>
						<?php
						$i++;
					}
				?>
			</table>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['offensetop10']; ?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr><th>#</th><th><?php echo $language['name']; ?></th><th><?php echo $language['score']; ?></th></tr>
				<?php
					$i=1;
					foreach($this->offenseTop as $key=>$row)
					{
						?>
							<tr>
							    <td><?php echo $i;?></td>
							    <td><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><?php echo $row['userName']; ?></a></td>
							    <td><?php echo nf($row['attackKills']); ?></td>
							</tr>
						<?php
						$i++;
					}
				?>
			</table>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['defensetop10']; ?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr><th>#</th><th><?php echo $language['name']; ?></th><th><?php echo $language['score']; ?></th></tr>
				<?php
					$i=1;
					foreach($this->defenseTop as $key=>$row)
					{
						?>
							<tr>
							    <td><?php echo $i;?></td>
							    <td><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><?php echo $row['userName']; ?></a></td>
							    <td><?php echo nf($row['defenseKills']); ?></td>
							</tr>
						<?php
						$i++;
					}
				?>
			</table>
		</div>
		<div class="thumbdiv">
			<h1><?php echo $language['thehighest'];?></h1>
			<table style="margin: 0 auto 0 auto">
				<tr><th><?php echo $language['building']; ?></th><th><?php echo $language['level']; ?></th></tr>
				<?php
					foreach($config['buildings'] as $key=>$value)
					{
						?>
							<tr><td><?php echo $language[$value['languageEntry']]; ?></td><td><?php echo nf($this->theHighest[$value['buildingLevelDbName']]); ?></td></tr>
						<?php
					}
				?>
			</table>
		</div>
		<div style="clear:both"></div>
	</div>
</div>

