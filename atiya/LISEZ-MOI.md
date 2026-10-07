# Site ATIYA — guide

Ton site est fait de fichiers simples : pas besoin d'abonnement ni de serveur.
Les commandes arrivent **sur ton WhatsApp**, et le paiement se fait **uniquement à la livraison**.

## Les pages

| Fichier | Page |
|---|---|
| `index.html` | Accueil |
| `boutique.html` | Boutique et catégories (Robes, Ensembles…) |
| `produit.html` | Fiche d'un article |
| `panier.html` | Panier |
| `commande.html` | Commande (coordonnées + paiement à la livraison) |
| `contact.html` | Contact et questions fréquentes |

## 1. Le seul fichier à modifier : `config.js`

Ouvre `config.js` avec un éditeur de texte (Bloc-notes, VS Code…).

1. **Ton numéro WhatsApp** (déjà rempli : 22375883468) : `whatsapp: "22375883468"`,
   c'est-à-dire 223 suivi de tes 8 chiffres, sans « + » ni espaces.
2. **Les frais de livraison** :
   - `livraison: null` : « selon le livreur », tu donnes le prix au téléphone.
   - `livraison: 1000` : 1 000 FCFA ajoutés au total.
   - `livraison: 0` : livraison gratuite.
3. **Tes articles** : dans `PRODUITS`, copie un bloc `{ ... },` et change :
   - le nom, le prix et la catégorie ;
   - les tailles et les couleurs ;
   - la description.
4. **Tes réseaux** : Instagram, TikTok et Facebook (laisse `""` si tu n'en as pas).

⚠️ Garde bien les guillemets `"..."` et les virgules à la fin des lignes.

## 2. Ajouter tes photos

Mets tes photos dans le dossier `images/`, puis :

- **Articles** : dans `config.js`, `images: ["images/robe-rose-1.jpg", "images/robe-rose-2.jpg"]`.
  La première photo est celle qu'on voit dans la boutique.
- **Accueil** : il suffit de nommer les fichiers ainsi :
  - `images/banniere.jpg` : ta propre bannière (faite sur Canva par exemple). Elle remplace l'accueil rose rayé.
  - `images/accueil-1.jpg`, `accueil-2.jpg`, `accueil-3.jpg` : les 3 photos en arche sous « Bienvenue ».
  - `images/questions.jpg` : la photo au-dessus des « Questions fréquentes ».
- **Catégories** : `images/categorie-robes.jpg`, `categorie-ensembles.jpg`, `categorie-hauts.jpg`, `categorie-bas.jpg`.

Tant qu'une photo manque, un cintre rose s'affiche à sa place.

## 3. Voir le site sur ton ordinateur

Double-clique sur `index.html` : le site s'ouvre dans ton navigateur.

## 4. Mettre le site en ligne gratuitement

Le plus simple est **Netlify Drop** :

1. Crée un compte gratuit sur https://www.netlify.com
2. Va sur https://app.netlify.com/drop et glisse-dépose le dossier `atiya`.
3. Tu reçois un lien à partager (sur Instagram, WhatsApp, TikTok…).

Pour mettre à jour le site, refais un glisser-déposer du dossier. Tu pourras aussi acheter un nom de domaine (ex. `atiya.store`) et le relier dans Netlify.

## 5. Comment se passe une commande

1. La cliente choisit un article, sa couleur et sa taille, puis clique sur **Acheter maintenant** (ou ajoute plusieurs articles au panier).
2. Elle remplit : prénom, nom, quartier, adresse ou point de repère, téléphone.
3. Elle clique sur **Valider la commande** : WhatsApp s'ouvre avec le récapitulatif, et elle appuie sur **Envoyer**.
4. Tu reçois la commande sur WhatsApp, avec son numéro (ex. `AT-4F7K2Q`), les articles, le total et l'adresse.
5. Tu l'appelles pour confirmer, tu livres, et elle paie en espèces à la livraison.
