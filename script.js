// ---------------navbar----------------------------
const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("navLinks");
hamburger.addEventListener("click", () => {
navLinks.classList.toggle("active");
});

const searchIcon = document.getElementById("searchIcon");
const searchContainer = document.getElementById("searchContainer");
const closeSearch = document.getElementById("closeSearch");
const searchInput = document.getElementById("searchInput");

// Show search box
searchIcon.addEventListener("click", () => {
    searchIcon.style.display = "none";
    searchContainer.style.display = "inline-block";
    searchInput.focus();
});

// Hide search box
closeSearch.addEventListener("click", () => {
    searchContainer.style.display = "none";
    searchIcon.style.display = "inline-block";
});

// Redirect exactly like shop.php search filter
document.getElementById("navbarSearchForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const keyword = searchInput.value.trim();
    if (keyword !== "") {
        window.location.href = "shop.php?search=" + encodeURIComponent(keyword);
    }
});


//========================= SHOPPING CART ICON =========================
// toggle dropdown
document.getElementById("cartIcon").onclick = () => {
    document.getElementById("cartMenu").classList.toggle("show");
};

// add to cart btn
document.querySelectorAll(".addToCartBtn").forEach(btn => {
    btn.onclick = function () {
        let id = this.dataset.id;

        fetch("add_to_cart_ajax.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "product_id=" + id
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById("cart-count").textContent = data.count;
            loadMiniCart();
            document.getElementById("cartMenu").classList.add("show");
        });
    };
});


function loadMiniCart() {
    fetch("mini_cart.php")
    .then(res => res.json())
    .then(data => {
        let box = document.getElementById("cart-items");
        box.innerHTML = "";
        document.getElementById("cart-total-price").textContent = data.total;

        // Update cart badge
        let totalQty = data.items.reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById("cart-count").textContent = totalQty;

        if (data.items.length === 0) {
            box.innerHTML = "<p>Cart is empty</p>";
            return;
        }

        data.items.forEach(item => {
            box.innerHTML += `
                <div class="cart-item">
                    <img src="images/${item.image}">
                    <div>
                        <p>${item.name}</p>
                        <p>$${item.price}</p>
                        <div class="qty-box">
                            <button onclick="updateQty(${item.index}, -1)">-</button>
                            <span>${item.quantity}</span>
                            <button onclick="updateQty(${item.index}, 1)">+</button>
                        </div>
                    </div>
                    <button class="delete-btn" onclick="removeItem(${item.index})">&times;</button>
                </div>
            `;
        });
    });
}


function updateQty(index, change) {
    fetch("update_cart_qty.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `index=${index}&change=${change}`
    }).then(() => loadMiniCart());
}

function removeItem(index) {
    fetch("remove_cart_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `index=${index}`
    })
    .then(res => res.json())
    .then(data => {
        loadMiniCart(); // refresh mini cart
        document.getElementById("cart-count").textContent = data.count; // update badge
    });
}
    // Refresh mini cart
    loadMiniCart();

// close cart button
document.getElementById("closeCart").onclick = function (e) {
    e.stopPropagation();
    document.getElementById("cartMenu").classList.remove("show");
};


// ======================== SHOPPING CART END ==========================


// -------------navbar end---------------------------

// -------------hero-section-------------------------
 let current = 0;
                const slides = document.querySelectorAll(".hero-slide");
                const dotContainer = document.querySelector(".hero-dots");

                // Create dots
                slides.forEach((_, i) => {
                    let dot = document.createElement("div");
                    dot.onclick = () => showSlide(i);
                    dotContainer.appendChild(dot);
                });
                const dots = dotContainer.querySelectorAll("div");

                function showSlide(i) {
                    slides.forEach(s => s.classList.remove("active"));
                    dots.forEach(d => d.classList.remove("active-dot"));

                    slides[i].classList.add("active");
                    dots[i].classList.add("active-dot");
                    current = i;
                }

                function nextSlide() {
                    current = (current + 1) % slides.length;
                    showSlide(current);
                }

                function prevSlide() {
                    current = (current - 1 + slides.length) % slides.length;
                    showSlide(current);
                }

                showSlide(0); // Default first slide

                // Auto Slide
                setInterval(nextSlide, 5000);

                // Swipe Support for Mobile
                let startX = 0;

                document.querySelector(".hero-slider").addEventListener("touchstart", e => {
                    startX = e.touches[0].clientX;
                });

                document.querySelector(".hero-slider").addEventListener("touchend", e => {
                    let endX = e.changedTouches[0].clientX;
                    if (startX - endX > 50) nextSlide();
                    if (endX - startX > 50) prevSlide();
                });
//-----------------hero-section end---------------------------------------------------------------


const filterLinks = document.querySelectorAll(".filter-links span");
const cards = document.querySelectorAll(".featured-card");
const grid = document.getElementById("productGrid");

filterLinks.forEach(link => {
    link.addEventListener("click", () => {

        filterLinks.forEach(l => l.classList.remove("active"));
        link.classList.add("active");

        const type = link.getAttribute("data-filter");

        let shuffled = [...cards].sort(() => Math.random() - 0.5);
        grid.innerHTML = "";

        if (type === "best") {
            shuffled.forEach(card => {
                card.classList.add("shuffle");
                grid.appendChild(card);
            });
        }
        else if (type === "new") {
            let filtered = shuffled.filter(c => c.dataset.type.includes("new")).slice(0, 4);
            filtered.forEach(card => {
                card.classList.add("shuffle");
                grid.appendChild(card);
            });
        }
        else if (type === "hot") {
            let filtered = shuffled.filter(c => c.dataset.type.includes("hot")).slice(0, 4);
            filtered.forEach(card => {
                card.classList.add("shuffle");
                grid.appendChild(card);
            });
        }
    });
});


//------------------- deal of the week-----------------------
function startCountdown() {
    const endDate = new Date("2025-12-31 23:59:59").getTime();

    setInterval(() => {
        const now = new Date().getTime();
        const diff = endDate - now;

        if (diff <= 0) return;

        document.getElementById("days").innerText = Math.floor(diff / (1000 * 60 * 60 * 24));
        document.getElementById("hours").innerText = Math.floor((diff / (1000 * 60 * 60)) % 24);
        document.getElementById("minutes").innerText = Math.floor((diff / (1000 * 60)) % 60);
        document.getElementById("seconds").innerText = Math.floor((diff / 1000) % 60);
    }, 1000);
}

startCountdown();
// deal of the week timer end----------------------------------------




// heart icon
document.querySelectorAll(".wish").forEach(wish => {
    wish.addEventListener("click", function (e) {
        e.stopPropagation();

        const productId = this.dataset.id;
        const icon = this.querySelector("i");

        fetch("wishlist_toggle.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "product_id=" + productId
        })
        .then(res => res.json())
        .then(data => {

            if (data.status === "login_required") {
                alert("Please login to use wishlist");
                return;
            }

            if (data.status === "added") {
                icon.classList.remove("fa-regular");
                icon.classList.add("fa-solid");
                icon.style.color = "red";
            }

            if (data.status === "removed") {
                icon.classList.remove("fa-solid");
                icon.classList.add("fa-regular");
                icon.style.color = "#777";
            }
        });
    });
});

// heart icon end
