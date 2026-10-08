/* ==========================================================
   ATIYA CONCEPT — RÉGLAGES DE TA BOUTIQUE
   C'est le seul fichier à modifier : tes infos, tes catégories,
   tes articles et les questions fréquentes.
   ========================================================== */

const CONFIG = {
  nom: "Atiya Concept", // nom affiché dans les titres et les messages WhatsApp

  // ⚠️ IMPORTANT : ton numéro WhatsApp = 223 + tes 8 chiffres, sans « + » ni espaces.
  // C'est sur ce numéro que tu reçois les commandes.
  whatsapp: "22375883468",

  telephone: "+223 75 88 34 68", // numéro affiché sur la page Contact (mets "" pour le cacher)
  quartier: "Kalaban Coura",     // ton quartier, affiché sur la page Contact et en bas du site
  devise: "FCFA",

  // Frais de livraison à Bamako (écris le nombre sans guillemets ni espace) :
  //   null → « selon le livreur » (le prix dépend du livreur, tu le confirmes au téléphone)
  //   1000 → prix fixe de 1 000 FCFA ajouté au total
  //   0    → livraison gratuite
  livraison: null,
  delaiLivraison: "24 à 48 h", // délai annoncé aux clientes

  joursEchange: 3, // nombre de jours pour échanger un article
  horaires: "Tous les jours, de 8 h à 21 h",

  // Tes réseaux : le nom du compte suffit (laisse "" si tu n'en as pas)
  instagram: "", // ex. "atiyaconcept" ou "https://www.instagram.com/atiyaconcept"
  tiktok: "",    // ex. "atiyaconcept"
  facebook: "",  // ex. "atiyaconcept"

  // Messages qui défilent dans le bandeau rose tout en haut
  annonces: [
    "Paiement à la livraison partout à Bamako",
    "Nouvelle collection disponible ✨",
    "Une question ? Écrivez-nous sur WhatsApp",
  ],
};

/* CATÉGORIES (menu du haut + blocs « Achetez » sur l'accueil)
   image : photo du bloc sur l'accueil (mets la photo dans le dossier images) */
const CATEGORIES = [
  { id: "robes", nom: "Robes", image: "images/categorie-robes.jpg" },
  { id: "ensembles", nom: "Ensembles", image: "images/categorie-ensembles.jpg" },
  { id: "hauts", nom: "Hauts", image: "images/categorie-hauts.jpg" },
  { id: "bas", nom: "Jupes & Pantalons", image: "images/categorie-bas.jpg" },
];

/* ARTICLES — pour en ajouter un, copie un bloc { ... }, colle-le et change :
   - id : un nom UNIQUE, sans espaces ni accents (ex. "robe-wax-bleue"). Deux articles ne doivent jamais avoir le même id.
   - nom : le nom affiché.
   - prix : le nombre sans espace, sans point et sans guillemets (ex. prix: 15000,).
   - ancienPrix : ancien prix barré pour une promo (facultatif : supprime la ligne sinon).
   - categorie : exactement l'id d'une catégorie ci-dessus : "robes", "ensembles", "hauts" ou "bas" (= Jupes & Pantalons).
   - badge : petit texte sur la photo ("Nouveau", "Promo"…), ou supprime la ligne.
   - images : tes photos, ex. ["images/robe-satin-1.jpg", "images/robe-satin-2.jpg"] (la 1re est la photo principale).
   - tailles / couleurs : ex. ["S", "M", "L"] et ["Rose", "Noir"]. Pour une couleur rare, tu peux écrire son code, ex. "#1f2a4d".
   - indisponible : tailles ou couleurs épuisées (elles apparaissent barrées), ex. ["XL", "Noir"].
   - stock: false → tout l'article est « Épuisé ».
   - description : une seule ligne de texte entre guillemets.
   Les premiers articles de la liste s'affichent dans « Nos nouveautés » sur l'accueil. */
const PRODUITS = [
  {
    id: "robe-satin-rose",
    nom: "Robe Satin Rosé",
    prix: 15000,
    ancienPrix: 18000,
    categorie: "robes",
    badge: "Nouveau",
    images: [],
    tailles: ["S", "M", "L", "XL"],
    couleurs: ["Rose", "Noir", "Champagne"],
    indisponible: [],
    description: "Robe longue en satin fluide, coupe ajustée et fente discrète. Parfaite pour les mariages, baptêmes et soirées.",
  },
  {
    id: "robe-longue-plissee",
    nom: "Robe Longue Plissée",
    prix: 17500,
    categorie: "robes",
    images: [],
    tailles: ["S", "M", "L"],
    couleurs: ["Lilas", "Beige"],
    indisponible: ["L"],
    description: "Robe plissée légère avec ceinture à nouer. Elle se porte aussi bien la journée qu'en soirée.",
  },
  {
    id: "ensemble-lin-creme",
    nom: "Ensemble Lin Crème",
    prix: 19000,
    categorie: "ensembles",
    badge: "Best-seller",
    images: [],
    tailles: ["S", "M", "L", "XL"],
    couleurs: ["Crème", "Rose poudré"],
    indisponible: [],
    description: "Chemise et pantalon large en lin doux. Frais et élégant, idéal pour la chaleur de Bamako.",
  },
  {
    id: "ensemble-tailleur-chic",
    nom: "Ensemble Tailleur Chic",
    prix: 22500,
    categorie: "ensembles",
    images: [],
    tailles: ["M", "L", "XL"],
    couleurs: ["Camel", "Noir"],
    indisponible: [],
    description: "Blazer cintré et pantalon assorti. Une allure professionnelle et glamour.",
  },
  {
    id: "chemise-soie-blush",
    nom: "Chemise en Soie Blush",
    prix: 10000,
    categorie: "hauts",
    images: [],
    tailles: ["S", "M", "L"],
    couleurs: ["Blush", "Blanc"],
    indisponible: [],
    description: "Chemise satinée au tombé fluide, manches longues et boutons nacrés.",
  },
  {
    id: "top-dentelle",
    nom: "Top Dentelle",
    prix: 7500,
    ancienPrix: 9000,
    categorie: "hauts",
    badge: "Promo",
    images: [],
    tailles: ["S", "M", "L"],
    couleurs: ["Noir", "Ivoire"],
    indisponible: ["Ivoire"],
    description: "Top en dentelle délicate avec doublure, à porter seul ou sous une veste.",
  },
  {
    id: "jupe-midi-satinee",
    nom: "Jupe Midi Satinée",
    prix: 11000,
    categorie: "bas",
    images: [],
    tailles: ["S", "M", "L", "XL"],
    couleurs: ["Prune", "Rose"],
    indisponible: [],
    stock: false,
    description: "Jupe midi en satin avec taille élastique confortable.",
  },
  {
    id: "pantalon-palazzo",
    nom: "Pantalon Palazzo",
    prix: 12500,
    categorie: "bas",
    badge: "Nouveau",
    images: [],
    tailles: ["S", "M", "L", "XL"],
    couleurs: ["Beige", "Noir"],
    indisponible: [],
    description: "Pantalon large et fluide taille haute, qui allonge la silhouette.",
  },
];

/* QUESTIONS FRÉQUENTES (accueil + page Contact) */
const FAQ = [
  {
    question: "Quels sont les délais de livraison ?",
    reponse: `Nous livrons partout à Bamako en ${CONFIG.delaiLivraison}. Après votre commande, nous vous appelons pour confirmer l'adresse et l'heure de livraison.`,
  },
  {
    question: "Comment se passe le paiement ?",
    reponse: "Le paiement se fait uniquement à la livraison, en espèces, au moment où vous recevez votre colis. Aucun paiement en ligne n'est demandé.",
  },
  {
    question: "Puis-je échanger un article ?",
    reponse: `Oui, vous avez ${CONFIG.joursEchange} jours après la livraison pour échanger un article (taille ou couleur), s'il n'a pas été porté et a encore son étiquette.`,
  },
  {
    question: "Comment passer commande ?",
    reponse: "Choisissez votre article, votre couleur et votre taille, puis cliquez sur « Acheter maintenant ». Remplissez vos coordonnées et validez : votre commande nous est envoyée sur WhatsApp.",
  },
  {
    question: "Comment choisir ma taille ?",
    reponse: "Consultez le guide des tailles sur la page de chaque article. En cas de doute, écrivez-nous sur WhatsApp : nous vous conseillons avec plaisir.",
  },
];

/* GUIDE DES TAILLES (affiché sur chaque article) */
const GUIDE_TAILLES = [
  ["Taille", "Équiv.", "Poitrine (cm)", "Taille (cm)", "Hanches (cm)"],
  ["S", "36–38", "84–88", "66–70", "92–96"],
  ["M", "38–40", "88–94", "70–76", "96–102"],
  ["L", "40–42", "94–100", "76–82", "102–108"],
  ["XL", "42–44", "100–106", "82–88", "108–114"],
];
