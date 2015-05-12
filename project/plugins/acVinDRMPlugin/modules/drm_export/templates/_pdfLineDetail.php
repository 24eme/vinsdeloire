<?php $i = 'a'; foreach($certification_config->detail->get($hash) as $item): ?>
	<?php include_partial('drm_export/pdfLineFloat', array('libelle' => $item->getLibelle(), 
													   	   'colonnes' => $colonnes,  
													   	   'counter' => $counter.$i, 
													   	   'unite' => 'hl',
														   'hash' => $hash.'/'.$item->getKey(),
														   'cssclass_value' => 'detail',
														   'cssclass_libelle' => 'detail')) ?>
<?php $i++; endforeach; ?>