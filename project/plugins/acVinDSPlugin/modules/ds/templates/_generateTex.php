<?php
use_helper('Date');
use_helper('Display');
$pointille = ' . . . . . . . . . . . . . . . . . . . . . . . . . .';
$coordonneesInterLoire = $ds->getCoordonneesIL();
?>
\documentclass[a4paper,8pt]{article}
\usepackage{geometry} % paper=a4paper
\usepackage[english]{babel}
\usepackage[utf8]{inputenc}
\usepackage{units}
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
\usepackage{amssymb}

\usetikzlibrary{fit}

\renewcommand\sfdefault{phv}
\renewcommand{\familydefault}{\sfdefault}


\setlength{\oddsidemargin}{-2cm}
\setlength{\evensidemargin}{-2cm}
\setlength{\textwidth}{19cm}
\setlength{\headheight}{1cm}
\setlength{\topmargin}{-4.6cm}
\addtolength{\textheight}{29.9cm} 


\def\DSDEADLINEDATE{31 ao\^{u}t 2012}	
\def\DSSTOCKSDATE{<?php echo format_date($ds->date_stock, 'dd/MM/yyyy'); ?>}		
\def\DSHEADTITRE{\textsl{\textbf{DECLARATION DE STOCKS DE VIN}}}       
\def\DSHEADTEXTE{<?php echo enteteDs($ds,format_date($ds->date_echeance, 'dd/MM/yyyy')); ?>}
\def\DSNUMERO{<?php echo $ds->_id; ?>}

\def\DSClientNUM{<?php echo $ds->identifiant; ?>}
\def\DSClientCVI{<?php echo ($ds->declarant->cvi) ? $ds->declarant->cvi : $pointille; ?>}

\def\DSClientNom{<?php
$nom = ($ds->declarant->raison_sociale) ? $ds->declarant->raison_sociale : $ds->declarant->nom;
echo ($nom) ? cut_latex_string($nom,35) : $pointille;
?>}
\def\DSClientAdresse{<?php echo ($ds->declarant->adresse) ? cut_latex_string(str_replace(';', ' ', $ds->declarant->adresse),35) : $pointille; ?>}

\def\DSClientCP{<?php echo $ds->declarant->code_postal; ?>}
\def\DSClientVille{<?php echo cut_latex_string($ds->declarant->commune,29); ?>}
\def\DSClientTelephone{<?php echo ($etablissement->getContact()->telephone_bureau) ? $etablissement->getContact()->telephone_bureau : $pointille; ?>}
\def\DSClientMobile{<?php echo ($etablissement->getContact()->telephone_mobile) ? $etablissement->getContact()->telephone_mobile : $pointille; ?>}
\def\DSClientFax{<?php echo ($etablissement->getContact()->fax) ? $etablissement->getContact()->fax : $pointille; ?>}


\def\DSClientNomFenetre{<?php  echo display_latex_string($nom,';',30); ?>}
\def\DSClientAdresseFenetre{<?php echo display_latex_string($ds->declarant->adresse,';',45); ?>}
\def\DSClientVilleFenetre{<?php echo display_latex_string($ds->declarant->commune,';',35); ?>}

\def\InterloireAdresse{\textbf{INTERLOIRE} - <?php echo $coordonneesInterLoire['adresse'].' - '.$coordonneesInterLoire['code_postal'].' '.$coordonneesInterLoire['ville']; ?> \\
<?php $email = '' ; if (isset($coordonneesInterLoire['email'])) { $email =' - '.$coordonneesInterLoire['email']; } echo $coordonneesInterLoire['telephone'].$email; ?>} 


\begin{document}

\begin{minipage}[t]{0.60\textwidth}
\begin{tikzpicture}
\node[inner sep=1pt] (tab0){
\begin{tabular}{c}
\DSHEADTITRE \\
\DSHEADTEXTE \\
\end{tabular}
};
\node[draw=gray, inner sep=0pt, rounded corners=3pt, line width=2pt, fit=(tab0.north west) (tab0.north east) (tab0.south east) (tab0.south west)] {};	
\end{tikzpicture}
\end{minipage}
\hfill
\begin{minipage}[t]{0.40\textwidth}
\begin{flushright}
\includegraphics[scale=0.8]{<?php echo realpath(dirname(__FILE__)."/../../../../../web/data")."/logo_new.jpg"; ?>}
\end{flushright}
\end{minipage}
\bigskip


\begin{minipage}[t]{0.5\textwidth}
\begin{flushleft}
$\square$ Informations correctes~~~$\square$ Informations à corriger
\begin{tikzpicture}
\node[inner sep=1pt] (tab2){
\begin{tabular}{>{\columncolor{lightgray}} l | p{102mm}}

\centering Ref. Client &
\multicolumn{1}{r}{\DSClientNUM} \\

\centering \textbf{N° CVI} &
\multicolumn{1}{r}{\DSClientCVI} \\

\centering Adresse cave : &
\multicolumn{1}{r}{\DSClientAdresse} \\

&
\multicolumn{1}{r}{\DSClientCP ~\DSClientVille} \\
&
\multicolumn{1}{r}{ . . . . . . . . . . . . . . . . . . . . . . . . .} \\

\centering Téléphone : &
\multicolumn{1}{r}{\DSClientTelephone} \\

\centering Télécopie : &
\multicolumn{1}{r}{\DSClientFax} \\

\centering Portable : &
\multicolumn{1}{r}{\DSClientMobile} \\
\hline                 
\end{tabular}
};
\node[draw=gray, inner sep=0pt, rounded corners=3pt, line width=2pt, fit=(tab2.north west) (tab2.north east) (tab2.south east) (tab2.south west)] {};	
\end{tikzpicture} 
\end{flushleft}
\end{minipage}
\hfill
\hspace{1.5cm}
\begin{minipage}[t]{0.5\textwidth}
\vspace{1cm}
\begin{flushleft}		
\textbf{\DSClientNomFenetre \\}				
\DSClientAdresseFenetre \\
\DSClientCP ~\DSClientVilleFenetre \\
\end{flushleft}
\end{minipage}

\begin{minipage}[t]{0.5\textwidth}
\begin{flushleft}
Référence : \textit{\textbf{\DSNUMERO}}
\end{flushleft}
\end{minipage}
\hfill
\begin{minipage}[t]{0.5\textwidth}
\begin{flushright}
page \thepage / 1
\end{flushright}
\end{minipage}
\begin{center}
\fbox{
\textbf{RENSEIGNEMENTS RELATIFS AUX STOCKS DE VIN AU \DSSTOCKSDATE}}
\end{center}

\centering
\begin{tikzpicture}
\node[inner sep=1pt] (tab1){

\begin{tabular}{b{15mm}| m{95mm} | m{22mm} | m{22mm} |b{22mm}|}

\rowcolor{lightgray}
\centering \textbf{Code} &
\centering \textbf{Appellations} &
\centering \textbf{Volume en hl} &
\centering \textbf{<?php //echo 'VCI'; ?>} &
\multicolumn{1}{>{\columncolor{lightgray}} c|}{ \textbf{<?php //echo 'Reserve qual.'; ?>}} 
\\
\hline
<?php
$nblignes = 0;
foreach ($ds->declarations as $declaration) :

    if ($declaration->hasElaboration()) :

      $nblignes += 2;

        echo sprintf("%04d",$declaration->code_produit) ?> &
        <?php echo $declaration->produit_libelle . ' (en cave)'; ?> ~ &
        ~ &
        <?php echo ($declaration->vci) ? $declaration->vci : '~'; ?>  &
        <?php echo ($declaration->reserve_qualitative) ? $declaration->reserve_qualitative : '~'; ?> 
        \\ \hline
        <?php echo sprintf("%04d",$declaration->code_produit) ?> ~ &
        <?php echo $declaration->produit_libelle . ' (en élaboration)'; ?> ~ &
        ~ &
        <?php echo ($declaration->vci) ? $declaration->vci : '~'; ?>  &
        <?php echo ($declaration->reserve_qualitative) ? $declaration->reserve_qualitative : '~'; ?> 
        \\ \hline
        <?php else:
	  $nblignes += 1;
        ?>
        <?php echo sprintf("%04d",$declaration->code_produit); ?> &
        <?php echo $declaration->produit_libelle; ?> ~ &
        ~ &
        <?php echo ($declaration->vci) ? $declaration->vci : '~'; ?> &
        <?php echo ($declaration->reserve_qualitative) ? $declaration->reserve_qualitative : '~'; ?>
        \\ \hline
    <?php
    endif;
endforeach;

for ($i = 0; $i < (33 - $nblignes); $i++) :
    ?>
    ~ & ~ & ~ & ~ & ~ \\ \hline 
<?php endfor; ?>            
\end{tabular}
};
\node[draw=gray, inner sep=0pt, rounded corners=3pt, line width=2pt, fit=(tab1.north west) (tab1.north east) (tab1.south east) (tab1.south west)] {};	

\end{tikzpicture}
\small{<?php echo '~'; //echo 'VCI : Volumes complémentaires individuels en attente de revendication - Réserve qualitative : Volumes en attente de revendication'; ?>}
\begin{center}
\hspace{5cm}
Date et signature :	
\end{center}
\begin{center}
~\\~\\~\\
\InterloireAdresse
\end{center}

\end{document}
