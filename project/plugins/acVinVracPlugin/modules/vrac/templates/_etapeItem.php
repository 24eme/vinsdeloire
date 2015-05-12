<?php
$liClass = '';
if($actif == $num_etape+1) $liClass = 'actif';
  else
  {
      if(($vrac->etape > $num_etape) && (($num_etape+1)!=$actif)) $liClass = 'passe';
  }

 $href ='';
  if($num_etape == 0 && $vrac->etape == 0 && isset($urlsoussigne) && $urlsoussigne) $href = 'href="'.$urlsoussigne.'"';
  else if($vrac->etape >= $num_etape) $href = 'href="'.url_for($url_etape,$vrac).'"';
?>


<li class="<?php echo $liClass; ?>">
    <a <?php echo $href; ?>>
        <?php if($actif == $num_etape+1) echo '<strong>'; ?>
        <span <?php echo ($vrac->etape < $num_etape)? 'style="cursor: default;"' : '' ?> ><?php echo $num_etape+1;?> </span>
        <?php echo $label; ?> 
        <?php if($actif == $num_etape+1) echo '</strong>'; ?>
    </a>    
</li>


   
