<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Mon Framework') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar bg-light mb-4" aria-label="Navigation principale">
        <div class="container justify-content-start gap-3">
            <a class="navbar-brand" href="/">Mon Framework</a>
            <a class="nav-link" href="/">Accueil</a>
            <a class="nav-link" href="/contacts">Contacts</a>
        </div>
    </nav>
    <main class="container">
        <?= $content ?>
    </main>
    <footer class="container border-top mt-5 py-3 text-muted">
        &copy; <?= date('Y') ?> — Micro-framework pédagogique
    </footer>
</body>
</html>
