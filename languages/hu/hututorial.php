<?php

global $language;

$tutorialText=array
(
	0=> <<< X
	<div class="left">
		<h1>Üdvözlünk a ${language['wtfbattles']} játékban!</h1>
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
		<h1>Üdvözlünk a ${language['wtfbattles']} játékban!</h1>
		<p>A játék célja az, hogy hónapokon át egymással háborúzva, falvakat alapítva és rombolva, a ti klánotok legyen a szerveren a legnagyobb klán! </p>
		<h2>Stratégia, taktika és diplomácia &ndash; kevesebb grindelés</h2>
		<p>
			A játék tervezésekor az volt a fő a cél, hogy minél kevesebb legyen falvakkal való pepecselés. Ezért van tömeges egységképzés, és tömeges építkezés, hogy néhány kattintással azonnal tudj építeni és katonákat képezni a falvaidban.
			Így elég csak az akciókra koncentrálnod.
		</p>
		<p>Ez az ablakot bármikor bezárhatod. A bal felső sarkán lévő <i>Bezárás</i> linkkel. Ha újra elő szeretnéd hozni, akkor baloldali menüben az <i>Útmutató</i> menüpontra kattintva tekintheted meg.</p>
		<h2><a href="tutorial.php?step=2">Kattints ide, hogy megismerd az egyszerű kezelőfelületet!</a></h2>
X
,
	2=> <<< X
		<h1>Egyszerű kezelőfelület</h1>
		<p>A játék kezelése egyszerű:</p>
		<ul style="text-align: left">
			<li>A térképet az (bal) egérgomb lenyomva tartásával bármerre görgetheted.</li>
			<li>A játékon belüli ablakokat arrébb lehet húzni.</li>
			<li>Kattints egy falura, hogy kiválaszd és megtekintsd az adatait.</li>
			<li>A Ctrl lenyomva tartása mellett húzótéglalappal több falut is kijelölhetsz.<small>(a kijelölő téglalap kapcsolja a kijelölést. A kijelöltekről leveszi, a jelöletleneket kijelöli)</small></li>
			<li>A shift-et lenyomva tartva, és a térkép egy mezőjére kattintva akciót indíthatsz a kiválasztott falvakból.</li>
		</ul>
		<h2><a href="tutorial.php?step=3">Következő oldal: építkezés és falvak szerzése</a></h2>
		<hr>
		<p>Előző oldalak:</p>
		<ul>
			<li><a href="tutorial.php?step=1">Üdvözlünk a ${language['wtfbattles']} játékban!</a></li>
		</ul>
X
,
	3=> <<< X
		<h1>Építkezés és falvak szerzése</h1>
		<h2>Építkezés</h2>
		<p>A falvak építési pontokat termelnek, minden nap 1 db-ot. Az építési pontok felhasználhatók épületek fejlesztésére. A fejlesztés azonnal megtörténik, nem kell várakozni az építés befejezésére. </p>
		<h2>Falvak szerzése</h2>
		<p>A játékban kétféleképpen szerezhetsz falut:</p>
		<ul style="text-align: left">
			<li><b>Alapítasz:</b> egy üres mezőn egy újabb kis falut alapíthatsz, és építhetsz fel.</li>
			<li><b>Foglalsz: </b> harcokkal elveszed a falut másoktól. (így nem kell minden falut nulláról kiépítened.)</li>
		</ul>
		<h2><a href="tutorial.php?step=4">Következő oldal: épületek és egységek</a></h2>
		<hr>
		<p>Előző oldalak:</p>
		<ul style="text-align: left">
			<li><a href="tutorial.php?step=1">Üdvözlünk a ${language['wtfbattles']} játékban!</a></li>
			<li><a href="tutorial.php?step=2">Egyszerű kezelőfelület</a></li>
		</ul>
		
X
,
	4=> <<< X
		<h1>Épületek és egységek</h1>
		<h2>Egységek</h2>
		<p>A játékban ötféle egység van, és egyszerű kő &ndash; papír &ndash; olló szabály van köztük: <span style="white-space: nowrap"><i>lándzsás</i> => <i>lovas</i> => <i>íjász</i> => <i> lándzsás.</i></span></p>
		<p>A másik két egység a <i>katapult</i>, amellyel a falvakat rombolhatod, illetve a <i>diplomata</i>, amelyekkel a falvakat foglalhatod el, vagy újakat alapíthatsz vele.</p>
		<h2>Épületek</h2>
		<p>Szintén egyszerű az építési rendszer. Az épületek tetszőleges szintig növelhetők, bár az áruk meredek növekedése miatt egy szint után nem éri meg tovább húzni őket.</p>
		<p>A következő épületek vannak a játékban:</p>
		<ul style="text-align: left">
			<li>
				<b>Egységképzők: </b> ezek meggyorsítják egyes egységek kiképzését. A <i>laktanya</i> a <i>lándzsás</i>, az <i>íjászlőtér</i> az <i>íjász</i>, a <i>lovarda</i> a <i>lovasok</i>, a <i>műhely</i> a <i>katapultok</i>
				kiképzését gyorsítja.
			</li>
			<li><b>Aranybánya: </b>ingyenes épület. Ahány szintű, annyiszor 10 aranyat termel óránként.</li>
			<li><b>Fal: </b>minden egyes falszinttől a faluban védő egységek ereje megtöbbszöröződik. 1. szinten kétszeres, 2. szinten háromszoros erejű a védelem, és így tovább.</li>
			<li><b>Városközpont: </b>a kiépítésével gyorsíthatod az építési pontok termelődését, így a falvaidat gyorsabban építheted. Továbbá ez az épület megnöveli a diplomaták kiképzésének a sebességét is.</li>
		</ul>
		<h2><a href="tutorial.php?step=5">Következő oldal: harc menete és hősök</a></h2>
		<hr>
		<p>Előző oldalak:</p>
		<ul style="text-align: left">
			<li><a href="tutorial.php?step=1">Üdvözlünk a ${language['wtfbattles']} játékban!</a></li>
			<li><a href="tutorial.php?step=2">Egyszerű kezelőfelület</a></li>
			<li><a href="tutorial.php?step=3">Építkezés és falvak szerzése</a></li>
		</ul>
		
X
,
	5 => <<< X
		<h1>Harc menete</h1>
		<p>A játék során előbb utóbb harcokra is sor kerül majd. A harc a falvakban zajlik. Valaki támadja a falut, a faluban állomásozó egységek pedig megvédik azt. Amikor támadást indítasz, akkor a sereg elindul a célfalu felé, megtámadja azt, majd 
		a túlélők hazatérnek az esetleges zsákmánnyal.</p>
		<p>Háromféle támadási lehetőség van:</p>
		<ul style="text-align:left">
			<li><b>Utolsó emberig tartó támadás: </b>Valamelyik harcoló félnek az összes embere meg fog halni. Ha a támadó fél hal meg, akkor a támadást indító játékos nem fogja megtudni, hogy a falut mivel védték.</li>
			<li><b>Rablótámadás: </b>Nincs utolsó emberig tartó harc, általában van néhány túlélő.</li>
			<li><b>Kémtámadás: </b>A sereg megtámadja a falut, majd gyorsan szétszóródik. Általában a falu védelme utánuk ered, és sokat le is mészárolnak belőlük. Az esetek nagy részében, még nagy túlerő esetén is, marad 1-2 egység, aki meg tudja 
			majd mondani, hogy hányan védték a falut. Kémtámadás esetén a sereg semmilyen sákmányt nem visz haza.</li>
		</ul>
		<p>A támadott fél is fogja látni, hogy mely a falujára megy a támadás. </p>
		<p>A támadás időpontjában a támadó és a védekező fél is egy jelentést kap a támadás kimenetéről.</p>
		<h2>Hősök</h2>
		<p>Minden játékosnak van egy hőse. Minden egyes túlélt csata (amikor a mi oldalunkon van túlélő) után tapasztalatot  szerez. Ha védekezünk, akkor védőtapasztalatot, ha támadunk, akkor támadótapasztalatot. Ha elegendő tapasztalat összejön,
		akkor a hős szintet lép. Minden egyes szint 10%-kal erősíti a vezetett sereg támadó vagy védőerejét, így elegendő szint után egész erőseket lehet támadni vagy védeni, hogy ha erős hős vezeti a sereget.</p>
		<p>Ha a hőssel vezetett sereg elveszti a csatát, akkor hős elmenekül a faluból, és nem engedelmeskedik neked többé. Egy véletlenszerűen kiválasztott faluban újra feltünhet, akkor azon falu tulajdonosa besorozhatja, ha nincs épp hőse.</p>
		<hr>
		<p>Most már szerintem eleget tudsz a játékról. A <a href="help.php">súgóban</a> minden nagyon részletesen le van írva. Kattints <a href="tutorial.php?turnoff">ide</a>, hogy kilépj az útmutató módból: </p>
		<hr>
		<p>Előző oldalak:</p>
		<ul style="text-align: left">
			<li><a href="tutorial.php?step=1">Üdvözlünk a ${language['wtfbattles']} játékban!</a></li>
			<li><a href="tutorial.php?step=2">Egyszerű kezelőfelület</a></li>
			<li><a href="tutorial.php?step=3">Építkezés és falvak szerzése</a></li>
			<li><a href="tutorial.php?step=4">Épületek és egységek</a></li>
		</ul>
X
,
	6 => <<< X
	
X
);


?>
