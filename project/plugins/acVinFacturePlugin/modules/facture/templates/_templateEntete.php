<?php
use_helper('Date');
use_helper('Display');
$coordonneesBancaires = $facture->getCoordonneesBancaire();
?>
\documentclass[a4paper,8pt]{article}
\usepackage{geometry} % paper=a4paper
\usepackage[english]{babel}
\usepackage[utf8]{inputenc}
\usepackage{units}
\usepackage{geometry}
\usepackage{graphicx}
\usepackage{fancyhdr}
\usepackage{fp}
\usepackage[table]{xcolor}
\usepackage{tikz}
\usepackage{array}
\usepackage{multicol}
\usepackage{textcomp}
\usepackage{marvosym}
\usepackage{lastpage}
\usepackage{truncate}



\usetikzlibrary{fit}

\renewcommand\sfdefault{phv}

\newcommand{\CutlnPapillon}{	
  	\multicolumn{4}{|c|}{ ~~~~~~~~~~~~~~~~~~~~~~~ } & 
  	\multicolumn{3}{c}{\Rightscissors \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline  \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline  \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline  \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline}
\\    
}

\newcommand{\CutlnPapillonEntete}{	
      & \centering \small{\textbf{Code échéance}} &
    \centering \small{\textbf{Date d'échéance}} &
    \multicolumn{1}{r|}{\small{\textbf{Montant TTC}}}  
     & 
  	\multicolumn{3}{c}{\Rightscissors \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline  \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline  \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline  \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline \Cutline}
\\    
}

\renewcommand{\familydefault}{\sfdefault}
\renewcommand{\TruncateMarker}{\small{...}}

\setlength{\oddsidemargin}{-2cm}
\setlength{\evensidemargin}{-2cm}
\setlength{\textwidth}{19cm}
\setlength{\headheight}{5.3cm}
\setlength{\headwidth}{20.5cm}
\setlength{\topmargin}{-4.5cm}
\addtolength{\textheight}{29.9cm} 

\def\TVA{19.60} 
\def\InterloireAdresse{<?php echo $facture->emetteur->adresse; ?> \\
		       <?php echo $facture->emetteur->code_postal.' '.$facture->emetteur->ville; ?> - France} 
\def\InterloireFacturation{\\Votre contact : <?php echo $facture->emetteur->service_facturation.' - '. $facture->emetteur->telephone;?>
                                             <?php if($facture->emetteur->exist('email')): ?>
                                                    \\ Email : <?php echo $facture->emetteur->email; ?> 
                                              <?php endif;?>} 
\def\InterloireSIRET{429 164 072 00077}
\def\InterloireAPE{APE 9499 Z} 
\def\InterloireTVAIntracomm{FR 73 429164072}
\def\InterloireBANQUE{<?php echo $coordonneesBancaires->banque; ?>}
\def\InterloireBIC{<?php echo $coordonneesBancaires->bic; ?>}
\def\InterloireIBAN{<?php echo $coordonneesBancaires->iban; ?>}

\def\FactureNum{<?php echo $facture->numero_interloire; ?>}
\def\FactureNumREF{<?php echo $facture->numero_reference; ?>}
\def\FactureDate{<?php echo format_date($facture->date_facturation,'dd/MM/yyyy'); ?>}
\def\FactureRefClient{<?php echo $facture->identifiant; ?>}

\def\FactureClientNom{<?php $nom = ($facture->declarant->raison_sociale == '')? $facture->declarant->nom : $facture->declarant->raison_sociale; 
                            echo display_latex_string($nom,';',40);
                     ?>}
\def\FactureClientAdresse{<?php $adresse = ($facture->declarant->adresse == '')? "~" : $facture->declarant->adresse;
                            echo display_latex_string($adresse,';',50,2); ?>}                            
\def\FactureClientCP{<?php echo $facture->declarant->code_postal; ?>}
\def\FactureClientVille{<?php echo $facture->declarant->commune; ?>}

\pagestyle{fancy}
\renewcommand{\headrulewidth}{0pt}

\fancyhf{}

\lhead{
 \textbf{InterLoire} \\  
 \InterloireAdresse \\
 \begin{small} \textbf{\begin{footnotesize}\InterloireFacturation\end{footnotesize}}\\ \end{small}
 \begin{tiny}
         RIB~:~\InterloireBANQUE~(BIC:~\InterloireBIC~IBAN:~\InterloireIBAN) 
 \end{tiny} \\
 \begin{tiny}
         SIRET~\InterloireSIRET ~-~\InterloireAPE ~- TVA~Intracommunutaire~\InterloireTVAIntracomm
\end{tiny}
 }
\rhead{\includegraphics[scale=1]{<?php echo realpath(dirname(__FILE__)."/../../../../../web/data")."/logo_new.jpg"; ?>}}