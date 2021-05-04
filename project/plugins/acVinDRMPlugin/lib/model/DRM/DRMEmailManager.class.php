<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DRMEmailManager
 *
 * @author mathurin
 */
class DRMEmailManager {

    protected $drm = null;
    protected $mailer = null;
    protected $user = null;

    public function __construct($mailer, $user) {
        $this->mailer = $mailer;
        $this->user = $user;
        sfProjectConfiguration::getActive()->loadHelpers("Date");
        sfProjectConfiguration::getActive()->loadHelpers("Orthographe");
        sfProjectConfiguration::getActive()->loadHelpers("DRM");
        sfProjectConfiguration::getActive()->loadHelpers("Url");
    }

    public function setDRM($drm) {
        $this->drm = $drm;
    }

    public function sendMailCoordonneesOperateurChanged($type, $diff) {
        if (!count($diff)) {
            return false;
        }

        $typeInfos = null;
        $typeLibelle = null;
        $mailsInterloire = sfConfig::get('app_teledeclaration_emails_interloire');
        switch ($type) {
            case CompteClient::TYPE_COMPTE_ETABLISSEMENT:
                $typeInfos = $this->drm->getDeclarant();
                $typeLibelle = "l'etablissement";
                $identification = $typeInfos->nom . " (" . $this->drm->identifiant . ")";
                $url = sfConfig::get('app_vinsi_url').url_for('etablissement_visualisation', array('identifiant' => $this->drm->identifiant));
                break;

            case CompteClient::TYPE_COMPTE_SOCIETE:
                $typeInfos = $this->drm->getSociete();
                $typeLibelle = 'la société';
                $identification = $typeInfos->raison_sociale . " (" . substr(0, 6, $this->drm->identifiant) . ")";
                $url = sfConfig::get('app_vinsi_url').url_for('societe_visualisation', array('identifiant' => substr(0, 6, $this->drm->identifiant)));
                break;
        }

        $mess = "Les coordonnées de " . $typeLibelle . " " . $identification . " <" .$url . "> ont été modifiées.

Voici les différentes modifications effectuées par l'opérateur sur la DRM " . getFrPeriodeElision($this->drm->periode) . " :

";
        foreach ($diff as $key => $value) {
            $mess .= "- ".$key . " : " . $value . "
";
        }

        $subject = "Changement de coordonnées de " . $typeLibelle . " " . $identification;

        $message = $this->getMailer()->compose(array(sfConfig::get('app_mail_from_email') => sfConfig::get('app_mail_from_name')), $mailsInterloire, $subject, $mess);
        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {
            $this->getUser()->setFlash('error', 'Erreur de configuration : Mail de confirmation non envoyé, veuillez contacter INTERLOIRE');
            return null;
        }
        return true;
    }


    public function sendMailValidation() {

        $etablissement = EtablissementClient::getInstance()->find($this->drm->identifiant);
        $contact = EtablissementClient::getInstance()->buildInfosContact($etablissement);


        $mess = "

La DRM " . getFrPeriodeElision($this->drm->periode) . " de " . $etablissement->nom . " a été validée électroniquement sur le portail de télédeclaration ". sfConfig::get('app_teledeclaration_url')." .

La version PDF de cette DRM est également disponible en pièce jointe dans ce mail.

Si vous n'avez pas signé la convention avec la douane qui active vos droits à la télédéclaration, la DRM doit être signée manuellement avant transmission par mail ou courrier postal à votre service local douanier.
Sinon, vous pouvez transmettre cette DRM directement sur le portail de la douane, qui apparaîtra en mode brouillon sur le portail douane.gouv.fr. Il vous restera alors à la valider une dernière fois en ligne sur le portail douanier.

Pour toutes questions, veuillez contacter:
  le service Economie et Etudes d'InterLoire: " . $contact->nom . " - " . $contact->email . " - " . $contact->telephone . "

--

L’application de télédéclaration des DRM ". sfConfig::get('app_teledeclaration_url') ." .";

        $pdf = new DRMLatex($this->drm);
        $pdfContent = $pdf->getPDFFileContents();
        $pdfName = $pdf->getPublicFileName();

        $subject = "Validation de la DRM " . getFrPeriodeElision($this->drm->periode) . " créée le " . $this->getDateSaisieDrmFormatted() . " .";

        $email = $this->getUser()->getCompte()->getEmail();
        if(!$email){
          $email = $etablissement->getEmailTeledeclaration();
        }

        $message = $this->getMailer()->compose(array(sfConfig::get('app_mail_from_email') => sfConfig::get('app_mail_from_name')), $email, $subject, $mess);

        $message->attach(new Swift_Attachment($pdfContent, $pdfName, 'application/pdf'));

//            $attachment = new Swift_Attachment(file_get_contents(dirname(__FILE__) . '/../../../../../web/data/reglementation_generale_des_transactions.pdf'), 'reglementation_generale_des_transactions.pdf', 'application/pdf');
//            $message->attach($attachment);

        try {
            $this->getMailer()->send($message);
            $resultEmailArr[] = $email;
            if ($this->drm->email_transmission) {
                $message_transmission = $this->getMailer()->compose(array(sfConfig::get('app_mail_from_email') => sfConfig::get('app_mail_from_name')), $this->drm->email_transmission, $subject, $mess);
                $message_transmission->attach(new Swift_Attachment($pdfContent, $pdfName, 'application/pdf'));
                $this->getMailer()->send($message_transmission);
                $resultEmailArr[] = $this->drm->email_transmission;
            }
        } catch (Exception $e) {
            $this->getUser()->setFlash('error', 'Erreur de configuration : Mail de confirmation non envoyé, veuillez contacter INTERLOIRE');
            return null;
        }

        return $resultEmailArr;
    }

    private function getMailer() {
        return $this->mailer;
    }

    private function getUser() {
        return $this->user;
    }

    private function getUrlVisualisationDrm() {
        return sfContext::getInstance()->getRouting()->generate('drm_visualisation', $this->drm, true);
    }

    protected function getDateSaisieDrmFormatted() {
        return date("d/m/Y", strtotime($this->drm->valide->date_saisie));
    }

}
