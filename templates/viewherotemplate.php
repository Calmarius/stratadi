<?php

global $config;
global $language;

?>

<h1><?php echo xprintf($language['heropagetitle'],array($this->hero['name'],$this->hero['level']));?></h1>
<p><?php echo  $this->hero['ownerName']==''  ?  $language['thisherodonthaveowner'] : xprintf($language['theownerofthisherois'],array('<a href="viewplayer.php?id='.$this->hero['ownerId'].'">'.$this->hero['ownerName'].'</a>')); ?></p>
<p class="center"><img src="<?php echo $this->hero['avatarLink']; ?>" alt="<?php echo $language['heropicture']; ?>"></p>
<p class="center"><?php echo xprintf($language['heroattackskill'],array($this->hero['attackskill'],$this->hero['attacknextxp'],$this->hero['offense']));?></p>
<p class="center"><?php echo xprintf($language['herodefendskill'],array($this->hero['defendskill'],$this->hero['defendnextxp'],$this->hero['defense']));?></p>
<p class="center">
    <?php
        echo xprintf($language['yourheroinvillage'],
            array('<a href="javascript:void(parent.initMap('.(int)$this->hero['villageX'].','.(int)$this->hero['villageY'].'))">'.
                xprintf($language['villagetext'], array($this->hero['villageName'], $this->hero['villageX'], $this->hero['villageY'])).
            '</a>'));
    ?>
</p>

