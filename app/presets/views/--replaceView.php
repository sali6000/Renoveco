<script>
    loadCss("<?= BASE_URL_PUBLIC_ASSETS_CSS ?>cguStylesheet.css");
    document.getElementsByClassName('cell border-bottom')[4].style.borderBottom = "2px solid darkgrey";
</script>

<title><?= $title ?></title>
<div class="container-cgu">

    <h1>Conditions Générales d'Utilisation (CGU)</h1>
    <h5>Dernière mise à jour : 17/07/2024</h5>
    <p>Bienvenue sur le site web d'<?= BASE_ENTREPRISE_TITLE ?>. Les présentes Conditions Générales d’Utilisation (ci-après “CGU”) sont conclues entre tout internaute
        naviguant, ou ayant accès aux fonctionnalités du Site (ci-après dénommé « l’Internaute » ou “le Client” ou
        à qui il est fait référence par « vous » ou «votre ») et <?= BASE_ENTREPRISE_TITLE ?>.</p>

    <p>En accédant et en utilisant notre site web (<?= BASE_ENTREPRISE_HTTP ?>), vous acceptez de vous conformer aux présentes conditions générales d'utilisation (CGU). Si vous n'acceptez pas ces CGU, veuillez ne pas utiliser notre site.</p>
    <hr>
    <h3>1. Informations légales</h3>

    <p><?= $message ?></p>

</div>