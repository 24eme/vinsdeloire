<!-- #principal -->
<form action="" method="post" id="principal">

    <h2 class="titre_principal">Création de votre compte</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <h3 class="titre_section">Connexion</h3>
            <div class="contenu_section">
                <p class="intro">Merci d'indiquer votre e-mail et un mot de passe: </p>

                <div class="ligne_form ligne_form_label">
                    <?php echo $form->renderHiddenFields(); ?>
                    <?php echo $form->renderGlobalErrors(); ?>

                    <?php echo $form['email']->renderError() ?>
                    <?php echo $form['email']->renderLabel() ?>
                    <?php echo $form['email']->render() ?>
                </div>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['mdp1']->renderError() ?>
                    <?php echo $form['mdp1']->renderLabel() ?>
                    <?php echo $form['mdp1']->render() ?>
                </div>
                <div class="ligne_form">
                    <?php echo $form['mdp2']->renderError() ?>
                    <?php echo $form['mdp2']->renderLabel() ?>
                    <?php echo $form['mdp2']->render() ?>
                </div>

                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
            </div>
        </div>
        <!-- fin #nouvelle_declaration -->

        <!-- #precedentes_declarations -->

        <!-- fin #precedentes_declarations -->
    </div>
    <!-- fin #application_dr -->

</form>
<!-- fin #principal -->
