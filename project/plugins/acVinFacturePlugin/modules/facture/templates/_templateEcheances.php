<?php
use_helper('Float');
use_helper('Date');
?>
\begin{center}
Echéances (hors régularisation) : A = à 60 jours fin de mois, B = au 31/03 et au 30/06, C = au 31/03, au 30/06 et au 30/09, D = au 30/09

\begin{minipage}[b]{1\textwidth}

\begin{tabular}{|p{9mm} p{25mm} p{25mm} p{20mm} | p{36mm} p{36mm} p{36mm}}
            \hline
	\multicolumn{4}{|>{\columncolor[rgb]{0.8,0.8,0.8}}c|}{\centering \small{\textbf{Partie à conserver}}} &
	\multicolumn{3}{>{\columncolor[rgb]{0.8,0.8,0.8}}c}{\centering \small{\textbf{Partie à joindre au règlement}}} \\  	
	
        \CutlnPapillonEntete
        <?php $nb = count($echeances) ; foreach ($echeances as $key => $papillon) : ?>
        &
    &
    &
    &
    \centering \small{Echéance} &
    \centering \small{Ref. Client / Ref. Facture} &
    \multicolumn{1}{c}{\small{Montant TTC}} \\
                        
                \centering \small{<?php echo $nb - $key; ?>} & 
                \centering \small{<?php echo $papillon->echeance_code ?>} &
                \centering \small{\textbf{<?php echo format_date($papillon->echeance_date,'dd/MM/yyyy'); ?>}} &
                \multicolumn{1}{r|}{\centering \small{\textbf{<?php echo echoArialFloat($papillon->montant_ttc); ?>~\texteuro{}}}} &
                \centering \small{\textbf{<?php echo format_date($papillon->echeance_date,'dd/MM/yyyy'); ?>}} &
                \centering \small{\FactureRefClient~/~\FactureNum} &               
                \multicolumn{1}{r}{\small{\textbf{<?php echo echoArialFloat($papillon->montant_ttc); ?>~\texteuro{}}}}  \\

                \CutlnPapillon
        <?php endforeach; ?> 
                
\end{tabular}
\end{minipage}
\end{center}