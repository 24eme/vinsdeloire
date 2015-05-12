<?php for($i = 0 ; $i < $add_blank_lines; $i++):  ?>
~ & ~ & ~ & ~ & ~ &\\
<?php endfor;?>
    <?php
    if(!$avoir){
    if (isset($end_document) && $end_document){
        echo "\multicolumn{6}{c}{Aucun escompte n'est prévu pour paiement anticipé. Pénalités de retard : 3 fois le taux d'intér\^{e}t légal} \\\\ ";
        echo "\multicolumn{6}{c}{Indemnité forfaitaire pour frais de recouvrement: 40~\\texteuro{}} \\\\ ";
    }
    else
        echo "\multicolumn{6}{c}{.../...} \\\\";
    }
      ?>
    ~ & ~ & ~ & ~ & ~ & \\
                \end{tabular}
        };
        \node[draw=gray, inner sep=-2pt, rounded corners=3pt, line width=2pt, fit=(tab1.north west) (tab1.north east) (tab1.south east) (tab1.south west)] {};	
\end{tikzpicture}