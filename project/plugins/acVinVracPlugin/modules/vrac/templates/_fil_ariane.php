<?php
use_helper('Vrac');
if($fil>0) $etablissement = EtablissementClient::getInstance()->find($identifiant);
if(!isset($vrac) || !$vrac){
  $urlAccueil = url_for('vrac');
}else{
$urlAccueil = ($vrac->isTeledeclare())? url_for('vrac_societe', array('identifiant' => $compte->identifiant)) : url_for('vrac');
}
?>
<p id="fil_ariane">
    <a href="<?php echo $urlAccueil; ?>">
        <?php if($fil==0): ?><strong><?php endif; ?>
            Page d'accueil
        <?php if($fil==0): ?></strong><?php endif; ?>
    </a>
    <?php 
    if($fil>0)
    {
    ?>
    &gt;
    <a href="<?php echo url_for('vrac_recherche',array('identifiant' => $identifiant)); ?>">
        <?php if($fil==1): ?><strong><?php endif; ?>
            <?php echo $etablissement->nom; ?>
        <?php if($fil==1): ?></strong><?php endif; ?>
    </a>
    <?php
    }
    ?>
    <?php 
    if($fil>1)
    {
        if(isset($statut)) {
            $label=$statut; 
            $urlFil = url_for('vrac_recherche',array('identifiant' => $identifiant,'statut' => $statut));
            }
        if(isset($type)){
            $label=showTypeFromLabel($type);
            $urlFil = url_for('vrac_recherche',array('identifiant' => $identifiant,'type' => $type));
        }
    ?>
    &gt;
    <a href="<?php echo $urlFil; ?>">
        <?php if($fil==2): ?><strong><?php endif; ?>
            <?php echo $label; ?>
        <?php if($fil==2): ?></strong><?php endif; ?>
    </a>
    <?php
    }
    ?>
    
    <?php 
    if($fil==-1){
    ?>
    &gt;
    <a href="<?php echo url_for('vrac_nouveau'); ?>">
        <?php if($fil==-1): ?><strong><?php endif; ?>Saisie contrat<?php if($fil==-1): ?></strong><?php endif; ?>
    </a>
    <?php
    }
    ?>
</p>