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
		header('Location: languages/hu/huhelp.php');
	}

}

@$language=array
(
	'abandonvillage'=>'Falu elhagyása', 	// in village view
	'acceptinvitation' => "Belépés a klánba",  // guild window when not member of a guild.
	'accessdenied'=>'Nincs jogosultságod a művelet végrehajtására', // in genernal, when access is denied
	'accountmaster'=>'Főkirály', // in edit kings window
	'actionmode'=>'Akciómód (SHIFT)', // in game
	'action'=>'Művelet', // in invite player window, edit kings window
	'actions'=>'Egérműveletek', //in game, top left bar.
	'activate' => "Aktiválás",  // after login in the inactive user form
	'activationcode' => "Aktivációs kód",  // after login in the inactive form
	'activityinyouraccount'=>'Aktivitás megtekintése az accountodban', // in the left menu
	'addparticipant'=>"Résztvevő hozzáadása: ", // in the compose message window.
	'addnewking'=>'Új király hozzáadása: ', //in the edit kings window
	'adminactivity'=>'Admin aktivitása', // in the activity plot
	'adminlogin'=>'Admin belépés', // in admin mode, in game
	'adminmessage'=>'Admin', //
	'adminstuff'=>'Deus ex machina', // in admin mode
	'age' => "Kor",  // In the profile
	'agebonus'=>"Korbónusz: ", // In battle simulator
	'agebonustext'=>"Korbónusz: {1}×", // In the profile
	'agebonusjstext'=>"Korbónusz: {0}×", // When attacking a village. // TODO (refactoring) make string formatters {0} based.
	'ally'=>'szövetséges', // diplomatic stance
	'amountform' => '{1}db {2}', // general from of amounts, {1} means the amount, {2} is the subject. Eg.: 1 pieces of furniture would be "{1} pieces of {2}", for in game example: 2 spearmen
	'april' => "április",  // in the date picker control
	'archerlevel' => "íjászok szintje",  // in game, village properties
	'archer' => "íjász",  // in game, village properties
	'archerattack'=>'Íjász támadóerő', // in unit description tables
	'archerdefense'=>'Íjász elleni védelem', // in unit description tables
	'archers' => "íjász",  // in game, village properties
	'archeryrange' => "íjászlőtér",  // in game, village properties
	'armytop10'=>'Seregméret top 10', // oraculum
	'armyvaluetext'=>'Támadósereg minimális értéke: {1}, a beállított sereg értéke: {2}', // in the window where you send troops {1}: the minimal value of the army you going to send. {2}: the value of the army you already set.
	'ascendingordering'=>'Növekvő rendezés', // in village summary
	'attack'=>'Támadás és harc utolsó emberig.', // in action window
	'attacker'=>'Támadó: ', // in battle simulator
	'attackerlosses'=>'A támadó vesztesége {1} arany', // in battle report {1} is the amount of of the gold
	'attackerheroes'=>'A támadó hősei a következők voltak {1}', // in battle report
	'attackerlostbattle'=>'A támadó fél elvesztette a csatát. Minden egysége meghalt.', // in battle report
	'attackerlostbattlehero'=>'A hős elmenekült a csatából, és nem mert hazatérni a gazdájához.', // in battle report
	'attackervillage'=>'Támadó falu: {1} ({2})', // in battle report the attacker village text. {1}: village name (as link), {2}: owner name (as link to profile)
//	'attackevent'=>'{7}. {1}: {2} faluból, {3} falu ellen, {4} egységekkel, {9} épület ellen. A sereg vezetője: {8}. {10}', // the description of the attack event {1} event type, {2} launcher village name, {3} destination village name, {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link, {11} the color code of the event
	'attackevent'=><<< X
		<table class="center" style="text-align:center; background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Indítófalu: {2}</td><td>Célfalu: {3}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">Sereg vezetője: {8}</td></tr>
			<tr><td colspan="2">{9} épület ellen</td></tr>
			<tr><td colspan="2">{10}</td></tr>
		</table>
X
	, // the description of the move event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link, {11} the color code of the event
	'attackfail'=>'Támadás, elbuktuk', // in reports window
	'attacklevelofhero'=>'Támadószintje a támadóhősnek: ', // in battlesimulator
	'attacknoloss'=>'Támadás, nem voltak veszteségek', // in reports window
	'attackpower'=>'Támadóerő', // in unit description tables
	'attacktitle'=>'{1}({2}) megtámadta {3}({4}) falut.', // in battle report's title {1}: attacker village's name (as link to village), {2}: owner (as link to the profile), {3} (as link to village): defender village's name, {4}: defender name (as link to profile)
	'attackwithloss'=>'Támadás, voltak veszteségek', // in reports window
	'august' => "augusztus",  // in the date picker control
	'available'=>'Besorozva', // in game, village view, unit table
	'avatargc'=>'Avatarszemét eltakarítása', // in admin panel
	'backtomainpage' => "Vissza a kezdő oldalra", 		// in the registration success page
	'backtoprevpage' => "Vissza az előző oldalra", 		// in general when you asked to step back.
	'backup'=>'Biztonsági mentés', // in admin panel
	'badpassword' => "Hibás a jelszó. Próbáld újra. Ha elfelejtetted a jelszavadat, írj az adminnak (${config['adminMail']}) arról az e-mail címről, amelyről regisztráltál.", 		// error in the login page
	'barracks' => "laktanya", 		// in game, village properties
	'battlesimulator'=>'Csataszimulátor', // in game, left top menu, battle simulator window's title
	'becarefulsendtroopsmultiplevillages'=>'Figyelem: ha több faluból indítasz egyszerre akciót, akkor a csapatok minden faluból külön-külön hullámban indulnak, emiatt támadáskor a hatékonyságuk nagyban csökkenhet, mintha egyetlen egy nagy seregként mennének.', // in send trooops window
	'birthdate' => "Születésnap: ", 		// in the registration form
	'blacksmith' => "kovács", 		// in the registration form
	'building'=>'Épület', // in the weekly oracle
	'buildinginfotooltip'=>'<b>{1}</b>,<br><b>{2}</b>. szintű,<br>Fejlesztés ára a köv. szintre: <b>{3}</b> arany, 1 építési pont<br><i>Kattints a gombra, ha fejleszteni szeretnél az épületen.</i>', // in game village view, the tooltip when you hover the building icon. {1}: the building name, {2}: the building level (js), {3}: it's cost (js)
	'buildingnotexist' => "Az épületfajta a {1} kóddal nem létezik", 		// in game, error when trying to upgrade a not existing building ({1} is the code of the building)
	'buildpoints' => "építési pont", 		// in game, village properties
	'buildthisbuilding'=>'Ezt az épületet építse', // in mass building window
	'buildtolevel'=>'A megadott szinting építsen: ', // in mass bulding window
	'calculate'=>'Számítás', // in battle simulator
	'cancel'=>'Mégse', // in 'cancel' operation (used in: task list )
	'cancelevent'=>'Esemény visszavonása', // in event list
	'cantkicklastking'=>'Nem rúghatod ki az utolsó királyt.', // in edit kings window
	'cantlaunchemptywave'=>'Nem indíthatsz üres egységmozgást!', // when sending troops
	'catapult' => "katapult", 		// in game, village properties
	'catapulttargetiswall'=>'A katapult a falat lövi', // in battle simulator
	'catapults' => "katapult", 		// in game, village properties
	'catapultlevel' => "katapultok szintje", 		// in game, village properties
	'cavalryattack'=>'Lovas támadóerő', // in unit description tables
	'cavalrydefense'=>'Lovas elleni védelem', // in unit description tables
	'checkreferred'=>'Ellenőrzés, hogy elérte-e a szintet', // in invite player window
	'city' => "Város: ", 		// in the registration form
	'cleartasks' => "Feladatok törlése", 		// in game, tasks
	'close' => "bezárás", 		// in game, village properties
	'closewindow' => "Ablak bezárása", 		// in game, village properties
	'committasks' => "Feladatok végrehajtása", 		// in game, tasks
	'community'=>'Közösség', // in game, top left bar
	'composemessage' => "Üzenet írása", 		// in game, tasks
	'composenewmessage' => "Új üzenet írása", 		// in compose message window
	'confirm'=>'Megerősít', // in abandon village window
	'conqueredthevillage'=>'A támadó elfoglalta a falut.', // in battle report
	'costraints'=>'Megkötések', // in the massive building window
	'coordinate'=>'({1};{2})', // the form of the coordinates
	'coordinates'=>'Koordináták', // in the village summary, world events window
	'cost'=>'Ár', // in unit description tables, and building cost view.
	'coststring'=>' {1} <img src="/img/gold_small.png" alt="arany" title="arany">', // cost string. {1} the amount of gold (as html <span></span> tags for buildings, and raw number for units)
	'createnewhero'=>'Új hős kiképzése ebben a faluban.', //
	'currentrights'=>'Jelenleg kiosztott jogok: ', //
	'databaseerror'=>'Nem lehetett kapcsolódni az adatbázishoz, a hiba oka: {1}.<br><br>Ezen a játék készítője se tud segíteni... Elkúrt valamit a rendszergazda... Nem kicsit...', // FATAL ERROR
	'daysbefore'=>'Ennyi nappal ezelőtti: ', //
	'debug'=>'Hibakeresés', // for admins
	'december' => "december",  // in the date picker control
	'defaultmode'=>'Alapértelmezett', // mouse mode, in left menu
	'delete'=>'Törlés', // in general
	'defender'=>'Védekező: ', // in battle simulator
	'defenderheroes'=>'A védekező hősei a következők voltak {1}', // in battle report
	'defenderwalllevel'=>'A védekező falának a szintje: ', // in battlesimulator
	'defendlevelofhero'=>'Védőszintje a védőhősnek: ', // in battlesimulator
	'defenderlosses'=>'A védekező fél vesztesége {1} arany', // in battle report {1} is the amount of of the gold
	'defenderlostbattle'=>'A védekező fél elvesztette a csatát. Minden egysége meghalt.', // in battle report
	'defenderlostbattlehero'=>'A védekező hősök elmenekültek a faluból, és szégyenükben nem térnek haza a gazdájukhoz.', // in battle report
	'defendertargetlevel'=>'A katapultcélpont szintje: ', // in battlesimulator
	'defendervillage'=>'Védekező falu: {1} ({2})', // in battle report the defender village text. {1}: village name (as link), {2}: owner name (as link to profile)
	'defensefail'=>'Védekezés, elvertek minket.', // in reports window.
	'defensenoloss'=>'Védekezés, nem volt veszteség.', // in reports window.
	'defensepower'=>'Védekezés', // in unit description tables
	'defensetop10'=>'Védekező Top 10', // in the weekly oracle
	'defensewithloss'=>'Védekezés, voltak veszteségek', // in reports window.
	'delegate'=>'Kinevezés', // in deputies window
	'delegationfinished'=>'Helyettesítés befejezve', // when the sponsor finishes your delegation.
	'delegationrequest'=>'Helyettesítési kérelem', // when you request someone as delegate
	'delete'=>'Törlés', // in report view
	'deletediplomacyrelation'=>'Kapcsolat törlése', // in diplomacy view
	'demolishedto'=>'(lerombolódott {1} szintre)', // in battle simulator
	'demolitiontext'=>'Rombolás: {1} {2}. szint => {3}.szint', // in battle report. {1} the building image (with the building name as title), {2} initial level, {3} demolished level
	'demoaccesscannotbechanged' => 'A demo felhasználó jelszava nem változtatható meg!', // When trying to change the password of the demo account
	'deputies'=>'Helyettesítés', // in the left menu
	'deputywillreceiveareport'=>'A helyettesnek jelölt játékos kap majd egy jelentést, hogy bejelölték. A helyettes építhet, egységet képezhet, falut alapíthat és mozgathatja az egységeidet a falvak között. De nem támadhat, és nem adhatja át az egységeid.', // in the deputies window.
	'descendingordering'=>'Csökkenő rendezés', // in village summary
	'destroyedvillage'=>'A falut leromboltuk.', // in reports window.
	'died'=>'Odaveszett: ', // in battle simulator
	'diplomacyreporttitle'=>'{1} klán diplomáciai kapcsolatot állított be a klánotokkal.', // {1} the report title when setting diplomacy.
	'diplomacyreporttext'=>'{1}, a(z) {2} klán diplomatája a következő diplomáciai kapcsolatot állította be a klánotokkal: <i>{3}</i>. Javasolt, hogy viszonozd a kapcsolatot. Részletekért lépj kapcsolatba {1} játékossal.', // The diplomacy report text. {1} the initiator name (as link), {2} the guild name (as link), {3} the diplomatic stance
	'diplomacybrokenreporttitle'=>'{1} klán a bontotta diplomáciai kapcsolatot a klánotokkal.', // {1} the report title when breaking diplomacy.
	'diplomacybrokenreporttext'=>'{1}, a(z) {2} klán diplomatája bonttotta a diplomáciai kapcsolatot a klánotokkal. Részletekért lépj kapcsolatba {1} játékossal.', // the report text of the diplomacy breaking report. {1}: the initiator's name (as link), {2}: guild name (as link)
	'diplomacyrequitedtitle'=>'{1} klán viszonozta a diplomáciai kapcsolatot.', // the diplomatic stance requited report title
	'diplomacyrequitedtext'=>'{1}, a(z) {2} klán diplomatája viszonozta diplomáciai kapcsolatot a klánotokkal.', // the diplomatic stance requited report text. {1}: initiator (as link), {2}: guild name (as link)
	'diplomacyrelationswithotherguilds'=>'Diplomáciai kapcsolatok más klánokkal:', // in diplomacy management window
	'diplomacywithguild'=>'<a href="viewguild.php?id={3}">{1}</a> klánnal {2}', // entry in the diplomacy window. {1}:guild name, {2}: diplomatic stance, {3}: guild Id
	'diplomaticstance'=>'A választott diplomáciai hozzállás: ', // in diplomacy management window
	'diplomaticstancealreadyexist'=>'Már van diplomáciai kapcsolatotok a megadott klánnal.', // error when setting diplomacy
	'diplomats' => "diplomata",  // the plural unit name
	'diplomat' => "diplomata",  // the singular unit name
	'deputies'=>'Helyettesítés', // title of the deputies window
	'dismissguild' => "Klán feloszlatása",  // guild right name, guild page
	'donatorvillage'=>'Átadó falu: {1} ({2})', // in move report the donator village text. {1}: village name (as link), {2}: owner name (as link to profile)
	'editguildprofile' => 'Klánprofil szerkesztése', // in guild page
	'editingprofile' => 'Profil szerkesztése', // my profile page, title; link name if you open your own profile
	'email' => "E-mail: ",  // in the registration form
	'emailalreadyused' => "Ezzel az e-mail címmel már regisztrált valaki más.",  // registration form error
	'enterpasswordtoconfirm'=>'Írd be a jelszavad újra a művelet megerősítéséhez:', // in abandon village window
	'enterplayernametoinvite'=>'Írd be meghívandó játékos nevét: ', // in invite player window
	'events' => 'Események',
	'eventsuccessfullycancelled' => 'Esemény sikeresen visszavonva', // when cancelling event, and referrers are disabled.
	'eventtime'=>'Esemény időpontja', // in world events
	'eventtype'=>'Esemény típusa', // in event bar
	'everyvillageyouselectedwilltrain'=>'A megadott mennyiségű egység kiképzése el lesz osztva a listában kiválasztott falvak között úgy, hogy a leghamarabb elkészüljön.', //in mass training window.
	'expansionpoints'=>'Terjeszkedési pont', // in game status indicator
	'extras'=>'Extrák ', // in the left top bar in game.
	'february' => "február",  // in the date picker control
	'female' => "nő",  // in the gender picker control
	'forum'=>'Fórum', // in top left bar.
	'foundguildbutton' => "Klán alapítása",  // in guild window, when you don't have guild. 
	'foundaguild' => "Alapíts klánt",  // in guild window, when you don't have guild. 
	'gamedescription'=><<< X
			<h3>Szerver indulása: ${config['gameStarted']}</h3>
			<div style="text-align:left; width:640px; margin-left:auto; margin-right:auto">
				<p>Változtatások a korábbi verzióhoz képest:</p>
				<ul>
					<li>Lesznek klánok, levelezés, és minden, ami a kommunikációval kapcsolatos.</li>
					<li>Sok hasonló játékkal ellentétben, itt kipróbálom, hogy mi történik, ha több falu seregét össze lehet vonni egyetlen egy faluba támadás céljából.</li>
					<li>Az összevonható seregek miatt bazi erős falat lehet építeni.</li>
					<li>Bár ezernyi falva is lehet az embernek, igyekszem minél kezelhetőbbé tenni őket. Minden a térképen zajlik, téglalapos falukijelölés lehetséges, hogy ne egyenként kelljen a sereget kiküldeni, hanem akár egyszerre az egészből.</li>
					<li>Építőpontos építkezés: nem kell többé nézni egyenként minden faluban, hogy mikor jár le az óra, hogy újabb építést betegyünk. A favak építési pontokat termelnek, amelyeket bármikor el lehet nyomkodni az épületekre. Az építés azonnal kész lesz.</li>
					<li>Nincs falvankénti kincstár. Nem kell faluközi aranyszállítgatással bajlódni, egyben van a teljes vagyonod.</li>
				</ul>
			</div>
			<h3>Fontos információk</h3>
			<p>Fontos megjegyzés: ez a játék &lt;canvas&gt; elemet használ a megjelenítéshez, ami szinte minden böngészőn megy - Internet Explorer-en kívül. Szóval, ha játszani szeretnél, illene szedned legalább egy Firefoxot.</p>
    		<p>Lájkold a játék Facebook oldalát: <a href="https://www.facebook.com/pages/StraTaDi/209096639149286">itt.</a></p>
			<p>Néma gyereknek anyja se hallja a szavát... Ha valami problémád van a bejelentkezéssel vagy a programhibát észlelsz, akkor azt jelentsd a játék oldalán.</p>
			<p><i>Úgy tűnik, hogy @facebook.com -os e-mail címről nem enged regisztrálni, ha nem engedélyezed az az admin e-mail címét (${config['adminMail']}) a beállításaidban. Figyelj erre oda. (kaptam egy pár visszapattintott levelet onnan már.)</i></p>
			<p>Ha gondolod, regisztráció nélkül is szétnézhetsz a pályán: <a href="game.php?guest">[Belépés nézelődőként]</a></p>
			<p>Ha nem szeretnél regelni, de ki szeretnéd próbálni a játékot, akkor beléphetsz <b>demo</b> felhasználónévvel, üres jelszóval.</p>
			<div style="width:70%; height:auto; margin:0 auto 0 auto; display:block; position:relative; left:0; top:0; clear:both">
				<h3>Screenshotok</h3>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/profile.png"><img src="${config['imageRoot']}/thumb_profile.png" alt="Profilod" title="Profilod"></a><br>Profilod
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/action1.png"><img src="${config['imageRoot']}/thumb_action1.png" alt="Akció indítása" title="Akció indítása"></a><br>Akció indítása
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/action2.png"><img src="${config['imageRoot']}/thumb_action2.png" alt="Akció indítása" title="Akció indítása"></a><br>Akció indítása
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/map.png"><img src="${config['imageRoot']}/thumb_map.png" alt="A térkép" title="A térkép"></a><br>A térkép
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/overview.png"><img src="${config['imageRoot']}/thumb_overview.png" alt="Faluáttekintés" title="Faluáttekintés"></a><br>Faluáttekintés
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/villageview.png"><img src="${config['imageRoot']}/thumb_villageview.png" alt="A falvad" title="A falvad"></a><br>A falvad
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/battlesim.png"><img src="${config['imageRoot']}/thumb_battlesim.png" alt="Csataszimulátor" title="Csataszimulátor"></a><br>Csataszimulátor
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/dusk.png"><img src="${config['imageRoot']}/thumb_dusk.png" alt="Alkonyodik" title="Alkonyodik"></a><br>Alkonyodik
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/night.png"><img src="${config['imageRoot']}/thumb_night.png" alt="Beesteledett" title="Beesteledett"></a><br>Beesteledett
				</div>
				<div class="thumbdiv">
					<a href="${config['imageRoot']}/newgraphics.png"><img src="${config['imageRoot']}/thumb_newgraphics.png" alt="Új grafika" title="Új grafika"></a><br>Új grafika
				</div>
				<div style="clear:both"></div>
			</div>
X
	,
	'gamemenu'=>'Játékmenü', // in left bar
	'gender' => "Nem: ", 		// in the registration form
	'generateactivityplot'=>"Aktivitás grafikon generálása", // in the generate activity plot window
	'generate'=>"Generálás", // in the generate activity plot window
	'go'=>'Mehet', // in mass building window
	'gobackprevious' => "Lépj vissza az előző oldalra.", 		// in the error page
	'gold' => "arany", 		// in game, playerinfo
	'goldmine' => "aranybánya", 		// in game, village properties
	'goldproductiontop10'=>'Aranytermelés top 10', //
	'goldtext'=>'{1} ({2}/h)', // in game, right top bar. {1}: gold you have (as JS counter spinning upward). {2}: gold production (as JS variable)
	'gotvillagebyconquer'=>'Elfoglaltuk a falut!', // in reports window
	'grantrightsinfo'=>'Itt adhatsz jogokat a játékosoknak, válassz ki egy vagy több játékost, majd egy vagy több jogot, a kiválasztott játékosok megkapják a kiválasztott jogokat, míg a kiválasztatlan jogokat elvesztik. Óvatosan bánj a \'jogok kiosztása\' jog továbbadásával!', // information in grant rights window
	'grantrightstoplayers' => "Jogok kiosztása",  // guild right name, guild page
	'guild' => "Klán", 		// in the top bar, in world events
	'guilddiplomat'=>'klándiplomata', // in guild page
	'guildinvitationreport'=>'<a href="viewplayer.php?id={1}">{2}</a> megkér, hogy csatlakozz a <a href="viewguild.php?id={3}">{4}</a> klánhoz. Ha nem vagy még klánba, akkor a meghívókat megtekintheted a <a href="guild.php">klán</a> oldalon.', // guild invitation report content {1}=user's is, {2}=user's name, {3}=guild's id, {4}=guild's name
	'guildinvitations' => "Klánmeghívások:", 		// in the guild page, the invitations text and on the recruit players page.
	'guildmemberlist' => "Tagok:",  // guild page
	'guildname' => "Klánja neve",  // in game, village properties
	'guildnotexist'=>'A klán nem létezik', // in guild page, and on diplomacy settings.
	'guildpermissionstring'=>'({1})', // in guild page, {1}: enumeration of permissions
	'guildproperty'=>'Klántag', // in map, when select a guild member's village
	'guildoperations' => "Klánműveletek:",  // in guild page
	'guildtopics'=>'Klántopikok: ', // in guild view window
	'help'=>'Súgó', // in top left bar.
	'hereyoucanswitchplayer'=>'Ezen az oldalon átléphetsz egy másik felhasználóhoz, de vigyázz, visszalépni nem tudsz, mert az a másik játékos valószínűleg nem admin.', // in admin mode, in game
	'hero'=>'Hős', // in the weekly oracle
	'heroattackskill'=>'Támadószint: <span title="{3}">{1}</span> ({2} XP a következő szinthez)', // in the hero view window {1}: current level {2}: xp to go, {3}: current xp
	'heroavatar' => "Hősöd képe: ", 		// in the registration form
	'herocreated'=>'Hős létrehozva.', // in hero created window
	'herodefendskill'=>'Védőszint: <span title="{3}">{1}</span> ({2} XP a következő szinthez)', // in the hero view window {1}: current level {2}: xp to go, {3}: current xp
	'heroesinyourvillages'=>'Falvadban tartózkodó hősök: ', //in hero window
	'heroinfo'=>'A hősödet átnevezheted, és képet tölthetsz fel neki a profilodban.', // in hero created window
	'heroisnowyours'=>'A hős mostantól neked engedelmeskedik.', // when you successfully adopt a hero.
	'heromove'=>'Hős mozgatása', // in event bar
	'heroname' => "Kezdő hős neve: ", 		// in the registration form
	'heronametooshort' => "A kezdő hős neve túl rövid. Legalább ".$config['minHeroNameLength']." karaktert kell tartalmaznia. ", 		// in the registration form
	'heromovetask'=>"Hős átmozgatása {1} faluból {2} faluba.", // in task window {1}: from village name, {2}: to village name.
	'heronotexist'=>'A hős nem létezik.', // error when a hero does not exist
	'heropagetitle'=>'{1} ({2}. szintű)', // title of the hero's page. {1}: hero name, {2}: hero level
	'heropicture'=>'(Hős képe)', // alt text for the hero image.
	'herotop10'=>'Hős top 10', // top 10 heroes in the oracle
	'hidden'=>'rejtett', // reports page
	'hidehiddenreports'=>'Rejtsd el a rejtett jelentéseket.',// in reports window
	'hire'=>'Felfogad', // in edit kings window
	'hirehero'=>'Hős besorozása', // in no hero window
	'idontneedtutorial'=>'[Köszönöm, nem kérek útmutatást!]', // in tutorial window
	'ifyoudontwanttochangepassword'=>'Ha nem szeretnél jelszót váltani, akkor az alábbi két mezőt hagyd üresen.', //
	'ifyouwanttochangetheemail'=>'Ha meg szeretnéd változtatni az e-mail címedet, írd be az új e-mail címet az alulsó mezőbe. Az új és a régi e-mail cím is kapni fog egy megerősítő levelet.', //
	'ifyouwanttodelete'=>'Ha törölni szeretnéd a királyságot, akkor kattints ide: <a href="dodeleteme.php">Törlés</a>. Ez kijelentkeztet a játékból. Ha ezután 2 hétig nem lép be senki, a királyság törölve lesz, a falvak elhagyatottá válnak.', // in edit profile page
	'imnotregistered' => "Még nem vagyok regisztálva: ",	// in the login form
	'imregistered' => "Már regisztrálva vagyok, belépek:",	// in the login form
	'inbuiltlevelspie'=>'Beépített szintek eloszlása', //in oracle window
	'incoming'=>'Bejövő', // in event bar
	'incomingattack'=>'Bejövő támadás', // in event bar
	'incomingmove'=>'Egységeket kaptunk', // in report view
	'infantryattack'=>'Gyalogos támadóerő', // in unit description tables
	'infantrydefense'=>'Gyalogos elleni védelem', // in unit description tables
	'invalidday' => "Érvénytelen nap.",	// in the registration form
	'invalidemail' => "Az e-mail cím formátuma hibás",	// in the registration form
	'invalidlaunchervillage'=>'Érvénytelen indítófalu (nem a tiéd, vagy nem létezik)', // response from the task manager php.
	'invalidpictureformat' => "Nem támogatott képformátum. csak JPG, PNG és BMP formátumokat fogad el.",	// in the registration form
	'invalidyear' => "Ha ez igaz, amit beírtál, akkor te még meg sem születtél. Írj be kisebb évet.",	// in the registration form error when birth year is bigger than the current year.
	'invitationrefused'=>'A meghívás visszautasítva', // when you reject a guild invitation
	'invitationsent'=>'Meghívás elküldve',// success window message, after you sent the 
	'inviteplayer' => "Játékos meghívása",  // guild right name, guild page
	'inviteplayerinfo'=>'Ajánlott kapcsolatba lépni a játékossal, mielőtt meghívót küldesz.', // on invite player page
	'inviteplayerandgetep'=>'Hívj meg játékosokat a játékba, és nyerj terjeszkedési pontokat! ', // on the invite player page.
	'inviteplayertogame' => "Játékos meghívása a játékba",  // guild right name, guild page
	'inviteplayerdescription'=><<< X
		<p>A fejlődésedet meggyorsíthatod azzal, hogy játékosokat hívsz meg a játékba. Ha a játékosnak 8 faluja lesz, akkor terjeszkedési pontokat kaphatsz.</p>
		<p>Hogy mennyit, az attól függ, hogy mennyire kevésbé van köze a két játékosnak egymáshoz.</p>
		<ul>
			<li>Ha a meghívó és a meghívott is legtöbbször közös internetkapcsolatról van fent, akkor azért sajnos nem jár pont.</li>
			<li>Ha a meghívott játékos internetkapcsolatát többen is <dfn title="azaz több játékos is azonos interkapcsolatról van legtöbbször a játékban.">rendszeresen használják</dfn>,
				hogy játszanak, de nem a meghívóval közös internetkapcsolatot használják legfőképp, és az elmúlt egy héten beléptek a játékba. Ekkor 1 pontot kapsz.</li>
			<li>Ha az előző kettő nem áll fenn, de más <dfn title="aki legalább egyszer belépett az elmúlt héten">aktívan játszó játékos</dfn> is valaha belépett arról az internetkapcsolatról, amelyet a meghívott leginkább használ, 2 terjeszkedési pontot kapsz.</li>
			<li>
				Ha az előző három nem áll fenn, tehát a meghívottal közös kapcsolatról nem volt belépés, és a meghívott játékos leginkább használt internetkapcsolatáról se játszott még soha se senki, akkor olyan területre jutattad el a játékot, ahol 
				az nagy valószínűséggel teljesen ismeretlen, ezért <b>5</b> terjeszkedési pontot kapsz.
			</li>
		</ul>
		<p>Ez a kicsit bonyolultnak tűnő szabályrendszer azért van, hogy elsősorban <dfn title="Tehát szemét módon ne magadnak regisztráld az accountokat. :)">reklámozással</dfn> terjedjen a játék. Tehát fórumokban linkekkel, facebookon, blogokban, review oldalakon stb.</p>
X
	, //  on the invite player page.
	'invitebutton'=>'Meghívás', // in invite player window
	'invitedplayername'=>'A meghívott játékos neve', //  in invite player window
	'january' => "január",  // in the date picker control
	'javascriptisnotenabled'=>"A böngésződben valószínűleg le van tiltva a <dfn title=\"Ennek segítségével lehet animációkat, és interaktív dolgokat csinálni a weboldalakra.\">javascript</dfn>. Engedélyezd, hogy működhessen a játék. Ha továbbra is gondod van, írj az adminnak a ${config['adminMail']} címen.", // it sucks when your browser not supports javascript.
	'july' => "július",  // in the date picker control
	'jump'=>'Ugrás', // in left top bar
	'june' => "június",  // in the date picker control
	'kick'=>'Elbocsátás', // in edit kings window
	'kickplayer' => "Játékos kirúgása a klánból",  // guild right name, guild page
	'kingdomname' => "Királyság neve:", // on registration form
	'kingdomprofile' => "Királyság profilja", // in the left menu
	'kingdomsavatar'=>'Királyság címere: ', // on registration form
	'kingdomsdata'=>'Királyság adatai ', // on registration form
	'knights' => "lovas",  // in game, village properties
	'knight' => "lovas",  // in game, village properties
	'knightlevel' => "lovasok szintje",  // in game, village properties
	'kings'=>'Királyok: ', // on kingdom profile
	'kingsdata'=>'Király adatai ', // on registration form
	'lastmessageposted'=>'Utolsó üzenet időpontja', // in messages window
	'lastposter'=>'Utolsó hozzászoló', // in messages window
	'lastupdate' => "Utoljára frissítve",  // in game village view and village summary
	'lastupdatesecondstext'=>'{1} mp-e', // in village summary
	'launchtroops'=>'Csapatok indítása', // in the action window
	'launchervillages'=>'Indítófalvak: ', // in the launch action panel
	'launchheroifinvillage'=>'A hős küldése, hogy ha a kiválasztott falvak valamelyikében van.', // in send troops window
	'leavekingdom'=>'Királyság elhagyása', // in edit kings window
	'leaveguild'=>'Klán elhagyása', // in the guild window, the title of the leave guild window, and the leave guild button
	'lettersent'=>'Levél elküldve', // the message after you sent the letter
	'level'=>'Szint', // in the weekly oracle, and building cost table
	'login' => "Bejelentkezés",  // in the login form
	'logout' => "Kijelentkezés", // in the top bar
	'loottext'=>'A zsákmányolt arany mennyisége: {1}', //in battle report. {1}: the looted gold
	'lostvillagebyconquer'=>'Elfoglalták a falvunkat. ', // in reports window
	'lostvillagebydestruct'=>'Lerombolták a falvunkat!', // in reports window
	'makehidden'=>'Elrejtés', // when viewing one report.
	'makepublic'=>'Publikálás',// when viewing one report.
	'mandatorydata' => "Ezeket az adatokat kötelező megadni:",  // in the registration form
	'march' => "márcus",  // in the date picker control
	'male' => "férfi",  // in the gender picker control
	'managediplomacy'=>'Diplomácia kezelése', // guild right name, guild page
	'managekings'=>'Királyok kezelése', // in edit kings window
	'mapstuff'=>'Térképműveletek', //in left bar
	'massbuilding'=>'Tömeges építkezés', // in left menu bar, and the title of the massive building window
	'massbuildinginfo'=>'Tömeges építéskor a kiválasztott falvakban mindig a legkisebb szintű épületen húz, és ezt addig ismételgeti, amíg az összes épület el nem éri a kívánt szintet, elfogy az építésre szánt arany vagy az építési pont.', // in mass building window
	'massbuildingtask'=>'{4} épület fejlesztése a következő azonosítójú falvakban: [{1}], Szint limit: {2}, Arany limit: {3}, ', // task name,{1} village id list, {2} level limit number, {3} gold limit number, {4} building name. All are javascript insertions.
	'masstraining'=>'Tömeges egységképzés', // in mass training window
	'masstrainingtask'=>'Tömeges képzés [{1}] mennyiségben a [{2}] azonosítójú falvakban.', // in mass training window. {1}: unit's going to be trained, {2}: identifiers of the trainer villages.
	'may' => "május",  // in the date picker control
	'messages' => "Levelek",  // in the top bar, and it's also the title of the messages window
	'miscvillageinfotext'=>'Pont: {1} | ({2};{3}) | #{4}', // in village info window. {1}: score of the village, {2}: x coordinate, {3}: y coordinate, {4}: identifier. (all parameters are passed as javascript includes)
	'modifybutton'=>'Módosít', // modify button
	'moderateforum' => "Fórum kezelése",  // guild right name, guild page
	'mousemode'=>'Egérmódok: ', // in game
	'moveevent'=><<< X
		<table class="center" style="background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Indítófalu: {2}</td><td>Célfalu: {3}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">Sereg vezetője: {8}</td></tr>
			<tr><td colspan="2">{10}</td></tr>
		</table>
X
	, // the description of the move event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link, {11} the color code of the event
	'movehero'=>'Hős mozgatása', //
	'movetitle'=>'{1}({2}) egységeket adott át {3}({4}) faluba.', // in battle report's title {1}: attacker village's name (as link to village), {2}: owner (as link to the profile), {3} (as link to village): defender village's name, {4}: defender name (as link to profile)
	'movetroops'=>'Egységek átadása', // in action window
	'moreinfo'=>'További infók', // in the send troops window.
	'myprofile' => 'Profilom', // in game player menu
	'na'=>'-', // The N/A symbol in you language
	'name'=>'Név', // The word: 'name' (in game, village view, unit and building name, in village summary, edit kings window)
	'neutral'=>'semleges', // diplomatic stance
	'new'=>'Új', // the word 'new' (in reports, messages, etc view)
	'newdiplomaticrelationship'=>'Új diplomáciai kapcsolat felvétele, a kapcsolat csak a saját oldalon lesz látható, nem jelenti automatikusan a másik klán beleegyezését. Vedd fel a kapcsolatot a klánnal mielőtt diplomáciai kapcsolatot építesz. Ha beállítod a kapcsolatot, a célklán diplomatája jelentést fog kapni a változtatásról.', // in diplomacy management window
	'newreplywhileyouwrote'=>'Új válaszok érkeztek, amíg a levelet írtad. A levél elküldése előtt átolvashatod azokat.', // in compose message window
	'newvillage' => "Új falu",  // the default name of the new villages
	'next' => "következő",  // in game, village properties, next to build point counter
	'nextreport'=>'Következő', // in report view
	'newguildname'=>'Klánnév:', // in guild window, when not member of a guild.
	'newhero'=>'Új hős', // the name of a new hero
	'nightbonustext'=>'{1}×', // night bonus in the right top window.
	'nightbonus'=>'Éjszakai bónusz: ', // the night bonus text in the battlesimulator
	'noaccountassociated'=>'A hozzáférés nincs hozzárendelve egyetlen királysághoz sem', // when no account associated with the access.
	'noaccountassociatedinfo'=>'Jelen pillanatban egyetlen királyságot sem irányítasz. Kérj meg valakit, hogy adjon hozzá egy királyság irányításához.', // when no account associated with the access.
	'noevents'=>'(nincs semmi egységmozgás)', // in the event bar
	'nofreeheroesgarrisoninginyourvillages'=>'(Nem állomásozik szabad hős egyik falvadban sem.)', // in no hero window
	'noinvitations'=>'(nincsenek meghívások)', // in clan window, when not member of a guild, this text saying 'no invitations yet'.
	'nolevellimit'=>'Nincs limit', // in mass building task
	'nosubject'=>'(nincs cím)', // the placeholder subject for letters without subject
	'notclanmember' => "Jelenleg nem vagy klánban",  // clan window, when not member of a guild (title)
	'notclanmemberindetail' => "Jelenleg nem vagy még tagja egy klánnak sem. Ha a játékosok klánokba szerveződnek, akkor erősebbek lehetnek, mint egyedül. Játékstílusodtól függően itt dönthetsz, hogy te magad szeretnél vezetni egy klánt, vagy inkább megvárod, amíg meghív valaki.",  // clan window, when not member of a guild, (detailed info)
	'notenoughbuildboints'=>'Nincs elég építési pontod', // response from task manager php.
	'notenoughexpansionpoints'=>'Nincs elég terjeszkedési pontod.', // response from task manager php.
	'notenoughgold' => "nincs elég arany",  // in game, village properties, next to build point counter
	'notenoughsettlerunits'=>'Nincs elég telepes egységed', // response from the task handler php.
	'notenougthtroops'=>'Nincs elég csapatod', // response from the task handler php.
	'notes'=>'Jegyzetek', // in the notes window
	'notessaved'=>'Jegyzetek mentve', // in the notes window
	'notloaded'=>'(Nincs betöltve)', // in the village summary window
	'notsupportedbrowser' => htmlspecialchars("A böngésződ nem támogatja a <canvas> elemet. Frissítsd a böngésződet vagy tölts le egy újabbat. A canvas elemet a következő böngészők vagy újabbak támogatják: Firefox 1.5 már támogatja (nem próbáltam vele), Google Chrome mindig is támogatta, Safari 2.0-tól, Internet Explorer 8 még nem támogatja, de az ExCanvas vagy InCanvas pluginnal működhet, Opera támogatja, de nincs infóm róla, hogy mikortól, valaki világosítson fel. De egy szó mint száz: tölts le egy újabb böngészőt! :)"),  // error message when the user don't have <canvas> capable browser.
	'notstartedyet' => "A játék még nem kezdődött el. A visszaszámlálás tart, várd meg míg lejárt, aztán próbálkozz újra.",  // when the game not started, the countdown page
	'november' => "november",  // in the date picker control
	'nowyoumemberoftheguild'=>'Mostantól a {1} klán tagja vagy!',// when you enter the guild. {1} the guild name you entered.
	'october' => "október",  // in the date picker control
	'offensetop10'=>'Támadó Top 10', // in the weekly oracle
	'oldpassword'=>'Régi jelszó biztonsági okokból', //
	'onevillagemustbeselected'=>'Legalább 1 falut ki kell választanod, hogy képezhess.', // in mass training window
	'operation'=>'Művelet: ',// in action window
	'optionaldata' => "Ezeket az adatokat megadhatod, ha akarod: ",  // in the registration form
	'or'=>'vagy', // the word 'or'. (in diplomacy management window)
	'oryoucanwaitaherotoappearinyouvillage'=>'Vagy várhatsz arra, hogy egy szabad hős állomásozzon a faludban.', // in the no hero page
	'others'=>'<Mások>', //in oracle
	'outgoing'=>'Kimenő', // in event bar
	'outgoingmove'=>'Egységeket adtunk át', // in report view
	'outsideguild'=>'<Klánon kívül>', //in oracle
	'own'=>'saját', // on map, the word placed when you select your own village.
	'ownername' => "tulajdonos neve",  // in game, village properties
	'participants'=>'Résztvevők: ', // in thread view
	'password' => "Jelszó: ",  // in the login and registration form
	'passwordnotmatch' => "A két megadott jelszó nem egyezik meg. Lehet elírtad. Írd be újra.",  // in the login and registration form error message
	'passwordtooshort' => "A jelszó túl rövid. Legalább ".$config['minUserPasswordLength']." db karaktert kell tartalmaznia.",  // in the login and registration form error message
	'passwordagain' => "Jelszó ismét: ",  // in the login and registration form
	'pc' => "db",  // in game, means "pieces" 
	'peace'=>'béke', // diplomatic stance
	'player'=>'Játékos', // in world events and in various table headers
	'playerfinishedyourdelegation'=>'{1} befejezte a helyettesítést, és bejelentkezett az accountjába.', // When player logs in again after sitting.
	'playerhasbeenkicked'=>'A(z) <a href="viewplayer.php?id={1}">{2}</a> játékos ki lett rúgva klánból!', // in guild page when you kick someine.
	'playerisnotreferredbyyou'=>'A játékost nem te hívtad a játékba, vagy nem a te reflinkeden regelt.', // in the invite player window.
	'playerlist'=>'Játékosok listája: ', // in grant permissions window
	'playernotreachedthevillagecount'=>'A meghívott játékos még nem érte el a {1} falut, vagy még nem jelentkezett be azóta.', //In check invited player window.
	'playerreachedthelevel'=>'Gratulálunk, a meghívott játékos elérte a kívánt szintet!',//In check invited player window
	'playerrequestyoutodelegatehim'=>'{1} felkért, hogy legyél a helyettese. A baloldali menü helyettesítés menüpontját használva beléphetsz hozzá.', // When sy request you to delegate him.
	'playnow'=>'Játssz most! Vegyél részt a harcokban! Regisztrálj most!', //advertisement text shown in public reports.
	'pleasechoosecatapulttarget'=>'Válassz célpontot a katapultnak, ha katapulttal támadsz: ', // in launch troops window.
	'pleaseenterthechoosendeputyname'=>'Írd be a kiválasztott helyettesed nevét: ', //
	'pleaselogin' => "Nem vagy bejelentkezve, jelentkezz be, vagy regisztálj.", // in login form
	'pleasesendmoretroops'=>'Legalább {1} arany értékű sereget kell küldened, amikor támadsz!', // in the launch action window. {1}: the minimal value (as js variable but may be used on the php side too.)
	'plotwidth'=>"Diagram szélesség: ", // in the generate activity plot window	
	'possiblecommands'=>'Lehetséges parancsok: ', // in action window
	'preview'=>'Előnézet', //
	'previousreport'=>'Előző', // in report view
	'profile' => "Profil", // in profile edit window
	'profilenotexists' => "Profil nem létezik", // error message when requesting non-existing profile.
	'public'=>'publikus', // reports page
	'publishthefollowinglink'=>'<p>Oszt meg a következő linket másokkal:</p><p><tt>{1}</tt></p><p>Aki rákattint, és ezen keresztül regisztrál, az megfog jelenni a meghívottjaid között!</p>', // in invite player window. {1} the reflink
	'raid'=>'Fosztogatás', // in action window
	'receivedat'=>'Érkezés időpontja', // in reports window
	'receivervillage'=>'Fogadó falu: {1} ({2})', // in move report the receiver village text. {1}: village name (as link), {2}: owner name (as link to profile)
	'recentevents'=>'Mostanában történt', //in left menu.
	'recipient'=>'Címzett: ', // in send message window
	'recipientplayer'=>'Címzett játékos ', // in massive training window
	'recipientisnotexist'=>'Címzettnek megadott felhasználónévvel nem létezik felhasználó. ', // in send message window
	'recon'=>'Kémkedés, majd hazatérés.', // in action window
	'recruiter'=>'toborzó', // in guild page
	'refresh'=>'Nézet frissítése', // in the village summary page
	'refuseinvitation' => "Meghívás elutasítása",  // guild window when not member of a guild.
	'reginfo' => <<< X
	<ul>
		<li>A regisztáció után a felhasználóneved nem változtatható meg.</li>
		<li>A feltöltött képek átlesznek méretezve ${config['avatarSize']}×${config['avatarSize']} pixelre. Csak JPG, PNG és BMP képet fogadunk el.</li>
		<li>A megadott e-mailre kapsz majd egy aktiváló kódot, amelynek segítségével majd aktiválhatod a felhasználói fiókod.</li>
		<li>Az e-mail címed csak akkor változtatható meg, ha a régihez és az újhoz is hozzáférésed van.</li>
	</ul>
X
	,
	//in the registration form
	'registerkingdom'=>'Királyság regisztrálása (jelöld be, ha szeretnél egy saját birodalmat)', //
	'registration' => "REGISZTRÁCIÓ - ingyenes", // in login form
	'registrationbutton' => "Regisztrálás", // in the registration form
	'registrationtitle' => "Regisztráció", // the registration form's title
	'regmailcontent' => <<< X
	Hello {1}!

	Ezt a levelet azért kapod, mert begisztráltál a ${language['wtfbattles']} játékba.
	Jelentkezz be a játékba, és másold be a következő aktivációs kódot, amikor kérik:

	{2}

	Jó játékot kíván: ${config['adminName']}, a játék adminisztrátora.

	(Ha mégsem regisztráltál be a játékba, akkor ezt a levelet nyugodtan törölheted, nem fog történni semmi rossz.)

X
	,
	 // the registration mail's body. The {1} replaced with the players name, the {2} will be the activation code.
	'regmailsubject' => "Regisztráció a ${language['wtfbattles']} játékban", // the registration mail's subject
	'rename' => "átnevez", // in game, in general
	'renamevillagetask' => "{1} falu (#{2}) új neve legyen {3}", // in game, rename village task name. 1: village's old name, 2: village's id, 3: village's new name
	'renamingvillage' => "Falu átnevezése", // in game, the task name
	'reports' => 'Jelentések', // in game, player menu
	'return'=>'Visszatérés', // event name
	'returnevent'=><<< X
		<table class="center" style="text-align:center; background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Indítófalu: {2}</td><td>Célfalu: {3}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">Sereg vezetője: {8}</td></tr>
			<tr><td colspan="2">{6} arannyal</td></tr>
		</table>
X
	, // the description of the move event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {9}: catapult target (image), {10} cancel this event link
	'revagebonustext'=>"Fordított korbónusz: {1}×", // In the profile
	'revoke'=>'Visszavon', // in the invite player window
	'revokeinvitationtext'=>'{1} klán toborzója visszavonta a meghívást a klánjába.', // in the invitation revocation report. {1}: is the link to the guild
	'revokeinvitationtitle'=>'Meghívás visszavonva', // in the invitation revocation report title.
	'rightlist'=>'Jogok listája: ', // in grant guild right window
	'rulefile'=>'languages/hu/hurules.php', // A html file containing the rules
	'savebackup'=>'Biztonsági mentés letöltése', // in admin panel
	'saveedits'=>'Módosítások mentése', // in guild profile edit (the button title)
	'score'=>'Pont', // in village summary window
	'scoretop10'=>'Pont top 10', // oraculum
	'selectmode'=>'Kiválasztó téglalap (CTRL)', // in game
	'selectplayertokick'=>'Válaszd ki a játékost, akit el szeretnél távolítani a klánból. A játékos azonnal elbocsátásra kerül.', // in kick player window
	'sendcircular'=>'Körlevél küldése', // guild right name
	'sendhimmessage'=>'Üzenet küldése neki', // in the profile view
	'sendletterbutton'=>'Levél küldése', // in send message window
	'sendmassreport'=>'Rendszerüzenet küldése', // in admin mode.
	'sendreply'=>'Válasz küldése', // in thread view window
	'sendtroops'=>'Csapatok küldése', // in action window
	'sendtroopscommand'=>'Egységek indítása {1} falvakból {2} arányban {3} faluba. A művelet: {4}',  // in task window. {1}: the id list of the launcher villages, {2}: the percentage of amounts, {3}: destination village, {4}: action type (inserted by javascript)
	'september' => "szeptember",  // in the date picker control
	'serverisclosed'=>'A szerver lezárult, további akció nem indítható!', // when server ends
	'sessionisover' => 'A bejelentkezés lejért, jelentkezz be újra!',  // in game when the users session is over
	'set'=>'Beállítás', // in general, when setting something.
	'setmaster'=>'Kinevezés főkirállyá', // in edit kings window
	'setdiplomaticstance'=>'Diplomáciai hozzállás beállítása', // in diplomacy settings window
	'setsparebptask'=>'Tartalék építési pontok beállítása {1} faluban {2} értékre.', // set spare bp task text.  {1} village name {2} spare build points to set. Both are js insertions.
	'settleevent'=><<< X
		<table class="center" style="background-color:{11}">
			<tr><th colspan="2">{1}, {7}</th></tr>
			<tr><td>Indítófalu: {2}</td><td>Célterület: {5}</td></tr>
			<tr><td colspan="2">{4}</td></tr>
			<tr><td colspan="2">{10}</td></tr>
		</table>
X
	, // the description of the settle event {1} event type, {2} launcher village name (as link), {3} destination village name (as link), {4} with units, {5} coordinates, {6} gold, {7}: event time, {8}: hero (as link), some of these may be undefined., {11} the color code of the event
	'settlevillage'=>'Falu alapítás a megadott mezőn', // in action window
	'settlevillagenow' => htmlspecialchars(">>> Alapíts falut most <<<"),  // in the date picker control
	'settlevillagetask'=>'Falualapítás indítása {1} nevű faluból ({2};{3}) mezőn.', // in task window. {1} launcher village name, {2}: destination's x coordinate, {3}: destination's y coordinate
	'shareit'=>'Oszd meg másokkal: ', // on various pages
	'shortdescription'=>'Ez egy új faluépítgetős harcolós játék. Építs, harcolj, támadj, védekezz, foglalj, szövetkezz, háborúzz...', // The short description of the game
	'showcost'=>'Árak mutatása', // in village info
	'showhiddenreports'=>'Mutasd a rejtett jelentéseket.',// in reports window
	'sitteractivity'=>'Helyettes aktivitása', // in the activity plot
	'someonekickedyoufromthesomeguild'=>'Sajnos <a href="viewplayer.php?id={1}">{2}</a> elbocsátott a(z) <a href="viewguild.php?id={3}">{4}</a> klánból!', // report text when you got fired from the alliance.
	'someonesettledthere'=>'Valaki már odatelepült arra a mezőre, amit kiválasztottál. Frissítsd a böngészőablakot, hogy lásd. Ha még sincs ott falu, akkor valami gáz van.', // response from the task manager php.
	'sorrybuterrorhappened' => "Sajnos hiba történt!",  // in the error page
	'sparebp'=>'Tartalék ÉP: ', // in village info
	'sparebptooltip'=>'Tömeges építkezés művelete ennyi építési pontot fog meghagyni a faluban. (Kattints a számra, hogy beállítsd.)', // in village info
	'speed'=>'Sebesség', //in unit description tables
	'speedtext'=>'{1} mező/h', //in unit description tables {1}: unit speed.
	'spearmanlevel' => "lándzsások szintje",  // in game, village properties
	'spearman' => "lándzsás",  // in game, village properties
	'spearmen' => "lándzsás",  // in game, village properties
	'specifyvalidnumbers'=>'Adj meg érvényes egységlézszámot!', // in mass training window
	'spendallbuildpoints'=>'Összes építési pont elköltése', //in mass building window
	'spendmaxgold'=>'Max ennyi aranyat költhet el:', // in mass building window
	'spokenlanguages'=>'Beszélt nyelvek: ', // in the registration form
	'stables' => "lovarda",  // in game, village properties
	'startnewtopic'=>'Új téma kezdése', // in manage forums window
	'strength'=>'<dfn title="Ennyi aranyat tud szállítani az egység, egy rablás után.">Teherbírás</dfn>', // in unit description tables
	'strengthtext'=>'{1} arany', // in unit description tables
	'subject'=>'Téma: ', // in compose message window
	'subscribetopic'=>'Felíratkozás a témára', // in guild window
	'successfulguildenter'=>'Sikeres belépés a klánba', // when you join a guild
	'successfulregistration' => "Sikeres regisztáció!", // in the successful registration page.
	'successfulregistrationdescription' => "Üdvözlünk {1}, sikeresen beregisztráltál a ${language['wtfbattles']} játékra. Az felhasználói fiókod aktiválásához szükséges e-mailt elküldtük a megadott {2} címre. Innentől kezdve 1 napod van aktiválni. Legkésőbb pár órán belül megkapod a levelet, ha mégsem, akkor írj az adminnak, hogy aktiváljon kézzel: ${config['adminMail']}.",  // in the successful registration page. The {1} replaced with the player's name, the {2} will be the e-mail address.
	'targetcell'=>'Célcella: ', // in action window
	'targetvillage'=>'Célfalu: ', // in action window
	'tasklist' => 'Feladatlista', // task list
	'thehighest'=>'A legnagyobb...', //
	'thekingmustnotcontrolakingdom'=>'A kiválasztott király nem irányíthat másik királyságot.', // in edit kings window
	'theownerofthisherois'=>'Ez a hős {1} játékosnak engedelmeskedik.', // in hero view window. {1}: the owner of the hero.
	'thisherodonthaveowner'=>'Ez a hős nem engedelmeskedik senkinek.', // in hero window for free heroes.
	'thisisaguildletter'=>'Ez egy klánlevél, csak klántagok tekinthetik meg.', // in thread view window
	'thiskingisalreadycontrollingakingdom'=>'Ez a király már irányít egy birodalmat!', // in edit kings window
	'thesearetheguildthreads'=>'A következő topikok klántopikok: ', // in the manage forums window
	'threadnotexist'=>'Az üzenetváltás nem létezik, nem vagy rá felíratkozva vagy nincs jogosultságod megtekinteni azt.', // in compose message window.
	'thismessagewillbeacircular'=>'Ez a levél egy körlevél lesz majd a klánodnak, automatikusan minden klántag felíratkozik rá. Azonban, ha kirúgsz valakit az nem fogja leíratkoztatni az illetőt a levélről!', // in send message window
	'thismessagewillbeaguildthread'=>'Ez a levél egy topik lesz majd a klánfórumban. Csak klántagok tekinthetik meg, meg fog jelenni a klánoldalon, és bármelyik klántag szabadon felíratkozhat rá.', // in send message window
	'thisplayerdonthavehero'=>'(A játékosnak nincs hőse)', // in player window
	'title'=>'Cím', // in general
	'toomanyvillagescantspawn' => "Túl sok falu van azon helyen, ahol megjelenhetnél. Próbálkozz később.", // when settling the first village and was unable to settle the first village
	'topic'=>'Téma', // in messages window
	'totalcost'=>'Összköltség: {1} arany', // in the mass training window. {1} is the gold needed as js insertion.
	'townhall' => "városközpont", // in game, village properties
	'townhalltop10'=>'Átlagos városközpont szint top 10', // oraculum
	'train'=>'Kiképzés', // in village view
	'trainedat'=>'<dfn title="Ezt az épületet kell fejlesztened, ha a képzés sebességét növelni szeretnéd.">Képzés helye</dfn>', // in unit description tables
	'trainingtext' => "Képzés alatt: {1} (köv: {2}).", // in game, village properties, {1}: units enqueued for recruit (as js war), {2}: next unit training status.
	'trainingtask' => "Egységképzés {1} faluban {2}db {3}", // in game, the text of the training task, 1: village name, 2: amount of units, 3: the name of the unit type
	'trainingtime'=>'<dfn title="Képzési idő a képző épület nulladik szintjén">Képzés időtartama</dfn>', // in unit description tables
	'troopnumberdescription'=>'Itt a kiválasztott falvakban lévő összes sereget láthatod, amennyi a bejelentkezésed pillanatában volt. Ha frissíted az összes kiválasztott falut, akkor ez a szám több is lehet. Minden kiválasztott faluból fog indulni sereg, hogy mennyi azt a jobb oldalon kiszámolt százalékérték határozza meg.', //
	'tutorial'=>'Útmutató', // in the left top bar
	'tutorialfile'=>'languages/hu/hututorial.php', // the filename of the tutorial file.
	'typeguildname'=>'Írd be a klán nevét: ', // in diplomacy management window
	'typeguildsid'=>'Írd be a klán azonosítóját (A # utáni szám a klán oldalán): ', // in diplomacy management window
	'unabletosendmail' => "Sajnos nem sikerült kiküldeni a levelet. Az ok egyelőre tisztázatlan. Bár regisztrálásra került a felhasználói fiók, az aktivációs kód hiányában nem fogod majd tudni aktiválni, így 24 órán belül automatikusan törlődik majd, próbálkozz holnap ilyenkor majd. Reméljük több szerencséd lesz.",  // in the registration when mail() function was unable to send the mail.
	'unitinfotooltip'=>'{1} (ár: <b>{5}</b> arany),<br><b>{2}</b> db áll rendelkezésre,<br><b>{3}</b> db van kiképzés alatt,<br>A következő egység kiképzése <b>{4}</b>%-nál tart.<br><i>Írd be az alábbi szövegdobozba,<br>hogy mennyi egységet szeretnél kiképezni,<br>majd nyomj entert!</i>', // in game village view, the tooltip when you hover the unit icon. {1}: unit name (as string), {2}: available units (as js var), {3}: under training (as js var), {4}: the precentage of the progress of the training of the next unit (as js var), {5} its cost
	'unitnotexist' => "Az egységfajta a {1} kóddal nem létezik", 		// in game, error when trying to refer a not unit type ({1} is the code of the unit)
	'unknown' => 'Ismeretlen',
	'unknownreport'=>'Ismeretlen típusú jelentés', // in reports window the image title
	'unsubscribe'=>'Leíratkozás', // in messages window
	'updatenow' => "frissítés most", // in game, village properties and player info
	'updatebuildingtask' => "Épületfejlesztés, falu: {1}, épület: {2}", // in game, the building upgrade task text. 1: village name (through JS), 2: building name (through JS)
	'upgrade'=>'Fejlesztés', // in game, village properties
	'usepasswordtoleave'=>'Add meg a jelszavad a kilépés megerősítéséhez', // in leave guild window
	'usepasswordtodismiss'=>'Add meg a jelszavad a feloszlatás megerősítéséhez', // in leave guild window
	'userisinactive' => "A felhasználói fiók inaktív",  // after login in the activation form
	'userisinactivepleaseactivate' => "Még nem aktiváltad a felhasználói fiókod, másold majd be azt a kódot, amit e-mailben kaptál.",  // after login in the activation form
	'username' => "Felhasználónév: ",  // in the login and registration form
	'usernamealreadyregistered' => "Ezzel a névvel már regisztrált valaki más!",  // in the login and registration form
	'usernamenotexist' => "A megadott felhasználói fiók nem létezik.",  // error in the login form (this appears on the error page too during activation if the user not exist)
	'usernamelong' => "Felhasználónév túl hosszú, legfeljebb ".$config['maxUserNameLength']." karaktert kell, hogy tartalmazzon.",  // in the login and registration form
	'usernameshort' => "Felhasználónév túl rövid, legalább ".$config['minUserNameLength']." karaktert kell, hogy tartalmazzon.",  // in the login and registration form
	'viewhero'=>'Hős megtekintése', // in profile
	'villageanduser'=>'{1} ({2})', // in action window {1}:village name, {2} username
	'villagecountscoretext'=>'{1} falu, összesen {2} pont', // in view profile page, {1}: village count, {2}: total score
	'villagedestroyed'=>'A település megsemmisült', // in battle report
	'villageisabandoned'=>'A falu elhagyva', // when you successfully abandon a village.
	'villageisnotyours' => "A falu nem a tiéd, amin a műveletet akartad végezni!", // in dotasks.php report when you want to do things on someone else's village
	'villagename' => "Falunév", // in game, village properties
	'villagenotexist'=>'A falu nem létezik', // response from task manager php
	'villages' => 'Falvak: ', // On the profile view before the village list.
	'villagescoretext'=>'{1} pontos falu', // in village view the score text. {1} the score (inserted as javascript variable)
	'villagesloaded'=>'Betöltött falvak (<i>csak azok</i>): ', // in mass training window and building window
	'villagesselectedmap'=>'A falvak, amelyeket kiválasztottál a térképen, automatikusan kiválasztásra kerültek ebben a listában is.', // in mass training window
	'villagesummary'=>'Faluösszesítő', // in the village summary window
	'villagetext'=>'{1} ({2};{3})', // Generic village text. {1}: village name, {2}: X coordinate, {3}: Y coordinate
	'villagetooltip'=>str_replace("\n",'',
	'
		<table>
			<tr><td colspan="2">{1}</td></tr>
			<tr><td>Hely: </td><td>({5};{6})</td></tr>
			<tr><td>Tulaj: </td><td>{2}</td></tr>
			<tr><td>Klán: </td><td>{3}</td></tr>
			<tr><td>Pont: </td><td>{4}</td></tr>
		</table>
	'), // In the map tooltip. {1}: village name, {2}: owner, {3}: alliance, {4}: score of the village, {5},{6}: x,y coordinate. All of them are javascript insertions.
	'wall' => "városfal",  // in game, village properties
	'war'=>'ellenség', // diplomatic stance (better word: enemy)
	'weeklyoracle'=>'Heti orákulum', // in left menu
	'wentbattle'=>'Csatába indult: ', // in battle simulator
	'workshop' => "műhely",  // in game, village properties
	'worldevents'=>'Világ eseményei', // in game left top menu.
	'worldeventsettle'=>'Falu alapítás', // translation of the world event
	'worldeventdestroy'=>'Falu megsemmisülés', // translation of the world event
	'worldeventconquer'=>'Falu foglalás', // translation of the world event
	'worldeventguildchange'=>'Klánváltás', // translation of the world event
	'worldeventrename'=>'Falu átnevezés', // translation of the world event
	'worldeventeventhappened'=>'Egyéb esemény', // translation of the world event
	'worldeventdiplomacychanged'=>'Diplomácia megváltozása', // translation of the world event
	'worldeventscorechanged'=>'Falu pontjának megváltozása', // translation of the world event
	'wouldyouliketocanceldeletion'=>'Ha meg szeretnéd szakítani a törlés folyamatát, és bejelentkezni, akkor kattints ide.', // in account deletion info box
	'wouldyouliketofinishsitting'=>'Ha belépsz a helyettesítés megszűnik, és a helyettesítőd nem tud majd belépni. Kattints erre a szövegre, ha be szeretnéd fejezni a helyettesítést, és belépni az accountodba.', // when you log in while you are deputized
	'wrongactivationcode' => "Hibás az aktivációs kód, próbáld újra.",  // in the activate account form
	'wtfbattles' => "StraTaDi",  // the game's name
	'wtfbattleslong' => "Stratégia, Taktika és Diplomácia",  // the game's name
	'xcoord'=>'X koordináta:', // name of the X coordinate
	'xnewmails'=>'{0} új levél',
	'xnewreports'=>'{0} új jelentés',
	'ycoord'=>'Y koordináta:', // name of the Y coordinate
	'youalreadyhaveahero'=>'Már van hősöd!', //error message when you try to create a hero when you have one.
	'youarebanned' => "Sajnos ki vagy tiltva a játékból. Írj az adminnak a ${config['adminMail']} e-mail címen, hogy megtudd, mi a helyzet.", 		// fatal error in the login form
	'youareinvitedtoguild'=>'Klánmeghívást kaptál', // guild invitation report title
	'youcancreateanewhero'=>'Kinevezhetsz új hőst, ha kiválasztod a falut, ahol szeretnéd, majd kattintasz a "Hős kinevezése itt" linkre.', // in no hero window
	'youcandelegatedeputy'=>'Megbízhatsz valakit, hogy helyettesítse az accountodat. Amint beírtad helyettesnek, ki leszel jelentkeztetve, és a helyettesítés addig, tart, amíg be nem lépsz, és vissza nem mondod a helyettesítést.', // in the deputies window
	'youcanreenterthetutorial'=>'Most már nem fog felbukkanni az ablak, ha belépsz. Az útmutatót továbbra is elérheted a súgóból.', // when finishing the tutorial.
	'youcantattackasdeputy'=>'Helyettesként nem támadhatsz!', // Error message when you logged in as deputy
	'youcantgiveawayasdeputy'=>'Nem adhatod át a csapatokat másnak, helyettesként!', // Error message when you logged in as deputy
	'youcantsendtroopstothisuser'=>'Nem küldhetsz ennek a játékosnak csapatokat, mert a játékban eltöltött időtök aránya túl nagy. ({1}× vagy nagyobb időkülönbség)', // error message when you attempt to send troops
	'youchoosetoabandonthisvillage'=>'Azt választottad, hogy elhagyod az alábbi falut. Ha az elhagyás mellett döntesz, akkor a falu nem lesz senkié. Senkinek se fog aranyat termelni, és bárki támadja csak nullát visz tőle. A faluban maradt egységek harcolni fognak, bárki is támadja a falut. A kiképzésre betett katonák ki fognak képződni és a faluban maradnak. A terjeszkedési pontot visszakapod az elhagyás után.', // in abandon village window
	'youdeputize'=>'A kövekező játékosokat helyettesíted: ', // in the deputies window
	'youdismissedtheguild'=>'Feloszlattad a klánod!', // the success dialog when you dismissed your own guild.
	'youdonthavehero'=>'Jelenleg nincs hősöd', // in no hero dialog
	'youdonthavevillage' => "Jelenleg nincs falvad.", 		// warning message in the game view
	'youfinishedthetutorial'=>'Befejezed az útmutatót', // when finishing the tutorial
	'yougotexpansionpoints'=>'{1} db terjeszkedési pontot kaptál. ', //in the check referredplayerwindow
	'youhavebeendeputized'=>'{1} helyettesít téged.', // when you log in while you are deputized. ({1} The name of the deputy)
	'youhavebeenkicked'=>'Elbocsátottak a klánból' ,// farewell report title when you got kicked from the guild...
	'youlefttheguild'=>'Elhagytad a klánt!', // when you leave a guild.
	'youmustgiveawaymasteraccessfirst'=>'Először át kell adnod a főkirály címet, hogy elhagyhasd a királyságot.', // in edit kings window
	'youraccountunderdeletion'=>'A felhasználód törlődni fog {1} időpontban, ha nem lépsz be.', // in account deletion box
	'youractivity'=>'Te aktivitásod', // in the activity plot
	'youravatar' => "Profilkép: ",		// in the registration form
	'yourbrowsernotsupportiframe'=>'Hoppá! A böngésződ nem támogatja az IFRAME-tagot, használd ezt a linket, hogy megnyisd az oldalt egy felbukkanó ablakban: <a href="javascript:void(window.open(\"{1}\"))">{1}</a>', //
	'yourheroinvillage'=>'A hős a következő faluban tartozkodik: {1}', // in hero window. {1}: village as link // TODO (wishlist): Should be heroisinvillage
	'yourheroisinthisvillage'=>'A hősöd ebben a faluban tartozkodik.', // in village info window
	'yourheroismoving'=>'A hős mozgásban van.', // in the view hero page. // TODO (wishlist): Should be heroismoving
	'yourreferreds'=>'Te meghívottjaid: ' // in inviteplayer window
);

$_languageIncluded;
if (!isset($_languageIncluded))
{
	$_languageIncluded=true;
	include("hu.php"); // include self once to resolve forward refences in the lang file
}


?>
