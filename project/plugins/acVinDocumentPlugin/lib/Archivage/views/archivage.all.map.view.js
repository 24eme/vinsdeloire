function(doc) {
    if (!doc.numero_archive) {
        return;
    }
   
	campagne_archive = doc.campagne;
 
	if(doc.campagne_archive) {
		campagne_archive = doc.campagne_archive;
	}

	type_archive = doc.type;

	if(doc.type_archive) {
		type_archive = doc.type_archive;
	}

    emit([type_archive, campagne_archive, doc.numero_archive], 1)
}