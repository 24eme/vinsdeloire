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
class acVinCompteLoginFirstForm extends BaseForm 
{
    public function configure() 
    {
        $this->setWidgets(array(
                'login'   => new sfWidgetFormInputText(),
                'mdp'   => new sfWidgetFormInputPassword()
        ));

        $this->widgetSchema->setLabels(array(
                'login'  => 'Numéro CVI : ',
                'mdp'  => 'Code de création : '
        ));

        $this->setValidators(array(
                'login' => new sfValidatorString(array('required' => true)),
                'mdp' => new sfValidatorString(array('required' => true, 'min_length' => 4)),
        ));
        
        $this->widgetSchema->setNameFormat('first_connection[%s]');

        $this->validatorSchema['login']->setMessage('required', 'Champ obligatoire');
        $this->validatorSchema['mdp']->setMessage('required', 'Champ obligatoire');
        

        $this->validatorSchema->setPostValidator(new acVinValidatorCompteLoginFirst());
    }
}