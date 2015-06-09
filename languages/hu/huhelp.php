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
	return str_replace('.',',',$input);
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
		<title>WTFBattles súgó</title>
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
		<h1>WTFBattles II súgó</h1>
		<div>
			<a href="javascript:void(toggleElement('userinterface'))">Használat és kezelőfelelület</a><br>
			<div id="userinterface" class="helpdiv">
				<p>Egyelőre csak a legfontosabbakkat írnám le, hogy hogy van.</p>
				<a href="javascript:void(toggleElement('menudescription'))">Menüpontok rövid leírása</a><br>
				<div id="menudescription" class="helpdiv">
					<ul>
						<li>Játékmenü</li>
							<ul>
								    <li><b>Levelek</b>: Itt érheted el a levelezést, kommunikálhatsz a többi játékossal. A menü mellett megjelenő szám azt jelenti, hogy új leveled van.</li>
								    <li><b>Jelentések</b>: A játékban történő eseményekről itt kapsz értesítést. A menü mellett megjelenő szám azt jelenti, hogy új jelentésed van.</li>
								    <li><b>Mostanában történt</b>: A játékvilágban mostanában történt eseményeket tekintheted meg.</li>
								    <li><b>Jegyzetek</b>: Jegyzeteket írhatsz magadnak, és a királyságot irányító többi felhasználónak.</li>

								    <li><b>Faluösszesítő</b>: Áttekintést nézhetsz meg az összes falvadról.</li>
								    <li><b>Tömeges egységképzés</b>: Egységképzés optimális elosztása több falu között</li>
								    <li><b>Tömeges építkezés</b>: Építkezés több faluban egyszerre (nem kell kézzel végigzongorázni a falvakat)</li>
								    <li><b>Hős megtekintése</b>: Itt tekintheted meg a hősödet, vagy besorozhatsz egy szabad hőst, ha hozzád érkezik meg.</li>

								    <li><b>Jegyzetek</b>: Magadnak írhatsz megjegyzéseket.</li>
								    <li><b>Csataszimulátor</b>: Csatákat szimulálhatsz itt. Ebben a szimulátorban ugyanaz a szimuláció fut, ami a játékban is.</li>
								    <li><b>Heti orákulum</b>: Itt a heti top10-eket tekintheted meg. Különféle kategóriákban.</li>
								    <li><b>Súgó</b>: Nem szorul magyarázatra.</li>

								    <li><b>Kijelentkezés</b>: Ha nem szeretnéd, hogy más a nevedben játsszon, amikor ahhoz a géphez ül, ahonnét felálltál. Mindenképp jelentkezz ki.</li>
							</ul>
						<li>Közösség</li>
							<ul>
							    <li><b>Helyettesítés</b>: Megbízhatsz egy másik királyságot, hogy helyettesítse a tiéteket.</li>
							    <li><b>Klán</b>: A klánod oldala. Itt végezheted a klánnal kapcsolatos dolgokat. Már ha a klánvezér adott jogot hozzá.</li>
							    <li><b>Királyság profilja</b>: A királyságod profilja.</li>
							    <li><b>Profilom</b>: a saját profilod.</li>
							    <li><b>Királyok kezelése</b>: A királyságotok királyait kezelhetitek itt.</li>
							    <li><b>Fórum</b>: A weboldalam nyilvános fórumát érheted el ezen a menüponton keresztül.</li>
							</ul>
						<li>Extrák</li>
							<ul>
								<li><b>Játékos meghívása a játékba</b>: Szerezz terjeszkedési pontokat azzal, hogy a játékot terjeszted!</li>
								<li><b>Aktivitás megtekintése az accountodban</b>: itt nézheted meg, hogy mikor volt aktivitás az accountodban.</li>
							</ul>
						<li><b>Térképműveletek</b>: Ebben a menüpontban ugorhatsz a térképen megadott koordinátákra.</li>
						<li><b>Egérműveletek</b>: Hasznos ha tenyérgépről játszol, mert azon nincs Shift meg Ctrl.</li>
					</ul>
				</div>
				<a href="javascript:void(toggleElement('readlettersreports'))">Levelek és jelentések olvasása</a><br>
				<div id="readlettersreports" class="helpdiv">
					<p>Ha a bal oldalon fent a levelek és jelentések mellet zárójelben számot látsz, az azt jelenti, hogy új leveled, jelentésed van, kattints rá a szövegre ekkor. A többinek magától értetődőnek kellene lennie.
					A levelezés szál alapú. Tehát nem üzeneteket küldöztök, hanem valaki, aki levelet ír, az elkezd egy szálat, és felíratkoztatja a címzettet, így egy hosszú levélváltás után nem egy halom levél van, hanem csak egy.
					A "levél törlése" a leíratkozás, ha leíratkozol, nem kapod meg a választ sem, hacsak nem a küldő ezt észreveszi, és újra hozzá nem ad a résztvevőkhöz.</p>
				</div>
				<a href="javascript:void(toggleElement('writeletters'))">Levelek írása</a><br>
				<div id="writeletters" class="helpdiv">
					<p>Egy játékosnak úgy írhatsz levelet, hogy a faluját megnyitod, majd ott a tulajdonos nevére kattintva megnyitod a profilját, és ott van olyan, hogy üzenet írása neki. De úgy is megteheted, hogy a Levelek menüben
					kattintasz az új levél írása, majd a "Résztvevő hozzáadása: " mezőbe beírod a nevét, akinek írni akarsz. Ha levélre válaszolsz nem kell oda beírni semmit, kivéve, ha egy harmadik félnek is szeretnéd továbbítani az üzenetet.</p>
				</div>
				<a href="javascript:void(toggleElement('build'))">Épületek építése a falvakban</a><br>
				<div id="build" class="helpdiv">
					<p>Épületeket úgy építhetsz a falvadban, hogy az ikonjukat ábrázoló gombra kattintasz, ekkor a feladat listába bekerül a művelet, amit végre kell hajtanod, hogy ténylegesen megtörténjen az építés. Az építkezés építési
					pontokkal történik (illetve aranyba is kerül)), egy faluban annyit építhetsz, amennyit az építési pontok engednek. Minden nap 1 építési pont termelődik a faluban, de ezt a termelést lehet növelni, a városközpont fejlesztésével.</p>
				</div>
				<a href="javascript:void(toggleElement('trainunits'))">Katonák kiképzése</a><br>
				<div id="trainunits" class="helpdiv">
					<p>Az egységek neve alatti szövegdobozokba számot beírva majd entert nyomva tehetsz be katonákat a kiképzésre.
					A megfelelő mennyiségű aranyat levonja majd. A véglegesítéshez végre kell hajtanod a feladatlistát (jobbra lent van). </p>
				</div>
				<a href="javascript:void(toggleElement('renamevillage'))">Falu átnevezése</a><br>
				<div id="renamevillage" class="helpdiv">
					<p>Nagyjából magától értődő, hogy hogy kell ezt csinálni. Válaszd ki a falut, kattints az átnevezésre, írd be az új nevet, majd üss entert. A feladatlista végrehajtása után kis idővel átneveződik a falu.</p>
				</div>
				<a href="javascript:void(toggleElement('movetroops'))">Csapatok mozgatása</a><br>
				<div id="movetroops" class="helpdiv">
					<p>Válaszd ki az indítófalut, amiből egységet kívánsz mozgatni. Majd shift+click a célfalun. A többi szerintem magától értetődö lesz,
					ha ez nem működik. Kattinthatsz a baloldali rádiógombokon (az egérműveletek menübe van elrejtve már egy ideje).</p>
				</div>
				<a href="javascript:void(toggleElement('settlevillages'))">Új falvak alapítása</a><br>
				<div id="settlevillages" class="helpdiv">
					<p>Válaszd ki az indítófalut, amiből egységet kívánsz mozgatni. Majd shift+click a célmezőn. A többi szerintem magától értetődö lesz,
					ha ez nem működik. Kattinthatsz a baloldali rádiógombokon. Csak akkor működik az alapítás, ha van diplomata a faluban, és van legalább 1 egész terjeszkedési pontod.</p>
				</div>
				<a href="javascript:void(toggleElement('managemultiplevillages'))">Több falu kezelése egyszerre.</a><br>
				<div id="managemultiplevillages" class="helpdiv">
					<p>A Ctrl nyomvatartása közben kiválasztó téglalapot húzhatsz, így több falut kiválaszthatsz egyszerre, és csapatokat küldhetsz belőlük.</p>
				</div>
				<p>Csak a legfontosabb funkciókat írom ide le. Termszetesen ez a lista bővülni fog az értetlenkedők számától függően.</p>
			</div>
			<a href="javascript:void(toggleElement('tutorial'))">Tippek kezdőknek</a><br>
			<div id="tutorial" class="helpdiv">
				<a href="javascript:void(toggleElement('forattackers'))">Támadóknak</a><br>
				<div id="forattackers" class="helpdiv">
					<dl>
						<dt>Soha ne támadj a teljes seregeddel!</dt>
							<dd>Ez a legjobb módja annak, hogy gyorsan elveszítsd mindened.</dd>
						<dt>Csak annyi katonával támadj, amennyi pont elég!</dt>
							<dd>Úgy tudod a veszteségeidet minimalizálni, hogy ha mindig a lehető legkevesebb egységet küldöd a csatába, amely a kívánt hatást eléri. Az erős falak miatt a támadóseregedet gyakran el fogod veszíteni.
							Veszíts minél kevesebb katonát.</dd>
						<dt>Bölcsen használd a támadásfajtákat!</dt>
							<dd>Utolsó emberig tartó támadásra csak akkor van szükség, ha rombolni akarsz a katapulttal, vagy foglalni. Minden más esetben használj rablótámadást vagy kémtámadást.</dd>
						<dt>Vigyázz a hősödre!</dt>
							<dd>Ha a hősöd vereséget szenved, akkor elhagy téged, és elindul egy véletlenszerűen kiválasztott faluba. Ha szerencséd van, akkor hozzád tér vissza, de ha nem, akkor másé lesz. Egy magas szintű hős
							nagy előnyt jelent támadáskor és védekezéskor egyaránt. Vigyázz rá!</dd>
						<dt>A támadások az utolsó pillanatig visszavonhatók.</dt>
							<dd>A támadásokat visszavonhatod az utolsó pillanatig, ha úgy tűnik, hogy reménytelen egy akció inkább vond vissza.</dd>
						<dt>Oszd szét a védekező fél figyelmét!</dt>
							<dd>Indíts több támadást az áldozatodra, így annak szét kell osztania az erőit, amikor védekezik, és könnyebben elfoglalható a kívánt falu.</dd>
					</dl>
				</div>
				<a href="javascript:void(toggleElement('fordefenders'))">Védekezőknek</a><br>
				<div id="fordefenders" class="helpdiv">
					<dl>
						<dt>Ne becsüld túl a falad védelmét!</dt>
							<dd>Valóigaz, hogy 5-6 szintes fal megvéd a támadások nagy részétől. De tartsd azt is észben, hogy a támadód kevés katapulttal is lerombolhatja a falad, utána pedig nem lesz falad, és a falu védtelen lesz.</dd>
						<dt>Használj minél több hőst!</dt>
							<dd>Ha klánoddal megbeszéled, és sok hőst sikerül a védendő faluba összehordani, akkor azok védelmi bónusza összeadódik, és kevés katonával is nagyokat lehet fogni.</dd>
						<dt>Működj együtt a klánoddal!</dt>
							<dd>Ha a védelmed vészesen fogy kérj egységeket a közeli klántársaktól.</dd>
						<dt>Állíts fel fontossági sorrendet</dt>
							<dd>Ha sok bejövő támadásod van sok falura, akkor döntsd el, hogy melyik elé érsz oda, és melyik elé érdemes beállni. Egy alacsony pontszámú falut felesleges védeni. Sőt az se baj, ha elveszíted, a terjeszkedési
							pontokat visszakapod érte, és foglalhatsz egy nagyobb falut helyette.</dd>
					</dl>
				</div>
				<a href="javascript:void(toggleElement('buildup'))">Építkezés</a><br>
				<div id="buildup" class="helpdiv">
					<dl>
						<dt>Falvak elhelyezése</dt>
							<dd>A sűrűn elhelyezet falvakat jól lehet védeni, míg a nagyobb területen szétszórt falvakból jobban ellenőrzés alatt lehet tartani az adott területet.</dd>
						<dt>Egységképzőkre sokáig nincs szükség</dt>
							<dd>Amíg az aranytermelésed kicsi, addig semmi értelme nincs emelni az egységképzőkön, mert úgy se lesz aranyad, hogy folyamatosan járasd.</dd>
					</dl>
				</div>
			</div>
			<a href="javascript:void(toggleElement('basics'))">Alapok</a><br>
			<div id="basics" class="helpdiv">
				<a href="javascript:void(toggleElement('gold'))">Arany</a><br>
				<div id="gold" class="helpdiv">
					<p>Az arany a játékban a fizetőeszköz. A jobb felső sarokban láthatod, hogy mennyi van.</p>
				</div>
				<a href="javascript:void(toggleElement('buildpoints'))">Építési pontok</a><br>
				<div id="buildpoints" class="helpdiv">
					<p>Az építési pontok (és arany) segítségével lehet az épületeket fejleszteni. Egy faluban minden nap 1 db építési pont termelődik, de ez a sebesség gyorsítható a városközpont építésével.</p>
				</div>
				<a href="javascript:void(toggleElement('expansionpoints'))">Terjeszkedési pontok</a><br>
				<div id="expansionpoints" class="helpdiv">
					<p>A terjeszkedési pontok határozzák meg, hogy mennyi falvad lehet. Minden nap kapsz 1db terjeszkedési pontot. Csak akkor alapíthatsz vagy foglalhatsz falut, ha van ilyen pontod. Ha falut veszítesz, akkor visszakapod
					a pontot.</p>
				</div>
				<a href="javascript:void(toggleElement('refreshes'))">Frissítések</a><br>
				<div id="refreshes" class="helpdiv">
					<p>A szerver terheltségét csökkentendő, csak akkor történik változás, ha te magad kéred meg rá a szervert.</p>
					<ul>
						<li>Bejelentkezéskor, vagy ha támadás ér, minden falvad állapota frissül.</li>
						<li>Minden percben lekéri a szervertől, hogy van-e új leveled, jelentésed stb. De te magad is kérhetsz azonnali frissítést, ha a frissítés mostra kattintasz.</li>
						<li>Ha egy falut több, mint 10 perce néztél még utoljára, akkor mikor meg szeretnéd nézni, automatikusan frissül; a következő kattintásra már a friss infó ugrik fel. Azonban ha húzó téglalappal kijelölsz
						egy csomó falut, akkor semmilyen frissítés nem fog történni.</li>
					</ul>
				</div>
				<a href="javascript:void(toggleElement('heroes'))">Hősök</a><br>
				<div id="heroes" class="helpdiv">
					<p>A hősök a hadvezéreid. Egyszerre csak 1 db hősöd lehet. Attól függően, hogy támadsz vagy védekezel vele, egyre jobb lesz a támadásban, illetve védekezésben. A hősnek nincs se támadó, se védekező
					értéke. Azonban a sereget, amit vezet erősíti.</p>
					<p>Ha a hős vezetésével a sereg egységeket győz le támadáskor, a hős támadóbónusza növekszik. Ha védéskor, akkor védekezőbónusz.</p>
					<p>A támadóerő bónusz az első néhány szintre a következő tábla szerint alakul:</p>
					<?php echo generateFnTable($config['heroAttackFormula'],'percentageFormat',0,20); ?>
					<p>A véderő bónusz az első néhány szintre a következő tábla szerint alakul:</p>
					<?php echo generateFnTable($config['heroDefendFormula'],'percentageFormat',0,20); ?>
					<p>A szintlépés mindkét képesség esetén azonos számú egység legyőzését jelenti. Minden egyes legyőzött egység 1XP pontot jelent. A következő tábla mutatja az első néhány szintre, hogy mennyi XP kell egy adott képességben való erősödéshez:</p>
					<?php echo generateFnTable($config['experienceFunctionInverse'],create_function('$input','return ceil($input)."&thinsp;XP";'),0,20); ?>
					<p>Ha a hősöd vereséget szenved, elhagy téged, és szabaddá válik. Támadás esetén a támadott faluból azonnal tovább indul egy véletlenszerűen kiválasztott faluba. Védőként a védett faluból szélednek szét a vereséget
						szenvedett hősök.</p>
					<p>A szabad hősök nem maradnak az idő végezetéig abban a faluban, ahol állomásoznak. Minden nap éjfélkor elindulnak egy véletlenszerűen kiválasztott faluba.</p>
					<p>Ha elveszítetted a hősödet, akkor két lehetőséged van:</p>
					<ul>
						<li>Kinevezel új hőst (aki tiszta lappal 0. szintről indul)</li>
						<li>Megvárod, míg véletlenül betoppan valamelyik szabad hős a falvadba, ha megérkezik szerződtetheted magadhoz, így téged fog szolgálni.</li>
					</ul>
				</div>
				<a href="javascript:void(toggleElement('troopmovement'))">Egységmozgások</a><br>
				<div id="troopmovement" class="helpdiv">
					<a href="javascript:void(toggleElement('heromovement'))">Hős mozgatása</a><br>
					<div id="heromovement" class="helpdiv">
						<p>A hőst a tulajdonos bármely faluba átküldheti. Csak a tulajdonos irányíthatja a hőst. Bármely faluba elküldhető a hős, és onnan továbbküldhető. Érdemes ezt észben tartani, mert ha egy hős érkezik a falvadba, azt
						nem feltétlenül a küldő falu tulajdonosa indítja.</p>
					</div>
					<a href="javascript:void(toggleElement('unitmovement'))">Egységek áthelyezése</a><br>
					<div id="unitmovement" class="helpdiv">
						<p>A faluban lévő egységek csapatmunka, közös védelem, támadás szempontjából összevonhatók. Érdemes több faluból egy faluba átzavarni az egységeidet, mielőtt támadsz.</p>
					</div>
					<a href="javascript:void(toggleElement('attack'))">Utolsó emberig tartó támadás</a><br>
					<div id="attack" class="helpdiv">
						<p>Ebben a harcfajtában a támadó vagy védő sereg minden embere el fog esni. Ha rombolni, vagy falut szeretnél foglalni, ilyen harctípust kell választanod. A túlélők hazatérnek a rabolt arannyal.</p>
					</div>
					<a href="javascript:void(toggleElement('raid'))">Fosztogatás</a><br>
					<div id="raid" class="helpdiv">
						<p>Ebben a harcfajtában nem utolsó emberig tart a harc. 1 katona gyakran hazatér, még nagy túlerő esetén is. Akkor érdemes használni, ha csak elrabolni szeretnéd az ellenség aranyát, és nem
						akarod mindenedet elveszteni. Ebben a módban nem tudsz rombolni, se falut foglalni.</p>
					</div>
					<a href="javascript:void(toggleElement('recon'))">Kémtámadás</a><br>
					<div id="recon" class="helpdiv">
						<p>Ebben a módban a sereg megtámadja a falut, de amint elkezdődik a csata azonnal el is menekülnek, és szétszélednek, így az esetek nagy részében néhány hazatér belőle, még nagy túlerő esetén is.
						Azonban, ha túl kevés katonával mész, akkor mindet lemészárolják.</p>
						<p>Érdemes akkor alkalmazni ezt a támadásformát, ha szeretnéd megtudni, hogy egy falu milyen erősen van védve. Ugyanis hatalmas védelem
						esetén is egy maroknyi seregből 1-2 ember hazatér, hogy megmondja, hogy mi a helyzet. Ebben a módban nem történik arany rablás.</p>
					</div>
				</div>
				<a href="javascript:void(toggleElement('conqeringvillages'))">Falvak elfoglalása</a><br>
				<div id="conqeringvillages" class="helpdiv">
					<p>Ha utolsó emberig tartó támadásban diplomata is megy, van 1 egész terjeszkedési pontod és a támadás sikeres, akkor a támadó elfoglalja a falut. A diplomata, aki ment a sereggel, átveszi a falu irányítását,
					és eltűnik. <i>Az építési pontok nullázodni fognak abban a faluban</i>.</p>
				</div>
				<a href="javascript:void(toggleElement('abandonvillages'))">Falvak elhagyása</a><br>
				<div id="abandonvillages" class="helpdiv">
					<p>Ha egy falura nincs szükséged, mert pl. egy nagyobbat akarsz helyette foglalni, akkor elhagyhatod azt, így a terjeszkedési pontot visszakaphatod, a falu nem lesz senkié sem.</p>
				</div>
				<a href="javascript:void(toggleElement('diplomacy'))">Diplomácia</a><br>
				<div id="diplomacy" class="helpdiv">
					<p>A klánok között különféle diplomáciai kapcsolat lehet. Ezt a falvak színén is lehet látni.</p>
					<ul>
						 <li>Saját falu (kék), klántárs (világoskék), szövetséges (sárga): ezeknek a falvaknak csak egységet adhatsz át, hogy segítsd őket</li>
						 <li>Béke (zöld): Ezeket a falvakat nem lehet támadni, se egységet küldeni nekik.</li>
						 <li>Semleges (fehér): ha nincs kapcsolat a két klán/játékos között, akkor ez érvényes. Ebben a módban szabad egységet küldeni, és támadni is.</li>
						 <li>Háború (piros): A piros falvakat csak támadni lehet.</li>
					</ul>
				</div>
				<a href="javascript:void(toggleElement('raidgold'))">Arany rablása</a><br>
				<div id="raidgold" class="helpdiv">
					<p>A játékos vagyona egyenletesen eloszlik a falvai között. Tehát ha valakinek 3000 aranya, és 6 falva van, az azt jelenti, hogy minden falvában van 500 arany. Csak egy faluból lehet elvinni az aranyat, így ezen játékos
					megtámadásával csak 500 aranyat hozhatsz el egyszerre. Minél több faluja van egy játékosnak annál többfelé oszlik a rabolható arany mennyisége.</p>
				</div>
				<a href="javascript:void(toggleElement('scoring'))">Falvak pontozása</a><br>
				<div id="scoring" class="helpdiv">
					<p>A falu pontszáma a faluban lévő épületek szintjének a négyzetösszege.</p>
				</div>
				<a href="javascript:void(toggleElement('nightbonus'))">Éjjeli bónusz</a><br>
				<div id="nightbonus" class="helpdiv">
					<p>A Budapesten érvényes napnyugtakor kezdődik, és az ottani napkeltekor ér véget. A bónusz maximális értéke: <?php echo $config['nightBonusMax']; ?>×. A bónusz, amint lemegy a nap, elkezd növekedni, amikor már
					<dfn title="A nap a csillagászi horizont alatt van 18°-kal.">teljesen sötét</dfn> van akkor éri el a maximumot. Ahányszoros az éjjeli bónusz annyiszorosára növekszik a <emph>védekező</emph> egységek
					ereje a falvakban, ezzel elvéve a kedvét a támadóknak attól, hogy éjszakára szervezzék az akciókat, amikor aludni kell.</p>
				</div>
				<a href="javascript:void(toggleElement('31rule'))">3:1 szabály</a><br>
				<div id="31rule" class="helpdiv">
					<p>Ez arról szól, hogy nem adhatsz át olyan játékosnak egységeket, akinek a játékban eltöltött ideje te eltöltött időd harmadánál kevesebb, vagy háromszor több. Így egy 30 napos játékos nem küldhet olyannak, aki
					10 napnál kevesebb ideje játszik, de egy olyannak sem, aki 90-nél több ideje.
					</p>
				</div>
				<a href="javascript:void(toggleElement('fakerule'))">Fake szabály</a><br>
				<div id="fakerule" class="helpdiv">
					<p>Ez azért van, hogy elkerüljük a flood szerű támadásokat, amelynek a célja csak az, hogy a játékosra olyan sok támadást indítsunk, hogy használhatatlanná válljon a bejövő támadások lap. Az elküldendő sereg értékének
					legalább az aranytermelésed 24%-ának kell lennie.</p>
				</div>
				<a href="javascript:void(toggleElement('randommovement'))">Menetidők véletlenszerűsége</a><br>
				<div id="randommovement" class="helpdiv">
					<p>A menetidők soha sem pontosak. Van benne ±1% hiba. Ez azért, hogy a védekező egy időzített támadássorozat közé tudjuk szúrni védelmet, vagy el tudjon menekülni a faluból.</p>
				</div>
				<a href="javascript:void(toggleElement('weeklyoracle'))">Heti orákulum</a><br>
				<div id="weeklyoracle" class="helpdiv">
					<p>Van egy hetente frissülő top10-es lista különféle kategóriákban. Ezt nevezik heti orákulumnak.</p>
				</div>
				<a href="javascript:void(toggleElement('agebonus'))">Korbónusz</a><br>
				<div id="agebonus" class="helpdiv">
					<p>Ha egy játékos később kezd el játszani, mint te, akkor neki korbónusza van veled szemben. Ez azt jelenti, hogy ha megtámadod, akkor neki annyiszor lesz erősebb a védelme, ahányszor kevesebb ideje játszik. Viszont nincs
					bónusza veled szemen, hogy ha ő támad. Tehát ha 50 napja játszol, és támadsz egy olyat, aki csak 10 napja játszik, akkor annak ötszörös védelmi bónusza lesz veled szemben. Ha 10 napos támad rád, akkor neki semmilyen
					előnye nem lesz. Szóval a korbónusz csak védelemkor érvényes.</p>
				</div>
			</div>
			<a href="javascript:void(toggleElement('units'))">Egységek</a><br>
			<div id="units" class="helpdiv">
				<a href="javascript:void(toggleElement('spearman'))">Lándzsás</a><br>
				<div id="spearman" class="helpdiv">
					<p>Ez az egység jó lovasok, rossz íjászok ellen. Egyedül elintéz egy lovast, egy másik lándzsást, de 2 kell egy íjász legyőzéséhez.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_0spearman']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('archer'))">Íjász</a><br>
				<div id="archer" class="helpdiv">
					<p>Ez az egység jó lándzsások, rossz lovasok ellen. Egyedül elintéz 2 lándzsást, 1 íjászt, de 4 db kell egy lovas legyőzéséhez.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_1archer']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('knight'))">Lovas</a><br>
				<div id="knight" class="helpdiv">
					<p>Ez az egység jó íjászok, rossz lándzsások ellen. Egyedül elintéz 4 íjászt, 1 lovast és 1 lándzsást.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_2knight']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('catapult'))">Katapult</a><br>
				<div id="catapult" class="helpdiv">
					<p>Ez az egység az ellenség épületeinek a lerombolására való. Támadóereje és védelme gyenge.</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_3catapult']));
						$tpl->render();
					?>
				</div>
				<a href="javascript:void(toggleElement('diplomat'))">Diplomata</a><br>
				<div id="diplomat" class="helpdiv">
					<p>
						Ez az egység használható új falvak alapítására, és elfoglalására. Ha egy utolsó emberig tartó támadást túlél, és a támadóoldalon van, azonnal átveszi az irányítást a falu felett. Kiképzése rendkívül lassú, és a sebessége is.
					</p>
					<?php
						$tpl=new Template('templates/unittable.php',array('unit'=>$config['units']['_4diplomat']));
						$tpl->render();
					?>
				</div>
			</div>
			<a href="javascript:void(toggleElement('buildings'))">Épületek</a><br>
			<div class="helpdiv" id="buildings">
				<a href="javascript:void(toggleElement('barracks'))">Laktanya</a><br>
				<div class="helpdiv" id="barracks">
					<p>A laktanya fejlesztésével gyorsabb a lándzsások kiképzése.
					A képzési idők az első néhány szinten következő táblázat szerint csökkennek:</p>
					<?php echo generateFnTable($config['buildings']['barracks']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('archeryrange'))">Íjászlőtér</a><br>
				<div class="helpdiv" id="archeryrange">
					<p>Az íjászlőtér fejlesztésével gyorsabb az íjászok kiképzése.
					A képzési idők az első néhány szinten következő táblázat szerint csökkennek:</p>
					<?php echo generateFnTable($config['buildings']['archeryrange']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('stables'))">Lovarda</a><br>
				<div class="helpdiv" id="stables">
					<p>A lovarda fejlesztésével gyorsabb a lovasok kiképzése.
					A képzési idők az első néhány szinten következő táblázat szerint csökkennek:</p>
					<?php echo generateFnTable($config['buildings']['stables']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('workshop'))">Műhely</a><br>
				<div class="helpdiv" id="workshop">
					<p>A műhely fejlesztésével gyorsabb a katapultok kiképzése.
					A képzési idők az első néhány szinten következő táblázat szerint csökkennek:</p>
					<?php echo generateFnTable($config['buildings']['workshop']['timeReductionFunction'],'percentageFormat',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('goldmine'))">Aranybánya</a><br>
				<div class="helpdiv" id="goldmine">
					<p>Az aranybánya fejlesztésével gyorsabban termelődik az arany.
					Az aranytermelés az első néhány szinten a következőképpen alakul:</p>
					<?php echo generateFnTable($config['buildings']['goldmine']['goldProductionSpeedFunction'],'identity',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('towncenter'))">Városközpont</a><br>
				<div class="helpdiv" id="towncenter">
					<p>A városközpont fejlesztésével gyorsabban termelődik az építési pont, és gyorsabb a diplomaták kiképzése.
					A képzési idők az első néhány szinten következő táblázat szerint csökkennek:</p>
					<?php echo generateFnTable($config['buildings']['townhall']['timeReductionFunction'],'percentageFormat',0,20); ?>
					<p>Az építési pontok előállításának a sebessége a következő módon alakul az első néhány szinten:</p>
					<?php echo generateFnTable($config['buildings']['townhall']['bpProductionSpeedFunction'],'multiplyFormat2Decs',0,20); ?>
				</div>
				<a href="javascript:void(toggleElement('wall'))">Városfal</a><br>
				<div class="helpdiv" id="wall">
					<p>A városfal alapvető fontosságú a város védelme szempontjából. Minden egyes fal szinttel többszörösére növeli a védelmi erőt (pl: a 3. szintű fal négyszeresére.)</p>
				</div>
			</div>
			<a href="javascript:void(toggleElement('battlesystem'))">Harcrendszer</a><br>
			<div class="helpdiv" id="battlesystem">
				<h3>Támadó és védekező vektorok</h3>
					<p>Minden egységtípusnak van egy támadó és egy védekező vektora, amely a gyalogos, íjász és lovas támadóerőkből és az azok elleni véderőből tevődik össze. Ezek a vektorok jelen pillanatban háromeleműek.
						Lándzsás támadóvektora pl. (60;0;0), a védekezővektora: (60;30;120).</p>
					<p>A támadósereg egységeinek a támadóvektorait össze kell adni, így kapsz egy össztámadóvektort.</p>
					<p>A védősereg egységeinek a védővektorait össze kell adni, így kapsz egy összvédővektort.</p>
					<p>Ezután támadóvektorra alkalmazni kell a hős támadóbónuszát, majd a védekezővektorra a fal bónuszát, éjjeli bónuszt, hős bónuszt, korbónuszt.</p>
					<p>A végleges össztámadóvektor legyen:  <b>a</b>=(a<sub>1</sub>,a<sub>2</sub>,a<sub>3</sub>).
						A végleges védővektor legyen:  <b>d</b>=(d<sub>1</sub>,d<sub>2</sub>,d<sub>3</sub>)</p>
				<h3>Erőarányok</h3>
					<p>Ez után ki kell számolni, hogy a támadósereg hányszor erősebb a védőseregnél, erre való a következő képlet:</p>
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
					<p>Ha k&gt;1, akkor a támadósereg az erősebb; ha k&lt;1, akkor  a védősereg erősebb.</p>
				<h3>Beütő katapultok száma</h3>
					<p>Az összes katapult csak akkor lő, hogy ha üres falut támadsz vele. Ha veszteségeid vannak, akkor kevesebb lő. Hogy hányad része, azt a következő képlet adja meg:</p>
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
				<h3>Veszteségarányok kiszámolása</h3>
					<p>Tehát ismerjük a seregek erőarányát, akkor most már kiszámolhatjuk, hogy a veszteségek arányát.</p>
					<h4>Utolsó emberig tartó támadás</h4>
						<p>Ha k&gt;1, akkor támadósereg győz, tehát a védősereg vesztesége 100%. A támadóseregé pedig:</p>
						<table class="math">
							<tr><td style="border-bottom:1px solid black">1</td></tr>
							<tr><td>k<sup><?php echo toComma($config['superiorityExponent']); ?></sup></td></tr>
						</table>
						<p>Ha k&lt;1, akkor védősereg győz, tehát a támadósereg vesztesége 100%. A védőseregé pedig: k<sup><?php echo toComma($config['superiorityExponent']); ?></sup></p>
					<h4>Rablótámadás</h4>
						<p>A támadósereg vesztesége:</p>
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
						<p>A védősereg vesztesége:</p>
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
					<h4>Kémlelőtámadás</h4>
						<p>A támadósereg vesztesége: </p>
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
						<p>A védősereg vesztesége: </p>
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
					<h4>Veszteségarányok, amikor a katapult a falra lő</h4>
					<p>Ilyenkor az történik, hogy a csata első részében egy fölénybónusz néküli rablótámadást szimulálunk. Ilyenkor a támadó vesztesége:</p>
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
					<p>A védősereg vesztesége:</p>
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
					<p>A katapultok ezután lőnek falra és rombolnak belőle. Ezután a túlélőkre újra ki kell számolni az erőarányokat, jelöljük most ez <var>l</var>-lel.</p>
					<p>Így a végleges veszteségek, a támadóseregnél:</p>
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
					<p>A védőseregnél</p>
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
				<h3>A tényleges veszteségek kiszámolása</h3>
					<p>Tehát a veszteségarányok kiszámolásánál kaptunk egy 0 és 1 közötti számot, a védő és a támadóra egyaránt. Ezzel meg kell szorozni a támadósereg minden egységszámát illetve a védőét is. Az eredményt pedig kerekíteni.</p>
				<h3>Katapult rombolásának a kiszámítása</h3>
					<p>A célépület szintjét 1,7-tel kell hatványozni, ekkor megkapjuk az épület erősségét. Ebből a számból vonjuk le, hogy mennyi katapult ütött be. Ez után vegyük az 1,7. gyökét a számnak, és kerekítsük felfelé.
					Ez lesz az új épületszint.</p>
				<h3>Néhány példa</h3>
					<h4>Utolsó emberig tartó támadás</h4>
						<?php $attackers=array(1000,1000,0,0,0); $defenders=array(0,0,2000,0,0); ?>
						<p>Legyen a támadósereg: <?php echo unitEnumerator($attackers); ?>.</p>
						<p>Legyen a védősereg: <?php echo unitEnumerator($defenders); ?>.</p>
						<p>Számoljuk ki a támadó és védekező vektorokat.</p>
						<p>A támadóvektor: <b>a</b>=<?php echo vectorCalculator($attackers,$attackVector,true); ?>.</p>
						<p>A védekezővektor: <b>d</b>=<?php echo vectorCalculator($defenders,$defenseVector,false); ?>.</p>
						<p>Számoljuk ki az erők arányát:</p>
						<table class="math">
							<tr>
								<td>k<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$powerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<p>
							A támadósereg vesztesége:
							<?php
								if ($powerRatio<1)
								{
									$attCasualties=1;
									?>
										100%, mert k&lt;1.
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
							A védősereg vesztesége:
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
						<p>Tehát a támadósereg vesztesége: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Tehát a védősereg vesztesége: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
					<h4>Rablótámadás</h4>
						<p>Most ugyanazzal a sereggel számoljunk egy rablótámadást. Az erőarány ugyanaz lesz, így azt nem kell újra számolni. Ami máshogy van, azok veszteségek.</p>
						<p>A támadósereg vesztesége:</p>
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
						<p>A védősereg vesztesége:</p>
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
						<p>Tehát a támadósereg vesztesége: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Tehát a védősereg vesztesége: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
					<h4>Kémtámadás</h4>
						<p>Az erőarányokat már kiszámoltuk, csak a képlet más itt is.</p>
						<p>A támadósereg vesztesége: </p>
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
						<p>A védősereg vesztesége: </p>
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
						<p>Tehát a támadósereg vesztesége: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Tehát a védősereg vesztesége: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
					<h4>Katapultos támadás</h4>
						<?php
							$catapults=1400;
							$buildingLevel=50;
							$attackers=array(1000,1000,0,$catapults,0);
							$defenders=array(0,0,2000,0,0);
						?>
						<p>Egészítsük ki az előző támadóseregünkket <?php echo $catapults ?>db katapulttal, és támadjunk egy <?php echo $buildingLevel; ?>. szintű épület ellen. Mivel csak utolsó emberig tartó támadásban lő a katapult, ezért csak azt az esetet nézzük csak.</p>
						<p>Legyen a támadósereg: <?php echo unitEnumerator($attackers); ?>.</p>
						<p>Legyen a védősereg: <?php echo unitEnumerator($defenders); ?>.</p>
						<p>Számoljuk ki a támadó és védekező vektorokat.</p>
						<p>A támadóvektor: <b>a</b>=<?php echo vectorCalculator($attackers,$attackVector,true); ?>.</p>
						<p>A védekezővektor: <b>d</b>=<?php echo vectorCalculator($defenders,$defenseVector,false); ?>.</p>
						<p>Számoljuk ki az erők arányát:</p>
						<table class="math">
							<tr>
								<td>k<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$powerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<p>
							A támadósereg vesztesége:
							<?php
								if ($powerRatio<1)
								{
									$attCasualties=1;
									?>
										100%, mert k&lt;1.
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
							A védősereg vesztesége:
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
						<p>Tehát a támadósereg vesztesége: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Tehát a védősereg vesztesége: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
						<p>Tehát a veszteségeket már tudjuk. Mi a helyzet az épületrombolással? Először számoljuk ki, hogy mennyi katapult lő:</p>
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
						<p>Ezután számoljuk ki az épületerősségét annak az épületnek: <?php echo $buildingLevel?><sup>1,7</sup>=<?php $bStrength=pow($buildingLevel,1.7); echo toComma(round($bStrength,3)); ?>.</p>
						<p>Ezután vonjuk ki belőle a beütő katapultok számát: <?php $rStrength=$bStrength-$catsFire; echo toComma(round($bStrength,3)).'&ndash;'.toComma(round($catsFire,3)).'='.toComma(round($rStrength,3));?>.</p>
						<?php
							if ($rStrength>0)
							{
								?>
									<p>Ebből az új épületerősségből számoljuk ki, hogy ez hanyadik szinthez tartozhat (vonjunk belőle 1,7. gyököt):
										<?php echo toComma(round($rStrength,3)); ?><sup>1/1,7</sup>=<?php $rLevel=pow($rStrength,1/1.7); echo toComma(round($rLevel,3)); ?>.</p>
									<p>Ezt a számot felfelé kerekítve megkapjuk, hogy az épület hanyadik szinten lesz a támadás után: <?php echo ceil($rLevel); ?>. szinten.</p>
								<?php
							}
							else
							{
								?>
									<p>Mivel több katapult ütött be, mint amennyi az épület erőssége, ezért az teljesen lerombolódik. 0. szintű lesz.</p>
								<?php
							}
						?>
					<h4>Katapultos támadás fal ellen</h4>
						<?php
							$catapults=1400;
							$wallLevel=15;
							$buildingLevel=$wallLevel;
							$attackers=array(1000,1000,0,$catapults,0);
							$defenders=array(0,0,2000,0,0);
							vectorCalculator($defenders,$baseDefenseVector,false);
						?>
						<p>Az előző katapultos seregünkkel most a falat romboljuk. Legyen a fal <?php echo $wallLevel; ?>. szintű.</p>
						<p>Legyen a támadósereg: <?php echo unitEnumerator($attackers); ?>.</p>
						<p>Legyen a védősereg: <?php echo unitEnumerator($defenders); ?>.</p>
						<p>Számoljuk ki a támadó és védekező vektorokat.</p>
						<p>A támadóvektor: <b>a</b>=<?php echo vectorCalculator($attackers,$attackVector,true); ?>.</p>
						<p>A védekezővektor: <b>d</b>=<?php echo vectorCalculator($defenders,$defenseVector,false,$wallLevel+1); ?>.</p>
						<p>A szükségünk lesz a falbónusz nélküli védekezővektorra is: <b>d<sub>0</sub></b>=<?php echo vectorCalculator($defenders,$baseDefenseVector,false); ?>.</p>
						<p>Számoljuk ki az erők arányát:</p>
						<table class="math">
							<tr>
								<td>k<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$powerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<p>A csata első felében egy fölénybónusz nélküli rablótámadást számolunk.</p>
						<p>A támadósereg vesztesége:</p>
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
						<p>A védősereg vesztesége:</p>
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
						<p>Ezeket a veszteségeket ezután alkalmazzuk a támadó és védővektorainkra.</p>
						<p>Tehát az új támadóvektor: <?php echo applyCasualtiesToVector($attackVector,$attCasualties,$attackVector); ?>.</p>
						<p>A védők veszteségét a falbónusz nélküli vektorra alkalmazzuk, mivel azt kell majd felszorozni a rombolás után falbónusszal: <?php echo applyCasualtiesToVector($baseDefenseVector,$defCasualties,$baseDefenseVector); ?>.</p>
						<p>Ezután lőnek a katapultok. Tudnunk kell, hogy mennyi katapult lő:</p>
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
						<p>Ezután számoljuk ki az épületerősségét annak az épületnek: <?php echo $buildingLevel?><sup>1,7</sup>=<?php $bStrength=pow($buildingLevel,1.7); echo toComma(round($bStrength,3)); ?>.</p>
						<p>Ezután vonjuk ki belőle a beütő katapultok számát: <?php $rStrength=$bStrength-$catsFire; echo toComma(round($bStrength,3)).'&ndash;'.toComma(round($catsFire,3)).'='.toComma(round($rStrength,3));?>.</p>
						<?php
							if ($rStrength>0)
							{
								?>
									<p>Ebből az új épületerősségből számoljuk ki, hogy ez hanyadik szinthez tartozhat (vonjunk belőle 1,7. gyököt):
										<?php echo toComma(round($rStrength,3)); ?><sup>1/1,7</sup>=<?php $rLevel=pow($rStrength,1/1.7); echo toComma(round($rLevel,3)); ?>.</p>
									<p>Ezt a számot felfelé kerekítve megkapjuk, hogy az épület hanyadik szinten lesz a támadás után: <?php echo $rLevel=ceil($rLevel); ?>. szinten.</p>
								<?php
							}
							else
							{
								$rLevel=0;
								?>
									<p>Mivel több katapult ütött be, mint amennyi az épület erőssége, ezért az teljesen lerombolódik. 0. szintű lesz.</p>
								<?php
							}
						?>
						<p>Tehát a fal a támadás után <?php echo $rLevel; ?>. szintű lesz.</p>
						<p>Számoljuk ki az új védekező vektort: <?php echo applyFactorToVector($baseDefenseVector,$rLevel+1,$defenseVector); ?></p>
						<p>Az új vektorok ismeretében egy újabb erőarányt lehet számolni:</p>
						<table class="math">
							<tr>
								<td>l<td>
								<td>=<td>
								<?php echo powerRatioCalculator($attackVector,$defenseVector,$newPowerRatio); ?>
								<td>.</td>
							</tr>
						</table>
						<?php $oldAttCasualties=$attCasualties; $oldDefCasualties=$defCasualties; ?>
						<p>Ezután számoljuk ki, hogy a második szakaszba mik lesznek a veszteségek, még itt is fölénybónusz nélkül.</p>
						<p>
							A támadósereg vesztesége:
							<?php
								if ($powerRatio<1)
								{
									$newAttCasualties=1;
									?>
										100%, mert l&lt;1.
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
							A védősereg vesztesége:
							<?php
								if ($powerRatio>1)
								{
									$newDefCasualties=1;
									?>
										100%, mert l&gt;1.
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
						<p>Ezután a végleges veszteségeket az első szakasz veszteségeiből, és a második szakasz veszteségeiből ki kelet majd számolni:</p>
						<p>A támadósereg vesztesége:
							<?php
								$attCasualties=pow($oldAttCasualties+$newAttCasualties*(1-$oldAttCasualties),$config['superiorityExponent']);
								echo '('.toComma(round($oldAttCasualties,3)).'+'.toComma(round($newAttCasualties,3)).'&middot;(1&ndash;'.toComma(round($oldAttCasualties,3)).'))<sup>'.toComma(round($config['superiorityExponent'],3)).'</sup>='.toComma(round($attCasualties,3));
							?>
						</p>
						<p>A védősereg vesztesége:
							<?php
								$defCasualties=pow($oldDefCasualties+$newDefCasualties*(1-$oldDefCasualties),$config['superiorityExponent']);
								echo '('.toComma(round($oldDefCasualties,3)).'+'.toComma(round($newDefCasualties,3)).'&middot;(1&ndash;'.toComma(round($oldDefCasualties,3)).'))<sup>'.toComma(round($config['superiorityExponent'],3)).'</sup>='.toComma(round($defCasualties,3));
							?>
						</p>
						<p>Tehát a támadósereg vesztesége: <?php echo casualtyCalculator($attackers,$attCasualties); ?>.</p>
						<p>Tehát a védősereg vesztesége: <?php echo casualtyCalculator($defenders,$defCasualties); ?>.</p>
			</div>
			<a href="javascript:void(toggleElement('miscfaq'))">Egyéb GYÍK :)</a><br>
			<div class="helpdiv" id="miscfaq">
				<a href="javascript:void(toggleElement('faq00'))">Hol nézhetem meg, hogy mikor érkezik be a támadásom vagy bármi más esemény?</a><br>
				<div class="helpdiv" id="faq00">
					<p>A baloldalon alul lévő ablakban kattints a számra, és kiírja az ahhoz a kategóriához tartozó eseményeket.</p>
				</div>
				<a href="javascript:void(toggleElement('faq0'))">Hogyan oszthatok meg egy csatajelentést másokkal?</a><br>
				<div class="helpdiv" id="faq0">
					<p>Nézd meg a jelentést, majd pipáld be, hogy publikálás, ezután lépj vissza a jelentések ablakba, ahol az összes többi jelentésed van, és másold a link címét. Oszd meg a linket másokkal, és látni fogják a jelentést.
					(érdemes elrejteni is, hogy nehogy kitöröld idő előtt)</p>
				</div>
				<a href="javascript:void(toggleElement('faq1'))">Mi történik, ha elfoglalják a falut, amelyből éppen kimenő egységmozgásom van?</a><br>
				<div class="helpdiv" id="faq1">
					<p>Ha támadásod vagy egységátadásod van, az a foglalás után is támadásként és egységátadásként fog beérni, hacsak nem a támadó a foglalás után leállítja az eseményt.
					Ha kimenő alapításod van, akkor visszakapsz minden terjeszkedési pontot, amit az alapítás megkezdéséért vont le a rendszer, míg a támadódtól levonásra kerül, mert az alapítás ezentúl az ő nevében történik.
					A terjeszkedési pontok ilyenkor a támadónál akár negatívba is átmehetnek. Persze a támadó azonnal vissza tudja szerezni a pontjait, ha az alapításokat visszavonja.
					Ha falufoglaló támadást indítóttál diplomatával, akkor amikor az beér, az új tulajdonostól vonja le a pontot, és a falut neki foglalod.</p>
				</div>
				<a href="javascript:void(toggleElement('faq2'))">Mi történik, ha leombolják a falut, amelyből éppen kimenő egységmozgásom van?</a><br>
				<div class="helpdiv" id="faq2">
					<p>Ha falualapításod vagy falufoglaló támadásod volt abból a faluból, az alapított, és elfoglalt falvaknak nem lesz tulajdonosa. Elhagyatatott falvak lesznek, amelyet senki sem irányít, ezeket a falvakat
					el lehet foglalni, ki lehet rabolni stb.
					Ha támadásod van, akkor a seregek ugyanúgy hazatérnek, mintha még meglenne a falu, azonban ha hazaértek, és látják, hogy nincs falu, akkor feloszlanak, a hősök törlődnek az adatbázisból.</p>
				</div>
				<a href="javascript:void(toggleElement('faq3'))">Mi történik, ha az a falut lerombolják, amelyiket támadom, vagy egységet mozgatok bele?</a><br>
				<div class="helpdiv" id="faq3">
					<p>A sereg egyszerűen hazatér.</p>
				</div>
			</div>
		</div>
		<hr>
		<a href="../../tutorial.php">Útmutató (ami akkor jelenik meg, amikor először belépsz.)</a><br>
	</body>
</html>










