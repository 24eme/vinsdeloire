<?php

/* This file is part of the acVinComptePlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :	
 * Tangui Morlier <tangui@tangui.eu.org>
 * Charlotte De Vichet <c.devichet@gmail.com>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * acVinComptePlugin form.
 * 
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acVinCompteForm extends acCouchdbObjectForm 
{
    public function configure() 
    {
        $this->setWidgets(array(
                'email' => new sfWidgetFormInputText(),
                'mdp1'  => new sfWidgetFormInputPassword(),
                'mdp2'  => new sfWidgetFormInputPassword()
        ));

        $this->widgetSchema->setLabels(array(
                'email' => 'Adresse e-mail*: ',
                'mdp1'  => 'Mot de passe*: ',
                'mdp2'  => 'Vérification du mot de passe*: '
        ));

        $this->widgetSchema->setNameFormat('ac_vin_compte[%s]');

        $this->setValidators(array(
                'email' => new sfValidatorEmail(array('required' => true),array('required' => 'Champ obligatoire', 'invalid' => 'Adresse email invalide.')),
                'mdp1'  => new sfValidatorString(array('required' => true, "min_length" => "4"), array('required' => 'Champ obligatoire', "min_length" => "Votre mot de passe doit comporter au minimum 4 caractères.")),
                'mdp2'  => new sfValidatorString(array('required' => true), array('required' => 'Champ obligatoire')),
        ));
        
        $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('mdp1', 
                                                                             sfValidatorSchemaCompare::EQUAL, 
                                                                             'mdp2',
                                                                             array(),
                                                                             array('invalid' => 'Les mots de passe doivent être identique.')));
    }
}