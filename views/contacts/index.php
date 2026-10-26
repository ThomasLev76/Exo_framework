<h1 class="mb-4">Contacts</h1>

<?php if ($contacts): ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Email</th>
                    <th scope="col">Téléphone</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><a href="/contacts/<?= (int) $contact->id ?>"><?= e($contact->nom) ?></a></td>
                        <td><?= e($contact->email ?? '') ?></td>
                        <td><?= e($contact->telephone ?? '') ?></td>
                        <td>
                            <form action="/contacts/<?= (int) $contact->id ?>/delete" method="post">
                                <button class="btn btn-danger btn-sm" type="submit" aria-label="Supprimer <?= e($contact->nom) ?>">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Aucun contact enregistré.</p>
<?php endif; ?>

<section id="nouveau-contact" class="mt-4" aria-labelledby="form-title">
    <h2 id="form-title">Ajouter un contact</h2>
    <form action="/contacts" method="post">
        <div class="mb-3">
            <label class="form-label" for="nom">Nom *</label>
            <input class="form-control" id="nom" type="text" name="nom" autocomplete="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" autocomplete="email">
        </div>
        <div class="mb-3">
            <label class="form-label" for="telephone">Téléphone</label>
            <input class="form-control" id="telephone" type="tel" name="telephone" autocomplete="tel">
        </div>
        <p class="text-muted">* Le nom est obligatoire.</p>
        <button class="btn btn-primary" type="submit">Ajouter</button>
    </form>
</section>
