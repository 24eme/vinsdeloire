<?php
$vendeur_coord = null;
$acheteur_coord = null;
$mandataire_coord = null;
if ($vrac) {
  $vendeur_coord = $vrac->getCoordonneesVendeur();
  $acheteur_coord = $vrac->getCoordonneesAcheteur();
  if($vrac->mandataire_identifiant)
    $mandataire_coord = $vrac->getCoordonneesMandataire();
}
?>

<div id="infos_contact" class="bloc_col">
    <h2>Infos contact</h2>

    <div class="contenu">
        <ul>
  <?php if ($vendeur_coord) : ?>
            <li id="infos_contact_vendeur">
                <a href="<?php echo ($vendeur_coord->identifiant)? url_for('compte_visualisation',
                        array('identifiant' => $vendeur_coord->identifiant)) : '#'; ?>">Coordonnées vendeur</a>
                <ul>
                    <li class="nom"><?php echo $vendeur_coord->nom_a_afficher; ?></li>
                    <?php if ($vendeur_coord->telephone_bureau != ""): ?>
                        <li class="tel"><?php echo $vendeur_coord->telephone_bureau; ?></li>
                    <?php endif; ?>
                    <?php if ($vendeur_coord->fax != ""): ?>
                        <li class="fax"><?php echo $vendeur_coord->fax; ?></li>
                    <?php endif; ?>
                    <?php if (trim($vendeur_coord->email) != ""): ?>
                        <li class="email"><a href="mailto:<?php echo $vendeur_coord->email; ?>"><?php echo $vendeur_coord->email; ?></a></li>
                    <?php endif; ?>
                </ul>
            </li>
<?php endif; ?>
<?php if ($acheteur_coord) : ?>
            <li id="infos_contact_acheteur">
                <a href="<?php echo ($acheteur_coord->identifiant)? url_for('compte_visualisation',
                        array('identifiant' => $acheteur_coord->identifiant)) : '#'; ?>">Coordonnées acheteur</a>
                <ul>
                    <li class="nom"><?php echo $acheteur_coord->nom_a_afficher; ?></li>
                    <?php if ($acheteur_coord->telephone_bureau != ""): ?>
                        <li class="tel"><?php echo $acheteur_coord->telephone_bureau; ?></li>
                    <?php endif; ?>
                    <?php if ($acheteur_coord->fax != ""): ?>
                        <li class="fax"><?php echo $acheteur_coord->fax; ?></li>
                    <?php endif; ?>
                    <?php if (trim($acheteur_coord->email) != ""): ?>
                        <li class="email"><a href="mailto:<?php echo $acheteur_coord->email; ?>"><?php echo $acheteur_coord->email; ?></a></li>
                    <?php endif; ?>
                </ul>
            </li>
<?php endif; ?>
<?php if($mandataire_coord): ?>
            <li id="infos_contact_mendataire">
                <a href="<?php echo ($mandataire_coord->identifiant)? url_for('compte_visualisation',
                        array('identifiant' => $mandataire_coord->identifiant)) : '#'; ?>">Coordonnées courtier</a>
                <ul>
                    <li class="nom"><?php echo $mandataire_coord->nom_a_afficher; ?></li>
                    <?php if ($mandataire_coord->telephone_bureau != ""): ?>
                        <li class="tel"><?php echo $mandataire_coord->telephone_bureau; ?></li>
                    <?php endif; ?>
                    <?php if ($mandataire_coord->fax != ""): ?>
                        <li class="fax"><?php echo $mandataire_coord->fax; ?></li>
                    <?php endif; ?>
                    <?php if (trim($mandataire_coord->email) != ""): ?>
                        <li class="email"><a href="mailto:<?php echo $mandataire_coord->email; ?>"><?php echo $mandataire_coord->email; ?></a></li>
                    <?php endif; ?>
                </ul>
            </li>
<?php endif; ?>
        </ul>
    </div>
</div>