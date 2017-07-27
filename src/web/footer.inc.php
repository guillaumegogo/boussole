<footer>
    <?php if (ENVIRONMENT === ENV_LOCAL) { ?>
        <div class="version"><?= $version ?></div>
    <?php } ?>
    <ul>
        <?php if (ENVIRONMENT === ENV_LOCAL) { ?>
            <li><a href="<?= $url_admin ?>" target="_blank">Administration</a></li>
        <?php } ?>
        <li><a href="mentions.php">Mentions légales</a>
        <li><a href="#">Contact</a>
        <li>Un service proposé par le <a href="http://jeunes.gouv.fr" target="_blank">Ministère chargé de la Jeunesse</a>.
    </ul>
</footer>