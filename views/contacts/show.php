<?php if ($contact): ?>
    <h1 class="mb-4"><?= e($contact->nom) ?></h1>
    <dl>
        <dt>Email</dt>
        <dd><?= e(($contact->email ?? '') ?: 'Non renseigné') ?></dd>
        <dt>Téléphone</dt>
        <dd><?= e(($contact->telephone ?? '') ?: 'Non renseigné') ?></dd>
    </dl>
<?php else: ?>
    <h1>Contact introuvable</h1>
    <p>Ce contact est introuvable.</p>
<?php endif; ?>
<a class="btn btn-secondary" href="/contacts">Retour aux contacts</a>
