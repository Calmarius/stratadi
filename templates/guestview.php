<?php

global $language;

?>
<div class="gamediv topleft">
	<a href="javascript:void(0)" onclick="toggleElement('game',this)" id="gamelink"><?php echo $language['gamemenu']; ?></a><br>
	<div id="game" style="display:none">
		<ul>
			<li><a href="javascript:void(openInWindow('battlesim.php'));"><?php echo $language['battlesimulator'];?></a></li>
			<li><a href="javascript:void(openInWindow('oracle.php?public'))"><?php echo $language['weeklyoracle']; ?></a></li>
			<li><a href="javascript:void(openInWindow('help.php'));"><?php echo $language['help'];?></a></li>
			<?php
				if ($this->tutorial)
				{
					?>
						<li><a href="javascript:void(openInWindow('tutorial.php'));"><?php echo $language['tutorial'];?></a></li>						
					<?php
				}
			?>
		</ul>
		<ul>
			<li><a href="doreset.php"><?php echo $language['logout'] ?></a></li>
		</ul>
	</div>
	<a href="javascript:void(0)" onclick="toggleElement('mapstuff',this)"><?php echo $language['mapstuff']; ?></a><br>
	<div id="mapstuff" style="display:none">
		<p>(<span id="cellX"></span>;<span id="cellY"></span>)<span id="debugspan"></span></p>
		<p>
			<form onsubmit="return false;">
				x: <input type="text" style="width:4em" id="jumpX" value="0">,
				y: <input type="text" style="width:4em" id="jumpY" value="0">
				<input type="submit" value="<?php echo $language['jump'];?>" onclick="void((function(){initMap(parseInt(_('jumpX').value),parseInt(_('jumpY').value));})())">
			</form>
		</p>
	</div>
	<a href="javascript:void(0)" onclick="toggleElement('actions',this)"><?php echo $language['actions']; ?></a><br>
	<div id="actions" style="display:none">
		<p>
			<?php echo $language['mousemode']; ?><br>
			<input type="radio" name="mousemode" id="defaultmode" onclick="javascript:void(selectMouseMode(new DefaultMouseMode(),'defaultmode'))"><label for="defaultmode"><?php echo $language['defaultmode']; ?></label><br>
			<input type="radio" name="mousemode" id="selectmode" onclick="javascript:void(selectMouseMode(new SelectMouseMode(), 'selectmode'))"><label for="selectmode"><?php echo $language['selectmode']; ?></label><br>
			<input type="radio" name="mousemode" id="actionmode" onclick="javascript:void(selectMouseMode(new ActionMouseMode(),'actionmode'))"><label for="actionmode"><?php echo $language['actionmode']; ?></label><br>
		</p>
	</div>
</div>

<canvas class="canvascontainer" id="maparea">
</canvas>
<script type="text/javascript">
	var elm=document.getElementById('maparea');
	if (!elm.getContext) location.href=("notsupportedbrowser.php");
</script>
<img src="nightimage.php?img=loadingcell" alt="" id="loading" style="display:none">
<img src="nightimage.php?img=grasscell" alt="" id="grass" style="display:none">
<img src="nightimage.php?img=towncell" alt="" id="town" style="display:none">
<script>
	function toggleElement(id,link)
	{
		var elm=document.getElementById(id);
		if (elm.style.display=='none')
		{
			elm.style.display='block';
			if (link)
			{
				link.prevText=link.innerHTML;
				link.innerHTML='[X]';
			}
		}
		else
		{
			elm.style.display='none';
			if (link && link.prevText)
			{
				link.innerHTML=link.prevText;
			}
		}
	}
	toggleElement('game',document.getElementById('gamelink'));
	// don't scroll the map when any of the divs have scrollbars.
	(
		function()
		{
			var elmSet=document.getElementsByTagName('div');
			var eHandler=function(ev)
			{
				ev=ev || window.event;
				if (ev.stopPropagation) ev.stopPropagation();
				ev.cancelBubble=true;
			}
			for(var i=0;i<elmSet.length;i++)
			{
				var elm=elmSet[i];
				var cn=' '+elm.className+' ';
				if (cn.match(new RegExp('\\sgamediv\\s','gi')))
				{
					elm.onclick=eHandler;
					elm.onmousemove=eHandler;
				}
			}
			
		}
	)()
</script>
<script>
	var playerVillages=eval('('+'<?php echo json_encode($this->villageInfo); ?>'+')');
	var guestMode=true;
	var slowNet=false;
	var dontMergeTiles=false;
</script>

