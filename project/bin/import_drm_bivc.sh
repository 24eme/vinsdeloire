#!/bin/bash

. bin/config.inc

EXPORTDIR=data/drm_externe
LOGFILE=/tmp/bivc2loire.$$.log

mkdir $EXPORTDIR 2> /dev/null
echo > $LOGFILE
echo "# Récuperations des fichiers de BIVC" >> $LOGFILE
echo >> $LOGFILE
curl -s $URLDRMBIVC | while read url
do
    FILENAME=$(echo -n $url | sed -r "s|^.+/(.+)$|\1|")
    if ! test -f "$EXPORTDIR/$FILENAME"; then
        echo $FILENAME
        curl -s $url > $EXPORTDIR/$FILENAME
    fi
done >> $LOGFILE
echo >> $LOGFILE
echo "# Import des fichiers" >> $LOGFILE
echo >> $LOGFILE
rm -f $EXPORTDIR/*err

ls $EXPORTDIR | grep -E "\.csv$" | awk -F '-' '{ version=99; if($4) { v=$4; gsub(".csv","",v); gsub("M", "", v); version=(version - v); } printf("%02d%d;%s\n", version, $3, $0) }' | sort | cut -d ";" -f 2 | while read csvfile
do
    csvfilevinsi=$csvfile".vinsi"

    if test -f $EXPORTDIR/$csvfilevinsi  ; then
	continue;
    fi

    grep -v ';acquitte;' $EXPORTDIR/$csvfile > $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;stocks_debut;initial;/;suspendu;stocks_debut;revendique;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;stocks_fin;final;/;suspendu;stocks_fin;revendique;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;sorties;ventefrancecrd;/;suspendu;sorties;ventefrancebouteillecrd;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;sorties;vracsanscontratacquitte;/;suspendu;sorties;vracsanscontrat;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;sorties;creationvractirebouche;/;suspendu;sorties;vracsanscontrat;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;sorties;creationvrac;/;suspendu;sorties;vracsanscontrat;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;sorties;embouteillage;/;suspendu;sorties;travailafacon;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;sorties;transfertcomptamatiere;/;suspendu;sorties;transfertsinternes;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;entrees;transfertcomptamatierecession;/;suspendu;entrees;transferts;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;entrees;regularisation;/;suspendu;entrees;excedents;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;entrees;achatnoncrd;/;suspendu;entrees;revendique;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;entrees;recolte;/;suspendu;entrees;revendique;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;suspendu;entrees;retourembouteillage;/;suspendu;entrees;travailafacon;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;Autres pays;/;Inconnu;/' $EXPORTDIR/$csvfilevinsi
    sed -i 's/;Côtes de Duras Blanc \(moelleux\|sec\) ();/;Côtes de Duras Blanc (1B117D);/' $EXPORTDIR/$csvfilevinsi

    periode=$(echo -n $csvfile | cut -d "_" -f 2)
    noaccises=$(cat $EXPORTDIR/$csvfilevinsi | head -n 1 | cut -d ";" -f 4)
    cvi=$(cat $EXPORTDIR/$csvfilevinsi | head -n 1 | cut -d ";" -f 3 | grep -Eo "\(.+\)" | sed 's/[()]//g')

    if ! test $noaccises; then
        noaccises="0"
    fi

    if test -s $EXPORTDIR/$csvfilevinsi  ; then
        echo -n "$csvfilevinsi : "
        php symfony drm:edi-import $EXPORTDIR/$csvfilevinsi $periode "$noaccises" "$cvi" 2>&1 | grep -i '[a-z0-9]' | tr '\n' '|' > /tmp/import_bivc.$$.log
        if ! grep -E 'Création :|Existe :' /tmp/import_bivc.$$.log ;then
            mv -f $EXPORTDIR/$csvfilevinsi  $EXPORTDIR/$csvfilevinsi".err"
            cat /tmp/import_bivc.$$.log;
            echo
        fi
        rm -f /tmp/import_bivc.$$.log
    fi

done >> $LOGFILE

for email in $EMAILS_RETOURXML ; do
cat $LOGFILE | mail -s "[VINSI] import bivc" $email
done
rm $LOGFILE
