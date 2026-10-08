/* ==========================================================
   ATIYA CONCEPT — fonctionnement du site
   Rien à modifier ici : tout se règle dans config.js
   ========================================================== */

/* ---------- Vérification de config.js ----------
   Si config.js contient une faute (guillemet ou virgule oubliés…), on affiche
   un message clair au lieu d'une page cassée. */
const CONFIG_OK = (() => {
  try { return typeof CONFIG === "object" && CONFIG !== null && Array.isArray(CATEGORIES) && Array.isArray(PRODUITS); }
  catch { return false; }
})();
if (!CONFIG_OK) {
  document.body.insertAdjacentHTML("afterbegin", `<p style="background:#b3261e;color:#fff;padding:1rem 1.25rem;margin:0;font:16px/1.5 system-ui,sans-serif">
    <b>Erreur dans config.js.</b> Vérifie ta dernière modification : les guillemets " ", les virgules en fin de ligne,
    les prix écrits sans espace (ex. <code>prix: 15000,</code>) et une description sur une seule ligne.</p>`);
  throw new Error("config.js invalide");
}

/* ---------- Outils ---------- */
const $ = (sel, el = document) => el.querySelector(sel);
const $$ = (sel, el = document) => [...el.querySelectorAll(sel)];
const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]);
const norm = s => String(s ?? "").normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase().trim();
const typo = s => String(s ?? "").replace(/ ([?!:;»])/g, " $1").replace(/« /g, "« "); // espaces insécables à la française
const params = new URLSearchParams(location.search);
const page = document.body.dataset.page;
const enLocal = location.protocol === "file:" || /^(localhost|127\.0\.0\.1)$/.test(location.hostname);

// Stockage du navigateur : tout accès est protégé (navigation privée, stockage bloqué…)
const lire = (nom, cle) => { try { return JSON.parse(window[nom].getItem(cle)); } catch { return null; } };
const ecrire = (nom, cle, val) => { try { window[nom].setItem(cle, JSON.stringify(val)); return true; } catch { return false; } };
const effacer = (nom, cle) => { try { window[nom].removeItem(cle); } catch {} };

/* ---------- Lecture tolérante de config.js ---------- */
// Une liste peut être écrite ["Rose", "Noir"] ou "Rose, Noir"
const enListe = v => (Array.isArray(v) ? v : v == null || v === "" ? [] : String(v).split(",")).map(x => String(x).trim()).filter(Boolean);
// Un prix peut être écrit 15000, "15000", "15 000" ou 15.000
const enNombre = v => {
  if (v == null || v === "") return null;
  let n = typeof v === "number" ? v : Number(String(v).replace(/[\s  .]/g, "").replace(",", "."));
  if (!Number.isFinite(n) || n < 0) return null;
  if (n > 0 && (!Number.isInteger(n) || n < 100)) n = Math.round(n * 1000); // 15.000 écrit à la française = 15 000
  return n;
};

CONFIG.nom ??= "Atiya Concept";
CONFIG.devise ??= "FCFA";
CONFIG.joursEchange ??= 3;
CONFIG.delaiLivraison ??= "24 à 48 h";
CONFIG.telephone ??= "";
CONFIG.horaires ??= "";
const LISTE_FAQ = (() => { try { return Array.isArray(FAQ) ? FAQ : []; } catch { return []; } })();
const GUIDE = (() => { try { return Array.isArray(GUIDE_TAILLES) ? GUIDE_TAILLES : []; } catch { return []; } })();

CATEGORIES.forEach(c => { c.id = String(c.id); c.nom = String(c.nom ?? c.id); });
const categorie = id => CATEGORIES.find(c => norm(c.id) === norm(id) || norm(c.nom) === norm(id));

const avertissements = [];
const idsVus = new Set();
for (let i = PRODUITS.length - 1; i >= 0; i--) {
  const p = PRODUITS[i];
  if (!p || typeof p !== "object") { PRODUITS.splice(i, 1); continue; }
  p.id = String(p.id ?? "").trim() || `article-${i + 1}`;
  p.nom = String(p.nom ?? "Article");
  for (const k of ["images", "tailles", "couleurs", "indisponible"]) p[k] = enListe(p[k]);
  p.prix = enNombre(p.prix);
  p.ancienPrix = enNombre(p.ancienPrix);
  if (typeof p.stock === "string") p.stock = !/^(false|non|0|epuise)$/.test(norm(p.stock));
  if (p.prix === null) { avertissements.push(`« ${p.nom} » n'a pas de prix valide (écris par ex. prix: 15000,) : il est caché.`); PRODUITS.splice(i, 1); continue; }
  const c = categorie(p.categorie);
  if (c) p.categorie = c.id;
  else avertissements.push(`« ${p.nom} » : la catégorie « ${p.categorie ?? ""} » n'existe pas (choisis : ${CATEGORIES.map(x => x.id).join(", ")}).`);
}
PRODUITS.forEach(p => {
  if (idsVus.has(p.id)) avertissements.push(`Deux articles ont le même id « ${p.id} » : change l'id du deuxième.`);
  idsVus.add(p.id);
});

/* ---------- Petits outils boutique ---------- */
const fmt = n => String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " " + CONFIG.devise; // le prix ne se coupe jamais
const numeroWa = () => {
  let n = String(CONFIG.whatsapp ?? "").replace(/\D/g, "").replace(/^00/, "");
  if (n.length === 8) n = "223" + n; // numéro malien écrit sans l'indicatif
  return n;
};
const waLink = texte => `https://wa.me/${numeroWa()}` + (texte ? `?text=${encodeURIComponent(texte)}` : "");
const lienReseau = (v, base) => {
  v = String(v ?? "").trim();
  if (!v) return "";
  if (/^https?:\/\//i.test(v)) return v;
  if (/^(www\.)?(instagram|tiktok|facebook|fb)\.com\//i.test(v)) return "https://" + v;
  return base + v.replace(/^@/, "");
};
const produit = id => PRODUITS.find(p => p.id === String(id));
const dispo = (p, v) => !p.indisponible.map(norm).includes(norm(v));
const enStock = p => p.stock !== false
  && (!p.tailles.length || p.tailles.some(t => dispo(p, t)))
  && (!p.couleurs.length || p.couleurs.some(c => dispo(p, c)));
const lienProduit = p => `produit.html?id=${encodeURIComponent(p.id)}`;
const lienCategorie = id => `boutique.html?cat=${encodeURIComponent(id)}`;
const img = (src, alt = "") => (src ? `<img src="${esc(src)}" alt="${esc(alt)}" loading="lazy" onerror="this.remove()">` : "");
const prixHTML = p => (p.ancienPrix ? `<s>${fmt(p.ancienPrix)}</s> ` : "") + fmt(p.prix);
const fraisLivraison = () => enNombre(CONFIG.livraison);
const texteLivraison = () => { const f = fraisLivraison(); return f === null ? "Selon le livreur" : f === 0 ? "Gratuite" : fmt(f); };
const jours = n => `${n} jour${n > 1 ? "s" : ""}`;
const variante = a => [a.couleur && `Couleur : ${a.couleur}`, a.taille && `Taille : ${a.taille}`].filter(Boolean).map(esc).join(" · ");
const correspond = (p, q) => norm([p.nom, categorie(p.categorie)?.nom, p.description, ...p.couleurs].join(" ")).includes(norm(q));

/* Couleurs des pastilles (nom de couleur → teinte). Un code comme "#1f2a4d" marche aussi. */
const TEINTES = {
  rose: "#f4a7b9", "rose poudre": "#f1c6cf", "rose pale": "#f7d3dc", "vieux rose": "#c99a9e", blush: "#f5cdd4",
  fuchsia: "#d6337a", framboise: "#c0265a", cerise: "#b0103a", rouge: "#c8102e", bordeaux: "#6d1a36", prune: "#5e2a4a",
  lilas: "#c8a8d8", lila: "#c8a8d8", lavande: "#b9a7d8", violet: "#7b4fa0", mauve: "#b28dbb",
  bleu: "#2f5fa8", "bleu ciel": "#a6cdea", "bleu roi": "#2440a8", "bleu nuit": "#16213e", marine: "#1f2a4d", "bleu marine": "#1f2a4d",
  turquoise: "#3cb6b0", vert: "#2f7d4f", emeraude: "#1f7a5a", menthe: "#a8dcc4", kaki: "#7a7a4a", olive: "#6b6b3a",
  jaune: "#f2c94c", moutarde: "#d4a017", orange: "#f08a3c", corail: "#f47a6b", saumon: "#f4a38c", peche: "#f8c3a6",
  beige: "#e5d3b8", sable: "#d8c3a0", creme: "#f4ead5", ivoire: "#fbf6e9", "blanc casse": "#f3efe6", champagne: "#ecd5ae",
  camel: "#c19a6b", nude: "#e3bfa5", cognac: "#9a5b2c", marron: "#7b4b2a", chocolat: "#4e2c1c", taupe: "#8b7d73",
  blanc: "#ffffff", noir: "#111111", gris: "#9a9a9a", "gris clair": "#cfcfcf", "gris fonce": "#555555",
  argent: "#c9c9c9", dore: "#cfa64a", or: "#cfa64a",
};
const ARC_EN_CIEL = "conic-gradient(#f4a7b9,#f2c94c,#a6cdea,#c8a8d8,#f4a7b9)";
const couleurCSS = nom => {
  const brut = String(nom).trim();
  if (/^#[0-9a-f]{3,8}$/i.test(brut)) return brut;
  const n = norm(brut);
  if (TEINTES[n]) return TEINTES[n];
  const mots = n.split(/\s+/); // « Rouge bordeaux foncé » → cherche « rouge bordeaux », puis « bordeaux »…
  for (let l = mots.length - 1; l > 0; l--) {
    for (let i = 0; i + l <= mots.length; i++) {
      const k = mots.slice(i, i + l).join(" ");
      if (TEINTES[k]) return TEINTES[k];
    }
  }
  return ARC_EN_CIEL;
};
const pastille = (nom, classe = "") => `<span class="teinte ${classe}" style="--c:${couleurCSS(nom)}" title="${esc(nom)}"></span>`;

/* ---------- Icônes ---------- */
const svg = d => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${d}</svg>`;
const ICON = {
  menu: svg('<path d="M4 9h16M4 15h16"/>'),
  search: svg('<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>'),
  user: svg('<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5c1.2-4 4-5.5 7.5-5.5s6.3 1.5 7.5 5.5z"/>'),
  bag: svg('<path d="M4.5 8.5h15l-1 11.5h-13z"/><path d="M8.5 8.5V7a3.5 3.5 0 0 1 7 0v1.5"/>'),
  bagPlus: svg('<path d="M12.5 20h-7l-1-11.5h15l-.5 5"/><path d="M8.5 8.5V7a3.5 3.5 0 0 1 7 0v1.5M18 16v6M15 19h6"/>'),
  close: svg('<path d="M6 6l12 12M18 6 6 18"/>'),
  chevL: svg('<path d="m15 5-7 7 7 7"/>'),
  chevR: svg('<path d="m9 5 7 7-7 7"/>'),
  chevD: svg('<path d="m6 9 6 6 6-6"/>'),
  filter: svg('<path d="M4 7h16M7 12h10M10 17h4"/>'),
  vue1: svg('<rect x="6" y="4.5" width="12" height="15" rx="2.5"/>'),
  vue2: svg('<rect x="4" y="4" width="7" height="7" rx="2"/><rect x="13" y="4" width="7" height="7" rx="2"/><rect x="4" y="13" width="7" height="7" rx="2"/><rect x="13" y="13" width="7" height="7" rx="2"/>'),
  trash: svg('<path d="M4 7h16M9 7V4.5h6V7M6.5 7l1 13h9l1-13"/>'),
  truck: svg('<path d="M2.5 6.5h11v10h-11zM13.5 10h4l3 3.2v3.3h-7"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>'),
  retour: svg('<path d="M12 3.5 19.5 7.5v9L12 20.5 4.5 16.5v-9z"/><path d="M4.5 7.5 12 11.5l7.5-4M12 11.5v9"/>'),
  cash: svg('<rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.8"/><path d="M6 9.5v5M18 9.5v5"/>'),
  support: svg('<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="2.5" y="13" width="4" height="6" rx="1.5"/><rect x="17.5" y="13" width="4" height="6" rx="1.5"/><path d="M19.5 19c0 1.5-2 2.5-5 2.5"/>'),
  sparkle: svg('<path d="M12 3c.6 4.6 2.4 6.4 7 7-4.6.6-6.4 2.4-7 7-.6-4.6-2.4-6.4-7-7 4.6-.6 6.4-2.4 7-7z"/>'),
  heart: svg('<path d="M12 20s-7.5-4.6-7.5-10A4.3 4.3 0 0 1 12 7.4 4.3 4.3 0 0 1 19.5 10c0 5.4-7.5 10-7.5 10z"/>'),
  hanger: svg('<path d="M10 6.5a2 2 0 1 1 2 2V10l-8.5 5.6a1.2 1.2 0 0 0 .7 2.2h15.6a1.2 1.2 0 0 0 .7-2.2L12 10"/>'),
  phone: svg('<path d="M5 3.5h3.5l1.5 4-2 1.5a11 11 0 0 0 5 5l1.5-2 4 1.5V17a2.5 2.5 0 0 1-2.5 2.5A15 15 0 0 1 2.5 6 2.5 2.5 0 0 1 5 3.5z"/>'),
  pin: svg('<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>'),
  clock: svg('<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>'),
  insta: svg('<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".6" fill="currentColor"/>'),
  whatsapp: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2A9.8 9.8 0 0 0 3.6 17l-1.4 4.8 4.9-1.3A9.8 9.8 0 1 0 12 2.2zm0 17.8a8 8 0 0 1-4.1-1.1l-.3-.2-2.9.8.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6.6 6.6 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.7-1.7c-.2-.4-.4-.4-.5-.4h-.5a.9.9 0 0 0-.7.3 2.8 2.8 0 0 0-.9 2.1 4.9 4.9 0 0 0 1 2.6 11.2 11.2 0 0 0 4.3 3.8c1.6.7 2.2.7 3 .6.5-.1 1.4-.6 1.6-1.2.2-.6.2-1 .1-1.2l-.4-.2z"/></svg>',
};

/* ---------- Panier (gardé dans le téléphone de la cliente) ---------- */
// Une ligne de panier est valable si l'article existe encore, est en stock,
// et si la taille / couleur choisies existent et ne sont pas épuisées.
function ligneValide(a) {
  const p = a && produit(a.id);
  if (!p || !enStock(p)) return false;
  const q = Math.floor(Number(a.qte));
  if (!(q >= 1)) return false;
  a.id = p.id;
  a.qte = Math.min(20, q);
  a.taille = a.taille == null ? "" : String(a.taille);
  a.couleur = a.couleur == null ? "" : String(a.couleur);
  if (p.tailles.length ? !(p.tailles.includes(a.taille) && dispo(p, a.taille)) : a.taille) return false;
  if (p.couleurs.length ? !(p.couleurs.includes(a.couleur) && dispo(p, a.couleur)) : a.couleur) return false;
  return true;
}

const Panier = {
  cle: "atiya-panier",
  articles: [],
  retires: 0, // articles retirés car plus disponibles
  charger() {
    const brut = lire("localStorage", this.cle);
    const liste = Array.isArray(brut) ? brut : [];
    this.articles = liste.filter(ligneValide);
    this.retires = liste.length - this.articles.length;
    if (this.retires) ecrire("localStorage", this.cle, this.articles);
  },
  sauver() { const ok = ecrire("localStorage", this.cle, this.articles); majPastille(); return ok; },
  ajouter(id, taille, couleur, qte) {
    this.charger(); // repartir du panier le plus récent (autre onglet…)
    effacer("sessionStorage", "atiya-derniere-commande");
    const a = this.articles.find(x => x.id === id && x.taille === taille && x.couleur === couleur);
    if (a) a.qte = Math.min(20, a.qte + qte);
    else this.articles.push({ id, taille, couleur, qte });
    return this.sauver();
  },
  quantite() { return this.articles.reduce((n, a) => n + a.qte, 0); },
  vider() { this.articles = []; this.sauver(); },
};
const sousTotal = liste => liste.reduce((n, a) => n + produit(a.id).prix * a.qte, 0);
const noteRetires = () => !Panier.retires ? "" : Panier.retires > 1
  ? `<p class="avis">Des articles de votre panier ne sont plus disponibles : ils ont été retirés.</p>`
  : `<p class="avis">Un article de votre panier n'est plus disponible : il a été retiré.</p>`;

function majPastille() {
  const b = $("#pastille");
  if (!b) return;
  const n = Panier.quantite();
  b.textContent = n;
  b.hidden = !n;
}

/* ---------- En-tête, menu, recherche, pied de page ---------- */
function liensNav() {
  return [
    { href: "index.html", label: "ACCUEIL", cle: "accueil" },
    ...CATEGORIES.map(c => ({ href: lienCategorie(c.id), label: c.nom, cle: "cat:" + c.id })),
    { href: "contact.html", label: "Contact", cle: "contact" },
  ];
}

function cleActive() {
  if (page === "boutique") { const c = categorie(params.get("cat")); return c ? "cat:" + c.id : ""; }
  if (page === "produit") { const p = produit(params.get("id")); return p ? "cat:" + p.categorie : ""; }
  return page;
}

function activerNav(cle) {
  $$("[data-nav]").forEach(a => a.classList.toggle("actif", a.dataset.nav === cle));
  // Sur téléphone, la barre défile : on amène la rubrique active au milieu
  const nav = $(".nav"), a = $(".nav a.actif");
  if (nav && a && nav.scrollWidth > nav.clientWidth) nav.scrollLeft = a.offsetLeft - (nav.clientWidth - a.offsetWidth) / 2;
}

// Logo : images/logo.png (si l'image manque, le nom s'affiche en texte)
const logoHTML = `<img class="logo-img" src="images/logo.png" alt="${esc(CONFIG.nom)}" width="959" height="405" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><span class="logo-texte" hidden><span class="logo-nom">Atiya</span><span class="logo-sous">Concept</span></span>`;

function montrerAvertissements() {
  avertissements.forEach(m => console.warn("config.js :", m));
  if (!enLocal || !avertissements.length) return;
  // Visible seulement sur ton ordinateur, jamais par les clientes une fois le site en ligne
  document.body.insertAdjacentHTML("afterbegin", `<div class="avertissement-config" style="background:#fff4d6;color:#5c4400;padding:.8rem 1.25rem;font:14px/1.5 system-ui,sans-serif;border-bottom:1px solid #e8d48a">
    <b>À corriger dans config.js :</b><br>${avertissements.map(esc).join("<br>")}</div>`);
}

function rendreStructure() {
  const nav = liensNav().map(l => `<a href="${l.href}" data-nav="${l.cle}">${esc(l.label)}</a>`).join("");
  const annee = new Date().getFullYear();


  if (page === "commande") {
    // Page de commande : en-tête simple, comme une vraie caisse
    document.body.insertAdjacentHTML("afterbegin", `
      <header class="co-entete"><div class="co-entete-in">
        <a class="logo" href="index.html" aria-label="Accueil ${esc(CONFIG.nom)}">${logoHTML}</a>
        <a class="co-retour" href="panier.html">Retour au panier</a>
      </div></header>`);
    document.body.insertAdjacentHTML("beforeend", `<div class="toast" id="toast" role="status"></div>`);
    return;
  }

  const annonces = [].concat(CONFIG.annonces ?? []).map(s => String(s).trim()).filter(Boolean);
  const bandeau = !annonces.length ? "" : `
    <div class="annonce" aria-label="Annonces">
      ${annonces.length > 1 ? `<button class="ann-btn" data-ann="-1" aria-label="Annonce précédente">${ICON.chevL}</button>` : ""}
      <p id="annTexte" aria-live="polite">${esc(typo(annonces[0]))}</p>
      ${annonces.length > 1 ? `<button class="ann-btn" data-ann="1" aria-label="Annonce suivante">${ICON.chevR}</button>` : ""}
    </div>`;

  document.body.insertAdjacentHTML("afterbegin", `${bandeau}
    <header class="entete" id="entete">
      <div class="entete-haut">
        <div class="entete-g">
          <button class="icone" id="btnMenu" aria-label="Ouvrir le menu">${ICON.menu}</button>
          <button class="icone" id="btnRecherche" aria-label="Rechercher">${ICON.search}</button>
        </div>
        <a class="logo" href="index.html" aria-label="Accueil ${esc(CONFIG.nom)}">${logoHTML}</a>
        <div class="entete-d">
          <a class="icone" href="contact.html" aria-label="Contact">${ICON.user}</a>
          <a class="icone" href="panier.html" aria-label="Mon panier">${ICON.bag}<span class="pastille" id="pastille" hidden></span></a>
        </div>
      </div>
      <nav class="nav" aria-label="Catégories">${nav}</nav>
    </header>
    <div class="voile" id="voile"></div>
    <aside class="menu-lateral" id="menuLateral" aria-label="Menu">
      <button class="icone" data-fermer aria-label="Fermer le menu">${ICON.close}</button>
      <nav class="menu-liens">${nav}</nav>
      <div class="menu-produits">${PRODUITS.slice(0, 8).map(p => `
        <a href="${lienProduit(p)}"><span class="ph menu-ph">${img(p.images[0], p.nom)}</span><span>${esc(p.nom)}</span><b>${fmt(p.prix)}</b></a>`).join("")}</div>
    </aside>
    <div class="recherche" id="recherche">
      <form class="recherche-barre" id="formRecherche" role="search">
        ${ICON.search}
        <input type="search" name="q" placeholder="Rechercher un article…" autocomplete="off" aria-label="Rechercher un article">
        <button type="button" class="icone" data-fermer aria-label="Fermer la recherche">${ICON.close}</button>
      </form>
      <div class="resultats" id="resultats"></div>
    </div>`);

  const reseaux = [
    [lienReseau(CONFIG.instagram, "https://www.instagram.com/"), "Instagram"],
    [lienReseau(CONFIG.tiktok, "https://www.tiktok.com/@"), "TikTok"],
    [lienReseau(CONFIG.facebook, "https://www.facebook.com/"), "Facebook"],
    [waLink(), "WhatsApp"],
  ].filter(([href]) => href).map(([href, nom]) => `<a href="${esc(href)}" target="_blank" rel="noopener">${nom}</a>`).join("");

  document.body.insertAdjacentHTML("beforeend", `
    <footer class="pied">
      <div class="pied-in">
        <div>
          <a class="logo" href="index.html">${logoHTML}</a>
          <p class="pied-texte">Mode féminine à Bamako. Paiement uniquement à la livraison.</p>
        </div>
        <div class="pied-cols">
          <div><h5>Boutique</h5>${CATEGORIES.map(c => `<a href="${lienCategorie(c.id)}">${esc(c.nom)}</a>`).join("")}<a href="boutique.html">Tout voir</a></div>
          <div><h5>Aide</h5><a href="contact.html">Contact</a><a href="contact.html#questions">Questions fréquentes</a><a href="panier.html">Mon panier</a></div>
          <div><h5>Suivez-nous</h5>${reseaux}</div>
        </div>
        <p class="pied-bas">© ${annee} ${esc(CONFIG.nom)} · ${esc(CONFIG.quartier ? CONFIG.quartier + ", " : "")}Bamako, Mali</p>
      </div>
    </footer>
    <a class="wa-flottant" href="${waLink()}" target="_blank" rel="noopener" aria-label="Nous écrire sur WhatsApp">${ICON.whatsapp}</a>
    <div class="toast" id="toast" role="status"></div>`);

  activerNav(cleActive());

  // Bandeau d'annonces
  if (annonces.length > 1) {
    let ai = 0, minuteur;
    const montrer = d => {
      ai = (ai + d + annonces.length) % annonces.length;
      const p = $("#annTexte");
      p.style.opacity = 0;
      setTimeout(() => { p.textContent = typo(annonces[ai]); p.style.opacity = 1; }, 220);
    };
    const relancer = () => { clearInterval(minuteur); minuteur = setInterval(() => montrer(1), 5000); };
    $$("[data-ann]").forEach(b => b.addEventListener("click", () => { montrer(+b.dataset.ann); relancer(); }));
    relancer();
  }

  // Menu et recherche
  $("#btnMenu").addEventListener("click", () => ouvrir("menuLateral"));
  $("#btnRecherche").addEventListener("click", () => ouvrir("recherche"));
  $("#voile").addEventListener("click", fermer);
  document.addEventListener("click", e => { if (e.target.closest("[data-fermer]")) fermer(); });
  document.addEventListener("keydown", e => { if (e.key === "Escape") fermer(); });

  const champ = $("#formRecherche input");
  champ.addEventListener("input", () => {
    const q = champ.value.trim();
    const res = q ? PRODUITS.filter(p => correspond(p, q)).slice(0, 6) : [];
    $("#resultats").innerHTML = res.map(p => `
      <a class="resultat" href="${lienProduit(p)}"><span class="ph">${img(p.images[0], p.nom)}</span><span><b>${esc(p.nom)}</b><small>${fmt(p.prix)}</small></span></a>`).join("")
      || (q ? `<p class="sans-resultat">Aucun article trouvé.</p>` : "");
  });
  $("#formRecherche").addEventListener("submit", e => {
    e.preventDefault();
    if (champ.value.trim()) location.href = "boutique.html?q=" + encodeURIComponent(champ.value.trim());
  });

  // Ombre sous l'en-tête quand on descend (la hauteur ne change pas, sinon la page « saute »)
  const entete = $("#entete");
  addEventListener("scroll", () => entete.classList.toggle("ombre", scrollY > 40), { passive: true });
}

function ouvrir(id) {
  fermer();
  $("#" + id)?.classList.add("ouvert");
  $("#voile")?.classList.add("ouvert");
  document.body.classList.add("bloque");
  if (id === "recherche") setTimeout(() => $("#recherche input").focus(), 60);
}

function fermer() {
  $$(".menu-lateral, .recherche, .feuille, .voile").forEach(e => e.classList.remove("ouvert"));
  document.body.classList.remove("bloque");
}

function toast(html) {
  const t = $("#toast");
  if (!t) return;
  t.innerHTML = html;
  t.classList.add("visible");
  clearTimeout(toast.minuteur);
  toast.minuteur = setTimeout(() => t.classList.remove("visible"), 3500);
}

/* ---------- Morceaux réutilisés ---------- */
const carteGrille = p => `
  <a class="article" href="${lienProduit(p)}">
    <div class="article-img ph">
      ${!enStock(p) ? `<span class="pill-epuise">Épuisé</span>` : p.badge ? `<span class="pill">${esc(p.badge)}</span>` : ""}
      ${img(p.images[0], p.nom)}
    </div>
    <div class="article-txt">
      <h3>${esc(p.nom)}</h3>
      <p class="article-prix">${prixHTML(p)}</p>
      ${p.couleurs.length ? `<div class="mini-teintes">${p.couleurs.map(c => pastille(c, "mini")).join("")}</div>` : ""}
    </div>
  </a>`;

function rendreFAQ(el) {
  if (!el) return;
  el.innerHTML = LISTE_FAQ.map(f => `<details><summary>${esc(typo(f.question))}</summary><p>${esc(typo(f.reponse))}</p></details>`).join("");
}

/* ---------- Page : Accueil ---------- */
function initAccueil() {
  // Photo d'ambiance : si le fichier existe, il remplace le satin (classe a-photo pour adapter le texte)
  const photo = src => (src ? `<img src="${esc(src)}" alt="" loading="lazy" onload="this.closest('[data-photo]').classList.add('a-photo')" onerror="this.remove()">` : "");
  // Teinte douce de la 1re couleur de l'article : l'emplacement photo ressemble à un échantillon de tissu
  const adoucir = hex => {
    const n = parseInt(hex.slice(1), 16), rgb = [n >> 16, (n >> 8) & 255, n & 255], fond = [255, 250, 248];
    const k = (rgb[0] + rgb[1] + rgb[2]) / 765 < 0.35 ? 0.24 : 0.42; // couleurs foncées (noir, prune…) : teinte plus légère
    return "rgb(" + rgb.map((c, i) => Math.round(c * k + fond[i] * (1 - k))).join(",") + ")";
  };
  const teinte = p => { const t = couleurCSS(p.couleurs[0] || ""); return /^#[0-9a-f]{6}$/i.test(t) ? ` style="--t:${adoucir(t)}"` : ""; };

  // Nouveautés : les 8 premiers articles de config.js
  const carte = p => `
    <a class="ed-carte${enStock(p) ? "" : " epuisee"}" href="${lienProduit(p)}">
      <span class="ed-carte-img ph"${teinte(p)}>
        ${!enStock(p) ? `<span class="ed-etiquette">Épuisé</span>` : p.badge ? `<span class="ed-etiquette">${esc(p.badge)}</span>` : ""}
        ${img(p.images[0], p.nom)}
      </span>
      <p class="ed-carte-cat">${esc(categorie(p.categorie)?.nom || "")}</p>
      <h3 class="ed-carte-nom">${esc(p.nom)}</h3>
      <p class="ed-carte-prix">${prixHTML(p)}</p>
      ${p.couleurs.length ? `<span class="ed-teintes">${p.couleurs.map(c => pastille(c)).join("")}</span>` : ""}
    </a>`;
  const piste = $("#nouveautes");
  piste.innerHTML = PRODUITS.slice(0, 8).map(carte).join("");

  // Petite barre qui suit le défilement des nouveautés (mobile)
  const barre = $("#defile span");
  const suivre = () => {
    const max = piste.scrollWidth - piste.clientWidth;
    const part = piste.scrollWidth ? piste.clientWidth / piste.scrollWidth : 1;
    barre.style.width = part * 100 + "%";
    barre.style.left = (max > 0 ? (piste.scrollLeft / max) * (1 - part) * 100 : 0) + "%";
  };
  if (barre) {
    piste.addEventListener("scroll", suivre, { passive: true });
    addEventListener("resize", suivre);
    suivre();
  }

  // Collections (grille éditoriale)
  $("#collections").innerHTML = CATEGORIES.map((c, i) => `
    <a class="ed-collection" href="${lienCategorie(c.id)}" data-photo>
      <span class="ed-collection-img ph">${photo(c.image)}</span>
      <span class="ed-collection-txt">
        <small>${String(i + 1).padStart(2, "0")}</small>
        <b>${esc(c.nom)}</b>
        <span class="ed-decouvrir">Découvrir</span>
      </span>
    </a>`).join("");

  // Questions fréquentes
  rendreFAQ($("#faq"));

  // Garanties (textes tirés de config.js)
  const f = fraisLivraison();
  const garanties = [
    [ICON.cash, "Paiement à la livraison", "En espèces, à la réception de votre colis. Aucun paiement en ligne."],
    [ICON.truck, "Livraison à Bamako", `Partout à Bamako, en ${CONFIG.delaiLivraison}. ${f === null ? "Le prix dépend du livreur." : f === 0 ? "Livraison gratuite." : `Frais : ${fmt(f)}.`}`],
    [ICON.retour, `Échange sous ${jours(CONFIG.joursEchange)}`, "Taille ou couleur, si l’article n’a pas été porté."],
    [ICON.support, "Conseil sur WhatsApp", CONFIG.horaires ? `${CONFIG.horaires}.` : "Nous vous répondons rapidement."],
  ];
  $("#garanties").innerHTML = garanties.map(([i, t, d]) => `<div class="ed-garantie">${i}<h3>${esc(typo(t))}</h3><p>${esc(typo(d))}</p></div>`).join("");

  // Liens WhatsApp et horaires (depuis config.js)
  $$("[data-wa]").forEach(a => { a.href = waLink(a.dataset.wa); });
  $$("[data-horaires]").forEach(e => { e.textContent = CONFIG.horaires ? CONFIG.horaires + "." : ""; });
}

/* ---------- Page : Boutique / catégorie ---------- */
function initBoutique() {
  const c0 = categorie(params.get("cat"));
  const etat = {
    cat: c0 ? c0.id : "",
    q: params.get("q") || "",
    tri: "",
    taille: "",
    dispo: false,
    vue: lire("localStorage", "atiya-vue") === 1 ? 1 : 2,
  };

  $("#puces").innerHTML = [{ id: "", nom: "Tout" }, ...CATEGORIES].map(c => `<button type="button" data-cat="${esc(c.id)}">${esc(c.nom)}</button>`).join("");
  const tailles = [...new Set(PRODUITS.flatMap(p => p.tailles))];
  $("#filtreTailles").innerHTML = tailles.map(t => `<button type="button" data-taille="${esc(t)}">${esc(t)}</button>`).join("");
  $("#btnFiltrer").insertAdjacentHTML("afterbegin", ICON.filter);
  $("[data-vue='1']").innerHTML = ICON.vue1;
  $("[data-vue='2']").innerHTML = ICON.vue2;

  function afficher() {
    let liste = PRODUITS.filter(p => !etat.cat || p.categorie === etat.cat);
    if (etat.q) liste = liste.filter(p => correspond(p, etat.q));
    if (etat.taille) liste = liste.filter(p => p.tailles.includes(etat.taille) && dispo(p, etat.taille));
    if (etat.dispo) liste = liste.filter(enStock);
    if (etat.tri === "asc") liste = [...liste].sort((a, b) => a.prix - b.prix);
    if (etat.tri === "desc") liste = [...liste].sort((a, b) => b.prix - a.prix);

    const titre = etat.q ? `« ${etat.q} »` : etat.cat ? categorie(etat.cat).nom : "Boutique";
    $("#titrePage").textContent = typo(titre);
    document.title = `${etat.q ? "Recherche" : titre} — ${CONFIG.nom}`;
    $("#nbArticles").textContent = `${liste.length} article${liste.length > 1 ? "s" : ""}`;
    const vide = etat.q ? "Aucun article ne correspond à votre recherche."
      : etat.taille || etat.dispo ? "Aucun article ne correspond à ces filtres."
      : "Bientôt de nouveaux articles ici !";
    const grille = $("#grille");
    grille.className = "grille vue-" + etat.vue;
    grille.innerHTML = liste.length
      ? liste.map(carteGrille).join("")
      : `<div class="vide"><p>${typo(vide)}</p><a class="btn-noir" href="boutique.html">Voir toute la boutique</a></div>`;

    $$("#puces button").forEach(b => b.classList.toggle("actif", b.dataset.cat === etat.cat && !etat.q));
    $$("[data-vue]").forEach(b => b.classList.toggle("actif", +b.dataset.vue === etat.vue));
    $$("[data-taille]").forEach(b => b.classList.toggle("actif", b.dataset.taille === etat.taille));
    $$("[data-tri]").forEach(b => b.classList.toggle("actif", b.dataset.tri === etat.tri));
    $("#filtreDispo").checked = etat.dispo;
    const n = (etat.tri ? 1 : 0) + (etat.taille ? 1 : 0) + (etat.dispo ? 1 : 0);
    $("#nbFiltres").textContent = n ? `(${n})` : "";
    activerNav(etat.cat ? "cat:" + etat.cat : "");
  }

  $("#puces").addEventListener("click", e => {
    const b = e.target.closest("button");
    if (!b) return;
    etat.cat = b.dataset.cat;
    etat.q = "";
    history.replaceState(null, "", etat.cat ? lienCategorie(etat.cat) : "boutique.html");
    afficher();
  });
  $("#btnFiltrer").addEventListener("click", () => ouvrir("feuilleFiltres"));
  $$("[data-vue]").forEach(b => b.addEventListener("click", () => {
    etat.vue = +b.dataset.vue;
    ecrire("localStorage", "atiya-vue", etat.vue);
    afficher();
  }));
  $("#feuilleFiltres").addEventListener("click", e => {
    const b = e.target.closest("button");
    if (!b) return;
    if (b.dataset.tri !== undefined) etat.tri = b.dataset.tri;
    else if (b.dataset.taille !== undefined) etat.taille = etat.taille === b.dataset.taille ? "" : b.dataset.taille;
    else if (b.id === "effacerFiltres") { etat.tri = ""; etat.taille = ""; etat.dispo = false; }
    else return;
    afficher();
  });
  $("#filtreDispo").addEventListener("change", e => { etat.dispo = e.target.checked; afficher(); });

  afficher();
}

/* ---------- Page : Article ---------- */
function initProduit() {
  const p = produit(params.get("id"));
  const zone = $("#fiche");
  if (!p) {
    zone.innerHTML = `<div class="introuvable"><h1 class="titre">Article introuvable</h1><p>Cet article n'existe plus ou le lien est incorrect.</p><a class="btn-noir" href="boutique.html">Retour à la boutique</a></div>`;
    $("#reco")?.remove();
    return;
  }
  document.title = `${p.nom} — ${CONFIG.nom}`;

  const photos = p.images.length ? p.images : [""];
  const premier = liste => liste.find(v => dispo(p, v)) || "";
  const choix = { taille: premier(p.tailles), couleur: premier(p.couleurs), qte: 1 };
  const disponible = enStock(p);

  const choixHTML = (k, label, liste) => !liste.length ? "" : `
    <div class="choix" data-k="${k}">
      <p class="choix-label">${label}<span data-val>${esc(choix[k])}</span></p>
      <div class="choix-liste">${liste.map(v => {
        const off = !dispo(p, v);
        const cls = `${v === choix[k] ? " sel" : ""}${off ? " barre" : ""}`;
        return k === "couleur"
          ? `<button type="button" class="teinte${cls}" style="--c:${couleurCSS(v)}" data-v="${esc(v)}" ${off ? "disabled" : ""} aria-label="${esc(v)}${off ? " (épuisé)" : ""}" title="${esc(v)}"></button>`
          : `<button type="button" class="taille${cls}" data-v="${esc(v)}" ${off ? "disabled" : ""} aria-label="Taille ${esc(v)}${off ? " (épuisée)" : ""}">${esc(v)}</button>`;
      }).join("")}</div>
    </div>`;

  const guide = GUIDE.length
    ? `<table class="guide">${GUIDE.map((r, i) => `<tr>${[].concat(r).map(c => i ? `<td>${esc(c)}</td>` : `<th>${esc(c)}</th>`).join("")}</tr>`).join("")}</table>` : "";

  zone.innerHTML = `
    <div class="fiche">
      <div class="galerie">
        <div class="galerie-piste" id="piste">${photos.map((s, i) => `<div class="galerie-photo ph">${i === 0 ? (!disponible ? `<span class="pill-epuise">Épuisé</span>` : p.badge ? `<span class="pill">${esc(p.badge)}</span>` : "") : ""}${img(s, p.nom)}</div>`).join("")}</div>
        ${photos.length > 1 ? `<div class="points" id="points">${photos.map((_, i) => `<button type="button" class="${i ? "" : "actif"}" data-i="${i}" aria-label="Photo ${i + 1}"></button>`).join("")}</div>` : ""}
      </div>
      <div class="fiche-infos">
        <p class="fil"><a href="index.html">Accueil</a> / <a href="${lienCategorie(p.categorie)}">${esc(categorie(p.categorie)?.nom || "Boutique")}</a></p>
        <h1 class="fiche-nom">${esc(p.nom)}</h1>
        <p class="fiche-prix">${prixHTML(p)}</p>
        <p class="fiche-cod">${ICON.cash} Paiement à la livraison · Livraison à Bamako</p>
        <hr>
        ${choixHTML("couleur", "Couleur", p.couleurs)}
        ${choixHTML("taille", "Taille", p.tailles)}
        <div class="fiche-achat">
          <div class="qte" id="qte"><button type="button" data-d="-1" aria-label="Diminuer la quantité">−</button><span>1</span><button type="button" data-d="1" aria-label="Augmenter la quantité">+</button></div>
          <button type="button" class="btn-gris" id="btnAjouter" ${disponible ? "" : "disabled"}>${disponible ? `${ICON.bagPlus} Ajouter au panier` : "Épuisé"}</button>
        </div>
        ${disponible ? `<button type="button" class="btn-noir large" id="btnAcheter">Acheter maintenant</button>` : ""}
        <a class="btn-wa" id="btnWa" target="_blank" rel="noopener">${ICON.whatsapp} ${disponible ? "Commander sur WhatsApp" : "Demander la disponibilité"}</a>
        <div class="accordeons">
          ${p.description ? `<details open><summary>Description</summary><div><p>${esc(typo(p.description))}</p></div></details>` : ""}
          <details><summary>Politique d'échange</summary><div><p>Vous avez ${jours(CONFIG.joursEchange)} après la livraison pour échanger un article (taille ou couleur), s'il n'a pas été porté et a encore son étiquette.</p></div></details>
          <details><summary>Livraison rapide</summary><div><p>Livraison partout à Bamako en ${esc(CONFIG.delaiLivraison)}. ${fraisLivraison() === null ? "Livraison payante : le prix dépend du livreur, nous vous le confirmons par téléphone." : `Frais de livraison : ${texteLivraison().toLowerCase()}.`} Vous payez uniquement à la livraison, en espèces.</p></div></details>
          ${p.tailles.length && guide ? `<details><summary>Guide des tailles</summary><div>${guide}</div></details>` : ""}
        </div>
      </div>
    </div>`;

  const majWa = () => {
    const details = [choix.couleur && `Couleur : ${choix.couleur}`, choix.taille && `Taille : ${choix.taille}`, `Quantité : ${choix.qte}`].filter(Boolean).join(" — ");
    const texte = disponible
      ? `Bonjour ${CONFIG.nom} 👋\nJe souhaite commander :\n• ${p.nom} — ${details}\nPrix : ${fmt(p.prix * choix.qte)}`
      : `Bonjour ${CONFIG.nom} 👋\nL'article « ${p.nom} » sera-t-il bientôt de nouveau disponible ?`;
    $("#btnWa").href = waLink(texte);
  };
  majWa();

  // Choix couleur / taille
  $$(".choix", zone).forEach(bloc => bloc.addEventListener("click", e => {
    const b = e.target.closest("button");
    if (!b || b.disabled) return;
    const k = bloc.dataset.k;
    choix[k] = b.dataset.v;
    $$("button", bloc).forEach(x => x.classList.toggle("sel", x === b));
    $("[data-val]", bloc).textContent = choix[k];
    const photoCouleur = p.photosCouleurs && p.photosCouleurs[choix.couleur];
    if (k === "couleur" && photoCouleur) {
      const i = photos.indexOf(photoCouleur);
      if (i >= 0) $("#piste").scrollTo({ left: i * $("#piste").clientWidth, behavior: "smooth" });
    }
    majWa();
  }));

  // Quantité
  $("#qte").addEventListener("click", e => {
    const b = e.target.closest("button");
    if (!b) return;
    choix.qte = Math.max(1, Math.min(20, choix.qte + +b.dataset.d));
    $("#qte span").textContent = choix.qte;
    majWa();
  });

  // Galerie photos
  const piste = $("#piste"), points = $("#points");
  if (points) {
    piste.addEventListener("scroll", () => {
      const i = Math.round(piste.scrollLeft / piste.clientWidth);
      $$("button", points).forEach((b, k) => b.classList.toggle("actif", k === i));
    }, { passive: true });
    points.addEventListener("click", e => {
      const b = e.target.closest("button");
      if (b) piste.scrollTo({ left: +b.dataset.i * piste.clientWidth, behavior: "smooth" });
    });
  }

  // Panier / achat direct (l'article voyage dans le lien : marche même si le navigateur bloque le stockage)
  $("#btnAjouter").addEventListener("click", () => {
    if (!disponible) return;
    if (Panier.ajouter(p.id, choix.taille, choix.couleur, choix.qte)) toast(`Ajouté au panier ✓ <a href="panier.html">Voir le panier</a>`);
    else toast(`Ce navigateur ne peut pas garder votre panier. Utilisez « Acheter maintenant » ou WhatsApp.`);
  });
  $("#btnAcheter")?.addEventListener("click", () => {
    effacer("sessionStorage", "atiya-derniere-commande");
    location.href = "commande.html?" + new URLSearchParams({ id: p.id, taille: choix.taille, couleur: choix.couleur, qte: choix.qte });
  });

  // Recommandations
  const autres = PRODUITS.filter(x => x !== p);
  const reco = [...autres.filter(x => x.categorie === p.categorie), ...autres.filter(x => x.categorie !== p.categorie)].slice(0, 4);
  if (reco.length) $("#grilleReco").innerHTML = reco.map(carteGrille).join("");
  else $("#reco").remove();
}

/* ---------- Page : Panier ---------- */
function initPanier() {
  const zone = $("#panier");
  function afficher() {
    if (!Panier.articles.length) {
      zone.innerHTML = `${noteRetires()}<div class="vide"><p>Votre panier est vide.</p><a class="btn-noir" href="boutique.html">Découvrir la boutique</a></div>`;
      return;
    }
    zone.innerHTML = `${noteRetires()}
      <div class="panier-grille">
        <div class="panier-liste">${Panier.articles.map((a, k) => {
          const p = produit(a.id);
          return `
          <div class="ligne">
            <a class="ligne-img ph" href="${lienProduit(p)}">${img(p.images[0], p.nom)}</a>
            <div class="ligne-infos">
              <a class="ligne-nom" href="${lienProduit(p)}">${esc(p.nom)}</a>
              <p class="ligne-var">${variante(a)}</p>
              <p class="ligne-prix">${fmt(p.prix * a.qte)}${a.qte > 1 ? ` <small>(${a.qte} × ${fmt(p.prix)})</small>` : ""}</p>
              <div class="qte petit"><button type="button" data-k="${k}" data-d="-1" aria-label="Diminuer">−</button><span>${a.qte}</span><button type="button" data-k="${k}" data-d="1" aria-label="Augmenter">+</button></div>
            </div>
            <button type="button" class="icone suppr" data-suppr="${k}" aria-label="Retirer ${esc(p.nom)}">${ICON.trash}</button>
          </div>`;
        }).join("")}</div>
        <div class="panier-resume">
          <div class="ligne-total"><span>Sous-total</span><b>${fmt(sousTotal(Panier.articles))}</b></div>
          <div class="ligne-total"><span>Livraison (Bamako)</span><b>${texteLivraison()}</b></div>
          <p class="note">${ICON.cash} Paiement uniquement à la livraison, en espèces.</p>
          <a class="btn-noir large" href="commande.html">Passer la commande</a>
          <a class="lien" href="boutique.html">Continuer mes achats</a>
        </div>
      </div>`;
  }
  zone.addEventListener("click", e => {
    const b = e.target.closest("button");
    if (!b) return;
    if (b.dataset.suppr !== undefined) Panier.articles.splice(+b.dataset.suppr, 1);
    else if (b.dataset.d) {
      const a = Panier.articles[+b.dataset.k];
      if (!a) return;
      a.qte = Math.max(1, Math.min(20, a.qte + +b.dataset.d));
    } else return;
    Panier.retires = 0;
    Panier.sauver();
    afficher();
  });
  afficher();
}

/* ---------- Page : Commande (paiement à la livraison uniquement) ---------- */
function afficherMerci(zone, c) {
  zone.innerHTML = `
    <div class="merci">
      <div class="merci-icone">${ICON.heart}</div>
      <h1 class="titre">Merci ${esc(c.prenom)} !</h1>
      <p>Votre commande n° <b>${esc(c.ref)}</b> est prête.</p>
      <p class="important">Dernière étape : envoyez-la-nous sur WhatsApp pour la confirmer.</p>
      <a class="btn-wa plein" href="${esc(c.url)}" target="_blank" rel="noopener">${ICON.whatsapp} Envoyer ma commande sur WhatsApp</a>
      <p>Nous vous appelons ensuite pour confirmer la livraison. Vous payez en espèces à la réception.</p>
      <a class="lien" href="index.html" id="finCommande">Retour à l'accueil</a>
    </div>`;
  $("#finCommande").addEventListener("click", () => effacer("sessionStorage", "atiya-derniere-commande"));
  scrollTo(0, 0);
}

function initCommande() {
  const zone = $("#commande");
  const estDirect = params.has("id"); // « Acheter maintenant » : l'article est dans le lien
  let articles;
  if (estDirect) {
    const a = { id: params.get("id"), taille: params.get("taille") || "", couleur: params.get("couleur") || "", qte: params.get("qte") || 1 };
    articles = ligneValide(a) ? [a] : [];
  } else {
    articles = Panier.articles;
  }

  // Commande déjà validée dans cet onglet (retour depuis WhatsApp, page rechargée…) : on remontre le récapitulatif
  const derniere = lire("sessionStorage", "atiya-derniere-commande");
  if (derniere && derniere.cle === location.search && (estDirect || !articles.length)) {
    afficherMerci(zone, derniere);
    return;
  }

  if (!articles.length) {
    zone.innerHTML = estDirect
      ? `<div class="vide"><p>Cet article n'est plus disponible dans ce choix de taille ou de couleur.</p><a class="btn-noir" href="boutique.html">Voir la boutique</a></div>`
      : `${noteRetires()}<div class="vide"><p>Votre panier est vide.</p><a class="btn-noir" href="boutique.html">Découvrir la boutique</a></div>`;
    return;
  }

  const st = sousTotal(articles);
  const frais = fraisLivraison();
  const total = st + (frais || 0);
  const totalTxt = fmt(total) + (frais === null ? " + livraison" : "");
  const totalHTML = fmt(total) + (frais === null ? `<small class="plus-livraison">+ livraison</small>` : "");
  const nb = articles.reduce((n, a) => n + a.qte, 0);
  const memo = lire("localStorage", "atiya-coordonnees") || {};
  const vignette = a => { const p = produit(a.id); return `<span class="vignette"><span class="vignette-img ph">${img(p.images[0], p.nom)}</span><i>${a.qte}</i></span>`; };

  const resume = `
    <div class="resume">
      ${articles.map(a => { const p = produit(a.id); return `
        <div class="resume-article">${vignette(a)}<div><p class="resume-nom">${esc(p.nom)}</p><p class="resume-var">${variante(a)}</p></div><b>${fmt(p.prix * a.qte)}</b></div>`; }).join("")}
      <div class="resume-totaux">
        <div><span>Sous-total</span><span>${fmt(st)}</span></div>
        <div><span>Livraison</span><span>${texteLivraison()}</span></div>
        <div class="grand"><span>Total</span><span>${totalHTML}</span></div>
      </div>
    </div>`;

  const champ = (nom, label, auto, type = "text", message = "Ce champ est obligatoire") => `
    <label class="champ"><input name="${nom}" type="${type}" placeholder=" " autocomplete="${auto}" value="${esc(memo[nom] || "")}" required${type === "tel" ? ' inputmode="tel"' : ""}><span>${label}</span><em>${message}</em></label>`;

  zone.innerHTML = `${estDirect ? "" : noteRetires()}
    <div class="co">
      <div class="co-gauche">
        <details class="co-resume-mobile"><summary><span>Résumé de la commande ${ICON.chevD}</span><b>${totalHTML}</b></summary>${resume}</details>
        <form id="formCommande" novalidate>
          <h2>Livraison</h2>
          <div class="champ fixe"><span>Pays/région</span><b>Mali</b></div>
          <div class="champ fixe"><span>Ville</span><b>Bamako</b></div>
          <div class="deux">${champ("prenom", "Prénom", "given-name")}${champ("nom", "Nom", "family-name")}</div>
          ${champ("quartier", "Quartier", "address-level3")}
          ${champ("adresse", "Adresse ou point de repère", "street-address")}
          ${champ("tel", "Téléphone", "tel", "tel", "Entrez un numéro valide (8 chiffres)")}
          <label class="case"><input type="checkbox" name="memo" ${memo.prenom ? "checked" : ""}> Sauvegarder mes coordonnées pour la prochaine fois</label>

          <h2>Mode de livraison</h2>
          <div class="option"><span>Livraison à domicile — Bamako</span><b>${texteLivraison()}</b></div>

          <h2>Paiement</h2>
          <p class="co-sous">Le paiement se fait uniquement à la livraison.</p>
          <div class="paiement">
            <div class="option"><span class="rond"></span><span>💵 Paiement à la livraison</span></div>
            <p class="paiement-note">Vous payez en espèces au livreur, au moment où vous recevez votre commande. Aucun paiement en ligne.</p>
          </div>

          <label class="champ"><textarea name="note" placeholder=" " rows="2"></textarea><span>Remarque (facultatif)</span></label>

          <div class="co-total-bas">${vignette({ ...articles[0], qte: nb })}<span class="co-total-txt"><b>Total</b><small>${nb} article${nb > 1 ? "s" : ""}</small></span><b class="co-total-montant">${totalHTML}</b></div>
          <button class="btn-valider" type="submit">Valider la commande</button>
          <p class="co-aide">En validant, WhatsApp s'ouvre avec le récapitulatif de votre commande : appuyez sur « Envoyer » pour nous la transmettre.</p>
        </form>
      </div>
      <aside class="co-droite">${resume}</aside>
    </div>`;

  const form = $("#formCommande");
  form.addEventListener("input", e => e.target.closest(".champ")?.classList.remove("erreur"));
  form.addEventListener("submit", e => {
    e.preventDefault();
    let premierFaux = null;
    $$("[required]", form).forEach(i => {
      const faux = !i.value.trim() || (i.name === "tel" && i.value.replace(/\D/g, "").length < 8);
      i.closest(".champ").classList.toggle("erreur", faux);
      if (faux && !premierFaux) premierFaux = i;
    });
    if (premierFaux) { premierFaux.focus(); return; }

    const f = Object.fromEntries(new FormData(form));
    for (const k in f) f[k] = String(f[k]).trim();
    if (f.memo) ecrire("localStorage", "atiya-coordonnees", { prenom: f.prenom, nom: f.nom, quartier: f.quartier, adresse: f.adresse, tel: f.tel });
    else effacer("localStorage", "atiya-coordonnees");

    const ref = "AT-" + Date.now().toString(36).toUpperCase().slice(-6);
    const lignes = articles.map(a => {
      const p = produit(a.id);
      return `• ${p.nom}${a.couleur ? " — " + a.couleur : ""}${a.taille ? " — Taille " + a.taille : ""} — ${a.qte} × ${fmt(p.prix)}`;
    });
    const message = [
      `🛍️ *Nouvelle commande ${CONFIG.nom}* — n° ${ref}`,
      "",
      ...lignes,
      "",
      `Sous-total : ${fmt(st)}`,
      `Livraison : ${texteLivraison()}`,
      `*Total : ${totalTxt}*`,
      "",
      `👤 ${f.prenom} ${f.nom}`,
      `📞 ${f.tel}`,
      `📍 Bamako — ${f.quartier}, ${f.adresse}`,
      ...(f.note ? [`📝 ${f.note}`] : []),
      "",
      "💵 Paiement à la livraison",
    ].join("\n");
    const commande = { ref, url: waLink(message), prenom: f.prenom, cle: location.search };

    // On garde le récapitulatif : si la page se recharge en revenant de WhatsApp, rien n'est perdu
    ecrire("sessionStorage", "atiya-derniere-commande", commande);
    window.open(commande.url, "_blank");
    if (!estDirect) Panier.vider();
    afficherMerci(zone, commande);
  });
}

/* ---------- Page : Contact ---------- */
function initContact() {
  const form = $("#formContact");
  form.addEventListener("input", e => e.target.closest(".champ")?.classList.remove("erreur"));
  form.addEventListener("submit", e => {
    e.preventDefault();
    let premierFaux = null;
    $$("[required]", form).forEach(i => {
      const faux = !i.value.trim();
      i.closest(".champ").classList.toggle("erreur", faux);
      if (faux && !premierFaux) premierFaux = i;
    });
    if (premierFaux) { premierFaux.focus(); return; }
    const f = Object.fromEntries(new FormData(form));
    const tel = String(f.tel || "").trim();
    const texte = `Bonjour ${CONFIG.nom} 👋\n\n${String(f.message).trim()}\n\n— ${String(f.nom).trim()}${tel ? " (" + tel + ")" : ""}`;
    window.open(waLink(texte), "_blank");
    form.reset();
    toast("Votre message est prêt dans WhatsApp ✓");
  });

  const reseaux = [
    [lienReseau(CONFIG.instagram, "https://www.instagram.com/"), ICON.insta, "Instagram", "Nos nouveautés en photos"],
    [lienReseau(CONFIG.tiktok, "https://www.tiktok.com/@"), ICON.sparkle, "TikTok", "Nos vidéos"],
    [lienReseau(CONFIG.facebook, "https://www.facebook.com/"), ICON.heart, "Facebook", "Suivez-nous"],
  ].filter(([href]) => href).map(([href, icone, titre, texte]) => ({ href, icone, titre, texte }));
  const infos = [
    { icone: ICON.whatsapp, titre: "WhatsApp", texte: "Réponse rapide", href: waLink() },
    CONFIG.telephone && { icone: ICON.phone, titre: "Téléphone", texte: CONFIG.telephone, href: "tel:" + String(CONFIG.telephone).replace(/[^\d+]/g, "") },
    { icone: ICON.pin, titre: CONFIG.quartier ? `${CONFIG.quartier}, Bamako` : "Bamako, Mali", texte: "Livraison dans tout Bamako" },
    CONFIG.horaires && { icone: ICON.clock, titre: "Horaires", texte: CONFIG.horaires },
    ...reseaux,
  ].filter(Boolean);
  $("#infosContact").innerHTML = infos.map(i => {
    const contenu = `<span class="info-icone">${i.icone}</span><span><b>${esc(i.titre)}</b><span>${esc(i.texte)}</span></span>`;
    const ext = i.href && i.href.startsWith("http") ? ' target="_blank" rel="noopener"' : "";
    return i.href ? `<a class="info" href="${esc(i.href)}"${ext}>${contenu}</a>` : `<div class="info">${contenu}</div>`;
  }).join("");
  rendreFAQ($("#faq"));
}

/* ---------- Démarrage ---------- */
Panier.charger();
rendreStructure();
montrerAvertissements();
({ accueil: initAccueil, boutique: initBoutique, produit: initProduit, panier: initPanier, commande: initCommande, contact: initContact })[page]?.();
majPastille();

// Panier modifié dans un autre onglet : on se remet à jour
addEventListener("storage", e => {
  if (e.key !== Panier.cle) return;
  if (page === "panier" || page === "commande") return location.reload();
  Panier.charger();
  majPastille();
});
// Page revenue avec le bouton « Retour » : on recharge pour afficher le panier à jour
addEventListener("pageshow", e => { if (e.persisted) location.reload(); });
