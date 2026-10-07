// Catalogue : remplace les emojis par tes vraies photos (ex. img: "images/robe.jpg")
const products = [
  { name: "Robe Satin Rosé", price: 129, cat: "vetements", icon: "👗", bg: "#f3c9d0", tag: "Nouveau" },
  { name: "Ensemble Lounge Soie", price: 98, cat: "vetements", icon: "🩱", bg: "#f6dfe3" },
  { name: "Blazer Crème Doré", price: 145, cat: "vetements", icon: "🧥", bg: "#f7e9d7", tag: "Best-seller" },
  { name: "Sac Pochette Or", price: 89, cat: "accessoires", icon: "👜", bg: "#f7e3c4" },
  { name: "Escarpins Velours", price: 115, cat: "accessoires", icon: "👠", bg: "#e9c4d0" },
  { name: "Collier Perle Lune", price: 59, cat: "bijoux", icon: "📿", bg: "#efe4f2", tag: "Nouveau" },
  { name: "Bague Étoile", price: 39, cat: "bijoux", icon: "💍", bg: "#f8ecd9" },
  { name: "Parfum Atiya N°1", price: 75, cat: "beaute", icon: "🌸", bg: "#fde2e4", tag: "Exclusif" },
];

const grid = document.getElementById("products");
let cart = 0;

function render(filter = "all") {
  grid.innerHTML = products
    .filter(p => filter === "all" || p.cat === filter)
    .map(p => `
      <article class="card">
        <div class="img" style="background:${p.img ? `url(${p.img}) center/cover` : `linear-gradient(160deg,${p.bg},#fffafb)`}">
          ${p.img ? "" : p.icon}
          ${p.tag ? `<span class="tag">${p.tag}</span>` : ""}
          <button class="add">Ajouter au panier</button>
        </div>
        <div class="info"><h3>${p.name}</h3><p class="price">${p.price} €</p></div>
      </article>`).join("");
}

document.getElementById("filters").addEventListener("click", e => {
  if (e.target.tagName !== "BUTTON") return;
  document.querySelectorAll("#filters button").forEach(b => b.classList.remove("active"));
  e.target.classList.add("active");
  render(e.target.dataset.f);
});

grid.addEventListener("click", e => {
  if (!e.target.classList.contains("add")) return;
  document.getElementById("cartCount").textContent = ++cart;
  const t = document.getElementById("toast");
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 1800);
});

window.addEventListener("scroll", () =>
  document.getElementById("header").classList.toggle("scrolled", scrollY > 40));

document.querySelectorAll(".nav-left a").forEach(a =>
  a.addEventListener("click", () => document.body.classList.remove("menu-open")));

render();
