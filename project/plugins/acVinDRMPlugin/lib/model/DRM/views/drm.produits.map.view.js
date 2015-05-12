function(doc) {
  	if (doc.type == "DRM") {
		for (certification in doc.declaration.certifications) {
	        for (appellation in doc.declaration.certifications[certification].appellations) {
	            for(lieu in doc.declaration.certifications[certification].appellations[appellation].lieux) {
	                for(couleur in doc.declaration.certifications[certification].appellations[appellation].lieux[lieu].couleurs) {
	                    for(cepage in doc.declaration.certifications[certification].appellations[appellation].lieux[lieu].couleurs[couleur].cepages) {    
	                        for(millesime in doc.declaration.certifications[certification].appellations[appellation].lieux[lieu].couleurs[couleur].cepages[cepage].millesimes) {
	                            var hash = "declaration/certifications/"+certification+"/appellations/"+appellation+"/lieux/"+lieu+"/couleurs/"+couleur+"/cepages/"+cepage+"/millesimes/"+millesime;
	                            
				   				emit(["produit", hash], 1);
	                        } // Boucle millesime
	                    } // Boucle cepage
	                } // Boucle couleur
	            } // Boucle lieu
	        } // Boucle appellation
	    } // Boucle certification
	}
}