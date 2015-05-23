<?php

global $language;

?>

<h1><?php echo $language['sendmassreport'];?></h1>
<form method="POST" action="dosendmassreport.php">
	<p><?php echo $language['subject'];?><input type="text" name="subject"></p>
	<p><textarea name="text" id="text" cols="60" rows="25"></textarea></p>
	<p><input type="submit" value="<?php echo $language['sendletterbutton'];?>"><input type="button" value="<?php echo $language['preview']; ?>" onclick="document.getElementById('preview').innerHTML=document.getElementById('text').value"></p>
</form>
<div id="preview">
</div>
<script>
	function getCaretPosition (ctrl) {
		var CaretPos = 0;	// IE Support
		if (document.selection) {
		ctrl.focus ();
			var Sel = document.selection.createRange ();
			Sel.moveStart ('character', -ctrl.value.length);
			CaretPos = Sel.text.length;
		}
		// Firefox support
		else if (ctrl.selectionStart || ctrl.selectionStart == '0')
			CaretPos = ctrl.selectionStart;
		return (CaretPos);
	}
	function setCaretPosition(ctrl, pos){
		if(ctrl.setSelectionRange)
		{
			ctrl.focus();
			ctrl.setSelectionRange(pos,pos);
		}
		else if (ctrl.createTextRange) {
			var range = ctrl.createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
		}
	}
	document.getElementById('text').onkeydown=
	function(e)
	{
		var ev=e || window.event;
		var kc=(ev.keyCode==undefined) ? ev.which: ev.keyCode;
		if (kc==9)
		{
			var str=this.value;
			var cp=getCaretPosition(this);
			str=str.substr(0,cp)+'\t'+str.substr(cp);
			this.value=str;
			setCaretPosition(this,cp+1);
			return false;
		}
		return true;
	}
</script>
