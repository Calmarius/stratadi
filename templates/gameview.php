<?php

global $language;
global $config

?>
<div class="gamediv gdbasestyle topleft">
	<a href="javascript:void(0)" onclick="toggleElement('game',this)" id="gamelink"><?php echo $language['gamemenu']; ?></a><br>
	<div id="game" style="display:none">
		<?php
			if ($this->guest)
			{
				?>
					<ul>
						<li><a href="registration.php" id="registrationlink"><?php echo $language['registration'] ?></a></li>
						<li><a href="login.php" id="loginlink"><?php echo $language['login'] ?></a></li>
					</ul>
				<?php
			}
			else
			{
				?>
					<ul>
						<li><a href="messages.php" id="messageslink"><?php echo $language['messages'] ?></a></li>
						<li><a href="reports.php" id="reportslink"><?php echo $language['reports'] ?></a></li>
						<li><a href="javascript:void(showRecentWorldEvents())"><?php echo $language['recentevents'] ?></a><span id="recentnotify"></span></li>
						<li><a href="notes.php" id="noteslink"><?php echo $language['notes'];?></a></li>
					</ul>
					<ul>
						<li><a href="javascript:void(showVillageSummary());"><?php echo $language['villagesummary'];?></a></li>
						<li><a href="javascript:void(massTraining());"><?php echo $language['masstraining'];?></a></li>
						<li><a href="javascript:void(massBuilding());"><?php echo $language['massbuilding'];?></a></li>
						<li><a href="viewhero.php" id="viewherolink"><?php echo $language['viewhero'];?></a></li>
					</ul>
				<?php
			}
		?>
		<ul>
			<li><a href="battlesim.php" id="battlesimlink"><?php echo $language['battlesimulator'];?></a></li>
			<li><a href="oracle.php?public" id="oraclelink"><?php echo $language['weeklyoracle']; ?></a></li>
			<li><a href="help.php" id="helplink"><?php echo $language['help'];?></a></li>
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
	<?php
		if (!$this->guest)
		{
			?>
				<a href="javascript:void(0)" onclick="toggleElement('community',this)" id="communitylink"><?php echo $language['community']; ?></a><br>
				<div id="community" style="display:none">
					<ul>
						<li><a id="sittinglink" href="sitting.php"><?php echo $language['deputies'] ?></a></li>
						<li><a id="guildlink" href="guild.php"><?php echo $language['guild'] ?></a></li>
						<li><a id="viewplayerlink" href="viewplayer.php"><?php echo $language['kingdomprofile'] ?></a></li>
						<li><a id="viewaccesslink" href="viewaccess.php"><?php echo $language['myprofile'] ?></a></li>
						<li><a id="editkingslink" href="editkings.php"><?php echo $language['managekings'] ?></a></li>
						<li><a href="javascript:void(window.open('<?php echo $config['forumLink']; ?>'))"><?php echo $language['forum'];?></a></li>
					</ul>
				</div>
				<a href="javascript:void(0)" onclick="toggleElement('extras',this)"><?php echo $language['extras']; ?></a><br>
				<div id="extras" style="display:none">
					<ul>
						<li><a href="javascript:void(openInWindow('invite.php'))"><?php echo $language['inviteplayertogame']; ?></a></li>
						<li><a href="javascript:void(generateActivityPlotWindow())"><?php echo $language['activityinyouraccount']; ?></a></li>
					</ul>
				</div>
			<?php
		}
		else
		{
			?>
				<a href="javascript:void(0)" onclick="toggleElement('community',this)" id="communitylink"><?php echo $language['community']; ?></a><br>
				<div id="community" style="display:none">
					<ul>
						<li><a href="javascript:void(window.open('<?php echo $config['forumLink']; ?>'))"><?php echo $language['forum'];?></a></li>
					</ul>
				</div>
			<?php
		}
	?>
	<?php
		if ($this->admin)
		{
			?>
				<a href="javascript:void(0)" onclick="toggleElement('community',this)" id="communitylink"><?php echo $language['community']; ?></a><br>
				<a href="javascript:void(0)" onclick="toggleElement('admin',this)"><?php echo $language['adminstuff']; ?></a><br>
				<div id="admin" style="display:none">
					<ul>
						<li><a href="javascript:void(debug())"><?php echo $language['debug']; ?></a></li>
						<li><a href="javascript:void(openInWindow('switchuser.php?simple'))"><?php echo $language['adminlogin']; ?></a></li>
						<li><a href="javascript:void(openInWindow('massreport.php'))"><?php echo $language['sendmassreport']; ?></a></li>
						<li>
							<a href="javascript:void(0)" onclick="toggleElement('backup',this)"><?php echo $language['backup']; ?></a><br>
							<div id="backup" style="display:none">
								<a href="javascript:void(openInWindow('domakebackup.php'))"><?php echo $language['savebackup']; ?></a>
							</div>
						</li>
						<li><a  href="javascript:void(openInWindow('doavatargc.php'))"><?php echo $language['avatargc']; ?></a></li>
					</ul>
				</div>
			<?php
		}
	?>
</div>
<div class="gamediv gdbasestyle topright">
	<?php
		if (!$this->guest)
		{
			?>
				<div class="right"><span id="trdiv"></span> | <a href="javascript:getPlayerInfo();"><img style="width:20px; height:20px; vertical-align:middle; border:none" src="img/refresh.png" alt="<?php echo $language['updatenow']; ?>" title="<?php echo $language['updatenow']; ?>"></a></div>
			<?php
		}
	?>
	<div>
		<a href="javascript:void(0)" onclick="toggleElement('actions',this)"><?php echo $language['actions']; ?></a><br>
		<div id="actions" style="display:none">
			<p>
				<?php echo $language['mousemode']; ?><br>
				<input type="radio" name="mousemode" id="defaultmode" onclick="javascript:void(selectMouseMode(new DefaultMouseMode(),'defaultmode'))"><label for="defaultmode"><?php echo $language['defaultmode']; ?></label><br>
				<input type="radio" name="mousemode" id="selectmode" onclick="javascript:void(selectMouseMode(new SelectMouseMode(), 'selectmode'))"><label for="selectmode"><?php echo $language['selectmode']; ?></label><br>
				<input type="radio" name="mousemode" id="actionmode" onclick="javascript:void(selectMouseMode(new ActionMouseMode(),'actionmode'))"><label for="actionmode"><?php echo $language['actionmode']; ?></label><br>
			</p>
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
	</div>
</div>
<?php
	if (!$this->guest)
	{
		?>
			<div class="gamediv gdbasestyle bottomleft" id="eventlist">

			</div>
			<div class="gamediv gdbasestyle bottomright">
				<p><? echo $language['tasklist'] ?></p>
				<div id="brdiv"></div>
				<p class="right">
					<a href="javascript:commitTasks()" title=""><? echo $language['committasks'] ?></a><br>
					<a href="javascript:clearTasks()" title=""><? echo $language['cleartasks'] ?></a>
				</p>
			</div>
		<?php
	}
?>
<img src="nightimage.php?img=town1" alt="" id="town1" style="display:none">
<img src="nightimage.php?img=town2" alt="" id="town2" style="display:none">
<img src="nightimage.php?img=town3" alt="" id="town3" style="display:none">
<img src="nightimage.php?img=town4" alt="" id="town4" style="display:none">
<img src="nightimage.php?img=town5" alt="" id="town5" style="display:none">
<img src="nightimage.php?img=town6" alt="" id="town6" style="display:none">
<img src="nightimage.php?img=town7" alt="" id="town7" style="display:none">
<img src="nightimage.php?img=loadingcell" alt="" id="loading" style="display:none">
<img src="nightimage.php?img=grasscell" alt="" id="grass" style="display:none">
<img src="nightimage.php?img=towncell" alt="" id="town" style="display:none">
<canvas class="canvascontainer" id="maparea">
</canvas>
<script type="text/javascript">
	var elm=document.getElementById('maparea');
	if (!elm.getContext) location.href=("notsupportedbrowser.php");
</script>
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
					elm.onclick=function(e)
					{
						if (bringElementToFront) bringElementToFront(this);
					}
/*					elm.onmousedown=eHandler;
					elm.onmouseup=eHandler;
					elm.onclick=eHandler;
					elm.onmousemove=eHandler;*/
				}
			}

		}
	)()
	// activate links

	function openerGenerator(link)
	{
                return function(e)
                {
                        var ev=e || window.event;

                        ev.cancelBubble=true;
                        ev.preventDefault();
                        openInWindow(link);
                }
	}

	if (document.getElementById('messageslink')) document.getElementById('messageslink').onclick=openerGenerator('messages.php');
	if (document.getElementById('reportslink')) document.getElementById('reportslink').onclick=openerGenerator('reports.php');
	if (document.getElementById('noteslink')) document.getElementById('noteslink').onclick=openerGenerator('notes.php');
	if (document.getElementById('viewherolink')) document.getElementById('viewherolink').onclick=openerGenerator('viewhero.php');
	if (document.getElementById('battlesimlink')) document.getElementById('battlesimlink').onclick=openerGenerator('battlesim.php');
	if (document.getElementById('oraclelink')) document.getElementById('oraclelink').onclick=openerGenerator('oracle.php?public');
	if (document.getElementById('helplink')) document.getElementById('helplink').onclick=openerGenerator('help.php');
	if (document.getElementById('helplink')) document.getElementById('helplink').onclick=openerGenerator('help.php');
	if (document.getElementById('sittinglink')) document.getElementById('sittinglink').onclick=openerGenerator('sitting.php');
	if (document.getElementById('guildlink')) document.getElementById('guildlink').onclick=openerGenerator('guild.php');
	if (document.getElementById('viewplayerlink')) document.getElementById('viewplayerlink').onclick=openerGenerator('viewplayer.php');
	if (document.getElementById('viewaccesslink')) document.getElementById('viewaccesslink').onclick=openerGenerator('viewaccess.php');
	if (document.getElementById('editkingslink')) document.getElementById('editkingslink').onclick=openerGenerator('editkings.php');
/*	if (document.getElementById('registrationlink')) document.getElementById('registrationlink').onclick=openerGenerator('registration.php');
	if (document.getElementById('loginlink')) document.getElementById('loginlink').onclick=openerGenerator('login.php');*/
</script>
<script>
	var playerVillages=eval('('+'<?php echo json_encode($this->villageInfo); ?>'+')');
	var guestMode=<?php echo $this->guest; ?>;
	var slowNet=<?php echo $this->slownet; ?>;
	var UPDATEREGIONSIZE=<?php echo $this->tilesize; ?>;
	var dontMergeTiles=<?php echo $this->nomerge; ?>;
	var tribalMode=<?php echo (int)isset($_GET['tribalmode']);?>;
	var AVGIMAGECOUNT=<?php echo $this->cellCount; ?>;
</script>

