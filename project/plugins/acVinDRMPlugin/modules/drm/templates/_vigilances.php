<div class="vigilance_list">
    <h3>Points de vigilance</h3>
    <ol>
        <?php foreach ($drmValidation->getWarnings() as $warning): ?>
            <li><?php echo $warning->getRawValue(); ?></li>
        <?php endforeach; ?>
    </ol>
</div>
<br />
