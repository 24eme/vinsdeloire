<li class="<?php echo $cssclass ?> <?php echo ($numero_courant >= $numero) ? 'passe' : '' ?> <?php echo ($numero_courant == $numero) ? 'actif' : '' ?>">
	  <?php if($numero <= $numero_autorise): ?>
      <a href="<?php echo $url ?>">
            <span><?php echo $numero ?>. <?php echo $libelle ?></span>
      </a>
      <?php else: ?>
      <span>
      	<span><?php echo $numero ?>. <?php echo $libelle ?></span>
      </span>
      <?php endif; ?>
</li>