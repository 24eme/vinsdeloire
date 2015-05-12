<!-- #principal -->
<h2 class="titre_principal">Mon compte</h2>

<!-- #application_dr -->
<div class="clearfix" id="application_dr">

    <!-- #nouvelle_declaration -->
    <div id="nouvelle_declaration">
        <h3 class="titre_section">Mot de passe oublié</h3>
        <div class="contenu_section">
            <form action="" method="post" id="principal">
                <p class="intro">Merci d'indiquer votre login :</p>

                <div class="ligne_form">
                    <?php echo $form->renderHiddenFields(); ?>
                    <?php echo $form->renderGlobalErrors(); ?>

                    <?php echo $form['login']->renderError() ?>
                    <?php echo $form['login']->renderLabel() ?>
                    <?php echo $form['login']->render() ?>
                </div>
                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
            </form>
        </div>
    </div>
    <!-- fin #nouvelle_declaration -->

    <!-- #precedentes_declarations -->

    <!-- fin #precedentes_declarations -->
</div>
<!-- fin #application_dr -->

<!-- fin #principal -->
