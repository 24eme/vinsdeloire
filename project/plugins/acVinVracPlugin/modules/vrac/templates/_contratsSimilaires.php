<?php
use_helper('Vrac');
if(is_null($vrac->type_transaction)) $vrac->type_transaction = VracClient::TYPE_TRANSACTION_VIN_VRAC;
$vracs = VracClient::getInstance()->retrieveSimilaryContracts($vrac);
$flagStatut = false;
?>
<div id="contrats_similaires" class="bloc_col">
        <h2>Contrats similaires</h2>
        <div class="contenu">
                <ul id="contrats_similaires_list">
                        <li class="legende_contrat"><span class="statut statut_solde"></span> Soldé <span class="f_right"><span class="statut statut_non-solde"></span> Non soldé</span></li>
                        <li class="separateur"></li>
                        <?php 
                        if($vracs)            
                        foreach ($vracs as $row) 
                        {                           
                            $elt = $row->value;
                            $statusColor = statusColor($elt[VracClient::VRAC_SIMILAIRE_VALUE_STATUT]);
                            //$statusColor = statusColor($elt[VracClient::VRAC_VIEW_STATUT]);
			    $flagStatut = true;
                        ?>
                        <li>
                            <span class="statut <?php echo $statusColor; ?>"></span>                         
                                <?php 
                            // echo $elt[VracClient::VRAC_SIMILAIRE_VALUE_STATUT];
                                $num_contrat = preg_replace('/VRAC-/', '',$elt[VracClient::VRAC_SIMILAIRE_VALUE_NUMCONTRAT]);
                                $volprop = $elt[VracClient::VRAC_SIMILAIRE_VALUE_VOLPROP];
                                $millesime = (is_null($elt[VracClient::VRAC_SIMILAIRE_VALUE_MILLESIME]))? null : $elt[VracClient::VRAC_SIMILAIRE_VALUE_MILLESIME];
				$archive = $elt[VracClient::VRAC_SIMILAIRE_VALUE_NUMARCHIVE];
				$datecontrat = preg_replace('/^\d{2}(\d{2})(\d{2})(\d{2}).*/', '$3/$2', $num_contrat);
                                                                ?>                            
                            <a class="contrat_similaire_num_contrat" target="_blank" href="<?php echo url_for('vrac_visualisation',array('numero_contrat' => $num_contrat)); ?>">
                                <span id="volprop"> <?php echo $volprop; ?>&nbsp;hl</span> -
							    <span id="num_contrat">n°<?php echo $archive ; ?>&nbsp;(<?php echo $datecontrat; ?>)</span>
                                <?php if($millesime) : ?>
                                    <span id="millesime"><?php echo $millesime ; ?></span>
                                <?php endif; ?>
                                <span class="btn_fleche_ronde"></span>
                            </a>
                        </li>
                        <li class="separateur"></li>
                        <?php
                        }
                        ?>
                </ul>
            <?php
                if(!$vracs || !$flagStatut || count($vracs)==0)
                {
                ?>
                <span>Il n'existe aucun contrat similaire</span>
                <?php
                }
            ?>
        </div>
</div>