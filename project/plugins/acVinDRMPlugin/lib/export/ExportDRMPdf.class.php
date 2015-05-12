<?php
class ExportDRMPdf extends ExportDRM 
{
	public function render($response, $cache = true, $format = 'pdf')
	{
		$format_class = array('html' => 'PrintableHTML',
							  'pdf' => 'PrintablePDF');

		if(!array_key_exists($format, $format_class)) {
			
			throw new sfException(sprintf("Le format %s n'est pas géré", $format));
		}

		$class = $format_class[$format];
		$document = new $class($this->drm->get('_id').'.pdf');
		if (!$cache) {
			$document->removeCache();
		}
		$document->setPaper(PrintableOutput::FORMAT_A4, PrintableOutput::ORIENTATION_LANDING);
		$document->addHtml($this->getContent());
		$content = $document->output();
		$document->addHeaders($response);

		return $content;
	}

	public function getContent() {
		return $this->getPartial('pdf', array('drm' => $this->getDRM(), 
											  'pagers_volume' => $this->getPagersVolume(),
											  'pagers_vrac' => $this->getPagersVrac(),
											  'pager_droits_douane' => $this->getPagerDroitsDouane(),
											  'pagers_code' => $this->getPagersCode(),
											  ));
	}
}