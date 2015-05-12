function(doc) 
{
    if (doc.type != "Configuration")
        return ;
    var nb = 0;
    for (certification in doc.declaration.certifications) 
    {
        var certificationLibelle = doc.declaration.certifications[certification].libelle;
        var departementsCertification = doc.declaration.certifications[certification].departements;
        var detailCertification = doc.declaration.certifications[certification].detail;
        var interprosCertification = new Array();
        for (i in doc.declaration.certifications[certification].interpro)
        {
            interprosCertification[i] = new Array();
            interprosCertification[i]["label"] = doc.declaration.certifications[certification].interpro[i].labels;
            interprosCertification[i]["douane"] = doc.declaration.certifications[certification].interpro[i].droits.douane.pop();
            interprosCertification[i]["cvo"] = doc.declaration.certifications[certification].interpro[i].droits.cvo.pop();
        }
        
        for (genre in doc.declaration.certifications[certification].genres)
        {
            var genreLibelle = doc.declaration.certifications[certification].genres[genre].libelle;
            var departementsGenre = doc.declaration.certifications[certification].genres[genre].departements;
            var detailGenre = detailCertification;
            var interprosGenre = interprosCertification;
            if (doc.declaration.certifications[certification].genres[genre].hasOwnProperty("interpro"))
            {
                nb = 0;
                for (item in doc.declaration.certifications[certification].genres[genre].interpro) {
                    nb = nb + 1;
                }
                if (nb > 0) {
                    interprosGenre = new Array();
                    for (i in doc.declaration.certifications[certification].genres[genre].interpro)
                    {
                        interprosGenre[i] = new Array();
                        interprosGenre[i]["label"] = new Array();
                        interprosGenre[i]["douane"] = new Array();
                        interprosGenre[i]["cvo"] = new Array();
                        if (interprosCertification.hasOwnProperty(i))
                        {
                            interprosGenre[i]["label"] = interprosCertification[i]["label"];
                            interprosGenre[i]["douane"] = interprosCertification[i]["douane"];
                            interprosGenre[i]["cvo"] = interprosCertification[i]["cvo"];
                        }
                        if (doc.declaration.certifications[certification].genres[genre].interpro[i].labels.length > 0)
                        {
                            interprosGenre[i]["label"] = doc.declaration.certifications[certification].genres[genre].interpro[i].labels;
                        }
                        if (doc.declaration.certifications[certification].genres[genre].interpro[i].hasOwnProperty("droits"))
                        {
                            if (doc.declaration.certifications[certification].genres[genre].interpro[i].droits.hasOwnProperty("douane") && doc.declaration.certifications[certification].genres[genre].interpro[i].droits.douane.length > 0) 
                                interprosGenre[i]["douane"] = doc.declaration.certifications[certification].genres[genre].interpro[i].droits.douane.pop();
                            if (doc.declaration.certifications[certification].genres[genre].interpro[i].droits.hasOwnProperty("cvo") && doc.declaration.certifications[certification].genres[genre].interpro[i].droits.cvo.length > 0)
                                interprosGenre[i]["cvo"] = doc.declaration.certifications[certification].genres[genre].interpro[i].droits.cvo.pop();
                        }
                    }
                }
            }
            if (departementsGenre.length == 0)
            {
                departementsGenre = departementsCertification;
            }
            if (doc.declaration.certifications[certification].genres[genre].hasOwnProperty('detail'))
            {
                detailGenre = doc.declaration.certifications[certification].genres[genre].detail;
            }
            for (appellation in doc.declaration.certifications[certification].genres[genre].appellations) 
            {
                var appellationLibelle = doc.declaration.certifications[certification].genres[genre].appellations[appellation].libelle;
                var departementsAppellation = doc.declaration.certifications[certification].genres[genre].appellations[appellation].departements;
                var detailAppellation = detailGenre;
                var interprosAppellation = interprosGenre;
                if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].hasOwnProperty("interpro"))
                {
                    nb = 0;
                    for (item in doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro) {
                        nb = nb + 1;
                    }
                    if (nb > 0) {
                        interprosAppellation = new Array();
                        for (i in doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro)
                        {
                            interprosAppellation[i] = new Array();
                            interprosAppellation[i]["label"] = new Array();
                            interprosAppellation[i]["douane"] = new Array();
                            interprosAppellation[i]["cvo"] = new Array();
                            if (interprosGenre.hasOwnProperty(i))
                            {
                                interprosAppellation[i]["label"] = interprosGenre[i]["label"];
                                interprosAppellation[i]["douane"] = interprosGenre[i]["douane"];
                                interprosAppellation[i]["cvo"] = interprosGenre[i]["cvo"];
                            }
                            if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].labels.length > 0)
                            {
                                interprosAppellation[i]["label"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].labels;
                            }
                            if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].hasOwnProperty("droits"))
                            {
                                if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].droits.hasOwnProperty("douane") && doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].droits.douane.length > 0)
                                    interprosAppellation[i]["douane"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].droits.douane.pop();
                                if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].droits.hasOwnProperty("cvo") && doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].droits.cvo.length > 0)
                                    interprosAppellation[i]["cvo"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].interpro[i].droits.cvo.pop();
                            }
                        }
                    }
                }
                if (departementsAppellation.length == 0)
                {
                    departementsAppellation = departementsGenre;
                }
                if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].hasOwnProperty('detail'))
                {
                    detailAppellation = doc.declaration.certifications[certification].genres[genre].appellations[appellation].detail;
                }
                for(mention in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions) 
                {
                    var mentionLibelle = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].libelle;
                    var departementsMention = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].departements;
                    var detailMention = detailAppellation;
                    var interprosMention = interprosAppellation;
                    if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].hasOwnProperty("interpro"))
                    {
                        nb = 0;
                        for (item in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro) {
                            nb = nb + 1;
                        }
                        if (nb > 0)
                        {
                            interprosMention = new Array();
                            for (i in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro)
                            {
                                interprosMention[i] = new Array();
                                interprosMention[i]["label"] = new Array();
                                interprosMention[i]["douane"] = new Array();
                                interprosMention[i]["cvo"] = new Array();
                                if (interprosAppellation.hasOwnProperty(i))
                                {
                                    interprosMention[i]["label"] = interprosAppellation[i]["label"];
                                    interprosMention[i]["douane"] = interprosAppellation[i]["douane"];
                                    interprosMention[i]["cvo"] = interprosAppellation[i]["cvo"];
                                }
                                if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro[i].labels.length > 0)
                                {
                                    interprosMention[i]["label"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro[i].labels;
                                }
                                if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro[i].hasOwnProperty("droits"))
                                {
                                    if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro[i].droits.hasOwnProperty("douane") && doc.declaration.certifications[certification].genres[genre].appellations[appellation].lieux[lieu].interpro[i].droits.douane.length > 0)
                                        interprosMention[i]["douane"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro[i].droits.douane.pop();
                                    if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro[i].droits.hasOwnProperty("cvo") && doc.declaration.certifications[certification].genres[genre].appellations[appellation].lieux[lieu].interpro[i].droits.cvo.length > 0)
                                        interprosMention[i]["cvo"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].interpro[i].droits.cvo.pop();
                                }
                            }
                        }
                    }
                    if (departementsMention.length == 0)
                    {
                        departementsMention = departementsAppellation;
                    }
                    if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].hasOwnProperty('detail'))
                    {
                        detailLieu = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].detail;
                    } // END
                    for(lieu in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux) 
                    {
                        var lieuLibelle = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].libelle;
                        var departementsLieu = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].departements;
                        var detailLieu = detailMention;
                        var interprosLieu = interprosMention;
                        if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].hasOwnProperty("interpro"))
                        {
                            nb = 0;
                            for (item in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro) {
                                nb = nb + 1;
                            }
                            if (nb > 0)
                            {
                                interprosLieu = new Array();
                                for (i in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro)
                                {
                                    interprosLieu[i] = new Array();
                                    interprosLieu[i]["label"] = new Array();
                                    interprosLieu[i]["douane"] = new Array();
                                    interprosLieu[i]["cvo"] = new Array();
                                    if (interprosMention.hasOwnProperty(i))
                                    {
                                        interprosLieu[i]["label"] = interprosMention[i]["label"];
                                        interprosLieu[i]["douane"] = interprosMention[i]["douane"];
                                        interprosLieu[i]["cvo"] = interprosMention[i]["cvo"];
                                    }
                                    if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].labels.length > 0)
                                    {
                                        interprosLieu[i]["label"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].labels;
                                    }
                                    if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].hasOwnProperty("droits"))
                                    {
                                        if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].droits.hasOwnProperty("douane") && doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].droits.douane.length > 0)
                                            interprosLieu[i]["douane"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].droits.douane.pop();
                                        if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].droits.hasOwnProperty("cvo") && doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].droits.cvo.length > 0)
                                            interprosLieu[i]["cvo"] = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].interpro[i].droits.cvo.pop();
                                    }
                                }
                            }
                        }
                        if (departementsLieu.length == 0)
                        {
                            departementsLieu = departementsMention;
                        }
                        if (doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].hasOwnProperty('detail'))
                        {
                            detailLieu = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].detail;
                        }
                        
                        for(couleur in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].couleurs) 
                        {
                            var couleurLibelle = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].couleurs[couleur].libelle;
                            
                            for(cepage in doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].couleurs[couleur].cepages) 
                            {    
                                var cepageLibelle = doc.declaration.certifications[certification].genres[genre].appellations[appellation].mentions[mention].lieux[lieu].couleurs[couleur].cepages[cepage].libelle;
                                var hash = "/declaration/certifications/"+certification+"/genres/"+genre+"/appellations/"+appellation+"/mentions/"+mention+"/lieux/"+lieu+"/couleurs/"+couleur+"/cepages/"+cepage;
                                
                                for(i in interprosLieu)
                                {
                                    emit([i, certificationLibelle, genreLibelle, appellationLibelle, mentionLibelle, lieuLibelle, couleurLibelle, cepageLibelle, hash], {"departements": departementsLieu, "douane": interprosLieu[i]["douane"], "cvo": interprosLieu[i]["cvo"], "labels": interprosLieu[i]["label"], "entrees": {"repli": detailLieu.entrees.repli.readable, "declassement": detailLieu.entrees.declassement.readable}, "sorties": {"repli": detailLieu.sorties.repli.readable, "declassement": detailLieu.sorties.declassement.readable}});
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}