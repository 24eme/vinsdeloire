<?php
$pourcentage = ($vrac->etape) * 25;
?>
<div id="contrat_progression" class="bloc_col">
    <h2>Saisie d'un contrat</h2>

    <div class="contenu">
            <p><strong><?php echo $pourcentage;?>%</strong> du contrat a été saisi</p>

            <div id="barre_progression">
                    <span style="width: <?php echo $pourcentage."%";?>;"></span>
            </div>
    </div>
</div>