<ul>
    <?php foreach ($points as $controle): ?>
        <li class="<?php echo $css_class ?>">
            <?php if($controle->getRawValue()->getLien()) :?>
            <?php echo $controle->getRawValue()->getMessage() ?> : <a href="<?php echo $controle->getRawValue()->getLien() ?>">
            <?php echo $controle->getRawValue()->getInfo() ?></a>
            <?php else: ?>
            <?php echo $controle->getRawValue() ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>