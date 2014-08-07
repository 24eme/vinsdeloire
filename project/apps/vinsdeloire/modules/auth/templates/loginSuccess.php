<h2 class="titre_principal" style="background-color: #24528c">Opérateur</h2>
<!-- #application_dr -->
<div class="clearfix" id="application_dr">

    <!-- #nouvelle_declaration -->
    <div id="nouvelle_declaration" style="width: 504px;">
        <form action="<?php echo url_for('auth_login_no_cas') ?>" method="post" id="principal">
        <h3 class="titre_section" style="background-color: #5e88bc; color: #0f2c50">Connexion à un compte</h3>
        <div class="contenu_section">
            <p class="intro">Pour vous connecter, merci d'indiquer le login :</p>
            <?php echo $form->renderHiddenFields(); ?>
            <?php echo $form->renderGlobalErrors(); ?>
            <div class="ligne_form ligne_form_label">
                <?php echo $form['login']->renderError() ?>
                <?php echo $form['login']->renderLabel() ?>
                <?php echo $form['login']->render() ?>
            </div>
            <div class="ligne_form ligne_btn">
               <input type="image" alt="Valider" src="/images/boutons/btn_valider_bleu.png" name="boutons[valider]" class="btn">
            </div>
        </div>
        </form>

        <a href="<?php echo url_for('compte_teledeclarant_code_creation') ?>">Creation de compte</a><br />
        <a href="<?php echo url_for('compte_teledeclarant_mot_de_passe_oublie') ?>">Mot de passe oublié</a>
    </div>
</div>

