<?php

//die(preg_replace('/\[img\]([^"\[]*)\[\/img\]/','FOO','[img]"[/img]'));

function parseBBCode($str)
{
	$bbCode=array
	(
		'/\r\n/',
		'/\n/',
		'/\r/',
		'/\[[Bb]\]/',
		'/\[\/[Bb]\]/',
		'/\[[Ii]\]/',
		'/\[\/[Ii]\]/',
		'/\[[Uu]\]/',
		'/\[\/[Uu]\]/',
		'/\[[Ss]\]/',
		'/\[\/[Ss]\]/',
		'/\[url\]([^\[]*)\[\/url\]/i',
		'/\[url=([^\]]*)\]([^\[]*)\[\/url\]/i',
		'/\[color=([^\]]*)\]([^\[]*)\[\/color\]/i',
		'/\[img\]([^\[]*)\[\/img\]/',
		'/\[code\]/i',
		'/\[\/code\]/i',
		'/\[spoiler\]/i',
		'/\[\/spoiler\]/i',
		'/\[quote=([^\]]*)\]([^\[]*)\[\/quote\]/i'
	);
	$replacement=array
	(
		'<br>',
		'<br>',
		'<br>',
		'<b>',
		'</b>',
		'<i>',
		'</i>',
		'<span style="text-decoration:underline">',
		'</span>',
		'<span style="text-decoration:line-through">',
		'</span>',
		'<a href="\\1">\\1</a>',
		'<a href="\\1">\\2</a>',
		'<span style="color:\\1">\\2</span>',
		'<img src="\\1" alt="BBCode inserted image">',
		'<div class="bbbox"><pre>',
		'</pre></div>',
		'<div class="bbbox"><a style="font-size: small" href="javascript:;" onclick="var ns=this.nextSibling.nextSibling; if (ns.style.display==\'inline\'){ ns.style.display=\'none\'; this.innerHTML=\'(Spoiler)\';} else {ns.style.display=\'inline\'; this.innerHTML=\'(Hide spoiler)\'}">(Spoiler)</a><br><span style="display:none"> ',
		'</span></div>',
		'<div class="bbbox"><span style="font-variant:small-caps">\\1:</span><br><blockquote>\\2</blockquote></div>',
	);
	$str=preg_replace($bbCode,$replacement,$str);
	return $str;
}

function generateDatepicker($yearName,$monthName,$dayName,$monthNames,$year,$month,$day)
{
	$monthStr='';
	for($i=0;$i<12;$i++)
	{
		$monthStr.='<option value="'.($i+1).'" '.($i+1==$month ? 'selected="selected"':'').'>'.$monthNames[$i].'</option>';
	}
	echo <<< X
		<input type="text" maxlength="2" name="$dayName" style="width: 2em" value="$day">
		<select name="$monthName">
			$monthStr
		</select>
		<input type="text" maxlength="4" name="$yearName" style="width:4em" value="$year">
		
X;
}

function generateBuildingSelector($name,$id)
{
	global $config;
	global $language;
	$selector='<select name="'.$name.'" id="'.$id.'">';
	foreach($config['buildings'] as $key=>$value)
	{
		$selector.='<option value="'.$key.'">'.$language[$value['languageEntry']].'</option>';
	}
	$selector.='</select>';
	return $selector;
}

function generateGenderPicker($genders,$inputName,$selected)
{
	$mSelect=$fSelect=$nSelect='';
	$sText='selected="selected"';
	if ($selected=='male') $mSelect=$sText;
	else if ($selected=='female') $fSelect=$sText;
	else $nSelect=$sText;
	echo <<< X
		<select name="$inputName">
			<option value="" $nSelect></option>
			<option value="male" $mSelect>${genders[0]}</option>
			<option value="female" $fSelect>${genders[1]}</option>
		</select>
X;
}

function generateLanguagePicker($name,$languageTableName,$selectedLangs=array())
{
	$name.='[]';
	$r=runEscapedQuery("SELECT * FROM $languageTableName ORDER BY language");
	$options='';
	foreach ($r[0] as $row)
	{
		$options.='<option value="'.$row['id'].'" '.(in_array($row['id'],$selectedLangs) ? 'selected="selected"':'').'>'.$row['language'].'</option>';
	}
	$selectControl=<<< X
	<select multiple="multiple" name="$name">
		$options
	</select>
X;
	echo $selectControl;
}

function hourString($seconds)
{
	$hours=floor($seconds/3600);
	$minutes=str_pad(floor(($seconds%3600)/60),2,'0',STR_PAD_LEFT);
	$seconds=str_pad(floor($seconds%60),2,'0',STR_PAD_LEFT);
	return "$hours:$minutes:$seconds";
}

function showInBox($templateFile,$contentArray,$title='',$description='',$altUrl='')
{
	global $language;
	global $config;

	$content=new Template($templateFile,$contentArray);
	$box=new Template('templates/standardboxtemplate.php',array('content'=>$content->getContents()));
	$page=new Template('templates/basiclayout.php',array('content'=>$box->getContents(),'altTitle'=>$title,'altDescription'=>$description,'altUrl'=>$altUrl));
	$page->render();
	
}



?>
