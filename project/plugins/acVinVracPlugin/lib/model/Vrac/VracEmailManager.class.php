<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VracEmailManager
 *
 * @author mathurin
 */
class VracEmailManager {

    protected $vrac = null;
    protected $mailer = null;

    public function __construct($mailer) {
        $this->mailer = $mailer;
    }

    public function setVrac($vrac) {
        $this->vrac = $vrac;
    }
    
    
    public function sendMailAttenteSignature() {
        $createurObject = $this->vrac->getCreateurObject();
        $nonCreateursArr = $this->vrac->getNonCreateursArray();

        $resultEmailArr = array();

        $resultEmailArr[] = $createurObject->email;
        $mess = $this->enteteMessageVrac();
        $mess .= '  
 

Ce contrat attend votre signature. Pour le visualiser et le signer cliquez sur le lien suivant : http://vinsdeloire.pro

 

Pour être valable, le contrat doit être signé par toutes les parties et visé par INTERLOIRE. Le PDF correspondant avec le numéro de visa INTERLOIRE vous sera alors envoyé par courriel.

 

Attention si le contrat n’est pas signé par toutes les parties dans les 5 jours à compte de sa date de saisie, il sera automatiquement supprimé.

 

Pour toutes questions, veuillez contacter l’interlocuteur commercial, responsable du contrat.

 

———

L’application de télédéclaration des contrats d’INTERLOIRE';


        foreach ($nonCreateursArr as $id => $nonCreateur) {

            $message = $this->getMailer()->compose(array('declaration@vinsvaldeloire.fr' => "Contrats INTERLOIRE"), $nonCreateur->email, '[Contact télédéclaration] Demande de signature (' . $createurObject->nom . ')', $mess);
            try {
                $this->getMailer()->send($message);
            } catch (Exception $e) {
                $this->getUser()->setFlash('error', 'Erreur de configuration : Mail de confirmation non envoyé, veuillez contacter INTERLOIRE');
                return null;
            }
            $resultEmailArr[] = $nonCreateur->email;
        }
    }

    public function sendMailContratValide() {
        $soussignesArr = $this->vrac->getNonCreateursArray();
        $createur = $this->vrac->getCreateurObject();
        $soussignesArr[$createur->_id] = $createur;

        $resultEmailArr = array();
        $mess = $this->enteteMessageVrac();
        $mess .= "  

 
Ce contrat a été signé éléctroniquement par l'ensemble des ... . Pour le visualiser à tout moment vous pouvez cliquez sur le lien suivant : http://vinsdeloire.pro

Ci joint, le PDF correspondant avec le numéro de visa INTERLOIRE.
 
Attention le contrat ne sera annulable par le responsable du contrat durant 10 jours à compter de cette présente validation.

Pour toutes questions, veuillez contacter l’interlocuteur commercial, responsable du contrat.

———

L’application de télédéclaration des contrats d’INTERLOIRE";
       
        $pdf = new VracLatex($this->vrac);
        $pdfContent = $pdf->getPDFFileContents();        
        $pdfName = $pdf->getPublicFileName();

        
        foreach ($soussignesArr as $id => $soussigne) {

            $message = $this->getMailer()->compose(array('declaration@vinsvaldeloire.fr' => "Contrats INTERLOIRE"), $soussigne->email, '[Contact télédéclaration] Validation du contrat n° '.$this->vrac->numero_contrat.' (' . $createur->nom . ')', $mess);
            $attachment = new Swift_Attachment($pdfContent, $pdfName, 'application/pdf');
            $message->attach($attachment);
            try {
                $this->getMailer()->send($message);
            } catch (Exception $e) {
                $this->getUser()->setFlash('error', 'Erreur de configuration : Mail de confirmation non envoyé, veuillez contacter INTERLOIRE');
                return null;
            }
            $resultEmailArr[] = $soussigne->email;
        }
        return $resultEmailArr;
    }

    private function enteteMessageVrac() {
        if (!$this->vrac) {
            throw new sfException("Le contrat Vrac n'existe pas.");
        }
        return 'Contrat : « ' . VracClient::$types_transaction[$this->vrac->type_transaction] . ' » du ' . $this->vrac->valide->date_saisie
                . '

 

Vendeur : ' . $this->vrac->vendeur->nom . '

Acheteur : ' . $this->vrac->acheteur->nom;
        if ($this->vrac->mandataire_exist) {
            $mess .= '

Courtier : ' . $this->vrac->mandataire->nom;
        }
    }
    
    private function getMailer(){
        return $this->mailer;
    }
}
