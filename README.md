# 🎓 Gestion des étudiants (TP PHP)

Petite application web PHP de gestion d'étudiants : connexion, liste avec recherche et tri,
ajout, modification, suppression et photo de profil. Les données sont stockées dans un
fichier texte (`data/etudiants.txt`), une ligne par étudiant :

```
id;nom;prenom;filiere;note;photo
1;benali;sara;GI;14.5;etu_65a1.jpg
```

## Fonctionnalités
- Connexion / déconnexion (sessions) + cookie « dernier login » (7 jours)
- Liste avec recherche (PHP + JavaScript instantané), tri, statistiques (nombre, moyenne, meilleure note)
- Ajout / modification avec validation côté serveur
- Envoi de photo (jpg, jpeg, png – 1 Mo max)
- Suppression avec confirmation JavaScript
- Bonus : export CSV, refus des doublons nom + prénom, moyenne par filière

## Installation
1. Copier le dossier `gestion_etudiants` dans `htdocs` (XAMPP/WAMP) ou lancer :
   ```bash
   php -S localhost:8000
   ```
2. Vérifier PHP via `http://localhost:8000/test.php`
3. S'assurer que `data/` et `uploads/` sont accessibles en écriture.
4. Ouvrir `http://localhost:8000/`

## Comptes de test
| Login | Mot de passe |
|-------|--------------|
| admin | admin123     |
| imane | ensa2026     |

(définis dans la constante `UTILISATEURS` de `functions.php`)

## Structure
```
gestion_etudiants/
├── data/etudiants.txt   # stockage
├── uploads/             # photos
├── functions.php        # constantes, session, fonctions
├── header.php / footer.php
├── index.php            # connexion
├── liste.php            # liste, recherche, tri, stats
├── ajouter.php          # ajout + modification (?id=)
├── supprimer.php        # suppression (?id=)
├── logout.php
├── export.php           # bonus : CSV
├── test.php             # phpinfo()
├── style.css
└── script.js
```

## Remarque
Projet pédagogique : mots de passe en clair et suppression via lien GET, comme demandé dans le TP.
Pour un vrai projet, utiliser `password_hash()` et des requêtes POST avec jeton CSRF.
