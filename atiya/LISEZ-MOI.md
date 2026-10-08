# Site Atiya Concept — guide

Ton site est fait de fichiers simples : pas besoin d'abonnement ni de serveur.
Les commandes arrivent **sur ton WhatsApp**, et le paiement se fait **uniquement à la livraison**.

## Les pages

| Fichier | Page |
|---|---|
| `index.html` + `accueil.css` | Accueil |
| `boutique.html` | Boutique et catégories (Robes, Ensembles…) |
| `produit.html` | Fiche d'un article |
| `panier.html` | Panier |
| `commande.html` | Commande (coordonnées + paiement à la livraison) |
| `contact.html` | Contact et questions fréquentes |

## 1. Le seul fichier à modifier : `config.js`

Ouvre `config.js` avec un éditeur de texte (Bloc-notes, VS Code…).

1. **Ton numéro WhatsApp** (déjà rempli) : `whatsapp: "22375883468"`.
2. **Les frais de livraison** :
   - `livraison: null` : « prix fixé par le livreur » (c'est le livreur qui fixe le prix).
   - `livraison: 1000` : 1 000 FCFA ajoutés au total.
   - `livraison: 0` : livraison gratuite.
3. **Tes articles** : dans `PRODUITS`, copie un bloc `{ ... },` et change :
   - **l'id** en premier : un nom unique sans espaces ni accents (ex. `robe-wax-bleue`), sinon le nouvel article ouvre l'ancien ;
   - le nom, le prix (**sans espace ni guillemets**, ex. `prix: 15000,`) et la catégorie (`"robes"`, `"ensembles"`, `"hauts"` ou `"bas"`) ;
   - les tailles et les couleurs ;
   - la description (sur une seule ligne).
4. **Délai, échanges et horaires** :
   - `heureLimite: "16 h"` : commande passée avant 16 h → livrée le jour même ; après → livrée le lendemain ;
   - `echange: "..."` : ta règle d'échange, affichée sur chaque article et dans les questions fréquentes ;
   - `horaires: "Tous les jours, de 10 h à minuit"`.
5. **Tes réseaux** : Instagram, TikTok et Facebook (laisse `""` si tu n'en as pas).

⚠️ Garde bien les guillemets `"..."` autour des textes et les virgules à la fin des lignes. Les nombres (prix, livraison) s'écrivent **sans** guillemets.
Si tu fais une erreur, le site affiche un message (rouge ou jaune) qui t'explique quoi corriger. Le message jaune n'apparaît que sur ton ordinateur, jamais chez les clientes.

## 2. Ajouter tes photos

Mets tes photos dans le dossier `images/`, puis :

- **Articles** : dans `config.js`, `images: ["images/robe-rose-1.jpg", "images/robe-rose-2.jpg"]`.
  La première photo est celle qu'on voit dans la boutique.
- **Accueil** : il suffit de nommer les fichiers ainsi :
  - `images/accueil-hero.jpg` : la grande photo en haut de l'accueil (photo en hauteur) ;
  - `images/accueil-maison.jpg` : la photo de la partie « La maison » ;
- **Catégories** : `images/categorie-robes.jpg`, `categorie-ensembles.jpg`, `categorie-hauts.jpg`, `categorie-bas.jpg`.
- **Logo** : déjà installé (`images/logo.png`). Pour le changer, remplace ce fichier par un autre du même nom.

Tant qu'une photo manque, un satin rose ou un cintre s'affiche à sa place.

## 3. Voir le site sur ton ordinateur

Double-clique sur `index.html` : le site s'ouvre dans ton navigateur.

## 4. Mettre le site en ligne gratuitement

Ton site est en ligne sur **Cloudflare Pages** (gratuit, et la vente est autorisée) :
👉 **https://atiya-concept.pages.dev**

Pour mettre à jour le site après une modification :

1. Connecte-toi sur https://dash.cloudflare.com
2. Va dans **Compute** → **Workers & Pages**, puis clique sur ton projet `atiya-concept`.
3. Clique sur **Create deployment** (nouveau déploiement), choisis ton dossier `ATIYA` en entier puis **Save and Deploy**.
4. Après une minute, recharge le site sur ton téléphone.

Tu pourras aussi acheter un nom de domaine (ex. `atiyaconcept.com`) et le relier dans Cloudflare (onglet **Custom domains** du projet).

## 5. Comment se passe une commande

1. La cliente choisit un article, sa couleur et sa taille, puis clique sur **Acheter maintenant** (ou ajoute plusieurs articles au panier).
2. Elle remplit : prénom, nom, quartier et téléphone.
3. Elle clique sur **Valider la commande** : WhatsApp s'ouvre avec le récapitulatif, et elle appuie sur **Envoyer**.
4. Tu reçois la commande sur WhatsApp, avec son numéro (ex. `AT-4F7K2Q`), les articles, le total et le quartier.
5. Tu l'appelles pour confirmer, tu livres, et elle paie à la livraison : en espèces ou par Orange Money (numéro `orangeMoney` dans `config.js`).
