<?php

global $language;

?>

<h1><?php echo $language['youdonthavehero'];?></h1>
<p><?php echo $language['youcancreateanewhero'];?></p>
<p><?php echo $language['oryoucanwaitaherotoappearinyouvillage'];?></p>
<p><?php echo $language['heroesinyourvillages'];?></p>
<p>
	<?php
		if (count($this->heroes)>0)
		{
			?>
				<ul>
			<?php
			foreach($this->heroes as $key=>$value)
			{
				?>
					<li>
						<a href="viewhero.php?id=<?php echo $value['id'];?>"><?php echo xprintf($language['heropagetitle'],array($value['name'],$value['level'])); ?></a> @ 
						<a href="javascript:void(parent.initMap(<?php echo $value['x']; ?>,<?php echo $value['y'];?>))"><?php echo $value['villageName']?></a>
						<a href="dohirehero.php?id=<?php echo $value['id']; ?>&rnd=<?php echo mt_rand(); ?>"><?php echo $language['hirehero'];?></a>
					</li>
				<?php
			}
			?>
				</ul>
			<?php
		}
		else
		{
			?>
				<p><?php echo $language['nofreeheroesgarrisoninginyourvillages'];?></p>
			<?php
		}
	?>
</p>

