<?php include_partial('drm_export/pdfLine', array('libelle' => $libelle,
						  'colonnes' => $colonnes,
						  'hash' => $hash,
						  'partial' => 'drm_export/pdfLineItemFloat',
						  'partial_params' => array('unite' => isset($unite) ? $unite : null),
						  'cssclass_libelle' => isset($cssclass_libelle) ? $cssclass_libelle : null,
						  'cssclass_value' => isset($cssclass_value) ? $cssclass_value.' number' : 'number',
						  'partial_cssclass_value' => isset($partial_cssclass_value) ? $partial_cssclass_value : null)) ?>