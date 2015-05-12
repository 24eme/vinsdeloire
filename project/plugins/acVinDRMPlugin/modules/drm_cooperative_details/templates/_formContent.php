<?php
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();

?>
<table id="drm_cooperative_details_table" class="drm_details_table">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Nom de la coopérative</th>
            <th>Volumes</th>
            <th>Dates</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="drm_cooperative_details_tableBody" class="drm_details_tableBody">
    <?php
    foreach ($form as $itemForm){
        if($itemForm instanceof sfFormFieldSchema) {
            include_partial('item',array('form' => $itemForm,'detail' => $detail));
        } else {
            $itemForm->renderRow();
        }
    }
    ?>        
        <tr id="drm_details_lastRow">
            <td class="cooperative_detail_produit"></td>
            <td class="cooperative_detail_destination">
                <a href="#" id="drm_cooperative_details_addTemplate" class="btn_majeur btn_modifier drm_details_addTemplate">Ajouter une coopérative</a>
            </td>
            <td class="cooperative_detail_volume">
                <div id="drm_details_cooperative_volume_total">
                    <strong>
                        <span class="drm_details_volume_somme">&Sigma;</span>
                        <span class="drm_details_volume_total"><?php echo $detail->sorties->cooperative > 0 ? $detail->sorties->cooperative : "0.00" ?></span>
                        <span class="drm_details_volume_unite unite">hl</span>
                    </strong>
                </div>      
            </td>
            <td class="cooperative_detail_date_enlevement"></td>   
            <td class="cooperative_detail_remove"></td>
        </tr>
    </tbody>
</table>

