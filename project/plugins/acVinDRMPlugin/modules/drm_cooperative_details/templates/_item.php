<tr>
        <td class="cooperative_detail_produit">
            <strong> 
                <?php echo $detail->getLibelle(ESC_RAW); ?>
            </strong>            
        </td>
        <td class="cooperative_detail_destination">    
            <?php
            echo $form['identifiant']->renderError();
            echo $form['identifiant']->render();
            ?>
        </td>
        <td class="volume cooperative_detail_volume">    
            <?php
            echo $form['volume']->renderError();
            echo $form['volume']->render();
            ?>
        </td>
        <td class="cooperative_detail_date_enlevement champ_datepicker">
                <?php
                echo $form['date_enlevement']->renderError();
                echo $form['date_enlevement']->render();
                ?>
        </td>   
        <td class="cooperative_detail_remove">    
            <a href="#"  class="btn_majeur btn_annuler drm_details_remove">&nbsp;</a>
        </td>  
</tr>