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
class acVinCompteMotDePasseOublieForm extends BaseForm 
{

    public function setup() 
    {
        $this->setWidgets(array(
            'login' => new sfWidgetFormInputText(array('label' => 'CVI'))
        ));

        $this->setValidators(array(
            'login' => new sfValidatorString(array('required' => true)),
        ));
        
        $this->validatorSchema['login']->setMessage('required', 'Champ obligatoire');

        $this->validatorSchema->setPostValidator(new acVinValidatorCompteMotDePasseOublie());

        $this->widgetSchema->setNameFormat('mot_de_passe_oublie[%s]');
    }

    public function save() 
    {
        if ($this->isValid()) {
            $compte = $this->getValue('compte');
            $compte->mot_de_passe = "{OUBLIE}" . sprintf("%04d", rand(0, 9999));
            $compte->save();
        } else {
            throw new sfException("form must be valid");
        }
        
        return $compte;
    }
}