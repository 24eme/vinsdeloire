<?php
$gitcommit = str_replace("\n",'',file_get_contents('../../.git/ORIG_HEAD'));
 ?>
		<!-- #footer -->
		<footer id="footer">
			<p>Copyright 2011 - <a href="#">Plan du site</a> - <a href="#">Contact</a> - <a href="#">Mentions légales</a> - <a href="#">Crédits</a></p>

            <p style="margin-top: 5px;">Logiciel libre sous License AGPL-3.0 version <a href="https://github.com/24eme/vinsdeloire/tree/<?php echo $gitcommit ?>"><?php echo substr($gitcommit, 0, 10) ?></a></p>
		</footer>
		<!-- fin #footer -->
