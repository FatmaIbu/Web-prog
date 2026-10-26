
(function () {
    "use strict";

    // 1. TESTIMONIAL SLIDER

    function initTestimonialSlider() {
        const sliderElement = document.querySelector(".testimonial-slider");

        if (!sliderElement || sliderElement.dataset.initialized === "true") {
            return;
        }

        if (typeof tns !== "function") {
            console.warn("Tiny Slider library is not loaded.");
            return;
        }

        const options = {
            container: sliderElement,
            items: 1,
            axis: "horizontal",
            swipeAngle: false,
            speed: 700,
            nav: true,
            controls: true,
            autoplay: true,
            autoplayHoverPause: true,
            autoplayTimeout: 3500,
            autoplayButtonOutput: false
        };

        const controls = document.querySelector("#testimonial-nav");

        if (controls) {
            options.controlsContainer = controls;
        }

        try {
            tns(options);
            sliderElement.dataset.initialized = "true";
        } catch (error) {
            console.error("Testimonial slider error:", error);
        }
    }

    // 2. PRODUCT FILTERS
    function initProductFilters() {
        const filterButtons = document.querySelectorAll(
            ".product-filters ul li"
        );

        const products = document.querySelectorAll(".product-item");

        filterButtons.forEach(function (button) {
            if (button.dataset.filterBound === "true") return;

            button.dataset.filterBound = "true";

            button.addEventListener("click", function () {
                filterButtons.forEach(function (btn) {
                    btn.classList.remove("active");
                });

                button.classList.add("active");

                const filterValue = button.getAttribute("data-filter");

                products.forEach(function (product) {
                    const productContainer = product.parentElement;

                    if (
                        filterValue === "*" ||
                        productContainer.matches(filterValue)
                    ) {
                        productContainer.style.display = "";
                    } else {
                        productContainer.style.display = "none";
                    }
                });
            });
        });
    }

    // 3. QUANTITY BUTTONS
    function initQuantityButtons() {
        document.querySelectorAll(".quantity-container").forEach(
            function (container) {
                if (container.dataset.quantityBound === "true") return;

                const input = container.querySelector(".quantity-amount");
                const increase = container.querySelector(".increase");
                const decrease = container.querySelector(".decrease");

                if (!input || !increase || !decrease) return;

                container.dataset.quantityBound = "true";

                increase.addEventListener("click", function () {
                    const current = parseInt(input.value, 10) || 0;
                    input.value = current + 1;
                });

                decrease.addEventListener("click", function () {
                    const current = parseInt(input.value, 10) || 0;
                    input.value = Math.max(1, current - 1);
                });
            }
        );
    }

    // 4. CART NOTIFICATION
    let cartCount = 0;
    let notificationTimeout;

    function showCartNotification() {
        let notification = document.querySelector(".cart-notification");

        if (!notification) {
            notification = document.createElement("div");
            notification.className = "cart-notification";
            notification.textContent = "Item added to cart!";
            document.body.appendChild(notification);
        }

        notification.classList.add("show");

        clearTimeout(notificationTimeout);

        notificationTimeout = setTimeout(function () {
            notification.classList.remove("show");
        }, 2000);
    }

    function initAddToCartButtons() {
        document.querySelectorAll(".add-to-cart").forEach(function (button) {
            if (button.dataset.cartBound === "true") return;

            button.dataset.cartBound = "true";

            button.addEventListener("click", function (event) {
                event.preventDefault();

                cartCount++;

                const cartCountElement = document.getElementById("cart-count");

                if (cartCountElement) {
                    cartCountElement.textContent = cartCount;
                    cartCountElement.style.opacity = "1";
                }

                showCartNotification();
            });
        });
    }

    // 5. CONTACT FORM
    function initContactForm() {
        const contactForm = document.getElementById("contact-form");

        if (!contactForm || contactForm.dataset.formBound === "true") {
            return;
        }

        // Only handle the full contact form here.
        // The footer newsletter form may share this ID.
        const fname = contactForm.querySelector("#fname");
        const lname = contactForm.querySelector("#lname");
        const email = contactForm.querySelector("#email");
        const message = contactForm.querySelector("#message");

        if (!fname || !lname || !email || !message) {
            return;
        }

        contactForm.dataset.formBound = "true";

        contactForm.addEventListener("submit", function (event) {
            event.preventDefault();

            if (!contactForm.reportValidity()) return;

            console.log("First Name:", fname.value);
            console.log("Last Name:", lname.value);
            console.log("Email:", email.value);
            console.log("Message:", message.value);

            contactForm.reset();

            const notification = document.getElementById("notification");

            if (notification) {
                notification.style.display = "block";

                setTimeout(function () {
                    notification.style.display = "none";
                }, 3000);
            }
        });
    }

    // 6. SHOPPING CART
    let cart = [
        {
            name: "Kruzo Aero Chair",
            price: 180,
            quantity: 1,
            image: "../images/product-1.png"
        }
    ];

    function renderCart() {
        const cartItemsContainer = document.getElementById("cart-items");
        const subtotalElement = document.getElementById("subtotal");
        const totalElement = document.getElementById("total");

        if (!cartItemsContainer || !subtotalElement || !totalElement) {
            return;
        }

        cartItemsContainer.innerHTML = "";

        let subtotal = 0;

        cart.forEach(function (item, index) {
            subtotal += item.price * item.quantity;

            const row = document.createElement("tr");

            row.innerHTML = `
                <td class="product-thumbnail">
                    <img src="${item.image}" alt="Product Image"
                         class="img-fluid">
                </td>

                <td class="product-name">
                    <h2 class="h5 text-black">${item.name}</h2>
                </td>

                <td class="product-price">
                    $${item.price.toFixed(2)}
                </td>

                <td class="product-quantity">
                    <div class="input-group mb-3 d-flex align-items-center quantity-container"
                         style="max-width: 120px;">

                        <div class="input-group-prepend">
                            <button class="btn btn-outline-black decrease"
                                    type="button">−</button>
                        </div>

                        <input type="text"
                               class="form-control text-center quantity-amount"
                               value="${item.quantity}"
                               readonly>

                        <div class="input-group-append">
                            <button class="btn btn-outline-black increase"
                                    type="button">+</button>
                        </div>
                    </div>
                </td>

                <td class="product-total">
                    $${(item.price * item.quantity).toFixed(2)}
                </td>

                <td class="product-remove">
                    <button class="btn btn-black btn-sm remove-item"
                            type="button">X</button>
                </td>
            `;

            row.querySelector(".increase").addEventListener(
                "click",
                function () {
                    item.quantity++;
                    renderCart();
                }
            );

            row.querySelector(".decrease").addEventListener(
                "click",
                function () {
                    item.quantity = Math.max(1, item.quantity - 1);
                    renderCart();
                }
            );

            row.querySelector(".remove-item").addEventListener(
                "click",
                function () {
                    cart.splice(index, 1);
                    renderCart();
                }
            );

            cartItemsContainer.appendChild(row);
        });

        subtotalElement.textContent = "$" + subtotal.toFixed(2);
        totalElement.textContent = "$" + subtotal.toFixed(2);
    }

    function initCart() {
        const cartItemsContainer = document.getElementById("cart-items");

        if (!cartItemsContainer) return;

        const updateCartButton = document.getElementById("update-cart");

        if (updateCartButton && updateCartButton.dataset.bound !== "true") {
            updateCartButton.dataset.bound = "true";

            updateCartButton.addEventListener("click", function () {
                renderCart();
            });
        }

        renderCart();
    }

    // 7. INITIALIZE PAGE FEATURES
    function initializePageFeatures() {
        initTestimonialSlider();
        initProductFilters();
        initQuantityButtons();
        initAddToCartButtons();
        initContactForm();
        initCart();
    }

    // Make this function available to app.js
    window.initializePageFeatures = initializePageFeatures;

    // Initialize elements already present on page load
    if (document.readyState === "loading") {
        document.addEventListener(
            "DOMContentLoaded",
            initializePageFeatures
        );
    } else {
        initializePageFeatures();
    }

})();
