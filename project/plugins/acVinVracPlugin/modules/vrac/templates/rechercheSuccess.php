<?php
use_helper('Vrac');

$fil_arianeArray = array('fil' => ((isset($type) || isset($statut)) + 1), 'identifiant' => $identifiant);
if (!isset($campagne))
    $campagne = dateCampagneViticolePresent();

$urlExport = url_for('vrac_exportCsv', array('identifiant' => $identifiant,'campagne' => $campagne));
if (isset($statut)) {
    $urlExport = url_for('vrac_exportCsv', array('identifiant' => $identifiant,'campagne' => $campagne, 'statut' => $statut));
    $fil_arianeArray['statut'] = $statut;
}

if (isset($type)) {
    $urlExport = url_for('vrac_exportCsv', array('identifiant' => $identifiant, 'campagne' => $campagne, 'type' => $type));
    $fil_arianeArray['type'] = $type;
}

if (isset($type) && isset($statut)) {
    $urlExport = url_for('vrac_exportCsv', array('identifiant' => $identifiant, 'campagne' => $campagne, 'type' => $type,'statut' => $statut));
}
?>
<section id="principal">
        <?php include_partial('fil_ariane', $fil_arianeArray); ?>
    <section id="contenu_etape">                
        <?php include_component('vrac', 'formEtablissementChoice', array('identifiant' => $identifiant)) ?>
        <a id="btn_export_csv" href="<?php echo $urlExport; ?>" >Ouvrir en tableur</a>
        <?php
        include_partial('rechercheLegende', array('rechercheMode' => true,
            'vracs' => $vracs,
            'identifiant' => $identifiant,
            'actifs' => $actifs,
            'multiCritereType' => $multiCritereType,
            'multiCritereStatut' => $multiCritereStatut,
            'type' => $type,
            'statut' => $statut, 'campagne' => $campagne));
        ?>
        <div class="section_label_maj">  
            <?php
            if (count($vracs->rows->getRawValue())) {
                echo '<label>Contrats saisis : </label>';
                include_partial('table_contrats', array('vracs' => $vracs, 'identifiant' => $identifiant, 'hamza_style' => true));
            } else {
                echo "<label>Il n'existe aucun contrat pour cette recherche</label>";
            }
            ?>
        </div>
    </section>
</section>

<?php
slot('colButtons');
include_partial('actions', array('debrayage' => true, 'identifiant' => $identifiant));
end_slot();

slot('colApplications');
include_partial('contrat_campagne', array('vracs' => $vracs, 'visualisation' => false, 'campagne' => $campagne, 'identifiant' => $identifiant));
end_slot();
?>



