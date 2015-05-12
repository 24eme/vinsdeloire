        <?php
        for($i=0; $i<($max_lignes - $nb_lignes);$i++):
        ?>
~ & ~ & ~ & ~ & ~ &\\
        <?php 
        endfor;
        ?>
        \end{tabular}
        };
        \node[draw=gray, inner sep=-2pt, rounded corners=3pt, line width=2pt, fit=(tab1.north west) (tab1.north east) (tab1.south east) (tab1.south west)] {};	
\end{tikzpicture}