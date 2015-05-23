<?php

global $language;

$tutorialText=array
(
	0=> <<< X
	<div class="left">
		<h1>Welcome to  ${language['wtfbattles']}!</h1>
		<h2>A játék célja</h2>
		<p>Ezen játék lényege az, hogy a környékbeliekkel szövetkezve, vagy éppen ellenük háborúzva világuralomra törj, és a te klánod uralja az egész világot.</p>
		<h2>Nézz körül!</h2>
		<p>
			Javaslom, hogy először nézz körül pályán, és mérd fel a helyzetet. A képernyő közepén a te falvad van most. A pályára kattintva, és az egér gombját lenyomva tartva görgetheted a pályát. Egy falura 
			kattintva megtekintheted annak a tulajdonságait, megnézheted a tulajdonosának és a tulajdonos klánjának a profilját. A falu pontja elárulja, hogy az mennyire van kiépítve. Azonban arról nem sok információt ad, hogy miféle épületek
			vannak benne.
		</p>
		<ul>
			<li>Ha a közvetlen környékeden több 100-200 pont feletti falu is van, akkor érdemes védekezésre berendezkedned.</li>
			<li>Ha körülötted a terep nagyjából üres, vagy csak kis falvak vannak, akkor lehet érdemes lesz elgondolkozni a támadójátékon. Bár erősen javasolt kezdetben védekező játékot játszani, és abból átmenni támadásba.</li>
		</ul>
		<h2>Zárd ezt az ablakot és nézz körül!</h2>
		<p>Nyilván ez az ablak útban van, ezért be kell zárnod, később újra megnyithatod, ha a bal felső menüben az "Útmutató" menüpontra kattintasz. Ha körülnéztél nyisd meg újra ezt az ablakot, és menj tövábbi a következő részre (kattints az alábbi linkre)!</p>
		<p><a href="tutorial.php?step=1000">Következő rész</a></p>
	</div>
X
,
	1000=> <<< X
	<div class="left">
		<h1>Eligazodás a játékban</h1>
		<h2>Levelek és jelentések</h2>
		<p>Mielőtt tovább mennénk érdemes lesz megismerkedni a két legfontosabb funkcióval, ez a levelezés és a jelentések; a baloldali menüben érhetőek ezek el. A levelek segítségével kommunikálhatsz más játékosokkal, a jelentések pedig a téged is
		érintő játékbeli eseményeket jelentik, pl. támadás ért, meghívtak a klánba stb.</p>
		<p>Ha új jelentésed van, akkor majd egy számot látsz kiírva majd menüben a jelentések vagy a levelek mellett; ilyenkor érdemes megnézni, hogy mi van.</p>
		<h2>Színek</h2>
		<p>A játékban különféle színek jelzik a falvaknál, hogy milyen viszony van.</p>
		<ul>
			<li>Kék: saját falvad</li>
			<li>Világoskék: klántag falva</li>
			<li>Fehér: semleges falu, támadható, megvédhető</li>
			<li>Zöld: béke, nem támadható, de nem is védhető</li>
			<li>Sárga: szövetséges, megvédhető</li>
			<li>Piros: háború, csak támadni lehet.</li>
		</ul>
		<h2>A falvad</h2>
		<p>Majd kattints a falvadra, ahol a sötétkék kis négyzet van, hogy megtekintsd, hogy mit tehetsz vele. Ehhez ismét be kell majd zárnod ezt az ablakot, de már tudod, hogy hogyan térhetsz ide vissza.</p>
		<hr>
		<p>Ha visszatértél lépj tövább a <a href="tutorial.php?step=2000">következő részre!</a></p>
		<hr>
		<p><a href="tutorial.php?step=0">Előző rész</a></p>
	</div>
X
,
	2000=> <<< X
	<div class="left">
		<h1>Falvad</h1>
		<p>Gondolom már szétnéztél a falvadban, és láttál egy pár dolgot. Ebben a részben azt tárgyaljuk, hogy mi mit jelent.</p>
		<h2>Építési pontok</h2>
		<p>Minden faluban minden nap képződik 1db építési pont. Az építési pontokat a falu építésére használhatod el, annyit építhetsz, amennyi építési pontod van, az építések egy bizonyos mennyiségű aranyba kerülnek.</p>
		<h2>Épületek</h2>
		<p>Láthattad, hogy 7db épületet építhetsz a faluban. Ezek közül 3 épület van, ami nagyon fontos:</p>
		<ul>
			<li>
				<img src="img/towncenter.png" alt="Városközpont"><b>Városközpont</b>: ennek segítségével növelheted az építési pont termelődését. Ha gyorsabban termelődik az építési pontod, gyorsabban építheted a falvadat.
				Az épület nagyon drága, így kezdetben nem éri meg építeni, de ahogy egyre több falvad lesz úgy érdemes majd elkezdeni ennek az építését is.
			</li>
			<li>
				<img src="img/goldmine.png" alt="Aranybánya"> <b>Aranybánya</b>: ez termeli az aranyat, folyamatos fejlesztése elengedhetetlen, hogy meglegyen az anyagi háttered a folyamatos és egyre gyorsabb seregképzéshez.
			</li>
			<li>
				<img src="img/wall.png" alt="Városfal"> <b>Városfal</b>: a középkorban egy maroknyi sereg elég volt arra, hogy megvédjenek egy várat. Ha egy szintet építesz a falra az olyan, mintha megdupláztad volna a seregedet, egy 2-es szintű
				fal már háromszoroz, egy 3. szintű fal négyszerez stb.
			</li>
		</ul>
		<p>A fennmaradó 4 épület segítségével a katonáid kiképzését gyorsíthatod meg; kaszárnya a lándzsás, íjászlőtér az íjász, lovarda a lovasok, műhely a katapultok kiképzésének a sebességét növeli.
		Azonban a kezdetben a képzés sebessége nem gond, mert a nincs sok bevételed, de később ez a helyzet megváltozik majd, és növelni kell ezen épületek szintjét is.</p>
		<h2>Egységek</h2>
		<p>A játékban ötféle egység van:</p>
		<ul>
			<li><img src="img/spearman.png" alt="lándzsás"> <b>Lándzsás</b>: olcsó egység, kíváló lovasok ellen, de az íjászok ellen nem sokat ér. Olcsósága miatt használható inváziós támadásra.</li>
			<li><img src="img/archer.png" alt="íjász"> <b>Íjász</b>: a falanxba rendeződött lándzsások ellen nagyon jó bevethető, egyszerűen csak be kell lőni a tömegbe. Azonban a lovasok gyorsan utólérik és levágják őket.</li>
			<li><img src="img/knight.png" alt="lovas"> <b>Lovas</b>: nagy sebességű páncélozott egység, az íjászokat könnyen le tudják vágni, azonban a falanxba rendeződött lándzsásokhoz nem igazán tudnak közel kerülni, így nem sok esélye van azok ellen.</li>
			<li><img src="img/catapult.png" alt="katapult"> <b>Katapult</b>: támadáskor a megtámadott városban épületeket rombolhatsz le vele, elsősorban a városfal ellen szokták bevetni.</li>
			<li><img src="img/diplomat.png" alt="diplomata"> <b>Diplomata</b>: ez egy nagyon lassan mozgó egység, melynek segítségével ellenséges városokat foglalhatsz el, vagy új falvakat alapíthatsz. Ha egy ellenséges falu elleni csatát
			megnyerünk, és van velünk diplomata, akkor átvesszük az irányítást azon falu felett. </li>
		</ul>
		<h2>Ideális egységösszeállítás kialakítása</h2>
		<p>A legtöbb seregösszetétel ellen csinálható azonos áron olyan összetételű sereg, amely azt leüti. Általában véve elmondható, hogy ha csak egyetlen fajta egységet csinálunk, akkor az nagyot tud ütni, de bukni is; két fajta már kevésbé;
			három fajtából készült sereg meg egyaránt jó és rossz bármi ellen.
		</p>
		<hr>
		<p><a href="tutorial.php?step=1000">Előző rész</a> | <a href="tutorial.php?step=3000">Következő rész</a></p>
	</div>
X
,
	3000=> <<< X
	<div class="left">
		<h1>Kezdő stratégia</h1>
		<p>Most eldöntheteted, hogy milyen stílusban szeretnél játszani.</p>
		<h2><a href="tutorial.php?step=4100">Támadó stratégia</a></h2>
		<p>Ebben az esetben azonnal el kezded képezni a katonákat és támadsz, hogy aranyat rabolj az inaktívaktól.</p>
		<h2><a href="tutorial.php?step=4200">Védekező stratégia</a></h2>
		<p>Ebben az esetben arra rendezkedsz be, hogy megvédd a falvaidat.</p>
		<h3>Válassz egyet a kettő közül!</h3>
		<hr>
		<p><a href="tutorial.php?step=2000">Előző rész</a></p>
	</div>
X
,
	4100=> <<< X
	<div class="left">
		<h1>Támadó stratégia</h1>
		<p>5 építési pontod van. Fejleszd az aranybányát ezekkel a pontokkal 6. szintre, így már is 60/h termelésed van. Ez máris elegendő arra, hogy megállás nélkül csináld az íjászt vagy a lándzsást a falvadban. 1 óra múlva már 6 lándzsásod vagy 
		4 íjászod lehet, amellyel már ki is rabolhatod a környező falvakat, hogy aranyat szerezz. Ha rablótámadásssal támadsz, akkor az esetek nagy részében van túlélőd, és így tudod, hogy hol védekeznek. Érdemes továbbá a katapultcélpontot is
		beállítani, hogy a jelentésben látszódjon a célpont szintje (még akkor is, ha nem volt katapult, ami rombolni tudott volna), így megtudhatod, hogy például mennyi a játékos falának a szintje. Ha valahol tiszta a terep, akkor oda eljárhatsz rabolni.</p>
		<p>Ha megtámadnak, akkor semmi baj, látni fogod szép pirossal, hogy valaki támadást indított ellened, megnézheted, hogy mikor érkezik; és az érkezés előtt támadsz a seregeddel valakire, elköltöd az aranyadat; így keveset rabol el majd, és a seregedet sem éri majd veszteség.</p>
		<p>Persze holnap, amikor megkapod az építési pontodat, rakd azt falra a biztonság kedvéért.</p>
		<h2>A következő részben megtudhatod, hogyan kell támadni; megnézni, hogy ki támad; és hogyan fejlessz falut.</h2>
		<hr>
		<p><a href="tutorial.php?step=3000">Előző rész</a> | <a href="tutorial.php?step=5000">Következő rész</a></p>
	</div>
X
,
	4200=> <<< X
	<div class="left">
		<h1>Védekező stratégia</h1>
		<p>Ebben az esetben arra törekszel, hogy ha valaki megtámad, azt megállítsák a katonáid a fal mögött. Kétféle lehetőség van:</p>
		<ul>
			<li><b>Magadat véded:</b> ekkor arra törekszel, hogy minél gyorsabban növeld a véderődet. Ekkor azt javaslom, hogy a kezdő 5 építési pontodból kettőt falra, négyet pedig aranyra nyomj el így van 40/h termelésed, amelyből óránként
			4 lándzsást tudsz csinálni, de a kettes falad ezt háromszorosára erősíti; így olyan, mint ha 12 lenne. Íjászból is majdnem 3-at ki tudsz hozni minden órában. Így két veled együtt kezdő támadójátékos ellen gyakorlatilag védve vagy.</li>
			<li><b>Mások védenek:</b> ez az az eset, amikor egységeket kapsz valakitől, hogy megvédjen (pl. sikerült felvetetned magad egy klánba), amíg te gyűjtöd az aranyadat, hogy meglegyen az 1000 és diplomatát képezhess. Sok egységet biztos nem kapsz, ezért azt a keveset kell
			minél nagyobb védelemre felturbóznod. Mind az 5 építési pontodat rakd falra, innentől kezdve ne csinálj semmit, ha megérkezik az erősítés nézd meg, hogy mennyi, aztán holnap jelentkezz be újra, és nyomd a következő pontot aranyra, és
			így tovább, aztán már alapíthatod is a következő falut, ahonnét majd fokozatosan felgyorsulnak az események.</li>
		</ul>
		<h2>A következő részben megtudhatod, hogyan kell támadni, megnézni, hogy ki támad, és hogy hogyan fejlessz falut.</h2>
		<hr>
		<p><a href="tutorial.php?step=3000">Előző rész</a> | <a href="tutorial.php?step=5000">Következő rész</a></p>	
	</div>
X
,
	5000=> <<< X
	<div class="left">
		<h1>Támadás, falufejlesztés</h1>
		<h2>Falufejlesztés</h2>
		<p>A falvadban értelemszerűen a fejlesztés gombra kattintva tudsz épületet fejleszteni, a kiképzés gombra kattintva egységet képezni (be kell írni a számot, hogy mennyit akarsz, és entert nyomni). Bármit is csinálsz, a böngészőablak
		jobb alsó részén lévő linkre kattintva végre kell hajtani a feladatokat, hogy azok ténylegesen megtörténjenek.</p>
		<h2>Támadások indítása</h2>
		<p>Válaszd ki a falut, amelyből támadást akarsz indítani, nyilvánvalóan a sajátodból tudsz csak indítani, kattints rá, majd a shift-et nyomvatartva kattints a célfalura. Meg fog jelenni egy ablak, hogy mit szeretnél, ott vannak olyanok is, mint
		pl. "utolsó emberig tartó támadás" vagy olyan, hogy "fosztogatás". A súgóban le van írva, hogy ezek között mi a különbség. kattints rá a kívánt műveletre, válaszd ki az egységeket, amit indítani akarsz, és mehet is.</p>
		<h2>Annak megtekintése, hogy ki támad</h2>
		<p>Bárki rád támad, de ha te magad mozgatsz egységeket a saját vagy más falvába, a bal alsó területen megjelenik, hogy milyen típusú dolog történik, és hogy mennyi. Kattints rá az ott megjelenő számra, hogy megtudd, hogy mikor történik az
		az esemény, egy ablakban fog majd megjelenni.</p>
		<h2>Itt a vége</h2>
		<p>A <a href="help.php">súgóban</a> mindenről sokkal részletesebb leírást találhatsz. Ha olyan kérdésed van, amelyre a súgóban sem találsz választ, írj az adminnak (${config['adminName']}) egy belső levelet,
			vagy írj neki egy e-mailt a ${config['adminMail']} címre.</p>
		<hr>
		<p><a href="tutorial.php?step=3000">Előző rész</a> | <a href="tutorial.php?turnoff">Kilépés az útmutatóból</a></p>	
	</div>
X
,
	1=> <<< X
		<h1>Welcome to ${language['wtfbattles']}!</h1>
		<p>The aim of the game is building, destructing villages, and battling with each other, to make your guild the biggest on the server! (Your guild must own the 80% of the building levels)</p>
		<h2>Strategy, Tactics and Diplomacy &ndash; less grinding</h2>
		<p>
			While designing the game the main approach was to decrease the amount fiddling needed with the villages. Therefore, There are options for massive unit training and massive building to make you able to manage your villages 
			with few clicks, so you can focus on your attacks and actions.
		</p>
		<p>You can close this window whenever you want, use the Close link at left top corner of this window. In the left menu, use the <i>Tutorial</i> menu item to bring this back.</p>
		<h2><a href="tutorial.php?step=2">Click here to learn about the simple user interface.</a></h2>
X
,
	2=> <<< X
		<h1>The simple user interface</h1>
		<p>The usage is simple:</p>
		<ul style="text-align: left">
			<li>You can scroll the by holding down the left mouse button.</li>
			<li>All windows in the game (except the corner menus) can be dragged anywhere this way.</li>
			<li>Click on a village to see its properties. If the village is yours, you can manage that village.</li>
			<li>
				By holding down the Ctrl key, you can drag selection rectangles to select multiple villages (the selection rectangle is in toggle mode, so you can both select and unselect with it).</li>
			<li>By holding down the Shift key, you can start actions from the selected villages on the clicked map cell.</li>
		</ul>
		<h2><a href="tutorial.php?step=3">Next page: Building and getting more villages.</a></h2>
		<hr>
		<p>Előző oldalak:</p>
		<ul>
			<li><a href="tutorial.php?step=1">Welcome to ${language['wtfbattles']}!</a></li>
		</ul>
X
,
	3=> <<< X
		<h1>Building and getting more villages</h1>
		<h2>Building</h2>
		<p>Villages produce build points, one point every day. These build points can be used to upgrade the buildings in the villgaes. The upgrade happens instantly, no need to wait for it to complete.</p>
		<h2>Getting more villages</h2>
		<p>There are two ways to get more villages:</p>
		<ul style="text-align: left">
			<li><b>Found:</b> You can create new village on an empty cell and build it up.</li>
			<li><b>Conquer: </b> You take the control over the village with battle, so you don't need to build it up from zero. This is the better but more risky approach.</li>
		</ul>
		<h2><a href="tutorial.php?step=4">Next page: units and buildings</a></h2>
		<hr>
		<p>Previous pages:</p>
		<ul style="text-align: left">
			<li><a href="tutorial.php?step=1">Welcome to ${language['wtfbattles']}!</a></li>
			<li><a href="tutorial.php?step=2">The simple user interface</a></li>
		</ul>
		
X
,
	4=> <<< X
		<h1>Units and buildings</h1>
		<h2>Units</h2>
		<p>There are five kinds of units in the game. Simple stone-paper-scissors rule applies for 3 of them:<span style="white-space: nowrap"><i>spearmen</i> beat <i>knights</i>, <i>knights</i> beat 
		<i>archers</i>, <i>archers</i> beat <i>spearmen</i>.</span></p>
		<p>The other two type is the catapult, which is used to destroy enemy buildings, and the diplomat, which is used to conquer or settle villages.</p>
		<h2>Buildings</h2>
		<p>The build system is simple: every building can be upgraded to an arbitrary level, though the upgrade costs are increasing rapidly, so it not worth upgrading after a level.</p>
		<p>There are 7 buildings in the game:</p>
		<ul style="text-align: left">
			<li>
				<b>Unit producers: </b> these buildings speed the production of the units up. The <i>barracks</i> makes the training of the <i>spearmen</i> faster; the <i>archery range</i>, the <i>archers</i>;
				 the <i>stables</i>, the <i>knights</i>; the <i>workshop</i>, the <i>catapults</i>.
			</li>
			<li><b>Gold mine: </b>free building. Every level produces 10 gold per hour.</li>
			<li><b>City wall: </b> every level multiplies the strength of the defender army. On level 1 the defense is 2× stronger, on level 2 it will be 3× stronger, and so on.</li>
			<li><b>Town center: </b> by upgrading this building, you can speed up the production of the build points, so you can build your villages faster. This building also speeds up the training of the diplomats.</li>
		</ul>
		<h2><a href="tutorial.php?step=5">Next page: Battles and heroes</a></h2>
		<hr>
		<p>Previous pages:</p>
		<ul style="text-align: left">
			<li><a href="tutorial.php?step=1">Welcome to ${language['wtfbattles']}!</a></li>
			<li><a href="tutorial.php?step=2">The simple user interface</a></li>
			<li><a href="tutorial.php?step=3">Building and getting more villages</a></li>
		</ul>
		
X
,
	5 => <<< X
		<h1>Battles and heroes</h1>
		<p>Sooner or later in the game there will be battles. The battles take place in villages. Someone attacks the village, and the defenders will defend it. When you start and attack the army will go to the target village, attack it, thereafter the 
		survivors return with the looted gold</p>
		<p>There are 3 ways to attack:</p>
		<ul style="text-align:left">
			<li><b>Last man standing battle: </b>One side (either the attacker or the defender) will lose all his soldiers. If the attacker loses the battle no one will return to tell the story.</li>
			<li><b>Raid: </b>Some survivors usually manage to escape.</li>
			<li><b>Scout: </b> The army attacks the village, the scatters. The defender will begin to chase them and kill most of them. In most of the cases, there are survivors to tell how many soldiers defending the village, even there is
			a huge overpower on the defender side. The survivors don't carry loot, because they don't ever the village.</li>
		</ul>
		<p>The defender side see which village of his villages is going to be attacked.</p>
		<p>At the moment of the attack, both the defender and the attacker side get a report about the results of the battle.</p>
		<h2>Heroes</h2>
		<p>Every player has one hero who will get experience after every battle he survives. If we are on the defender side, the hero gets defense experience, if we attack with the hero, the hero gets offense experience.
		If hero have enough experience it will level up. Every offense or defense level increases the strength of the army, the hero leads, with 10% per level. So an army can be very strong when a skilled hero leads it.</p>
		<p>However, if army led by our hero loses the battle, the hero will escape from the battle and won't serve you anymore. He may show up in a random village and the owner of the village can recruit the hero, if don't have one currently.</p>
		<hr>
		<p>Now I think you know the basics of the game. Every written in greater detail in the <a href="help.php">Help</a>. Click <a href="tutorial.php?turnoff">here</a> to finish the tutorial.</p>
		<hr>
		<p>Previous pages:</p>
		<ul style="text-align: left">
			<li><a href="tutorial.php?step=1">Welcome to ${language['wtfbattles']}!</a></li>
			<li><a href="tutorial.php?step=2">The simple user interface</a></li>
			<li><a href="tutorial.php?step=3">Building and getting more villages</a></li>
			<li><a href="tutorial.php?step=4">Units and buildings</a></li>
		</ul>
X
,
	6 => <<< X
	
X
);


?>
