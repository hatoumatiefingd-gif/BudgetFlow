// ===================== RÉGLAGES DE LA BOUTIQUE =====================
const CONFIG = {
  whatsapp: "22300000000",    // ⚠️ ton numéro WhatsApp avec l'indicatif du Mali, sans + (ex. 22376123456)
  devise: "FCFA",
  livraison: 1000,            // frais de livraison à Bamako (0 = offerte)
};

// Catalogue : pour mettre une vraie photo, ajoute  img: "images/nom-photo.jpg"
const products = [
  { id: 1, name: "Robe Satin Rosé",      price: 15000, old: 18000, cat: "robes",     icon: "👗", bg: "#f3c9d0", tag: "Nouveau",     sizes: ["S","M","L","XL"], colors: ["Rose","Noir","Champagne"] },
  { id: 2, name: "Robe Longue Plissée",  price: 17500,             cat: "robes",     icon: "👗", bg: "#efe4f2",                     sizes: ["S","M","L"],      colors: ["Lilas","Beige"] },
  { id: 3, name: "Ensemble Lin Crème",   price: 19000,             cat: "ensembles", icon: "🥻", bg: "#f7e9d7", tag: "Best-seller", sizes: ["S","M","L","XL"], colors: ["Crème","Rose poudré"] },
  { id: 4, name: "Ensemble Tailleur Or", price: 22500,             cat: "ensembles", icon: "🧥", bg: "#f7e3c4",                     sizes: ["M","L","XL"],     colors: ["Camel","Noir"] },
  { id: 5, name: "Chemise Soie Blush",   price: 10000,             cat: "hauts",     icon: "👚", bg: "#fde2e4",                     sizes: ["S","M","L"],      colors: ["Blush","Blanc"] },
  { id: 6, name: "Top Dentelle",         price: 7500, old: 9000,  cat: "hauts",     icon: "👚", bg: "#f6dfe3", tag: "Promo",       sizes: ["S","M","L"],      colors: ["Noir","Ivoire"] },
  { id: 7, name: "Jupe Midi Satinée",    price: 11000,             cat: "bas",       icon: "🩳", bg: "#e9c4d0",                     sizes: ["S","M","L","XL"], colors: ["Prune","Rose"] },
  { id: 8, name: "Pantalon Palazzo",     price: 12500,             cat: "bas",       icon: "👖", bg: "#f8ecd9", tag: "Nouveau",     sizes: ["S","M","L","XL"], colors: ["Beige","Noir"] },
];
// ===================================================================

const $ = id => document.getElementById(id);
const fmt = n => n.toLocaleString("fr-FR") + " " + CONFIG.devise;
const pic = p => p.img ? `background:url(${p.img}) center/cover` : `background:linear-gradient(160deg,${p.bg},#fffafb)`;
let cart = [];
try { cart = JSON.parse(localStorage.getItem("atiya-cart")) || []; } catch {}

// ---------- Boutique ----------
function render(filter = "all") {
  $("products").innerHTML = products
    .filter(p => filter === "all" || p.cat === filter)
    .map(p => `
      <article class="card" data-id="${p.id}">
        <div class="img" style="${pic(p)}">
          ${p.img ? "" : p.icon}
          ${p.tag ? `<span class="tag">${p.tag}</span>` : ""}
          <button class="add">Commander</button>
        </div>
        <div class="info"><h3>${p.name}</h3>
          <p class="price">${p.old ? `<span class="old">${fmt(p.old)}</span>` : ""}${fmt(p.price)}</p></div>
      </article>`).join("");
}
function setFilter(f) {
  document.querySelectorAll("#filters button").forEach(b => b.classList.toggle("active", b.dataset.f === f));
  render(f);
}
$("filters").addEventListener("click", e => { if (e.target.dataset.f) setFilter(e.target.dataset.f); });
document.querySelectorAll("[data-go]").forEach(a => a.addEventListener("click", () => setFilter(a.dataset.go)));
$("products").addEventListener("click", e => {
  const c = e.target.closest(".card"); if (c) openProduct(+c.dataset.id);
});

// ---------- Fiche produit ----------
function openProduct(id) {
  const p = products.find(x => x.id === id);
  const sel = { size: p.sizes[0], color: p.colors[0], qty: 1 };
  const m = $("modal");
  m.innerHTML = `
    <div class="pic" style="${pic(p)}">${p.img ? "" : p.icon}</div>
    <div class="body">
      <button class="x" onclick="closeAll()">✕</button>
      <h3>${p.name}</h3>
      <p class="price">${p.old ? `<span class="old">${fmt(p.old)}</span>` : ""}${fmt(p.price)}</p>
      <label>Taille</label><div class="opts" data-k="size">${p.sizes.map(s => `<button class="${s===sel.size?"sel":""}">${s}</button>`).join("")}</div>
      <label>Couleur</label><div class="opts" data-k="color">${p.colors.map(s => `<button class="${s===sel.color?"sel":""}">${s}</button>`).join("")}</div>
      <label>Quantité</label><div class="qty"><button data-q="-1">−</button><span>1</span><button data-q="1">+</button></div>
      <button class="btn btn-gold full" id="addBtn">Ajouter au panier</button>
      <p class="cod">💵 Paiement à la livraison · Échange possible</p>
    </div>`;
  m.querySelectorAll(".opts").forEach(o => o.addEventListener("click", e => {
    if (e.target.tagName !== "BUTTON") return;
    o.querySelectorAll("button").forEach(b => b.classList.remove("sel"));
    e.target.classList.add("sel"); sel[o.dataset.k] = e.target.textContent;
  }));
  m.querySelector(".qty").addEventListener("click", e => {
    if (!e.target.dataset.q) return;
    sel.qty = Math.max(1, sel.qty + +e.target.dataset.q);
    m.querySelector(".qty span").textContent = sel.qty;
  });
  $("addBtn").onclick = () => { addToCart(p, sel); closeAll(); toast("Ajouté au panier ✦"); };
  $("overlay").classList.add("on"); m.classList.add("on");
}

// ---------- Panier ----------
function addToCart(p, s) {
  const ex = cart.find(i => i.id === p.id && i.size === s.size && i.color === s.color);
  ex ? ex.qty += s.qty : cart.push({ id: p.id, name: p.name, price: p.price, size: s.size, color: s.color, qty: s.qty });
  saveCart();
}
function saveCart() {
  try { localStorage.setItem("atiya-cart", JSON.stringify(cart)); } catch {}
  $("cartCount").textContent = cart.reduce((n, i) => n + i.qty, 0);
  drawCart();
}
function total() { return cart.reduce((n, i) => n + i.price * i.qty, 0); }
function drawCart() {
  $("cartFoot").style.display = cart.length ? "" : "none";
  $("cartItems").innerHTML = cart.length ? cart.map((i, k) => {
    const p = products.find(x => x.id === i.id) || {};
    return `<div class="ci"><div class="th" style="${pic(p)}">${p.img ? "" : p.icon || ""}</div>
      <div><b>${i.name}</b><br>Taille ${i.size} · ${i.color}<br>${i.qty} × ${fmt(i.price)}</div>
      <button class="rm" data-k="${k}" aria-label="Retirer">✕</button></div>`;
  }).join("") : `<p class="empty">Votre panier est vide</p>`;
  $("subtotal").textContent = fmt(total());
  $("shipping").textContent = CONFIG.livraison ? fmt(CONFIG.livraison) : "offerte";
  $("grandTotal").textContent = fmt(total() + CONFIG.livraison);
}
$("cartItems").addEventListener("click", e => {
  if (e.target.dataset.k !== undefined) { cart.splice(+e.target.dataset.k, 1); saveCart(); }
});
function openCart() { drawCart(); $("overlay").classList.add("on"); $("drawer").classList.add("on"); }
function closeAll() { ["overlay", "modal", "drawer"].forEach(id => $(id).classList.remove("on")); }

// ---------- Commande (paiement à la livraison, envoyée sur WhatsApp) ----------
$("orderForm").addEventListener("submit", e => {
  e.preventDefault();
  const f = Object.fromEntries(new FormData(e.target));
  const lignes = cart.map(i => `• ${i.name} — Taille ${i.size}, ${i.color} — ${i.qty} × ${fmt(i.price)}`).join("\n");
  const msg = `🛍️ *Nouvelle commande ATIYA*\n\n${lignes}\n\nSous-total : ${fmt(total())}\nLivraison : ${CONFIG.livraison ? fmt(CONFIG.livraison) : "offerte"}\n*Total : ${fmt(total() + CONFIG.livraison)}*\n\n👤 ${f.nom}\n📞 ${f.tel}\n📍 Bamako — ${f.quartier}, ${f.adresse}\n🚚 ${f.livraison}${f.note ? "\n📝 " + f.note : ""}\n\n💵 Paiement à la livraison`;
  window.open(`https://wa.me/${CONFIG.whatsapp}?text=${encodeURIComponent(msg)}`, "_blank");
  cart = []; saveCart();
  $("cartItems").innerHTML = `<div class="done"><h4>Merci ${f.nom} ! ✦</h4><p>Votre commande a été envoyée. Nous vous appelons très vite pour la confirmer.</p></div>`;
});

// ---------- Divers ----------
function toast(t) { const el = $("toast"); el.textContent = t; el.classList.add("show"); setTimeout(() => el.classList.remove("show"), 1800); }
document.querySelectorAll(".wa-link").forEach(a => { a.href = `https://wa.me/${CONFIG.whatsapp}`; a.target = "_blank"; });
addEventListener("keydown", e => e.key === "Escape" && closeAll());
addEventListener("scroll", () => $("header").classList.toggle("scrolled", scrollY > 40));
document.querySelectorAll(".nav-left a").forEach(a => a.addEventListener("click", () => document.body.classList.remove("menu-open")));

render(); saveCart();
