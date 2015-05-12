    <div id="action" class="bloc_col">
            <h2>Action</h2>

            <div id="contrat_actions" class="contenu">
                <div class="btnRetourAccueil">
                    <a href="<?php echo url_for('vrac'); ?>" class="btn_majeur">Retour accueil</a>
                </div>
                <div class="btnNouveau">
                    <a href="<?php echo url_for('vrac_nouveau'); ?>" class="btn_majeur btn_nouveau"><span>Saisir un nouveau contrat</span></a>
                </div>
                <?php if(isset($debrayage) && $debrayage && isset($identifiant)): ?>
                    <div class="btnConnexion">
                        <a href="<?php echo url_for('vrac_debrayage', array('identifiant' => $identifiant)); ?>" class="btn_majeur lien_connexion"><span>Connexion à la télédecl.</span></a>
                    </div>
                <?php endif; ?>
            </div>
    </div>