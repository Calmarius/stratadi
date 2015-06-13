<?php
header('content-type: text/html; charset=utf-8');
require_once('../../presenterphps.php');
require_once('../../utils/gameutils.php');

function identity($input)
{
	return $input;
}

function percentageFormat($input)
{
	return round($input*100).'%';
}

function multiplyFormat2Decs($input)
{
	return round($input,2).'×';
}

function toComma($input)
{
//	return str_replace('.',',',$input);
	return $input;
}

function generateFnTable($fn,$formatterFn,$start,$end)
{
	$str='<table>';
	$str.='<tr>';
	for($i=$start;$i<=$end;$i++)
	{
		$str.='<td>'.$i.'</td>';
	}
	$str.='</tr>';
	$str.='<tr>';
	for($i=$start;$i<=$end;$i++)
	{
		$str.='<td>'.$formatterFn($fn($i)).'</td>';
	}
	$str.='</tr>';
	$str.='</table>';
	return $str;
}

// calculations for the battle system part

$defVectors=array();
$attVectors=array();
$unitNames=array();
foreach($config['units'] as $key=>$value)
{
	$attVectors[]=$value['attack'];
	$defVectors[]=$value['defense'];
	$unitNames[]=$language[$value['languageEntry']];
}

function vectorCalculator($amounts,&$vector,$isAttack=true,$bonus=1)
{
	global $defVectors;
	global $attVectors;
	$n=count($amounts);
	$targetVector=$isAttack ? $attVectors:$defVectors;
	$vector=array();
	$calcString=array();
	for($i=0;$i<$n;$i++)
	{
		$am=$amounts[$i];
		$target=$targetVector[$i];
		$m=count($target);
		if ($am!=0)
		{
			$calcString[]=$am.'&middot;('.implode(';',$target).')';
		}
		for($j=0;$j<$m;$j++)
		{
			if (!isset($vector[$j])) $vector[$j]=0;
			$vector[$j]+=$am*$target[$j]*$bonus;
		}
	}
	if ($bonus==1)
	{
		return implode('+',$calcString).'=('.implode(';',$vector).')';
	}
	else
	{
		return $bonus.'&middot;('.implode('+',$calcString).')=('.implode(';',$vector).')';		
	}
}

function powerRatioCalculator($attackVector,$defenseVector,&$k)
{
	$n=count($attackVector);
	$k=0;
	$calcString=array();
	for($i=0;$i<$n;$i++)
	{
		$a=$attackVector[$i];
		$d=$defenseVector[$i];
		$k+=$a/$d;
		$calcString[]=
		'
			<td>
				<table class="math">
					<tr><td style="border-bottom: 1px solid black">'.toComma(round($a,3)).'</td></tr>
					<tr><td>'.toComma(round($d,3)).'</td></tr>
				</table>
			</td>
		';
	}
	return implode('<td>+</td>',$calcString).'<td>=</td><td>'.toComma(round($k,3)).'</td>';
}

function casualtyCalculator($amounts,$casualtyRate)
{
	$n=count($amounts);
	$results=array();
	for($i=0;$i<$n;$i++)
	{
		$cr=round($amounts[$i]*$casualtyRate);
		$results[]=$cr;
	}
	return count($results)==0 ? 'Nem volt veszteség':unitEnumerator($results);
}

function unitEnumerator($amounts)
{
	global $unitNames;
	$n=count($amounts);
	$results=array();
	for($i=0;$i<$n;$i++)
	{
		if ($amounts[$i]==0) continue;
		$results[]=$amounts[$i].' '.$unitNames[$i];
	}
	return implode(', ',$results);
}

function applyCasualtiesToVector($vector,$casualtyRate,&$newVector)
{
	$n=count($vector);
	$newVector=array();
	$printVector=array();
	for($i=0;$i<$n;$i++)
	{
		$newVector[$i]=$vector[$i]*(1-$casualtyRate);
		$printVector[$i]=toComma(round($newVector[$i],3));
	}
	return '(1-'.toComma(round($casualtyRate,3)).')&middot;('.implode(';',$vector).')=('.implode(';',$printVector).')';
}

function applyFactorToVector($vector,$factor,&$newVector)
{
	$n=count($vector);
	$newVector=array();
	$printVector=array();
	$printBaseVector=array();
	for($i=0;$i<$n;$i++)
	{
		$newVector[$i]=$vector[$i]*$factor;
		$printBaseVector[$i]=toComma(round($vector[$i],3));
		$printVector[$i]=toComma(round($newVector[$i],3));
	}
	return toComma(round($factor,3)).'&middot;('.implode(';',$printBaseVector).')=('.implode(';',$printVector).')';
}

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title><?php $language['wtfbattles']; ?> súgó</title>
		<link rel="stylesheet"  href="../../main.css">
		<script>
			function toggleElement(id)
			{
				var elm=document.getElementById(id);
				if (elm.style.display=='block') elm.style.display='none'; else elm.style.display='block';
			}
		</script>
	</head>
	<body>
		<h1><?php $language['wtfbattles']; ?> help</h1>
		<div>
			<a href="javascript:void(toggleElement('userinterface'))">Usage and user interface</a><br>
			<div id="userinterface" class="helpdiv">
				<p>I write here only the most inportant things.</p>
				<a href="javascript:void(toggleElement('menudescription'))">Short description of the menu items</a><br>
				<div id="menudescription" class="helpdiv">
					<ul>
						<li>Game menu</li>
							<ul>
								    <li><b>Messages</b>: Here you can reach the messaging. You may communicate with others. The number that appears next to the menu point is the number of the new messages.</li>
								    <li><b>Reports</b>: You are notified here when something happens in game. The number next to this menu item idicates now many new reports you have.</li>
								    <li><b>Recent events</b>: see here what happened recently in the game world.</li>
								    <li><b>Notes</b>: write notes for yourself or your account mates.</li>

								    <li><b>Village summary</b>: view a summary about your villages.</li>
								    <li><b>Massive training</b>: optimally distribute unit training among multiple villages.</li>
								    <li><b>Massive build</b>: spend build points in multiple villages simultaneously.</li>
								    <li><b>View hero</b>: view your hero's stats. If you don't have a hero you can view the free heroes staying in your villages.</li>

								    <li><b>Battle simulator</b>: calculate the outcome of a battle. It uses he same algorithm as the game.</li>
								    <li><b>Weekly oracle</b>: view the weekly Top 10 of various categories.</li>
								    <li><b>Help</b>: you are viewing this.</li>

								    <li><b>Logout</b>: logs you out. Use this every time when are in a public place.</li>
							</ul>
						<li>Community</li>
							<ul>
							    <li><b>Account sitting</b>: delegate a player who may access your account.</li>
							    <li><b>Guild</b>: Your guild's page. Here you can manage your guild if you have permission.</li>
							    <li><b>Kingdom's profile </b>: The public profile of your kingdom.</li>
							    <li><b>My profile</b>: your own profile.</li>
							    <li><b>Manage kings</b>: manage the kings controlling the kingdom.</li>
							    <li><b>Forum</b>: You can open the official forum.</li>
							</ul>
						<li>Extras</li>
							<ul>
								<li><b>Invite player to the game</b>: get expansion points by advertising the game.</li>
								<li><b>View activity in your account</b>: view the activity in your account.</li>
							</ul>
						<li><b>Map operations</b>: here you can jump to arbitrary coordinates.</li>
						<li><b>Mouse actions</b>: this may be useful if you play on a device without keyboard.</li>
					</ul>
				</div>
				<a href="javascript:void(toggleElement('readlettersreports'))">Reading letters and reports</a><br>
				<div id="readlettersreports" class="helpdiv">
					<p>If you see numbers in parentheses, that means you have new messages or reports, click on the menupoint to view them.</p>
					<p>
					The messaging is thread based. So you don't send messages. If someone write a message, he will start a new thread and will subscribe the recipient. This is useful because after a long correspondence you won't have
					dozens of messages but only one: thread. Deleting the letter just means 'unsubscribe' from the thread, so you won't see if the sender responds unless he add you to the participants again.</p>
				</div>
				<a href="javascript:void(toggleElement('writeletters'))">Writing letters</a><br>
				<div id="writeletters" class="helpdiv">
					<p> You can write letters to others by selecting one of his villages, then click on the owners name, open his profile amd click on the 'Send him message' and write. Or you can write a new message by opening the messages
					menu and click on the 'Compose new message' link and type the recipient name to the 'Add participant' field.</p>
				</div>
				<a href="javascript:void(toggleElement('build'))">Building in villages</a><br>
				<div id="build" class="helpdiv">
					<p> You can build buildings in your villages by clicking on their icons when you select your village. The task will appear in your task list and you need to commit the operation. The cost of the operation is gold and one build
					point. Every village produces one build point each day, but you can increase this rate by upgrading your town center. 
					</p>
				</div>
				<a href="javascript:void(toggleElement('trainunits'))">Training units</a><br>
				<div id="trainunits" class="helpdiv">	
					<p>
					Type the amount of units to create in text boxes under the unit icons then press enter. The task will appear in the tasklist, so you need to commit the task.
					</p>
				</div>
				<a href="javascript:void(toggleElement('renamevillage'))">Renaming villages</a><br>
				<div id="renamevillage" class="helpdiv">
					<p>
					It's obvious. Click on the 'Rename' link, type the new name and press enter.
					</p>
				</div>
				<a href="javascript:void(toggleElement('movetroops'))">Moving troops, attacking with troops.</a><br>
				<div id="movetroops" class="helpdiv">
					<p>
					Select the starting villages, then shift+click (or use mouse operations) on the village you want to send troops to, select the operation, set the amount of troops on the next window, then click 'Send troops' and commit the tasks.
					</p>
				</div>
				<a href="javascript:void(toggleElement('settlevillages'))">Settling new villages</a><br>
				<div id="settlevillages" class="helpdiv">
					<p>
					Select the starting village, shift+click (or use mouse operations) the target cell and select 'Settle village on this cell'. You must have at least one diplomat on the starting village and one expansion point.
					</p>
				</div>
				<a href="javascript:void(toggleElement('managemultiplevillages'))">Managing multiple villages</a><br>
				<div id="managemultiplevillages" class="helpdiv">
					<p>You can select multiple villages by holding the Ctrl and drag select multiple villages. (the selection is in toggle mode, so you can select and unselect with this mode too.)</p>
				</div>
			</div>
			<a href="javascript:void(toggleElement('tutorial'))">Several tips for beginners</a><br>
			<div id="tutorial" class="helpdiv">
				<a href="javascript:void(toggleElement('forattackers'))">For attackers</a><br>
				<div id="forattackers" class="helpdiv">
					<dl>
						<dt>Never attack with your entire army.</dt>
							<dd>This is the best way to lose your entire army.</dd>
						<dt>Attack with the least possible units that does the job.</dt>
							<dd>You can minimize your losses, if you send as little amount troops as you can to achieve the desired effect (eg. demolishing the enemy's wall). You will often lose your attacking wave.
							</dd>
						<dt>Use the ways of attack wisely.</dt>
							<dd>Use last man standing battle if you want to demolish buildings or want to conquer villages. On every other cases use raid or scouting.
							</dd>
						<dt>Look after your hero!</dt>
							<dd>If your hero loses a battle he will abandon you and will move to a random village. A skilled hero is a big advantage. Look after your hero!</dd>
						<dt>You can cancel every action till the last second.</dt>
							<dd>If an action is seems to be hopeless, cancel it.</dd>
						<dt>Distract the defender.</dt>
							<dd>If everything fails try sending as many attack waves to the defender as you can. He will need to spread his defensive forces among his villages in order to defend. So it will be easier to capture a villages. </dd>
					</dl>
				</div>
				<a href="javascript:void(toggleElement('fordefenders'))">For defenders</a><br>
				<div id="fordefenders" class="helpdiv">
					<dl>
						<dt>Never overestimate your wall.</dt>
							<dd>It's true that a level 5-6 wall can defend you against most of the attacks but keep the fact in the mind that your attacker can destroy your wall and clean the village after.</dd>
						<dt>Use multiple heroes.</dt>
							<dd>
							Consult with your guild and order multiple heroes in the village to defend, their defense bonus will sum up and on successful defense, multiple heroes can easily level up.</dd>
						<dt>Cooperate with your guild!</dt>
							<dd>If your units are running out ask your guildmates to help you and give you units.</dd>
						<dt>Set up priorization.</dt>
							<dd>
							If you have lot of incoming attacks, decide which one is worth defend against. It's not worth defending a village with low score. Moreover you can lose it without problems, you will get the expansion point back
							and you can conquer an ever bigger village.
							</dd>
					</dl>
				</div>
				<a href="javascript:void(toggleElement('buildup'))">Tips on building</a><br>
				<div id="buildup" class="helpdiv">
					<dl>
						<dt>On village placement</dt>
							<dd>
							A dense village cluster is easy to defend. But you can control a bigger area if you spread your villages.
							</dd>
						<dt>No need to upgrade unit producers early</dt>
							<dd>While your gold production is low, there is no need to upgrade your unit producers, because you can't keep them working.</dd>
					</dl>
				</div>
			</div>
			<a href="javascript:void(toggleElement('basics'))">Basics</a><br>
			<div id="basics" class="helpdiv">
				<a href="javascript:void(toggleElement('gold'))">Gold</a><br>
				<div id="gold" class="helpdiv">
					<p>Gold is the money in the game. You can see how many gold you have on the right top box.</p>
				</div>
				<a href="javascript:void(toggleElement('buildpoints'))">Build points</a><br>
				<div id="buildpoints" class="helpdiv">
					<p>With build points (and gold), you can build buildings. Every village produces one build point but you can increase the build point production by upgrading your Town center.</p>
				</div>
				<a href="javascript:void(toggleElement('expansionpoints'))">Expansion points</a><br>
				<div id="expansionpoints" class="helpdiv">
					<p>
					Expansion points determining how many villages you may own. You get exactly one expansion point every day. You can settle or conquer a village only if you have at least one expansion point. If you lose or abandon a 
					village you will get your expansion points back.</p>
				</div>
				<a href="javascript:void(toggleElement('refreshes'))">Refreshes and updates</a><br>
				<div id="refreshes" class="helpdiv">
					<p>To decrease the server load changes only happen if you ask it to.</p>
					<ul>
						<li>On login, all your villages will update.</li>
						<li>The game ask the server in every minutes if you have new letters, reports, etc. But you can explicitly ask the server if you click on the refresh icon on the right top box.</li>
						<li>
						If you haven't viewed your villages for 10 minutes, when you view it will ask the server to update. Drag selection won't update the villages.</li>
					</ul>
				</div>
				<a href="javascript:void(toggleElement('heroes'))">Heroes</a><br>
				<div id="heroes" class="helpdiv">
					<p>Heroes are your warlords. You can have only one hero. Depends on you defend or attack him. It will get better at attack and defense skill. Hero don't have defense and offense value but it buffs the army he leads.</p>
					<p>If the army led by hero defeats units during offense the hero will gain offensive experience. If he defeat units during defense, he will gain defensive experience.</p>
					<p>The offensive bonus the hero gives is shown in the following table for the first few levels:</p>
					<?php echo generateFnTable($config['heroAttackFormula'],'percentageFormat',0,20); ?>
					<p>The defensive bonus the hero gives is shown in the following table for the first few levels:</p>
					<?php echo generateFnTable($config['heroDefendFormula'],'percentageFormat',0,20); ?>
					<p>To level the defensive/offensive skill up you need to defeat certain amount of units. Every killed unit means 1XP. The following table shows the XP values for the first few levels:</p>
					<?php echo generateFnTable($config['experienceFunctionInverse'],create_function('$input','return ceil($input)."&thinsp;XP";'),0,20); ?>
					<p>If your hero loses a battle he will abandon you and become free and he will immediately set off and moves to a random village. Free heroes can reach any village in 24 hour.</p>
					<p>Free heroes will set off every midnight and moves to another randomly choosen village.</p>
					<p>If you lost your hero, you have 2 choices::</p>
					<ul>
						<li>Create a new hero which will be level 1.</li>
						<li>Or you can wait for a strong hero to show up in one of your village. You can hire him and will serve you.</li>
					</ul>
					<p>The speed of heroes are <?php echo $config['heroSpeed']; ?> fields/hour when traveling alone.</p>
				</div>
				<a href="javascript:void(toggleElement('troopmovement'))">Troop movements</a><br>
				<div id="troopmovement" class="helpdiv">
					<a href="javascript:void(toggleElement('heromovement'))">Hero movements</a><br>
					<div id="heromovement" class="helpdiv">
						<p>Hero can be sent to any village (even to others' villages). Only the owner can control the hero (even if it is in the village of someone else).</p>
					</div>
					<a href="javascript:void(toggleElement('unitmovement'))">Transferring troops</a><br>
					<div id="unitmovement" class="helpdiv">
						<p>The troops in villgaes can be combined to one big army for cooperation, common defense or attacks. It's worth send all your army into one village you attack. 
						</p>
					</div>
					<a href="javascript:void(toggleElement('attack'))">Last man standing battle</a><br>
					<div id="attack" class="helpdiv">
						<p>In this battle mode either the attacker or the defender side will lose all his troops. If you want to demolish or conquer village you must select this mode. The survivors will return with the looted gold.</p>
					</div>
					<a href="javascript:void(toggleElement('raid'))">Raid</a><br>
					<div id="raid" class="helpdiv">
						<p>In this mode battle does not last to the last man. Often one soldier returns even when fighting against big overpower. When you want to rob gold from your enemy, use this way of attack. You can not destroy buildings
						or conquer villages in this mode.</p>
					</div>
					<a href="javascript:void(toggleElement('recon'))">Scout</a><br>
					<div id="recon" class="helpdiv">
						<p>In this attack mode the army attacks the village and retreat the defenders begin to chase your army and kill most of them. In most of the cases one soldier will return even when fighting aginst huge overpower. The
						army won't enter the village so it can't rob gold from the enemy.</p>
						<p>Use this mode if you want to know how big the defender's army.</p>
					</div>
				</div>
				<a href="javascript:void(toggleElement('conqeringvillages'))">Conquering villages</a><br>
				<div id="conqeringvillages" class="helpdiv">
					<p>If you send a diplomat into last man standing battle and you have one expansion point and the battle is won, the attacker will conquer the village. The diplomat, who is escorted by the army, will take the control of the village
					and disappear. <i>The build points will be set to zero in that village.</i></p>
				</div>
				<a href="javascript:void(toggleElement('abandonvillages'))">Abandoning villages</a><br>
				<div id="abandonvillages" class="helpdiv">
					<p>If you don't need a village because you want to conquer a bigger for example, you can abandon it thus you will get your expansion point back. The village will be owner by no one.</p>
				</div>
				<a href="javascript:void(toggleElement('diplomacy'))">Diplomacy</a><br>
				<div id="diplomacy" class="helpdiv">
					<p>There can be various diplomatic stances between guilds. The village's color indicates this.</p>
					<ul>
						 <li>Own village (blue), guildmate (cyan), ally (yellow): you can transfer troops to these villages to help them.</li>
						 <li>Peace (green): you can not attack or help them with troops.</li>
						 <li>Neutral (white): when there is no relation between the two guilds or players. You can attack them and give them units too.</li>
						 <li>War (red): you can attack them only.</li>
					</ul>
				</div>
				<a href="javascript:void(toggleElement('raidgold'))">Looting gold</a><br>
				<div id="raidgold" class="helpdiv">
					<p>One player's gold is distributed uniformly between his villages. So if someone has 3000 gold and has 6 villages, then all his villages contain 500 gold. During raid you can take gold from only one village. So if someone
					attack this player can take max 500 gold. The more villages the player have less gold can be taken from him.</p>
				</div>
				<a href="javascript:void(toggleElement('scoring'))">Scoring of the villages</a><br>
				<div id="scoring" class="helpdiv">
					<p>The village's score is the sum of the squares of the building levels.</p>
				</div>
				<a href="javascript:void(toggleElement('nightbonus'))">Night bonus</a><br>
				<div id="nightbonus" class="helpdiv">
					<p>It starts when the Sun sets and ends when the Sun rises (at Budapest, Hungary). The maximum value of the bonus is: <?php echo $config['nightBonusMax']; ?>×. The bonus, as soon as the sun set, 
					starts to rise, when there is completely dark it will reach the maximum. The defense strength of the units in multiplied by the night bonus. So it's not worth attacking at night.</p>
				</div>
				<a href="javascript:void(toggleElement('31rule'))">3:1 szabály</a><br>
				<div id="31rule" class="helpdiv">
					<p>You can not give troops to players whose account 3 times older or younger than yours. So if you have been playing the game for 30 days you can give troops to players who playing the game less than 10 days or more 
					90 days.</p>
				</div>	
				<a href="javascript:void(toggleElement('fakerule'))">Fake rule</a><br>
				<div id="fakerule" class="helpdiv">
					<p>This rule exists to avoid flood like attacks, whose aim is to make the incoming attacks page useless. So the value of the army, you send for attack, need to be bigger than the 24% of your the hourly gold production. So if 
					you produce 100 gold per hour, you need to send an army worth at least 24 gold.</p>
				</div>
				<a href="javascript:void(toggleElement('randommovement'))">Randomized travel times</a><br>
				<div id="randommovement" class="helpdiv">
					<p>Travel times are never accurate. There is ±1% error in them intentionally. So if an army would travel for 1 hour then it can arrive 36 seconds earlier or later.
					So if you want to ensure the order of the waves you sent, you will need to keep several minute long gaps between their arrival times.</p>
				</div>
				<a href="javascript:void(toggleElement('weeklyoracle'))">Weekly oracle</a><br>
				<div id="weeklyoracle" class="helpdiv">
					<p>This is the top 10 of various categories. Updated on every Monday.</p>
				</div>
				<a href="javascript:void(toggleElement('agebonus'))">Age bonus</a><br>
				<div id="agebonus" class="helpdiv">
					<p> If a player started player later than you, then he have age bonus against you. This means if you attack that player, his defensive troops will be stronger by the ratio of the age difference of the players. So if you are
					playing for 50 days and attack a player who is playing for 10 years. The defender side will be 5 times stronger than usual. If you attack an older player. He won't have age bonus against you. Age bonus can be viewed
					on the kingdom's profile.</p>
				</div>
			</div>
			<a href="javascript:void(toggleElement('units'))">Units</a><br>
			<div id="units" class="helpdiv">
				<a href="javascript:void(toggleElement('spearman'))">Spearman</a><br>
				<div id="spearman" class="helpdiv">
					<p>This unit is good against cavalry, bad against archers. It can defeat one knight, but 2 spearmen needed to defeat one archer.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_0spearman']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('archer'))">Archer</a><br>
				<div id="archer" class="helpdiv">
					<p>This unit is good against spearmen, but bad against knights. it can beat 2 spearmen alone, 4 needed to defeat one knight.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_1archer']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('knight'))">Knight</a><br>
				<div id="knight" class="helpdiv">
					<p>This unit is good against archers, and bad against sparmen. It can defeat 4 archers or 1 spearmen.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_2knight']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('catapult'))">Catapult</a><br>
				<div id="catapult" class="helpdiv">
					<p>It can be used to destroy enemy villages's buildings. It's attack and defense strength is weak.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_3catapult']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('diplomat'))">Diplomat</a><br>
				<div id="diplomat" class="helpdiv">
					<p>
						This unit is used to settle new villages or conquer existing ones. If it survives a last man standing battle it will take control the attacked village (if you have enough expansion points).
						Training time is very long and so it's speed.
					</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_4diplomat']));
						$tpl->render();
					?>
				</div>
			</div>
			<a href="javascript:void(toggleElement('buildings'))">Buildings</a><br>
			<div class="helpdiv" id="buildings">
				<a href="javascript:void(toggleElement('barracks'))">Barracks</a><br>
				<div class="helpdiv" id="barracks">
					<p>If you upgrade this building the training of the spearmen will be faster.
					See the following table for the training time reduction:</p>
					<?php echo generateFnTable($config['buildings']['barracks']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('archeryrange'))">Archery range</a><br>
				<div class="helpdiv" id="archeryrange">
					<p>If you upgrade this building the training of the archers will be faster.
					See the following table for the training time reduction:</p>
					<?php echo generateFnTable($config['buildings']['archeryrange']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('stables'))">Stables</a><br>
				<div class="helpdiv" id="stables">
					<p>If you upgrade this building the training of the knights will be faster.
					See the following table for the training time reduction:</p>
					<?php echo generateFnTable($config['buildings']['stables']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('workshop'))">Workshop</a><br>
				<div class="helpdiv" id="workshop">
					<p>If you upgrade this building the training of the catapults will be faster.
					See the following table for the training time reduction:</p>
					<?php echo generateFnTable($config['buildings']['workshop']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('goldmine'))">Gold mine</a><br>
				<div class="helpdiv" id="goldmine">
					<p>If you upgrade the gold mine the gold production increases.
					The gold production for the first few levels:</p>
					<?php echo generateFnTable($config['buildings']['goldmine']['goldProductionSpeedFunction'],'identity',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('towncenter'))">Town center</a><br>
				<div class="helpdiv" id="towncenter">
					<p>By upgrading the town center the village will produce build points faster, and produces diplomats faster.
					
					A városközpont fejlesztésével gyorsabban termelődik az építési pont, és gyorsabb a diplomaták kiképzése.
					See the following table for the training time reduction:</p>
					<?php echo generateFnTable($config['buildings']['townhall']['timeReductionFunction'],'percentageFormat',0,20); ?>
					<p>See the following table for the daily build point production on the first few levels:</p>
					<?php echo generateFnTable($config['buildings']['townhall']['bpProductionSpeedFunction'],'multiplyFormat2Decs',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('wall'))">City wall</a><br>
				<div class="helpdiv" id="wall">
					<p>The city wall is essential for the protection of the villages. Every level of the wall multiplies the defensive power of the defending units. (so if you have a level 5 wall, your defensive troops will be 6 times stronger.)</p>
				</div>
			</div>
			<a href="javascript:void(toggleElement('battlesystem'))">Battle system</a><br>
			<div class="helpdiv" id="battlesystem">
				<h3>Attack and defense vectors</h3>
					<p>Every unit type has an attack and defense vector which consists of pierce, arrow and slash damage and the defense value against these attacks. These vectors have 3 elements currently.
					For example the spearman's attack vector is (60;0;0), it's defense vector is: (60;30;120).</p>
					<p>To get the full attack vector, sum up the attack vectors of the individual attacker units.</p>
					<p>To get the full defense vector, sum up the defense vectors of the individual defender units.</p>
					<p>Now apply the hero's attack bonus on the attackvector, then apply the wall bonus, night bonus, hero bonus and age bonus on the defense vector.</p>
					
					<p>Let the final attack vector:  <b>a</b>=(a<sub>1</sub>,a<sub>2</sub>,a<sub>3</sub>).
						Let the final defense vector:  <b>d</b>=(d<sub>1</sub>,d<sub>2</sub>,d<sub>3</sub>)</p>
				<h3>Ratio of the forces</h3>
					<p>Now use the following formula to obtain how stronger the offensive army:</p>
					<table class="math">
						<tr>
							<td>k</td>
							<td>=</td>
							<td>
								3<br>
								<span class="large">&Sigma;</span><br>
								i=1
							</td>
							<td>
								<table class="math">
									<tr><td style="border-bottom: 1px solid black">a<sub>i</sub></td></tr>
									<tr><td>d<sub>i</sub></td></tr>
								</table>
							</td>
						</tr>
					</table>
					<!--img style="vertical-align:middle" src="../img/powerrate.png" alt="k=\displaystyle\sum_{i=1}^{3} \frac{a_i}{d_i}">-->
					<p>If k&gt;1, then the offensive army is stronger, else the defensive army is stronger.</p>
				<h3>Number of the hitting catapults</h3>
					<p>There is only one case when every catapult shots: when you attack an empty vilage. If you have casualties, then less catapults will shot. You can calculate it using the following formula:</p>
					<table class="math">
						<tr>
							<td>
								<table class="math">
									<tr><td style="border-bottom: 1px solid black">k</td></tr>
									<tr><td>1+k</td></tr>
								</table>
							</td>
						</tr>
					</table>
				<h3>Calculating the casualty ratio</h3>
					<p>So now we know the ratio of the forces we can calculate the rate of the casualties.</p>
					<h4>Last man standing battle</h4>
						<p>If k&gt;1, then the offensive army wins, so casualty rate of the defenders are 100%, the offsive army's casualty rate is given by the following formula:</p> <!--<img style="vertical-align:middle" src="../img/lmac.png" alt="\frac{1}{k^{1,2}}">-->
						<table class="math">
							<tr><td style="border-bottom:1px solid black">1</td></tr>
							<tr><td>k<sup><?php echo toComma($config['superiorityExponent']); ?></sup></td></tr>
						</table>
						<p>If  k&lt;1, then the defenders win, so the offensive army's casualty rate is 100%.  The defenders' casualties are given by the following formula: k<sup><?php echo toComma($config['superiorityExponent']); ?></sup></p><!--<img style="vertical-align:middle" src="../img/lmdc.png" alt="k^{1,2}"></p>-->
					<h4>Raid</h4>
						<p>Offensive army's casualty ratio:</p>
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>1</td>
								<td>&ndash;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black">k</td></tr>
										<tr><td>1+k</td></tr>
									</table>
								</td>
								<td style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top"><sup><?php echo toComma($config['superiorityExponent']); ?></sup></td>
							</tr>
						</table>
						<!--<img style="vertical-align:middle" src="../img/rac.png" alt="\left(1-\frac{k}{1+k}\right)^{1,2}"></p>-->
						<p>Defensive army casualty ratio:</p><!-- <img style="vertical-align:middle" src="../img/rdc.png" alt="\left(\frac{k}{1+k}\right)^{1,2}"></p>-->
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black">k</td></tr>
										<tr><td>1+k</td></tr>
									</table>
								</td>
								<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top"><sup><?php echo toComma($config['superiorityExponent']); ?></sup></td>
							</tr>
						</table>
					<h4>Scout attack</h4>
						<p>Casualty rate of the attackers: </p><!--<img style="vertical-align:middle" src="../img/sac.png" alt="\left(1-\frac{k}{1+k}\right)^{1+\left(1-\frac{k}{1+k}\right)}"></p>-->
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>1</td>
								<td>&ndash;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom:1px solid black">k</td></tr>
										<tr><td>1+k</td></tr>
									</table>
								</td>
								<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top;">
									<table class="math" style="font-size:0.7em; margin-top:-1em">
										<td>1</td>
										<td>+</td>
										<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
										<td>1</td>
										<td>&ndash;</td>
										<td>
											<table class="math">
												<tr><td style="border-bottom:1px solid black">k</td></tr>
												<tr><td>1+k</td></tr>
											</table>
										</td>
										<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
									</table>
								</td>
							</tr>
						</table>
						<p>Casualty rate of the defenders: </p>
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom:1px solid black">k</td></tr>
										<tr><td>1+k</td></tr>
									</table>
								</td>
								<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top"><sup>2</sup></td>								
							</tr>
						</table>
					<h4>Casualties when catapults attack the City wall</h4>
					<p>First, we calculate a raid without the superiority bonus. In this case the casualty rate of the attackers are:</p>
					<table class="math">
						<tr>
							<td>1</td>
							<td>&ndash;</td>
							<td>
								<table class="math">
									<tr><td style="border-bottom: 1px solid black">k</td></tr>
									<tr><td>1+k</td></tr>
								</table>
							</td>
						</tr>
					</table>
					<!--<img style="vertical-align:middle" src="../img/rac.png" alt="\left(1-\frac{k}{1+k}\right)^{1,2}"></p>-->
					<p>Casualty rate of the defenders:</p><!-- <img style="vertical-align:middle" src="../img/rdc.png" alt="\left(\frac{k}{1+k}\right)^{1,2}"></p>-->
					<table class="math">
						<tr>
							<td>
								<table class="math">
									<tr><td style="border-bottom: 1px solid black">k</td></tr>
									<tr><td>1+k</td></tr>
								</table>
							</td>
						</tr>
					</table>
					<p>After this phase the catapults will shot the wall and destroy several levels. After this recalculate the force ratio for the survivors with the new wall level, denote this with <var>l</var>.</p>
					<p>So the actual casualty ratio of the offensive army is:</p>
					<table class="math">
						<tr>
							<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
							<td>1</td>
							<td>&ndash;</td>
							<td>
								<table class="math">
									<tr><td style="border-bottom: 1px solid black">k</td></tr>
									<tr><td>1+k</td></tr>
								</table>
							</td>
							<td>+</td>
							<td>
								<table class="math">
									<tr><td style="border-bottom: 1px solid black">k</td></tr>
									<tr><td>(1+k)l</td></tr>
								</table>
							</td>							
							<td style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
							<td style="vertical-align:top"><sup><?php echo toComma($config['superiorityExponent']); ?></sup></td>
						</tr>
					</table>
					<p>For the defenders: </p>
					<table class="math">
						<tr>
							<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
							<td>
								<table class="math" style="padding: 0.5em 0 0.5em 0">
									<tr>
										<td>
											<table class="math">
												<tr><td style="border-bottom: 1px solid black">k</td></tr>
												<tr><td>1+k</td></tr>
											</table>
										</td>
										<td>+</td>
										<td>l&middot;</td>
										<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
										<td>1</td>
										<td>&ndash;</td>							
										<td>
											<table class="math">
												<tr><td style="border-bottom: 1px solid black">k</td></tr>
												<tr><td>1+k</td></tr>
											</table>
										</td>							
										<td style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
									</tr>
								</table>
							</td>
							<td style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
							<td style="vertical-align:top"><sup><?php echo toComma($config['superiorityExponent']); ?></sup></td>
						</tr>
					</table>
				<h3>Calculation of the actual value of the casualties</h3>
					<p>We have a casualty rate number which is between 0 and 1 (inclusice), for both the attackers and defenders. Now multiply the amount of the soldiers with this number respectively and round the result. Done.</p>
				<h3>Calculation of the catapult's demolition</h3>
					<p>Raise the target building's level to the power of 1.7. We get the stability number of the building. Subtract the amount of catapults hit in. The calculate the 1.7th root of the number. And finally round it upward. This will be
					the new level of the building.</p>
				<h3>Several examples</h3>
					<h4>Last man standing battle</h4>
						<?php $attackers=array(1000,1000,0,0,0); $defenders=array(0,0,2000,0,0); ?>
						<p>Let the offensive army be the following: <?php echo unitEnumerator($attackers); ?>.</p>
						<p>Let the defensive army be the following: <?php echo unitEnumerator($defenders); ?>.</p>
						<p>Calculate the defense and offense vectors.</p>
						<p>The offense vector is: <b>a</b>=<?php echo vectorCalculator($attackers,$attackVector,true); ?>.</p>
						<p>The defense vector is: <b>d</b>=<?php echo vectorCalculator($defenders,$defenseVector,false); ?>.</p>
						<p>Calculate the rate of the forces:</p>
						<table class="math">
							<tr>
								<td>k<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$powerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<p>
							Casualty ratio of the offensive army is:
							<?php
								if ($powerRatio<1)
								{
									$attCasualties=1;
									?>
										100% because k&lt;1.
									<?php
								}
								else
								{
									$attCasualties=1/pow($powerRatio,$config['superiorityExponent']);
									?>
										</p>
										<table class="math">
											<tr>
												<td>
													<table class="math">
														<tr><td style="border-bottom: 1px solid black">1</td></tr>
														<tr><td>k<sup><?php echo toComma($config['superiorityExponent']); ?></sup></td></tr>
													</table>
												</td>
												<td>=</td>
												<td><?php echo toComma(round($attCasualties,3)); ?></td>
											</tr>
										</table>
										<p>
									<?php
								}
							?>
						</p>
						<p>
							Casualty ratio of the defensive army is:
							<?php
								if ($powerRatio>1)
								{
									$defCasualties=1;
									?>
										100%, mert k&gt;1.
									<?php
								}
								else
								{
									$defCasualties=pow($powerRatio,$config['superiorityExponent']);
									?>
										k<sup><?php echo toComma($config['superiorityExponent']); ?></sup>=<?php echo toComma(round($defCasualties,3)); ?>
									<?php
								}
							?>
						</p>
						<p>Casualties of the offensive army: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Casualties of the defensive army: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
					<h4>Raid</h4>
						<p>Now calculate a raid with the same army. The force ratio will be the same so we don't need to recalculate it again.</p>
						<p>Casualty ratio of the offensive army is:</p>
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>1</td>
								<td>&ndash;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top"><sup><?php echo toComma($config['superiorityExponent']); ?></sup></td>
								<td>=</td>
								<td>
									<?php
										$attCasualties=pow(1-$powerRatio/(1+$powerRatio),$config['superiorityExponent']);
										echo toComma(round($attCasualties,3));
									?>
								</td>
							</tr>
						</table>
						<p>Casualty ratio of the defensive army is</p>
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top"><sup><?php echo toComma($config['superiorityExponent']); ?></sup></td>
								<td>=</td>
								<td>
									<?php
										$defCasualties=pow($powerRatio/(1+$powerRatio),$config['superiorityExponent']);
										echo toComma(round($defCasualties,3));
									?>
								</td>
							</tr>
						</table>			
						<p>Casualties of the attacker side: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Casualties of the defender side: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
					<h4>Scout attack</h4>
						<p>We know the force ratio only the formula is different: </p>
						<p>Casualty ratio of the attacker side: </p>
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>1</td>
								<td>&ndash;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom:1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top;">
									<table class="math" style="font-size:0.7em; margin-top:-1em">
										<td>1</td>
										<td>+</td>
										<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
										<td>1</td>
										<td>&ndash;</td>
										<td>
											<table class="math">
												<tr><td style="border-bottom:1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
												<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
											</table>
										</td>
										<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
									</table>
								</td>
								<td>=</td>
								<td>
									<?php
										$baseCasualty=1-$powerRatio/(1+$powerRatio);
										$attCasualties=pow($baseCasualty,1+$baseCasualty);
										echo toComma(round($attCasualties,3));
									?>
								</td>
							</tr>
						</table>
						<p>Casualty ratio of the defender side: </p>
						<table class="math">
							<tr>
								<td style="border-left: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom:1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td  style="border-right: 1px solid black;border-top: 1px solid black;border-bottom: 1px solid black;">&nbsp;</td>
								<td style="vertical-align:top"><sup>2</sup></td>								
								<td>=</td>
								<td>
									<?php
										$baseCasualty=$powerRatio/(1+$powerRatio);
										$defCasualties=pow($baseCasualty,2);
										echo toComma(round($defCasualties,3));
									?>
								</td>
							</tr>
						</table>		
						<p>Casualties of the attacker side: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Casualties of the defender side: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
					<h4>Attack with catapults</h4>
						<?php
							$catapults=1400;
							$buildingLevel=50;
							$attackers=array(1000,1000,0,$catapults,0);
							$defenders=array(0,0,2000,0,0);
						?>
						<p>Add <?php echo $catapults ?> catapults to our army, attack a level <?php echo $buildingLevel; ?> building. Catapults fire only in last man standing battles, so we only handle this case.</p>
						<p>So the attacking army is: <?php echo unitEnumerator($attackers); ?>.</p>
						<p>So the defending army is: <?php echo unitEnumerator($defenders); ?>.</p>
						<p>Calculate the offense and defense vectors.</p>
						<p>The offense vector is: <b>a</b>=<?php echo vectorCalculator($attackers,$attackVector,true); ?>.</p>
						<p>The defense vector is: <b>d</b>=<?php echo vectorCalculator($defenders,$defenseVector,false); ?>.</p>
						<p>Calculate the ratio of the forces:</p>
						<table class="math">
							<tr>
								<td>k<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$powerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<p>
							Casualty ratio of the attacker army is:
							<?php
								if ($powerRatio<1)
								{
									$attCasualties=1;
									?>
										100% because k&lt;1.
									<?php
								}
								else
								{
									$attCasualties=1/pow($powerRatio,$config['superiorityExponent']);
									?>
										</p>
										<table class="math">
											<tr>
												<td>
													<table class="math">
														<tr><td style="border-bottom: 1px solid black">1</td></tr>
														<tr><td>k<sup><?php echo toComma($config['superiorityExponent']); ?></sup></td></tr>
													</table>
												</td>
												<td>=</td>
												<td><?php echo toComma(round($attCasualties,3)); ?></td>
											</tr>
										</table>
										<p>
									<?php
								}
							?>
						</p>
						<p>
							Casualty ratio of the defender army is:
							<?php
								if ($powerRatio>1)
								{
									$defCasualties=1;
									?>
										100% because k&gt;1.
									<?php
								}
								else
								{
									$defCasualties=pow($powerRatio,$config['superiorityExponent']);
									?>
										k<sup><?php echo toComma($config['superiorityExponent']); ?></sup>=<?php echo toComma(round($defCasualties,3)); ?>
									<?php
								}
							?>
						</p>
						<p>So the casualties of the attacker army is: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>So the casualties of the defender army is: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
						<p>So we know the casualties. Now what's up with the building destruction? Calculate how many catapults fire:</p>
						<table class="math">
							<tr>
								<td><?php echo $catapults; ?></td>
								<td>&middot;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td>=</td>
								<td>
									<?php
										$catsFire=$catapults*($powerRatio/(1+$powerRatio));
										echo toComma(round($catsFire,3));
									?>
								</td>
							</tr>
						</table>
						<p>Now calculate the strength of the building: <?php echo $buildingLevel?><sup>1.7</sup>=<?php $bStrength=pow($buildingLevel,1.7); echo toComma(round($bStrength,3)); ?>.</p>
						<p>Now subtract the count of the firing catapults: <?php $rStrength=$bStrength-$catsFire; echo toComma(round($bStrength,3)).'&ndash;'.toComma(round($catsFire,3)).'='.toComma(round($rStrength,3));?>.</p>
						<?php
							if ($rStrength>0)
							{
								?>
									<p>Now calculate the building level from this number (calculate the 1.7th root):
										<?php echo toComma(round($rStrength,3)); ?><sup>1/1.7</sup>=<?php $rLevel=pow($rStrength,1/1.7); echo toComma(round($rLevel,3)); ?>.</p>
									<p>Now round this up to get building level after the demolition: level <?php echo ceil($rLevel); ?>.</p>
								<?php
							}
							else
							{
								?>
									<p>Since more catapults fired than the strength of the building, the building will be demolished (will be level 0.)</p>
								<?php
							}
						?>
					<h4>Attack city wall with catapults</h4>
						<?php
							$catapults=1400;
							$wallLevel=15;
							$buildingLevel=$wallLevel;
							$attackers=array(1000,1000,0,$catapults,0);
							$defenders=array(0,0,2000,0,0);
							vectorCalculator($defenders,$baseDefenseVector,false);
						?>
						<p>Attack the wall with our previous army. Let the defender village have a level <?php echo $wallLevel; ?> city wall.</p>
						<p>So the attacker army: <?php echo unitEnumerator($attackers); ?>.</p>
						<p>The defender army: <?php echo unitEnumerator($defenders); ?>.</p>
						<p>Calculate the vectors.</p>
						<p>The offense vector is: <b>a</b>=<?php echo vectorCalculator($attackers,$attackVector,true); ?>.</p>
						<p>The defender vector is: <b>d</b>=<?php echo vectorCalculator($defenders,$defenseVector,false,$wallLevel+1); ?>.</p>
						<p>We will need the defense vector without wall's bonus: <b>d<sub>0</sub></b>=<?php echo vectorCalculator($defenders,$baseDefenseVector,false); ?>.</p>
						<p>Számoljuk ki az erők arányát:</p>
						<table class="math">
							<tr>
								<td>k<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$powerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<p>First we simulate a raid without the superiority bonus.</p>
						<p>The casualty ratio of the attackers:</p>
						<table class="math">
							<tr>
								<td>1</td>
								<td>&ndash;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td>=</td>
								<td>
									<?php
										$attCasualties=1-$powerRatio/(1+$powerRatio);
										echo toComma(round($attCasualties,3));
									?>
								</td>
							</tr>
						</table>
						<p>The casualty ratio of the defenders:</p>
						<table class="math">
							<tr>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td>=</td>
								<td>
									<?php
										$defCasualties=$powerRatio/(1+$powerRatio);
										echo toComma(round($defCasualties,3));
									?>
								</td>
							</tr>
						</table>
						<p>Apply these casualty ratios on our attacker and defense vectors.</p>
						<p>So the new offense vector: <?php echo applyCasualtiesToVector($attackVector,$attCasualties,$attackVector); ?>.</p>						
						<p>Apply the the defender's casualty ratio on the defense vector without the wall bonus, because we will need to remultiply it with the reduced wall's bonus: <?php echo applyCasualtiesToVector($baseDefenseVector,$defCasualties,$baseDefenseVector); ?>.</p>						
						<p>After this, the catapults fire. We need to know, how many catapult will fire:</p>
						<table class="math">
							<tr>
								<td><?php echo $catapults; ?></td>
								<td>&middot;</td>
								<td>
									<table class="math">
										<tr><td style="border-bottom: 1px solid black"><?php echo toComma(round($powerRatio,3)); ?></td></tr>
										<tr><td>1+<?php echo toComma(round($powerRatio,3)); ?></td></tr>
									</table>
								</td>
								<td>=</td>
								<td>
									<?php
										$catsFire=$catapults*($powerRatio/(1+$powerRatio));
										echo toComma(round($catsFire,3));
									?>
								</td>
							</tr>
						</table>
						<p>Now calculate the building strength of the wall: <?php echo $buildingLevel?><sup>1.7</sup>=<?php $bStrength=pow($buildingLevel,1.7); echo toComma(round($bStrength,3)); ?>.</p>
						<p>Then subtract the number of the catapults that fired: <?php $rStrength=$bStrength-$catsFire; echo toComma(round($bStrength,3)).'&ndash;'.toComma(round($catsFire,3)).'='.toComma(round($rStrength,3));?>.</p>
						<?php
							if ($rStrength>0)
							{
								?>
									<p>Now calculate the building level from this number (calculate the 1.7th root):
										<?php echo toComma(round($rStrength,3)); ?><sup>1/1.7</sup>=<?php $rLevel=pow($rStrength,1/1.7); echo toComma(round($rLevel,3)); ?>.</p>
									<p>Now round this up to get building level after the demolition: level <?php echo ceil($rLevel); ?>.</p>								
								<?php
							}
							else
							{
								$rLevel=0;
								?>
									<p>Since more catapults fired than the strength of the building, the building will be demolished (will be level 0.)</p>
								<?php
							}
						?>
						<p>So the wall will be level <?php echo $rLevel; ?> after the attack.</p>
						<p>Now calculate the new defense vector: <?php echo applyFactorToVector($baseDefenseVector,$rLevel+1,$defenseVector); ?></p>
						<p>Calculate the new force ratio:</p>
						<table class="math">
							<tr>
								<td>l<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$newPowerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<?php $oldAttCasualties=$attCasualties; $oldDefCasualties=$defCasualties; ?>
						<p>Now calculate the casualties again. Now for last man standing battle, without the superiority bonus.</p>
						<p>
							Casualty ratio of the attacker:
							<?php
								if ($powerRatio<1)
								{
									$newAttCasualties=1;
									?>
										100%, because l&lt;1.
									<?php
								}
								else
								{
									$newAttCasualties=1/$newPowerRatio;
									?>
										</p>
										<table class="math">
											<tr>
												<td>
													<table class="math">
														<tr><td style="border-bottom: 1px solid black">1</td></tr>
														<tr><td>l</td></tr>
													</table>
												</td>
												<td>=</td>
												<td><?php echo toComma(round($newAttCasualties,3)); ?></td>
											</tr>
										</table>
										<p>
									<?php
								}
							?>
						</p>
						<p>
							Casualty ratio of the defender:
							<?php
								if ($powerRatio>1)
								{
									$newDefCasualties=1;
									?>
										100%, because l&gt;1.
									<?php
								}
								else
								{
									$newDefCasualties=$newPowerRatio;
									?>
										l=<?php echo toComma(round($newDefCasualties,3)); ?>
									<?php
								}
							?>
						</p>
						<p>Then we can calculate the final casualties from the first and second phase's casualty ratios:</p>
						<p>The final casualty ratio of the attacker:
							<?php
								$attCasualties=pow($oldAttCasualties+$newAttCasualties*(1-$oldAttCasualties),$config['superiorityExponent']);
								echo '('.toComma(round($oldAttCasualties,3)).'+'.toComma(round($newAttCasualties,3)).'&middot;(1&ndash;'.toComma(round($oldAttCasualties,3)).'))<sup>'.toComma(round($config['superiorityExponent'],3)).'</sup>='.toComma(round($attCasualties,3));  
							?>
						</p>
						<p>The final casualty ratio of the defender:
							<?php
								$defCasualties=pow($oldDefCasualties+$newDefCasualties*(1-$oldDefCasualties),$config['superiorityExponent']);
								echo '('.toComma(round($oldDefCasualties,3)).'+'.toComma(round($newDefCasualties,3)).'&middot;(1&ndash;'.toComma(round($oldDefCasualties,3)).'))<sup>'.toComma(round($config['superiorityExponent'],3)).'</sup>='.toComma(round($defCasualties,3));  
							?>
						</p>
						<p>The final casualties of the attacker: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>The final casualties of the defender: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>						
			</div>
			<a href="javascript:void(toggleElement('miscfaq'))">Misc FAQ :)</a><br>
			<div class="helpdiv" id="miscfaq">
				<a href="javascript:void(toggleElement('faq00'))">Where can I view when does my troops arrive?</a><br>
				<div class="helpdiv" id="faq00">
					<p>In the left bottom panel click on the number. The events will appear in a window.</p>
				</div>
				<a href="javascript:void(toggleElement('faq0'))">How can I share battle reports with others?</a><br>
				<div class="helpdiv" id="faq0">
					<p>View the report, then check 'publish', then copy and paste the link you see at the top of the report. You should also hide the report too to avoid accedental deletion of it.</p>
				</div>
				<a href="javascript:void(toggleElement('faq1'))">What happens if they capture my village I have outgoing troop movements from?</a><br>
				<div class="helpdiv" id="faq1">
					<p>If you have outgoing attack or troop movement, it will arrive unless the new owner of the village cancels the action. (If you escape with your troops, moving them to another village, the new owner can't see that, so he 
					can not cancel it.)<p>
					<p>
					If you have outgoing village settling from the village you've just lost, you will get back all your expansion points which are taken when you started the settling action, and those points will be taken from the new owner, 
					because the settled villages will be the property of the new owner. This way, the new owner of your village may have negative expansion points, but he can cancel the settling, so he will get his points back.</p>
					<p>If you have outgoing village conquering action, the expansion point will be taken from the new owner, because the diplomat will conquer the village for him.</p>		
				</div>
				<a href="javascript:void(toggleElement('faq2'))">What happens if they destroy the village I have outgoing troop movements from?</a><br>
				<div class="helpdiv" id="faq2">
					<p>If you have settling or village conquering action from the village in question, the conquered and settled villages will be abandoned villages. You will get back all you expansion points you spent on settling those villages.
					If you have outgoing attacks, they will arrive and attempt to return, but seeing there is no village, they will disband and disappear. If hero led the army, it will disappear, be careful!</p>
					<p>Please note, if there are survivors when the village is demolished, the village will stay here with zero points.</p>
				</div>
				<a href="javascript:void(toggleElement('faq3'))">What happens if they destroy the village I have troop movements to?</a><br>
				<div class="helpdiv" id="faq3">
					<p>The troops, seeing the empty cell, will return.</p>
				</div>
			</div>
		</div>
		<hr>
		<a href="../../tutorial.php">Tutorial (which appears when you login first time.)</a><br>
	</body>
</html>










