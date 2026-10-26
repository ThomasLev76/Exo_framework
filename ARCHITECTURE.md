# Architecture du micro-framework PHP

Ce projet est un micro-framework pédagogique utilisé pour une application de gestion de contacts : consulter la liste, afficher une fiche, ajouter et supprimer un contact. Il sépare la gestion HTTP, le routage, les contrôleurs et les vues. Les données sont conservées dans `data/data.json`, sans base de données. L’interface utilise Bootstrap.

## 1. Cycle de vie d’une requête

Le parcours habituel est le suivant :

```text
Navigateur → public/index.php → Request → Router
                                          ↓
                                     Contrôleur ↔ fichier JSON
                                          ↓
                                         View
                                          ↓
Navigateur ← Response::send() ← Response contenant le HTML
```

1. **Entrée HTTP et initialisation.** Le serveur dirige les requêtes de l’application vers `public/index.php`, le contrôleur frontal. Celui-ci charge `vendor/autoload.php` : Composer associe le namespace `App\` à `src/`, et `Controllers\` à `app/Controllers/`. Il charge aussi `helpers.php`. Le point d’entrée crée ensuite un `Router` et inclut `config/routes.php` pour enregistrer les routes.

2. **Construction de la requête.** `Request::fromGlobals()` lit `$_SERVER`, `$_GET` et `$_POST`. L’objet obtenu contient la méthode HTTP, le chemin sans la query string, les paramètres de recherche et les champs du formulaire.

3. **Recherche de la route.** `Router::resolve()` compare la méthode et le chemin aux routes enregistrées. Les paramètres comme `{id}` sont transformés en captures nommées dans une expression régulière. Le routeur crée le contrôleur puis appelle l’action avec la requête et les paramètres extraits.

4. **Traitement et rendu.** Par exemple, `GET /contacts/1` appelle `ContactController::show()` avec l’identifiant `1`. Le contrôleur lit le JSON et recherche le contact. Il transmet le résultat à `View`, qui rend `contacts/show.php`, puis insère ce contenu dans `layouts/main.php`.

5. **Réponse HTTP.** Le contrôleur renvoie une `Response` HTML, avec un statut 200 si le contact existe ou 404 sinon. Le point d’entrée appelle `send()`, qui envoie le statut, les en-têtes et le corps au navigateur.

Pour un ajout ou une suppression, le formulaire envoie une requête POST. Le contrôleur modifie le JSON puis effectue actuellement une redirection 303 vers `/contacts` avec `header()` et `exit`. Ce cas quitte donc le parcours avant `Response::send()`. L’action `create()`, elle, renvoie une `Response::redirect()` vers le formulaire.

## 2. Rôle des classes et interactions

| Classe | Responsabilité et interactions |
| --- | --- |
| `Request` | Encapsule les données entrantes. Le routeur utilise `method()` et `uri()` ; les contrôleurs lisent les champs avec `input()` et peuvent lire la query string avec `query()`. |
| `Router` | Enregistre les routes GET et POST, extrait leurs paramètres et exécute une fonction ou une action de contrôleur. Il retourne une `Response` et transforme aussi un résultat simple en réponse HTML. |
| `Response` | Regroupe le corps, le statut et les en-têtes. Les méthodes `html()`, `json()` et `redirect()` préparent une réponse ; `send()` l’émet. |
| `View` | Reçoit le dossier des templates. `render()` expose les données avec `extract()` et capture le rendu PHP dans un tampon de sortie. `renderWithLayout()` compose une vue avec le gabarit commun. |
| `HomeController` | Crée sa vue et renvoie la page d’accueil sous forme de réponse HTML. |
| `ContactController` | Gère les actions liées aux contacts et les accès au fichier JSON, grâce aux méthodes privées `lire()` et `sauver()`. Il utilise `View` pour les pages. |
| `Application` | Regroupe la création du routeur et l’enchaînement requête → résolution → envoi. Cette classe existe mais le point d’entrée actuel effectue ces étapes directement, sans l’utiliser. |
| `Container` | Enregistre des fabriques de services avec `set()`, crée les objets à la première demande avec `get()` et réutilise les instances. `has()` vérifie leur disponibilité. Il n’est pas branché au parcours actuel. |

Les vues contiennent la présentation HTML. La fonction `e()` de `helpers.php` échappe les données affichées avec `htmlspecialchars()` pour éviter qu’une saisie utilisateur soit interprétée comme du HTML. Cet échappement est explicite dans les templates.

## 3. Injection de dépendances et réduction du couplage

L’injection de dépendances consiste à fournir à une classe les objets dont elle a besoin, plutôt que de lui faire construire ces objets. Par exemple, un contrôleur pourrait recevoir `View` dans son constructeur. La configuration du chemin des templates serait alors centralisée au lieu d’être répétée dans les contrôleurs.

Le conteneur fourni prépare cette organisation : une fabrique peut créer une vue, puis une autre fabrique peut récupérer cette vue pour construire un contrôleur. Cela facilite les changements de configuration et les tests, car les dépendances peuvent être remplacées sans modifier le traitement des actions.


## 4. Limites et améliorations possibles

- **Persistance et responsabilités :** le contrôleur contient aussi la lecture et l’écriture du JSON. Les erreurs de fichier ou de décodage ne sont pas traitées, et des écritures concurrentes peuvent perdre des modifications. Un dépôt de contacts dédié, avec gestion des erreurs et verrouillage du cycle lecture-modification-écriture, puis éventuellement SQLite, améliorerait cette partie.

- **Validation et sécurité :** le serveur vérifie seulement que le nom n’est pas vide. Il faudrait contrôler les types, longueurs et formats des champs, afficher les erreurs de saisie et ajouter une protection CSRF aux opérations POST. L’application ne dispose pas d’authentification ni de contrôle d’accès.

- **Cohérence HTTP et erreurs :** remplacer les appels directs à `header()` et `exit` par des réponses de redirection permettrait de centraliser tous les envois. Une gestion globale des exceptions fournirait des réponses 500 maîtrisées et des journaux d’erreurs.*

- **Routage :** le routeur prévoit des réponses 404 et 405. Toutefois, une méthode totalement absente de la table des routes retourne immédiatement 404, même si le chemin existe pour une autre méthode. Il faudrait corriger ce cas, ajouter l’en-tête `Allow` aux réponses 405 et mieux encadrer les motifs des routes et leurs paramètres.

- **Vues et vérification :** l’échappement reste manuel et doit être appliqué à chaque donnée utilisateur. Des tests du routage, des statuts HTTP et du stockage, notamment avec un JSON vide ou invalide, permettraient de vérifier les évolutions du framework.
