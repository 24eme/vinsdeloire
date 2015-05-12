<?php
use_helper('Vrac');

$etablissements = array('' => '');
$datas = EtablissementClient::getInstance()->findAll()->rows;
foreach ($datas as $data) {
    $labels = array($data->key[4], $data->key[3], $data->key[1]);
    $etablissements[$data->id] = implode(', ', array_filter($labels));
}
?>
<section id="principal">
    <?php
    include_partial('fil_ariane', array('fil' => 0));
    ?>
    <div id="contenu_etape">
        <?php include_component('vrac', 'formEtablissementChoice') ?>
        <div class="section_label_maj"> 
            <label>10 derniers contrats saisis : </label>   
            <?php include_partial('table_contrats', array('vracs' => $vracs)); ?>
        </div>
    </div>
</section>

<?php
slot('colButtons');
include_partial('actions');
end_slot();

slot('colApplications');
?>
<div id="export_soussignes" class="bloc_col">
    <h2>Export des soussignés</h2>
        <?php
        include_partial('exportEtiquettes',array('etiquettesForm' => $etiquettesForm));
        ?>
</div>

<div id="recherche_legende" class="bloc_col">
    <h2>Légende</h2>
    <?php
    include_partial('rechercheLegende');
    ?>
</div>


<?php
end_slot();
?>