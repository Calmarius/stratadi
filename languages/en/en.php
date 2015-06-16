<?php

/*

This is the language definition file of the game. Every text that appears in the game is stored here as a key => value pair. If you are making a translation you can modify these values. You may see placeholders ({n})
in some strings, the game will substitute a value there. The context of every entry is provided as eol comments.

Please don't add entries to this file without the permission of the admin. You may make grammar related functions but please prefix your function names with 'lang_'. If you declare global variables prefix them with 'lang_'.

Strings containing {n} placeholders will be fed through our xprintf utility function. Its first argument will be the format string found in this file. But sometimes may need to do some preprocessing on the word that going
to be inserted, in this case you can specify an array instead of the format string. It must have a 'format' key containing the original format string and it should also have numeric keys with the name of the function (as string) that will be
executed on the string and the string it returns will be executed.

*/

$lang_vowels=array('a','á','e','é','i','í','o','ó','ö','ő','u','ú','ü','ű','A','Á','E','É','I','Í','O','Ó','Ö','Ő','U','Ú','Ü','Ű');

if (!isset($_languageIncluded))
{
	// your language related functions are going here.
	function lang_capArticle($word)
	{
		global $lang_vowels;
		if (in_array($word[0],$lang_vowels)) return "Az $word"; else return "A $word";
	}

	function lang_minArticle($word)
	{
		global $lang_vowels;
		if (in_array($word[0],$lang_vowels)) return "az $word"; else return "a $word";
	}
	
	function jumpHelpPage()
	{
		header('Location: languages/en/enhelp.php');
	}

}

@$language=array
(
	'abandonvillage'=>'Abandon village', 	// in village view
	'acceptinvitation' =>"Join guild",  // guild window when not member of a guild.
	'accessdenied'=>'Access denied', // in genernal, when access is denied
	'accountmaster'=>'Account master', // in edit kings window
	'actionmode'=>'Action mode (SHIFT)', // in game
	'action'=>'Action', // in invite player window, edit kings window
	'actions'=>'Mouse actions', //in game, top left bar.
	'activate' => "Activate",  // after login in the inactive user form
	'activationcode' => "Activation code",  // after login in the inactive form
	'activityinyouraccount'=>'Show activity in your account', // in the left menu
	'addparticipant'=>"Add participant: ", // in the compose message window.
	'addnewking'=>'Add new king: ', //in the edit kings window
	'adminactivity'=>'Admin\'s activity', // in the activity plot
	'adminlogin'=>'Admin login', // in admin mode, in game
	'adminmessage'=>'Admin', // report icon's text
	'adminstuff'=>'Deus ex machina', // in admin mode
	'age' => "Age",  // In the profile
	'agebonus'=>"Age bonus: ", // In battle simulator
	'agebonustext'=>"Age bonus: {1}×", // In the profile
	'agebonusjstext'=>"Age bonus: {0}×", // When attacking a village. // TODO make the PHP string formatters {0} based.
	'ally'=>'ally', // diplomatic stance
	'amountform' => '{1} units of {2}', // general from of amounts, {1} means the amount, {2} is the subject. Eg.: 1 pieces of furniture would be "{1} pieces of {2}", for in game example: 2 spearmen
	'april' => "April",  // in the date picker control
	'archerlevel' => "Level of archers",  // in game, village properties
	'archer' => "archer",  // in game, village properties
	'archerattack'=>'Arrow damage', // in unit description tables
	'archerdefense'=>'Defense against arrows', // in unit description tables
	'archers' => "archers",  // in game, village properties
	'archeryrange' => "Archery range",  // in game, village properties
	'armytop10'=>'Army size top 10', // oraculum
	'armyvaluetext'=>'Minimal value of the offensive army: {1}, value of the army you set: {2}', // in the window where you send troops {1}: the minimal value of the army you going to send. {2}: the value of the army you already set.
	'ascendingordering'=>'Ascending sort', // in village summary
	'attack'=>'Last man standing battle', // in action window
	'attacker'=>'Attacker: ', // in battle simulator
	'attackerlosses'=>'Attacker\'s loss was {1} gold', // in battle report {1} is the amount of of the gold
	'attackerheroes'=>'Attacker\'s heroes was the following: {1}', // in battle report
	'attackerlostbattle'=>'Attacker has been defeated, lost all his troops.', // in battle report
	'attackerlostbattlehero'=>'The hero fled from the battle and didn\'t dare to return to the kingdom.', // in battle report
	'attackervillage'=>'Attacker village: {1} ({2})', // in battle report the attacker village text. {1}: village name (as link), {2}: owner name (as link to profile)
//	'attackevent'=>'{7}. {1}: {2} faluból, {3} falu ellen, {4} egységekkel, {9} épület ellen. A sereg vezetője: {8}. {10}', // the description of the attack event {1} event type, {2} launcher village name, {3} destination village name, {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link, {11} the color code of the event
	'attackevent'=><<< X
		<table class="center" style="text-align:center; background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Starter village: {2}</td><td>Target village: {3}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">Leader: {8}</td></tr>
			<tr><td colspan="2">Against {9}</td></tr>
			<tr><td colspan="2">{10}</td></tr>
		</table>
X
	, // the description of the move event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link, {11} the color code of the event
	'attackfail'=>'Failed attack', // in reports window
	'attacklevelofhero'=>'Offense level of the attacking hero: ', // in battlesimulator
	'attacknoloss'=>'Attack without casualties', // in reports window
	'attackpower'=>'Attack power', // in unit description tables
	'attacktitle'=>'{1}({2}) attacked {3}({4}).', // in battle report's title {1}: attacker village's name (as link to village), {2}: owner (as link to the profile), {3} (as link to village): defender village's name, {4}: defender name (as link to profile)
	'attackwithloss'=>'Attack with casualties', // in reports window
	'august' => "August",  // in the date picker control
	'available'=>'Available', // in game, village view, unit table
	'avatargc'=>'Clear avatar junk', // in admin panel
	'backtomainpage' => "Return to the main page", 		// in the registration success page
	'backtoprevpage' => "Return to the previous page", 		// in general when you asked to step back.
	'backup'=>'Backup', // in admin panel
	'badpassword' => "Wrong password. Try again. If you forgot your password please write an e-mail to admin (${config['adminMail']} from the e-mail address you registered from (in English or Hungarian).", 		// error in the login page
	'barracks' => "Barracks", 		// in game, village properties
	'battlesimulator'=>'Battle simulator', // in game, left top menu, battle simulator window's title
	'becarefulsendtroopsmultiplevillages'=>'Attention! if you launch actions from multiple villages, the troops going in separate waves, so their efficiency is smaller than you send it all in one large wave.', // in send trooops window
	'birthdate' => "Birthday: ", 		// in the registration form
	'blacksmith' => "Blacksmith", 		// in the registration form
	'building'=>'Building', // in the weekly oracle
	'buildinginfotooltip'=>'<b>{1}</b>,Level {2},<br>Cost of upgrade to the next level: <b>{3}</b> gold + one build point<br><i>Click the button to upgrade the building.</i>', // in game village view, the tooltip when you hover the building icon. {1}: the building name, {2}: the building level (js), {3}: it's cost (js)
	'buildingnotexist' => "Building does not exist with the code {1}", 		// in game, error when trying to upgrade a not existing building ({1} is the code of the building)
	'buildpoints' => "Build points", 		// in game, village properties
	'buildthisbuilding'=>'Upgrade this building', // in mass building window
	'buildtolevel'=>'Upgrade the building to this level: ', // in mass bulding window
	'calculate'=>'Calculate', // in battle simulator
	'cancel'=>'Cancel', // in 'cancel' operation (used in: task list )
	'cancelevent'=>'Cancel action', // in event list
	'cantkicklastking'=>'You can not kick the last king.', // in edit kings window
	'cantlaunchemptywave'=>'You can not launch empty army.', // when sending troops
	'catapult' => "catapult", 		// in game, village properties
	'catapulttargetiswall'=>'Catapult attacking the wall.', // in battle simulator
	'catapults' => "catapults", 		// in game, village properties
	'catapultlevel' => "Level of the catapults", 		// in game, village properties
	'cavalryattack'=>'Slash damage', // in unit description tables
	'cavalrydefense'=>'Defense against slash', // in unit description tables
	'checkreferred'=>'Check user whether he reached the desired level.', // in invite player window
	'city' => "City: ", 		// in the registration form
	'cleartasks' => "Cancel tasks", 		// in game, tasks
	'close' => "Close", 		// in game, village properties and all other windows
	'closewindow' => "Close window", 		// in iframe
	'committasks' => "Commit tasks", 		// in game, tasks
	'community'=>'Community', // in game, top left bar
	'composemessage' => "Compose message", 		// in game, tasks
	'composenewmessage' => "Compose new message", 		// in compose message window
	'confirm'=>'Confirm', // in abandon village window
	'conqueredthevillage'=>'Attacker conquered the village.', // in battle report
	'costraints'=>'Constraints', // in the massive building window
	'coordinate'=>'({1};{2})', // the form of the coordinates
	'coordinates'=>'Coordinates', // in the village summary, world events window
	'cost'=>'Cost', // in unit description tables, and building cost view.
	'coststring'=>' {1} <img src="/img/gold_small.png" alt="gold" title="gold">', // cost string. {1} the amount of gold (as html <span></span> tags for buildings, and raw number for units)
	'createnewhero'=>'Create new hero in this village.', //
	'currentrights'=>'Currently granted rights: ', //
	'databaseerror'=>'Unable to connect to the database, the cause of the error: {1}, creator of the game can not fix this.', // FATAL ERROR
	'daysbefore'=>'Days before: ', //
	'debug'=>'Debugging', // for admins
	'december' => "December",  // in the date picker control
	'defaultmode'=>'Default', // mouse mode, in left menu
	'delete'=>'Delete', // in general
	'defender'=>'Defender: ', // in battle simulator
	'defenderheroes'=>'Defender\'s heroes were the following: {1}', // in battle report
	'defenderwalllevel'=>'Defender village\'s wall level: ', // in battlesimulator
	'defendlevelofhero'=>'Defense level of the defender hero: ', // in battlesimulator
	'defenderlosses'=>'Defender\'s loss is {1} gold', // in battle report {1} is the amount of of the gold
	'defenderlostbattle'=>'Defender has been defeated. All his troops perished.', // in battle report
	'defenderlostbattlehero'=>'Defender heroes fled from the battle and they won\'t dare to return to their kings.', // in battle report
	'defendertargetlevel'=>'Catapult target building level: ', // in battlesimulator
	'defendervillage'=>'Defender village: {1} ({2})', // in battle report the defender village text. {1}: village name (as link), {2}: owner name (as link to profile)
	'defensefail'=>'Defense, fail.', // in reports window.
	'defensenoloss'=>'Defense, no casualties.', // in reports window.
	'defensepower'=>'Defense', // in unit description tables
	'defensetop10'=>'Defense Top 10', // in the weekly oracle
	'defensewithloss'=>'Defense, there were casualties', // in reports window.
	'delegate'=>'Delegate', // in deputies window
	'delegationfinished'=>'Account sitting finished', // when the sponsor finishes your delegation.
	'delegationrequest'=>'Account sitting request received', // when you request someone as delegate
	'delete'=>'Delete', // in report view
	'deletediplomacyrelation'=>'Delete diplomatic stance', // in diplomacy view
	'demolishedto'=>'(Demolished to level {1})', // in battle simulator
	'demolitiontext'=>'Destruction: {1} {2}. szint => {3}.szint', // in battle report. {1} the building image (with the building name as title), {2} initial level, {3} demolished level
	'deputies'=>'Account sitting', // in the left menu
	'deputywillreceiveareport'=>'The  will get a report about the delegation. The deputy can build, can train units, settle villages and move your troops among your villages. But he can\' attack or give away your troops to others.', // in the deputies window.
	'descendingordering'=>'Descending sort', // in village summary
	'destroyedvillage'=>'Village destroyed.', // in reports window.
	'died'=>'Perished: ', // in battle simulator
	'diplomacyreporttitle'=>'{1} guild set a diplomatic stance with your guild.', // {1} the report title when setting diplomacy.
	'diplomacyreporttext'=>'{1}, the diplomat of the  {2} guild, set the following diplomatic stance with your guild: <i>{3}</i>. It\'s recommended to requite the diplomatic stance. Please contact {1} for details..', // The diplomacy report text. {1} the initiator name (as link), {2} the guild name (as link), {3} the diplomatic stance
	'diplomacybrokenreporttitle'=>'{1} guild broken the diplomatic stance with your guild.', // {1} the report title when breaking diplomacy.
	'diplomacybrokenreporttext'=>'{1}, the diplomat of the {2} guild, broken the diplomatic stance with you guild. Please contact {1} for details.', // the report text of the diplomacy breaking report. {1}: the initiator's name (as link), {2}: guild name (as link)
	'diplomacyrequitedtitle'=>'{1} requited the diplomatic stance.', // the diplomatic stance requited report title
	'diplomacyrequitedtext'=>'{1}, the diplomat of the {2} guild, requited the diplomatic stance with you guild.', // the diplomatic stance requited report text. {1}: initiator (as link), {2}: guild name (as link)
	'diplomacyrelationswithotherguilds'=>'Diplomatic stances with other guilds:', // in diplomacy management window
	'diplomacywithguild'=>'With <a href="viewguild.php?id={3}">{1}</a> guild {2}', // entry in the diplomacy window. {1}:guild name, {2}: diplomatic stance, {3}: guild Id
	'diplomaticstance'=>'The choosen diplomatic stance: ', // in diplomacy management window
	'diplomaticstancealreadyexist'=>'You already have diplomatic stance with that guild..', // error when setting diplomacy
	'diplomats' => "diplomats",  // the plural unit name
	'diplomat' => "diplomat",  // the singular unit name
	'deputies'=>'Account sitting', // title of the deputies window
	'dismissguild' => "Dismiss guild",  // guild right name, guild page
	'donatorvillage'=>'Transferring village: {1} ({2})', // in move report the donator village text. {1}: village name (as link), {2}: owner name (as link to profile)
	'editguildprofile' => 'Edit guild profile', // in guild page
	'editingprofile' => 'Edit profile', // my profile page, title; link name if you open your own profile
	'email' => "E-mail: ",  // in the registration form
	'emailalreadyused' => "Someone already registered with this e-mail.",  // registration form error
	'enterpasswordtoconfirm'=>'Type your password to confirm the operation:', // in abandon village window
	'enterplayernametoinvite'=>'Type the player\'s name to invite: ', // in invite player window
	'Events' => 'Events',
	'eventtime'=>'Time of the event', // in world events
	'eventtype'=>'Type of the event', // in event bar
	'everyvillageyouselectedwilltrain'=>'The training of the given amount of troops will be distributed among the selected villages so they will be trained the fastest possible.', //in mass training window.
	'expansionpoints'=>'Expansion points', // in game status indicator
	'extras'=>'Extras ', // in the left top bar in game.
	'february' => "February",  // in the date picker control
	'female' => "female",  // in the gender picker control
	'forum'=>'Forum', // in top left bar.
	'foundguildbutton' => "Found guild",  // in guild window, when you don't have guild. 
	'foundaguild' => "Found a guildt",  // in guild window, when you don't have guild. 
	'gamedescription'=><<< X
		<p>Important note: this game uses <i>&lt;canvas&gt;</i> element for display which works in most modern browsers except Internet Explorer (tough IE9 will support it).</p>
		<p>Like the game's Facebook page: <a href="https://www.facebook.com/pages/StraTaDi/209096639149286">here</a> (Most of users are Hungarian there, but English is accepted too.)</p>
		<p>Silent child's mother never heards the word... If you have problems with login or you experience bugs, please report it on the Facebook page!</p>
		<p><i>It seems you can't register using @facebook.com mail if you don't white list the admin's e-mail address (${config['adminMail']}) in your setting. Be aware of this! (I already got some bounced mails from there.)</i></p>
		<p>You look around on the map without registration: <a href="game.php?guest">[Login as guest]</a></p>
		<p>If you want to try the game, use demo as username and empty password.</p>
		<div style="width:70%; height:auto; margin:0 auto 0 auto; display:block; position:relative; left:0; top:0; clear:both">
			<h4>Screenshots</h4>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/profile.png"><img src="${config['imageRoot']}/thumb_profile.png" alt="Your profile" title="Your profile"></a><br>Your profile
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/action1.png"><img src="${config['imageRoot']}/thumb_action1.png" alt="Starting action" title="Starting action"></a><br>Starting action
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/action2.png"><img src="${config['imageRoot']}/thumb_action2.png" alt="Starting action" title="Starting action"></a><br>Starting action
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/map.png"><img src="${config['imageRoot']}/thumb_map.png" alt="The map" title="The map"></a><br>The map
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/overview.png"><img src="${config['imageRoot']}/thumb_overview.png" alt="Village summary" title="Village summary"></a><br>Village summary
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/villageview.png"><img src="${config['imageRoot']}/thumb_villageview.png" alt="Your village" title="Your village"></a><br>Your village
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/battlesim.png"><img src="${config['imageRoot']}/thumb_battlesim.png" alt="Battle simulator" title="Battle simulator"></a><br>Battle simulator
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/dusk.png"><img src="${config['imageRoot']}/thumb_dusk.png" alt="Dusk" title="Dusk"></a><br>Dusk
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/night.png"><img src="${config['imageRoot']}/thumb_night.png" alt="Evening came" title="Evening came"></a><br>Evening came
			</div>
			<div class="thumbdiv">
				<a href="${config['imageRoot']}/newgraphics.png"><img src="${config['imageRoot']}/thumb_newgraphics.png" alt="New graphics" title="New graphics"></a><br>New graphics
			</div>
			<div style="clear:both"></div>
		</div>
		<h2>Start of the game: ${config['gameStarted']} </h2>
		<div style="text-align:left; width:640px; margin-left:auto; margin-right:auto">
			<p>This is an online strategy game (MMORTS) similar to Travian or Tribal Wars. You control, build villages and battle against others. But I try to reduce the annoyances you experience on those games (though I might introduce even new annoyances...)</p>
			<ul>
				<li>You can give away and transfer your troops. So their no such thing `reinforcements`. You can move your troops to another village an you can start attack from there too.</li>
				<li>No fake storms: there is minimum limit of the army you send proportional your gold production. So it won't worth spamming fakes anymore.</li>
				<li>Your internet connection lag won't decide between success and failure: There is 1% uncertainty in the troop movement times. So if you want to guarantee that your waves arrive in a given order you need to have gaps
				between them leaving the defender a chance to react.</li>
				<li>One common treasury: gold is not stored in every village, instead there is a big treasury where all gold are held. So no need for internal trading between your villages.</li>
				<li>Build point building system: no need to wait for the build timers to count down. Your villages producing build points that you can spend anytime.</li>
				<li>Massive building and unit training: you can build your villages and train units in all your villages with several clicks.</li>
				<li>You cannot max out villages, every building can be built up to the infinity.</li>
			</ul>
			<p>So you can concentrate on your actions. Good luck, have fun!</p>
		</div>
X
	,
	'gamemenu'=>'Game menu', // in left bar
	'gender' => "Gender: ", 		// in the registration form
	'generateactivityplot'=>"Generate activity plot", // in the generate activity plot window
	'generate'=>"Generate", // in the generate activity plot window
	'go'=>'Go', // in mass building window
	'gobackprevious' => "Go back to the previous page.", 		// in the error page
	'gold' => "gold", 		// in game, playerinfo
	'goldmine' => "Gold mine", 		// in game, village properties
	'goldproductiontop10'=>'Gold production top 10', //
	'goldtext'=>'{1} ({2}/h)', // in game, right top bar. {1}: gold you have (as JS counter spinning upward). {2}: gold production (as JS variable)
	'gotvillagebyconquer'=>'We conquered the village!', // in reports window
	'grantrightsinfo'=>'You can grant rights here to the players. Select one or more players, and one or more rights. The selected rights will be granted to, while the unselected ones will be revoked from the selected players. Be careful when granting the \'granting rights\' right.', // information in grant rights window
	'grantrightstoplayers' => "Granting rights",  // guild right name, guild page
	'guild' => "Guild", 		// in the top bar, in world events
	'guilddiplomat'=>'Guild diplomat', // in guild page
	'guildinvitationreport'=>'<a href="viewplayer.php?id={1}">{2}</a> asks you to join to the  <a href="viewguild.php?id={3}">{4}</a> guild. If you are not a member of a  guild then you can see the invitations at the  <a href="guild.php">guild</a> page.', // guild invitation report content {1}=user's is, {2}=user's name, {3}=guild's id, {4}=guild's name
	'guildinvitations' => "Guild invitations:", 		// in the guild page, the invitations text and on the recruit players page.
	'guildmemberlist' => "Members:",  // guild page
	'guildname' => "Member of: ",  // in game, village properties
	'guildnotexist'=>'Guild does not exist:', // in guild page, and on diplomacy settings.
	'guildpermissionstring'=>'({1})', // in guild page, {1}: enumeration of permissions
	'guildproperty'=>'Guildmate', // in map, when select a guild member's village
	'guildoperations' => "Guild operations:",  // in guild page
	'guildtopics'=>'Guild topics: ', // in guild view window
	'help'=>'Help', // in top left bar.
	'hereyoucanswitchplayer'=>'Here you login to anothers players. Logging out will throw you back to your account.', // in admin mode, in game
	'hero'=>'Hero', // in the weekly oracle
	'heroattackskill'=>'Offense level: <span title="{3}">{1}</span> ({2} XP to the next level)', // in the hero view window {1}: current level {2}: xp to go, {3}: current xp
	'heroavatar' => "Your hero's avatar: ", 		// in the registration form
	'herocreated'=>'Hero created.', // in hero created window
	'herodefendskill'=>'Defense level: <span title="{3}">{1}</span> ({2} XP to the next level)', // in the hero view window {1}: current level {2}: xp to go, {3}: current xp
	'heroesinyourvillages'=>'Heroes in your villages: ', //in hero window
	'heroinfo'=>'You can rename your hero and change its avatar in your profile.', // in hero created window
	'heroisnowyours'=>'This hero now obeys you.', // when you successfully adopt a hero.
	'heromove'=>'Move hero', // in event bar
	'heroname' => "Name of the starting hero: ", 		// in the registration form
	'heronametooshort' => "Name of the startio name is too short. It must contain at least ".$config['minHeroNameLength']." characters. ", 		// in the registration form
	'heromovetask'=>"Moving hero from  {1} to {2}.", // in task window {1}: from village name, {2}: to village name.
	'heronotexist'=>'Hero does not exist.', // error when a hero does not exist
	'heropagetitle'=>'{1} (Level {2})', // title of the hero's page. {1}: hero name, {2}: hero level
	'heropicture'=>'(Hero\'s image)', // alt text for the hero image.
	'herotop10'=>'Hero level top 10', // top 10 heroes in the oracle
	'hidden'=>'hidden', // reports page
	'hidehiddenreports'=>'Hide hidden reports.',// in reports window
	'hire'=>'Hire', // in edit kings window
	'hirehero'=>'Hire hero', // in no hero window
	'idontneedtutorial'=>'[Thanks, I don\'t need tutorial]', // in tutorial window
	'ifyoudontwanttochangepassword'=>'If you don\'t want to change your password, leave the fields blank.', //
	'ifyouwanttochangetheemail'=>'If you want to change your e-mail, type the new e-mail address to the field below. Both mail address will get a confirmation mail to confirm the change.', //
	'ifyouwanttodelete'=>'If you want to delete your kingdom, click here: <a href="dodeleteme.php">Delete</a>. This will log you out. If nobody logins to the kingdom in 2 weeks, the kingdom will be deleted. The villages will be abandoned.', // in edit profile page
	'imnotregistered' => "I haven't been registered yet, I will register: ",	// in the login form
	'imregistered' => "I'm registered, I login:",	// in the login form
	'inbuiltlevelspie'=>'Distribution of the building levels', //in oracle window
	'incoming'=>'Incoming', // in event bar
	'incomingattack'=>'Incoming attack', // in event bar
	'incomingmove'=>'We got troops', // in report view
	'infantryattack'=>'Pierce damage', // in unit description tables
	'infantrydefense'=>'Defense against pierce', // in unit description tables
	'invalidday' => "Invalid day.",	// in the registration form
	'invalidemail' => "Invalid e-mail address format",	// in the registration form
	'invalidlaunchervillage'=>'Invalid launcher village (it\'s not yours or does not exist)', // response from the task manager php.
	'invalidpictureformat' => "Unsupported image format. Only jpg, png and bmp are supported.",	// in the registration form
	'invalidyear' => "You haven't been born yet? Type a valid year.",	// in the registration form error when birth year is bigger than the current year.
	'invitationrefused'=>'Guild invitation rejected', // when you reject a guild invitation
	'invitationsent'=>'Guild invitation sent',// success window message, after you sent the 
	'inviteplayer' => "Invite kingdom",  // guild right name, guild page
	'inviteplayerinfo'=>'It is suggested to contact with the kingdom before you send invitation.', // on invite player page
	'inviteplayerandgetep'=>'Invite players to the game and get expansion points!', // on the invite player page.
	'inviteplayertogame' => "Invite player to the game",  // guild right name, guild page
	'inviteplayerdescription'=><<< X
		<p>You can speed your expansion by inviting player to the game. If the player have 8 villages, you may get expansion points. But you cannot have more villages than that player who has the most villages.</p>
		<p>It depends on how does the two players are related.</p>
		<ul>
			<li>If they use the same internet connection no EP given.</li>
			<li>I more players using the same connections regularly but they don't use the internet connection that the inviter use you will get 1 EP.</li>
			<li>If the previous 2 does not true and anyone logged in from the inviters internet connection you will get 2 EP.</li>
			<li>
				If the prevous 3 is not true, so no one ever played from the inviters internet connection. You get 5 EP.
			</li>
		</ul>
		<p>So this will stimulate you to advertise my game. :)</p>
X
	, //  on the invite player page.
	'invitebutton'=>'Invite', // in invite player window
	'invitedplayername'=>'Player name to invite', //  in invite player window
	'january' => "January",  // in the date picker control
	'javascriptisnotenabled'=>"You may disabled <dfn title=\"With this it is possible to do animations and interactive stuff on webpages.\">Javascript</dfn>in youur browser javascript. Enable javascript to make the game work. If you still have problems write an e-mail to the admin at ${config['adminMail']}.", // it sucks when your browser not supports javascript.
	'july' => "July",  // in the date picker control
	'jump'=>'Jump', // in left top bar
	'june' => "June",  // in the date picker control
	'kick'=>'Kick', // in edit kings window
	'kickplayer' => "Kick player from the guild",  // guild right name, guild page
	'kingdomname' => "Kingdom's name:", // on registration form
	'kingdomprofile' => "Profile of the kingdom", // in the left menu
	'kingdomsavatar'=>'Crest of the kingdom: ', // on registration form
	'kingdomsdata'=>'Kingdom information', // on registration form
	'knights' => "knights",  // in game, village properties
	'knight' => "knight",  // in game, village properties
	'knightlevel' => "level of the knights",  // in game, village properties
	'kings'=>'Kings: ', // on kingdom profile
	'kingsdata'=>'King information ', // on registration form
	'lastmessageposted'=>'Last post date', // in messages window
	'lastposter'=>'Last poster', // in messages window
	'lastupdate' => "Last update",  // in game village view and village summary
	'lastupdatesecondstext'=>'{1} seconds ago', // in village summary
	'launchtroops'=>'Launch troops', // in the action window
	'launchervillages'=>'Starting villages: ', // in the launch action panel
	'launchheroifinvillage'=>'Send hero if he is in one of the selected villages.', // in send troops window
	'leavekingdom'=>'Leave kingdom', // in edit kings window
	'leaveguild'=>'Leave guild', // in the guild window, the title of the leave guild window, and the leave guild button
	'lettersent'=>'Message sent', // the message after you sent the letter
	'level'=>'Level', // in the weekly oracle, and building cost table
	'login' => "Login",  // in the login form
	'logout' => "Logout", // in the top bar
	'loottext'=>'Amount of gold looted: {1}', //in battle report. {1}: the looted gold
	'lostvillagebyconquer'=>'They conquered our village ', // in reports window
	'lostvillagebydestruct'=>'They destroyed our village!', // in reports window
	'makehidden'=>'Hide', // when viewing one report.
	'makepublic'=>'Publish',// when viewing one report.
	'mandatorydata' => "This information is required:",  // in the registration form
	'march' => "March",  // in the date picker control
	'male' => "male",  // in the gender picker control
	'managediplomacy'=>'Manage diplomacy', // guild right name, guild page
	'managekings'=>'Manage kings', // in edit kings window
	'mapstuff'=>'Map operations', //in left bar
	'massbuilding'=>'Massive building', // in left menu bar, and the title of the massive building window
	'massbuildinginfo'=>'When doing massive building, it will upgrade the lowest level building and it repeats this till all buildings reach the desired level or the specified amount of gold spent or it ran out of build points.', // in mass building window
	'massbuildingtask'=>'Upgrading of building {4} in villages having the following ids: [{1}], Level limit: {2}, Gold limit: {3}, ', // task name,{1} village id list, {2} level limit number, {3} gold limit number, {4} building name. All are javascript insertions.
	'masstraining'=>'Massive training', // in mass training window
	'masstrainingtask'=>'Massive training  [{1}] amounts at villages having the following ids: [{2}].', // in mass training window. {1}: unit's going to be trained, {2}: identifiers of the trainer villages.
	'may' => "May",  // in the date picker control
	'messages' => "Messages",  // in the top bar, and it's also the title of the messages window
	'miscvillageinfotext'=>'Score: {1} | ({2};{3}) | #{4}', // in village info window. {1}: score of the village, {2}: x coordinate, {3}: y coordinate, {4}: identifier. (all parameters are passed as javascript includes)
	'modifybutton'=>'Modify', // modify button
	'moderateforum' => "Manage forums",  // guild right name, guild page
	'mousemode'=>'Mouse modes: ', // in game
	'moveevent'=><<< X
		<table class="center" style="background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Starting village: {2}</td><td>Target village: {3}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">Leader: {8}</td></tr>
			<tr><td colspan="2">{10}</td></tr>
		</table>
X
	, // the description of the move event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link, {11} the color code of the event
	'movehero'=>'Move hero', // in tasks
	'movetitle'=>'{1}({2}) transfered troops to {3}({4}).', // in battle report's title {1}: attacker village's name (as link to village), {2}: owner (as link to the profile), {3} (as link to village): defender village's name, {4}: defender name (as link to profile)
	'movetroops'=>'Transfer units', // in action window
	'moreinfo'=>'More info', // in the send troops window.
	'myprofile' => 'My profile', // in game player menu
	'na'=>'N/A', // The N/A symbol in you language
	'name'=>'Name', // The word: 'name' (in game, village view, unit and building name, in village summary, edit kings window)
	'neutral'=>'neutral', // diplomatic stance
	'new'=>'New', // the word 'new' (in reports, messages, etc view)
	'newdiplomaticrelationship'=>'Add new diplomatic stance. The stance can only viewed on your own guild page, it does not mean that the specified guild will accept it. Contact the guild before building diplomatic relationship. If you set the diplomatic stance, the specified guild\'s diplomat will get a report.', // in diplomacy management window
	'newreplywhileyouwrote'=>'New replies arrived while you wrote this letter. You can read them before you send you letter.', // in compose message window
	'newvillage' => "New village",  // the default name of the new villages
	'next' => "Next",  // in game, village properties, next to build point counter
	'nextreport'=>'Next', // in report view
	'newguildname'=>'Guild name:', // in guild window, when not member of a guild.
	'newhero'=>'new hero', // the name of a new hero
	'nightbonustext'=>'{1}×', // night bonus in the right top window.
	'nightbonus'=>'Night bonus: ', // the night bonus text in the battlesimulator
	'noaccountassociated'=>'The access is not assigned to any kingdom.', // when no account associated with the access.
	'noaccountassociatedinfo'=>'You don\'t control a kingdom at this moment. You must ask someone to add you to a kingdom\'s controllers.', // when no account associated with the access.
	'noevents'=>'(no troop movements)', // in the event bar
	'nofreeheroesgarrisoninginyourvillages'=>'(No free hero garrinsoning in your villages)', // in no hero window
	'noinvitations'=>'(no invitations)', // in clan window, when not member of a guild, this text saying 'no invitations yet'.
	'nolevellimit'=>'No limit', // in mass building task
	'nosubject'=>'(no subject)', // the placeholder subject for letters without subject
	'notclanmember' => "You are not guild member",  // clan window, when not member of a guild (title)
	'notclanmemberindetail' => "You are not a member of a guild. If players are organized into guilds, they can be stronger than alone. Depening on your play style, you may decide to found and lead guild or wait for another guild to invite you.",  // clan window, when not member of a guild, (detailed info)
	'notenoughbuildboints'=>'Not enough build points.', // response from task manager php.
	'notenoughexpansionpoints'=>'Not enough expansion points.', // response from task manager php.
	'notenoughgold' => "Not enough gold.",  // in game, village properties, next to build point counter
	'notenoughsettlerunits'=>'Not enough settler units.', // response from the task handler php.
	'notenougthtroops'=>'Not enough troops.', // response from the task handler php.
	'notes'=>'Notes', // in the notes window
	'notessaved'=>'Notes saved', // in the notes window
	'notloaded'=>'(Not loaded)', // in the village summary window
	'notsupportedbrowser' => htmlspecialchars("You browser does not support the <canvas> element. Refresj ypi browser or download a newer version. The following or newer browsers support the canvas element: Firefox 1.5 (expiremental), Google Chrome , Safari from 2.0l, Internet Explorer 9 (or lower version with the ExCanvas plugin), Opera supports it. Download a better browser! :)"),  // error message when the user don't have <canvas> capable browser.
	'notstartedyet' => "The game haven't been started yet. The countdown is ticking, wait till it elapses then try to login again.",  // when the game not started, the countdown page
	'november' => "November",  // in the date picker control
	'nowyoumemberoftheguild'=>'You are a member if the {1} guild from now!',// when you enter the guild. {1} the guild name you entered.
	'october' => "October",  // in the date picker control
	'offensetop10'=>'Offensive Top 10', // in the weekly oracle
	'oldpassword'=>'Old password for security reasons', //
	'onevillagemustbeselected'=>'You need to select at least one village to train units.', // in mass training window
	'operation'=>'Operation: ',// in action window
	'optionaldata' => "You can provide this information if you want: ",  // in the registration form
	'or'=>'or', // the word 'or'. (in diplomacy management window)
	'oryoucanwaitaherotoappearinyouvillage'=>'Or you can wait till a hero arrives to your villages.', // in the no hero page
	'others'=>'<Others>', //in oracle
	'outgoing'=>'Outgoing', // in event bar
	'outgoingmove'=>'We transfered units', // in report view
	'outsideguild'=>'<Outside guild>', //in oracle
	'own'=>'own', // on map, the word placed when you select your own village.
	'ownername' => "name of the owner",  // in game, village properties
	'participants'=>'Participants: ', // in thread view
	'password' => "Password: ",  // in the login and registration form
	'passwordnotmatch' => "The two password does not match. You probably mistyped it. Type it again.",  // in the login and registration form error message
	'passwordtooshort' => "Password is too short. It must contains at least ".$config['minUserPasswordLength']." characters.",  // in the login and registration form error message
	'passwordagain' => "Password again: ",  // in the login and registration form
	'pc' => "units",  // in game, means "pieces" 
	'peace'=>'peace', // diplomatic stance
	'player'=>'Kingdom', // in world events and in various table headers
	'playerfinishedyourdelegation'=>'{1} finished to account sitting and loggedin his account', // When player logs in again after sitting.
	'playerhasbeenkicked'=>'<a href="viewplayer.php?id={1}">{2}</a> is kicked from this guild', // in guild page when you kick someine.
	'playerisnotreferredbyyou'=>'You didn\'t invited this player or he didn\'t registered from your reflink.', // in the invite player window.
	'playerlist'=>'List of kingdoms: ', // in grant permissions window
	'playernotreachedthevillagecount'=>'The invited player does not reached the {1} villages or he does not logged in.', //In check invited player window.
	'playerreachedthelevel'=>'Congratulations, the invited player reached the desired level.',//In check invited player window
	'playerrequestyoutodelegatehim'=>'{1} asked you as his deputy. Using the account sitting menu in the left bar you can login to him.', // When sy request you to delegate him.
	'playnow'=>'Play now! Participate in the battles! Register now!', //advertisement text shown in public reports.
	'pleasechoosecatapulttarget'=>'Choose target for the catapults if you attack with catapults: ', // in launch troops window.
	'pleaseenterthechoosendeputyname'=>'Type the name of the player you chose as deputy: ', //
	'pleaselogin' => "You are not logged in. Login  or register..", // in login form
	'pleasesendmoretroops'=>'You need to send at least an army worth of {1} gold.', // in the launch action window. {1}: the minimal value (as js variable but may be used on the php side too.)
	'plotwidth'=>"Plot width: ", // in the generate activity plot window	
	'possiblecommands'=>'Possible commands: ', // in action window
	'preview'=>'Preview', //
	'previousreport'=>'Previous', // in report view
	'profile' => "Profile", // in profile edit window
	'profilenotexists' => "Profile does not exist", // error message when requesting non-existing profile.
	'public'=>'public', // reports page
	'publishthefollowinglink'=>'<p>Share the following link with others:</p><p><tt>{1}</tt></p><p>Who clicks on it and registers will appear among your invitees.</p>', // in invite player window. {1} the reflink
	'raid'=>'Raid', // in action window
	'receivedat'=>'Arrival time: ', // in reports window
	'receivervillage'=>'Receiving village: {1} ({2})', // in move report the receiver village text. {1}: village name (as link), {2}: owner name (as link to profile)
	'recentevents'=>'Recent events', //in left menu.
	'recipient'=>'Recipient: ', // in send message window
	'recipientplayer'=>'Recipient player ', // in world eventswindow
	'recipientisnotexist'=>'No player exists with that username you given as recipient. ', // in send message window
	'recon'=>'Attempt to scout and return.', // in action window
	'recruiter'=>'recruiter', // in guild page
	'refresh'=>'Refresh', // in the village summary page
	'refuseinvitation' => "Refuse invitation",  // guild window when not member of a guild.
	'reginfo' => <<< X
	<ul>
		<li>After registration you can not change your name and your kingdom's name.</li>
		<li>The images you uploaded will be scaled to ${config['avatarSize']}×${config['avatarSize']}. Only jpg, png and bmp file types are accepted.</li>
		<li>You will get an activation code on the mail address you provide. You will need it  activate your user account.</li>
		<li>You may only change your e-mail address if you have access to both old and the new e-mail address.</li>
	</ul>
X
	,
	//in the registration form
	'registerkingdom'=>'Register kingdom (Check it if you want your own empire.)', //
	'registration' => "REGISTRATION - it's free", // in login form
	'registrationbutton' => "Register", // in the registration form
	'registrationtitle' => "Registration", // the registration form's title
	'regmailcontent' => <<< X
	Hello {1}!

	You received this letter, because you registered to the ${language['wtfbattles']} game.
	Login to the game and copy the activation code when you are asked to:

	{2}

	${config['adminName']}, the game's administrator, wishes you a good game!

	(If you didn't registered to game, you can ignore this letter. Nothing bad will happen.)
X
	,
	 // the registration mail's body. The {1} replaced with the players name, the {2} will be the activation code.
	'regmailsubject' => "Registration at ${language['wtfbattles']}", // the registration mail's subject
	'rename' => "rename", // in game, in general
	'renamevillagetask' => "Renaming {1}  (#{2}) to {3}", // in game, rename village task name. 1: village's old name, 2: village's id, 3: village's new name
	'renamingvillage' => "Rename village", // in game, the task name
	'reports' => 'Reports', // in game, player menu
	'return'=>'Return', // event name
	'returnevent'=><<< X
		<table class="center" style="text-align:center; background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Starting village: {2}</td><td>Target village: {3}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">Leader: {8}</td></tr>
			<tr><td colspan="2">With {6} gold</td></tr>
		</table>
X
	, // the description of the move event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link
	'revagebonustext'=>"Reverse age bonus: {1}×", // In the profile
	'revoke'=>'Revoke', // in the invite player window
	'revokeinvitationtext'=>'The {1} guild\'s recruiter revoked the invition to their guild.', // in the invitation revocation report. {1}: is the link to the guild
	'revokeinvitationtitle'=>'Invitation revoked', // in the invitation revocation report title.
	'rightlist'=>'List of rights: ', // in grant guild right window
	'rulefile'=>'languages/en/enrules.php', // A html file containing the rules
	'savebackup'=>'Download backup', // in admin panel
	'saveedits'=>'Save changes', // in guild profile edit (the button title)
	'score'=>'score', // in village summary window
	'scoretop10'=>'Score top 10', // oraculum
	'selectmode'=>'Selection rectangle (CTRL)', // in game
	'selectplayertokick'=>'Select the player to kick. He will be fired immediately.', // in kick player window
	'sendcircular'=>'Send circular', // guild right name
	'sendhimmessage'=>'Send him message', // in the profile view
	'sendletterbutton'=>'Send letter', // in send message window
	'sendmassreport'=>'Send system message', // in admin mode.
	'sendreply'=>'Send reply', // in thread view window
	'sendtroops'=>'Send troops', // in action window
	'sendtroopscommand'=>'Launch {2} troops from {1} villages to {3} village. The action is: {4}',  // in task window. {1}: the id list of the launcher villages, {2}: the percentage of amounts, {3}: destination village, {4}: action type (inserted by javascript)
	'september' => "September",  // in the date picker control
	'serverisclosed'=>'Server is closed. No further action can be started.', // when server ends
	'sessionisover' => 'Session is over. Please log in again.',  // in game when the users session is over
	'set'=>'Set', // in general, when setting something.
	'setmaster'=>'Set as master', // in edit kings window
	'setdiplomaticstance'=>'Set diplomatic stance', // in diplomacy settings window
	'setsparebptask'=>'Set spare build points at {1} to {2}.', // set spare bp task text.  {1} village name {2} spare build points to set. Both are js insertions.
	'settleevent'=><<< X
		<table class="center" style="background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Starting village: {2}</td><td>Target cell: {5}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">{10}</td></tr>
		</table>
X
	, // the description of the settle event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {11} the color code of the event
	'settlevillage'=>'Settle village', // in action window
	'settlevillagenow' => htmlspecialchars(">>> Settle village now <<<"),  // in the date picker control
	'settlevillagetask'=>'Start settling units from {1} to ({2};{3}).', // in task window. {1} launcher village name, {2}: destination's x coordinate, {3}: destination's y coordinate
	'shareit'=>'Share it: ', // on various pages
	'shortdescription'=>'This is a brand new village builder, war game. Build, battle, defend, conquer, cooperate, war...', // The short description of the game
	'showcost'=>'Show costs', // in village info
	'showhiddenreports'=>'Show hidden reports',// in reports window
	'sitteractivity'=>'Account sitter\'s activity', // in the activity plot
	'someonekickedyoufromthesomeguild'=>'Unfortunately <a href="viewplayer.php?id={1}">{2}</a> kicked you from the <a href="viewguild.php?id={3}">{4}</a> guild!', // report text when you got fired from the alliance.
	'someonesettledthere'=>'Somebody already settled on the cell you chose. Refresh the browser window to see whether there is a village on that cell.', // response from the task manager php.
	'sorrybuterrorhappened' => "Unfortunately an error happened.",  // in the error page
	'sparebp'=>'Spare BP: ', // in village info
	'sparebptooltip'=>'The massive building operation will keep this many build points on this village. (Click on the number to set it.)', // in village info
	'speed'=>'speed', //in unit description tables
	'speedtext'=>'{1} cell/h', //in unit description tables {1}: unit speed.
	'spearmanlevel' => "Level of spearmen",  // in game, village properties
	'spearman' => "spearman",  // in game, village properties
	'spearmen' => "spearmen",  // in game, village properties
	'specifyvalidnumbers'=>'Please type valid numbers.', // in mass training window
	'spendallbuildpoints'=>'Spend all build points', //in mass building window
	'spendmaxgold'=>'Maximum amount of gold to spend:', // in mass building window
	'spokenlanguages'=>'Spoken languages: ', // in the registration form
	'stables' => "Stables",  // in game, village properties
	'startnewtopic'=>'Start new topic', // in manage forums window
	'strength'=>'<dfn title="How many gold an unit can carry">Strength</dfn>', // in unit description tables
	'strengthtext'=>'{1} gold', // in unit description tables
	'subject'=>'Subject: ', // in compose message window
	'subscribetopic'=>'Subscribe to this topic', // in guild window
	'successfulguildenter'=>'Joined to the guild successfully', // when you join a guild
	'successfulregistration' => "Successful registration!", // in the successful registration page.
	'successfulregistrationdescription' => "Welcome {1}, you have succesfully registered for ${language['wtfbattles']}. To activate your account we sent an activation code to your e-mail ({2}). You have one day to activate the account. If you don't activate the account will be deleted.  <i>If you have problems, you may write a mail to the admins at ${config['adminMail']}. </i>",  // in the successful registration page. The {1} replaced with the player's name, the {2} will be the e-mail address.
	'targetcell'=>'Target cell: ', // in action window
	'targetvillage'=>'Target village: ', // in action window
	'tasklist' => 'Task list', // task list
	'thehighest'=>'The biggest...', //
	'thekingmustnotcontrolakingdom'=>'The selected king can not control another kingdom.', // in edit kings window
	'theownerofthisherois'=>'This hero obeys {1}.', // in hero view window. {1}: the owner of the hero.
	'thisherodonthaveowner'=>'This hero obeys no one.', // in hero window for free heroes.
	'thisisaguildletter'=>'This is a guild letter. Only guild members can view it.', // in thread view window
	'thiskingisalreadycontrollingakingdom'=>'This king already controls a kingdom.', // in edit kings window
	'thesearetheguildthreads'=>'The following topics are guild topics:', // in the manage forums window
	'threadnotexist'=>'The thread does not exist, you don\'t subscribed to or you don\'t have permission to view it.', // in compose message window.
	'thismessagewillbeacircular'=>'This letter will be a circular for your guild members. Automatically every guild member will subscribe to it. BUT kicking someone won\'t unsubscribe him from the thread!', // in send message window
	'thismessagewillbeaguildthread'=>'This letter will be a topic in the guild forum. Only guild members can view it. It will appear on the guild page, any guild member can subscribe to it..', // in send message window
	'thisplayerdonthavehero'=>'(This kingdom doesn\'t have a hero)', // in player window
	'title'=>'Title', // in general
	'toomanyvillagescantspawn' => "Too many villages are where where your village could appear, try again later.", // when settling the first village and was unable to settle the first village
	'topic'=>'Topic', // in messages window
	'totalcost'=>'Total cost : {1} gold', // in the mass training window. {1} is the gold needed as js insertion.
	'townhall' => "Town center", // in game, village properties
	'townhalltop10'=>'Average top center level top 10', // oraculum
	'train'=>'Train', // in village view
	'trainedat'=>'<dfn title="You need to upgrade this building in order to increase the speed of the training.">Place of training</dfn>', // in unit description tables
	'trainingtext' => "Training: {1} (Next: {2}).", // in game, village properties, {1}: units enqueued for recruit (as js war), {2}: next unit training status.
	'trainingtask' => "Unit training in village {1} {2} units of {3}", // in game, the text of the training task, 1: village name, 2: amount of units, 3: the name of the unit type
	'trainingtime'=>'<dfn title="Training time for a 0th level building.">Training time</dfn>', // in unit description tables
	'troopnumberdescription'=>'Here you can see the entire army that is in the selected villages, when you logged in. If you refresh all selected villages, this number can be even more. Army start from every village, the amount is determined by the percentage value at right.', //
	'tutorial'=>'Tutorial', // in the left top bar
	'tutorialfile'=>'languages/en/entutorial.php', // the filename of the tutorial file.
	'typeguildname'=>'Type the name of the guild: ', // in diplomacy management window
	'typeguildsid'=>'Type the guild\'s identifier (The number after the # on the guild\'s page): ', // in diplomacy management window
	'unabletosendmail' => "I was unable to send out the mail. Please write an e-mail to the admin at ${config['adminMail']} for manual activation.",  // in the registration when mail() function was unable to send the mail.
	'unitinfotooltip'=>'{1} (cost: <b>{5}</b> gold),<br><b>{2}</b> units available,<br><b>{3}</b> units training,<br>The training progress of next unit is at <b>{4}</b>%.<br><i>Type the number, the amount of units you want to train, in the textbox below<br> then press enter.</i>', // in game village view, the tooltip when you hover the unit icon. {1}: unit name (as string), {2}: available units (as js var), {3}: under training (as js var), {4}: the precentage of the progress of the training of the next unit (as js var), {5} its cost
	'unitnotexist' => "The unit type with the code {1} does not exist.", 		// in game, error when trying to refer a not unit type ({1} is the code of the unit)
	'unknown' => 'Unknown',
	'unknownreport'=>'Report of unknown type', // in reports window the image title
	'unsubscribe'=>'Unsubscribe', // in messages window
	'updatenow' => "Update now", // in game, village properties and player info
	'updatebuildingtask' => "Upgrading {2} in village {1}.", // in game, the building upgrade task text. 1: village name (through JS), 2: building name (through JS)
	'upgrade'=>'Upgrade', // in game, village properties
	'usepasswordtoleave'=>'Type your password to confirm leaving.', // in leave guild window
	'usepasswordtodismiss'=>'Type your password to confirm dismiss.', // in leave guild window
	'userisinactive' => "The account is inactive",  // after login in the activation form
	'userisinactivepleaseactivate' => "The account is inactive, copy the activation code you got in e-mail.",  // after login in the activation form
	'username' => "Username: ",  // in the login and registration form
	'usernamealreadyregistered' => "This name is already registered by someone else.",  // in the login and registration form
	'usernamenotexist' => "User account does not exist.",  // error in the login form (this appears on the error page too during activation if the user not exist)
	'usernamelong' => "Username is too long, it must contain ${config['maxUserNameLength']} or less characters.",  // in the login and registration form
	'usernameshort' => "Username is too short, it must constain ${config['minUserNameLength']} or more characters.",  // in the login and registration form
	'viewhero'=>'View hero', // in profile
	'villageanduser'=>'{1} ({2})', // in action window {1}:village name, {2} username
	'villagecountscoretext'=>'The kingdom has {1} villages, and {2} score', // in view profile page, {1}: village count, {2}: total score
	'villagedestroyed'=>'Village have been destroyed.', // in battle report
	'villageisabandoned'=>'Village have been abandoned.', // when you successfully abandon a village.
	'villageisnotyours' => "The villages is not yours you want to operate on.", // in dotasks.php report when you want to do things on someone else's village
	'villagename' => "Village name", // in game, village properties
	'villagenotexist'=>'Village does not exist.', // response from task manager php
	'villages' => 'Villages: ', // On the profile view before the village list.
	'villagescoretext'=>'{1} score village', // in village view the score text. {1} the score (inserted as javascript variable)
	'villagesloaded'=>'Loaded villages (<i>only those</i>): ', // in mass training window and building window
	'villagesselectedmap'=>'Villages you selected on the map are selected on this list too.', // in mass training window
	'villagesummary'=>'Village summary', // in the village summary window
	'villagetext'=>'{1} ({2};{3})', // Generic village text. {1}: village name, {2}: X coordinate, {3}: Y coordinate
	'villagetooltip'=>str_replace("\n",'',
	'
		<table>
			<tr><td colspan="2">{1}</td></tr>
			<tr><td>Location: </td><td>({5};{6})</td></tr>
			<tr><td>Owner: </td><td>{2}</td></tr>
			<tr><td>Guild: </td><td>{3}</td></tr>
			<tr><td>Score: </td><td>{4}</td></tr>
		</table>
	'), // In the map tooltip. {1}: village name, {2}: owner, {3}: alliance, {4}: score of the village, {5},{6}: x,y coordinate. All of them are javascript insertions.
	'wall' => "City wall",  // in game, village properties
	'war'=>'enemy', // diplomatic stance (better word: enemy)
	'weeklyoracle'=>'Weekly oracle', // in left menu
	'wentbattle'=>'Went battle: ', // in battle simulator
	'workshop' => "Workshop",  // in game, village properties
	'worldevents'=>'World\'s events', // in game left top menu.
	'worldeventsettle'=>'Founding villages', // translation of the world event
	'worldeventdestroy'=>'Village destructions', // translation of the world event
	'worldeventconquer'=>'Village takeovers', // translation of the world event
	'worldeventguildchange'=>'Guild changes', // translation of the world event
	'worldeventrename'=>'Village renames', // translation of the world event
	'worldeventeventhappened'=>'Misc events', // translation of the world event
	'worldeventdiplomacychanged'=>'Change of diplomatic stance', // translation of the world event
	'worldeventscorechanged'=>'Change of village\'s score', // translation of the world event
	'wouldyouliketocanceldeletion'=>'If you want to cancel the deletion process, click here.', // in account deletion info box
	'wouldyouliketofinishsitting'=>'If you login, the account sitting will be finished and your deputy can not log in anymore. Click on this text to finish account sitting an log in your account.', // when you log in while you are deputized
	'wrongactivationcode' => "Wrong activation code, try again.",  // in the activate account form
	'wtfbattles' => "StraTaDi",  // the game's name
	'wtfbattleslong' => "Strategy, Tactics and Diplomacy",  // the game's name
	'xcoord'=>'X coordinate:', // name of the X coordinate
	'xnewmails'=>'New mails: {0}',
	'xnewreports'=>'New reports: {0}',
	'ycoord'=>'Y coordinate:', // name of the Y coordinate
	'youalreadyhaveahero'=>'You already have a hero.', //error message when you try to create a hero when you have one.
	'youarebanned' => "Unfortunately you are banned. Please write to the admin at ${config['adminMail']} to know what's up.", 		// fatal error in the login form
	'youareinvitedtoguild'=>'You got a guild invitation', // guild invitation report title
	'youcancreateanewhero'=>'You can create new hero if you select the village where you want to create it and click on the `Create new hero` link there.', // in no hero window
	'youcandelegatedeputy'=>'You can delegate a deputy to sit your account. As soon you do this you will be logged out. Account sitting lasts till you log in again.', // in the deputies window
	'youcanreenterthetutorial'=>'The tutorial window won\'t pop up anymore from now. You can still access the tutorial from the Help menu.', // when finishing the tutorial.
	'youcantattackasdeputy'=>'You may not attack as deputy.', // Error message when you logged in as deputy
	'youcantgiveawayasdeputy'=>'You may not give away the troops as deputy.', // Error message when you logged in as deputy
	'youcantsendtroopstothisuser'=>'You may not send troops to this player because rate of the time spent in game is too large. ({1}× or bigger difference)', // error message when you attempt to send troops
	'youchoosetoabandonthisvillage'=>'You have chosen to abandon this village. If you abandon it, the village will be owned by no one. It will produce gold for no one and who attacks it will take away 0 gold. The units remaining in the village will fight and defend the village. The unit training in the village will be finished and the trained units will remain in the village. You will get back your expansion points.', // in abandon village window
	'youdeputize'=>'You sit the following kingdoms: ', // in the deputies window
	'youdismissedtheguild'=>'You dismissed your guild.', // the success dialog when you dismissed your own guild.
	'youdonthavehero'=>'You don\'t have a hero.', // in no hero dialog
	'youdonthavevillage' => "You don't own a village.", 		// warning message in the game view
	'youfinishedthetutorial'=>'You finished the tutorial', // when finishing the tutorial
	'yougotexpansionpoints'=>'You got {1} expansion points. ', //in the check referredplayerwindow
	'youhavebeendeputized'=>'{1} sitting your kingdom.', // when you log in while you are deputized. ({1} The name of the deputy)
	'youhavebeenkicked'=>'You have been fired from the guild.' ,// farewell report title when you got kicked from the guild...
	'youlefttheguild'=>'You left the guild.', // when you leave a guild.
	'youmustgiveawaymasteraccessfirst'=>'You need to give away the <i>account master</i> rank to leave the kingdom.', // in edit kings window
	'youraccountunderdeletion'=>'Your kingdom will be deleted at {1} if you don\'t login.', // in account deletion box
	'youractivity'=>'Your activity', // in the activity plot
	'youravatar' => "Kingdom's crest: ",		// in the registration form
	'yourbrowsernotsupportiframe'=>'Ooops! Your browser does not support the IFRAME tag, use this link to open the page in popup window: <a href="javascript:void(window.open(\"{1}\"))">{1}</a>.', //
	'yourheroinvillage'=>'The hero is in {1}', // in hero window. {1}: village as link
	'yourheroisinthisvillage'=>'Your hero is in this village.', // in village info window
	'yourreferreds'=>'Your invitees: ' // in inviteplayer window
);

$_languageIncluded;
if (!isset($_languageIncluded))
{
	$_languageIncluded=true;
	include("en.php"); // include self once to resolve forward refences in the lang file
}


?>
