function(doc) {
    if (doc.type != "DRM") {
        return;     
    }

    if (!doc.valide.date_saisie) {
        return;
    }

    var societe = null;

    for (certification_key in doc.declaration.certifications) {
        var certification = doc.declaration.certifications[certification_key];
        for (genre_key in certification.genres) {
            var genre = certification.genres[genre_key];
            for (appellation_key in genre.appellations) {
                var appellation = genre.appellations[appellation_key];
                for(mention_key in appellation.mentions) {
                    var mention = appellation.mentions[mention_key];
                    for(lieu_key in mention.lieux) {
                        var lieu = mention.lieux[lieu_key];
                        for(couleur_key in lieu.couleurs) {
                            var couleur = lieu.couleurs[couleur_key];
                            for(cepage_key in couleur.cepages) { 
                                var cepage = couleur.cepages[cepage_key];
                                var hash = "/declaration/certifications/"+certification_key+"/genres/"+genre_key+"/appellations/"+appellation_key+"/mentions/"+mention_key+"/lieux/"+lieu_key+"/couleurs/"+couleur_key+"/cepages/"+cepage_key;
                                emit([doc.campagne, societe, doc.identifiant, hash, doc.periode, doc.version], [cepage.total_debut_mois, cepage.total_entrees, cepage.total_recolte, cepage.total_sorties, cepage.total_facturable, cepage.total, doc.declarant.nom, null]);
                            } // Boucle cepage
                        } // Boucle couleur
                    } // Boucle lieu
                } // Boucle mention
            } // Boucle appellation
        } // Boucle genre
    } // Boucle certification
}