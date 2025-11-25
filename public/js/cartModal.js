// public/js/cartModal.js

// ---------- Toast helper ----------
function showToast(message = "Added to cart!") {
  const toast = document.getElementById("toast");
  const toastMessage = document.getElementById("toastMessage");

  if (!toast || !toastMessage) {
    console.log("TOAST:", message);
    return;
  }

  toastMessage.textContent = message;

  toast.classList.remove("hidden");
  setTimeout(() => toast.classList.add("show"), 10);

  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.classList.add("hidden"), 300);
  }, 2000);
}

// ---------- Cart counter helper ----------
async function refreshCartCount() {
  try {
    const r = await fetch("index.php?page=get_cart_count", {
      credentials: "include",
    });
    const data = await r.json();

    if (data.success) {
      const badge = document.getElementById("cartCount");
      if (badge) badge.textContent = data.count;
    }
  } catch (err) {
    console.error("Failed to refresh cart count:", err);
  }
}

// ============= PAYMONGO HELPERS (CARD) ===================

async function createPaymongoPaymentMethod(card, billing) {
  const payload = {
    data: {
      attributes: {
        type: "card",
        details: {
          card_number: card.number.replace(/\s+/g, ""),
          exp_month: parseInt(card.exp_month, 10),
          exp_year: parseInt(card.exp_year, 10),
          cvc: card.cvc,
        },
        // PayMongo requires billing.email (and usually name/phone are nice)
        billing: {
          name: billing?.name || undefined,
          email: billing?.email || undefined,
          phone: billing?.phone || undefined,
        },
      },
    },
  };

  const resp = await fetch("https://api.paymongo.com/v1/payment_methods", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: "Basic " + window.PAYMONGO_PUBLIC_KEY_B64,
    },
    body: JSON.stringify(payload),
  });

  const data = await resp.json();

  if (!resp.ok) {
    const detail =
      (data.errors && data.errors[0] && data.errors[0].detail) ||
      "Failed to create payment method.";
    throw new Error(detail);
  }

  return data.data.id; // payment_method id
}

async function attachPaymongoPaymentIntent(clientKey, paymentMethodId) {
  // client_key is like: pi_xxx_client_yyy
  const paymentIntentId = clientKey.split("_client")[0];

  const payload = {
    data: {
      attributes: {
        payment_method: paymentMethodId,
        client_key: clientKey,
        return_url: window.location.origin + "/paymongo-return", // adjust if needed
      },
    },
  };

  const resp = await fetch(
    "https://api.paymongo.com/v1/payment_intents/" + paymentIntentId + "/attach",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: "Basic " + window.PAYMONGO_PUBLIC_KEY_B64,
      },
      body: JSON.stringify(payload),
    }
  );

  const data = await resp.json();

  if (!resp.ok) {
    const detail =
      (data.errors && data.errors[0] && data.errors[0].detail) ||
      "Failed to attach payment intent.";
    throw new Error(detail);
  }

  return data; // full payment_intent data
}

// ---------- Local order creation (DB) ----------
// NOTE: now accepts name + phone + address
async function createLocalOrder(totalAmount, shippingName, shippingPhone, shippingAddress) {
  // Collect selected cart item IDs
  const selectedCheckboxes = document.querySelectorAll(
    ".cart-item .item-select:checked"
  );

  const selectedIds = Array.from(selectedCheckboxes)
    .map((cb) => cb.closest(".cart-item")?.dataset.cartItemId)
    .filter((id) => id);

  if (!selectedIds.length) {
    alert("No items selected to checkout.");
    return { success: false };
  }

  const cartItemIds = selectedIds.join(",");

  try {
    const response = await fetch("index.php?page=create_order", {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body:
        `total_amount=${encodeURIComponent(totalAmount)}` +
        `&shipping_name=${encodeURIComponent(shippingName)}` +
        `&shipping_phone=${encodeURIComponent(shippingPhone)}` +
        `&shipping_address=${encodeURIComponent(shippingAddress)}` +
        `&cart_item_ids=${encodeURIComponent(cartItemIds)}`,
    });

    const text = await response.text();
    console.log("create_order raw response:", text);

    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("Failed to parse JSON:", e);
      alert("Error saving order. Please contact support.");
      return { success: false };
    }

    if (!data.success) {
      console.error("Failed to create order:", data.message);
      alert("Order save failed: " + (data.message || "Unknown error"));
    } else {
      console.log("Order created:", data);
    }

    return data;
  } catch (err) {
    console.error("Error creating local order:", err);
    alert("Error saving order. Please contact support.");
    return { success: false };
  }
}

// ---------- Update / Delete cart item (AJAX) ----------
async function updateCartItemQuantity(cartItemId, newQuantity) {
  try {
    const response = await fetch("index.php?page=update_cart_item", {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body:
        `cart_item_id=${encodeURIComponent(cartItemId)}` +
        `&quantity=${encodeURIComponent(newQuantity)}`,
    });

    const data = await response.json();
    if (!data.success) {
      alert(data.message || "Failed to update quantity.");
      return;
    }

    // if backend returns cart_count, use it; otherwise refresh
    const badge = document.getElementById("cartCount");
    if (badge && typeof data.cart_count !== "undefined") {
      badge.textContent = data.cart_count;
    } else {
      await refreshCartCount();
    }

    await loadCartFromServer();
  } catch (err) {
    console.error("Error updating cart item:", err);
  }
}

async function deleteCartItem(cartItemId) {
  try {
    const response = await fetch("index.php?page=delete_cart_item", {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `cart_item_id=${encodeURIComponent(cartItemId)}`,
    });

    const data = await response.json();
    if (!data.success) {
      alert(data.message || "Failed to remove item.");
      return;
    }

    const badge = document.getElementById("cartCount");
    if (badge && typeof data.cart_count !== "undefined") {
      badge.textContent = data.cart_count;
    } else {
      await refreshCartCount();
    }

    showToast("Removed from cart");
    await loadCartFromServer();
  } catch (err) {
    console.error("Error deleting cart item:", err);
  }
}

// ---------- Add to cart ----------
function sendAddToCart(productId, inventoryId, price) {
  fetch("index.php?page=add_to_cart", {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body:
      `PRODUCT_ID=${encodeURIComponent(productId)}` +
      `&INVENTORY_ID=${encodeURIComponent(inventoryId ?? "")}` +
      `&PRICE=${encodeURIComponent(price)}`,
  })
    .then(async (r) => {
      const text = await r.text();
      console.log("Raw response from add_to_cart:", text);
      return JSON.parse(text);
    })
    .then(async (data) => {
      console.log("Parsed JSON:", data);
      if (data.success) {
        showToast("Added to cart!");
        await refreshCartCount();
      } else {
        alert(data.message || "Error adding to cart");
      }
    })
    .catch((err) => {
      console.error("Add to cart failed:", err);
    });
}

// ---------- Load cart items ----------
async function loadCartFromServer() {
  try {
    const response = await fetch("index.php?page=get_cart", {
      method: "GET",
      credentials: "include",
    });

    const data = await response.json();
    console.log("Cart data from server:", data);

    if (!data.success) {
      console.warn(data.message || "Failed to load cart");
      renderCartItems([]);
      const badge = document.getElementById("cartCount");
      if (badge) badge.textContent = "0";
      return;
    }

    renderCartItems(data.items || []);

    // Some backends send cart_count, others totalQty – try both
    const badge = document.getElementById("cartCount");
    if (badge) {
      const cnt = data.cart_count ?? data.totalQty ?? 0;
      badge.textContent = cnt;
    }
  } catch (err) {
    console.error("Error loading cart:", err);
  }
}

// ---------- Render cart ----------
function renderCartItems(items) {
  const container = document.getElementById("cartItemsContainer");
  if (!container) return;

  container.innerHTML = "";

  if (!items.length) {
    container.innerHTML = `
      <div class="empty-state">
        <i class="bi bi-bag"></i>
        <p>Your cart is empty.</p>
      </div>
    `;
    const selectedCountEl = document.getElementById("selectedCount");
    const selectedTotalEl = document.getElementById("selectedTotal");
    const cartTotalEl = document.getElementById("cartTotal");
    const checkoutItemCountEl = document.getElementById("checkoutItemCount");

    if (selectedCountEl) selectedCountEl.textContent = "0";
    if (selectedTotalEl) selectedTotalEl.textContent = "₱0.00";
    if (cartTotalEl) cartTotalEl.textContent = "₱0.00";
    if (checkoutItemCountEl) checkoutItemCountEl.textContent = "0";
    return;
  }

  items.forEach((item) => {
    const row = document.createElement("div");
    row.className = "cart-item";
    row.dataset.cartItemId = item.cart_item_id;

    const stockLeft = item.stock_left ?? null;
    const maxReached =
      stockLeft !== null &&
      stockLeft !== undefined &&
      stockLeft !== "" &&
      Number(stockLeft) <= Number(item.quantity);

    row.innerHTML = `
      <input type="checkbox" class="item-select" checked>
      <div class="item-image">
        <img src="${item.image}" alt="${item.name}">
      </div>
      <div class="item-details">
        <div class="item-name">${item.name}</div>
        <div class="item-meta">
          ${
            item.color || item.size
              ? `Color: ${item.color || "-"} • Size: ${item.size || "-"}<br>`
              : ""
          }
          Qty: <span class="quantity-value">${item.quantity}</span>
          ${
            stockLeft
              ? `<span style="font-size:0.8rem; color:#777;">(Max: ${stockLeft})</span>`
              : ""
          }
        </div>
        <div class="item-actions">
          <div class="quantity-control">
            <button class="quantity-btn qty-minus" data-id="${item.cart_item_id}">-</button>
            <span class="quantity-value">${item.quantity}</span>
            <button 
              class="quantity-btn qty-plus ${maxReached ? "qty-plus-disabled" : ""}"
              data-id="${item.cart_item_id}"
              data-stock="${stockLeft ?? ""}"
              ${maxReached ? "disabled aria-disabled='true'" : ""}
            >+</button>
          </div>
          <div class="item-price">₱${Number(item.price).toFixed(2)}</div>
        </div>
      </div>
      <button class="remove-btn" data-id="${item.cart_item_id}">
        <i class="bi bi-trash"></i>
      </button>
    `;

    container.appendChild(row);
  });

  const totalAmount = items.reduce((sum, i) => sum + Number(i.line_total), 0);
  const totalQty = items.reduce((sum, i) => sum + Number(i.quantity), 0);

  const selectedCountEl = document.getElementById("selectedCount");
  const selectedTotalEl = document.getElementById("selectedTotal");
  const cartTotalEl = document.getElementById("cartTotal");
  const checkoutItemCountEl = document.getElementById("checkoutItemCount");

  if (selectedCountEl) selectedCountEl.textContent = totalQty;
  if (selectedTotalEl)
    selectedTotalEl.textContent = `₱${totalAmount.toFixed(2)}`;
  if (cartTotalEl) cartTotalEl.textContent = `₱${totalAmount.toFixed(2)}`;
  if (checkoutItemCountEl) checkoutItemCountEl.textContent = totalQty;

  if (window._updateCartSummary) window._updateCartSummary();
}

// ---------- Place order (PayMongo + local order) ----------
async function placeOrder() {
  const totalText = document
    .getElementById("cartTotal")
    .textContent.replace("₱", "")
    .trim();
  const totalAmount = parseFloat(totalText) || 0;

  // Only card now
  const card = {
    number: document.getElementById("card-number").value,
    exp_month: document.getElementById("card-exp-month").value,
    exp_year: document.getElementById("card-exp-year").value,
    cvc: document.getElementById("card-cvc").value,
  };

  if (!card.number || !card.exp_month || !card.exp_year || !card.cvc) {
    alert("Please fill all card fields.");
    return;
  }

  // Shipping / billing details from editable fields
  const shippingNameInput    = document.getElementById("shippingNameInput");
  const shippingPhoneInput   = document.getElementById("shippingPhoneInput");
  const shippingAddressInput = document.getElementById("shippingAddressInput");
  const shippingEmailInput   = document.getElementById("shippingEmailInput");

  const shippingName = shippingNameInput ? shippingNameInput.value.trim() : "";
  const shippingPhone = shippingPhoneInput ? shippingPhoneInput.value.trim() : "";
  const shippingAddress = shippingAddressInput
    ? shippingAddressInput.value.trim()
    : "";
  const shippingEmail = shippingEmailInput
    ? shippingEmailInput.value.trim()
    : "";

  if (!shippingEmail) {
    alert("Please provide an email address for the payment receipt.");
    return;
  }

  try {
    // 1) Create Payment Intent via your MVC controller (already working)
    const response = await fetch("index.php?api=paymongo", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ amount: totalAmount }),
    });

    const data = await response.json();
    console.log("PI created:", data);

    if (!data.client_key) {
      alert("Failed to create payment. Please try again.");
      return;
    }

    const clientKey = data.client_key;

    // 2) Create payment method with card details + billing
    const billing = {
      name: shippingName || undefined,
      email: shippingEmail || undefined,
      phone: shippingPhone || undefined,
    };
    const paymentMethodId = await createPaymongoPaymentMethod(card, billing);
    console.log("PaymentMethod created:", paymentMethodId);

    // 3) Attach payment intent
    const attachResult = await attachPaymongoPaymentIntent(
      clientKey,
      paymentMethodId
    );
    console.log("Attach result:", attachResult);

    const status = attachResult.data.attributes.status;
    const nextAction = attachResult.data.attributes.next_action;

    if (status === "awaiting_next_action" && nextAction?.redirect?.url) {
      window.location.href = nextAction.redirect.url;
      return;
    }

    if (status === "succeeded") {
      // 4) Only now create local order in your DB
      const orderResult = await createLocalOrder(
        totalAmount,
        shippingName,
        shippingPhone,
        shippingAddress
      );

      if (orderResult.success) {
        showToast("Order placed successfully!");
        if (typeof closeModals === "function") closeModals();
        await refreshCartCount();
        await loadCartFromServer();
      }
    } else if (status === "processing") {
      alert("Payment is processing. We'll confirm shortly.");
    } else {
      alert("Payment did not succeed. Status: " + status);
    }
  } catch (error) {
    console.error("PayMongo error:", error);
    alert("Error processing payment: " + error.message);
  }
}

// ---------- DOM + modal wiring ----------
document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("cartOverlay");
  const cartModal = document.getElementById("cartModal");
  const checkoutModal = document.getElementById("checkoutModal");
  const variantModal = document.getElementById("variantModal");

  // ---------- Checkout summary builder (no Max: X here) ----------
  function buildCheckoutSummaryFromSelection() {
    const list = document.getElementById("checkoutItemsList");
    if (!list) return;

    list.innerHTML = "";

    const cartItems = document.querySelectorAll(".cart-item");
    let totalQty = 0;
    let totalAmount = 0;

    cartItems.forEach((item) => {
      const checkbox = item.querySelector(".item-select");
      if (!checkbox || !checkbox.checked) return;

      const nameEl = item.querySelector(".item-name");
      const metaEl = item.querySelector(".item-meta");
      const priceEl = item.querySelector(".item-price");
      const qtyEls = item.querySelectorAll(".quantity-value");

      const name = nameEl ? nameEl.textContent.trim() : "";
      const metaHtml = metaEl ? metaEl.innerHTML : "";

      const priceText = priceEl
        ? priceEl.textContent.replace("₱", "").trim()
        : "0";

      // there are 2 .quantity-value spans, take the last one (inside controls)
      const qtyEl = qtyEls[qtyEls.length - 1];
      const quantity = qtyEl ? parseInt(qtyEl.textContent, 10) || 0 : 0;

      const price = parseFloat(priceText) || 0;
      const lineTotal = price * quantity;

      totalQty += quantity;
      totalAmount += lineTotal;

      const row = document.createElement("div");
      const cleanMetaHtml = metaHtml.replace(/\(Max:\s*\d+\)/g, "").trim();

      row.className = "checkout-item-row";
      row.innerHTML = `
        <div class="checkout-item-left">
          <div class="checkout-item-name">${name}</div>
          <div class="checkout-item-meta">${cleanMetaHtml}</div>
        </div>
        <div class="checkout-item-right">
          <div class="checkout-item-qty">x${quantity}</div>
          <div class="checkout-item-line">₱${lineTotal.toFixed(2)}</div>
        </div>
      `;

      list.appendChild(row);
    });

    const checkoutItemCountEl = document.getElementById("checkoutItemCount");
    const cartTotalEl = document.getElementById("cartTotal");

    if (checkoutItemCountEl) checkoutItemCountEl.textContent = totalQty;
    if (cartTotalEl) cartTotalEl.textContent = `₱${totalAmount.toFixed(2)}`;
  }

  // --- Cart summary (selected items) ---
  function updateCartSummary() {
    const items = document.querySelectorAll(".cart-item");
    let total = 0;
    let count = 0;

    items.forEach((item) => {
      const checkbox = item.querySelector(".item-select");
      if (checkbox && checkbox.checked) {
        const priceEl = item.querySelector(".item-price");
        const qtyEl = item.querySelector(".quantity-value");
        if (!priceEl || !qtyEl) return;

        const priceText = priceEl.textContent.replace("₱", "").trim();
        const quantity = parseInt(qtyEl.textContent, 10) || 0;

        total += parseFloat(priceText) * quantity;
        count += quantity;
      }
    });

    const selectedCountEl = document.getElementById("selectedCount");
    const selectedTotalEl = document.getElementById("selectedTotal");
    const checkoutItemCountEl = document.getElementById("checkoutItemCount");

    if (selectedCountEl) selectedCountEl.textContent = count;
    if (selectedTotalEl) selectedTotalEl.textContent = `₱${total.toFixed(2)}`;
    if (checkoutItemCountEl) checkoutItemCountEl.textContent = count;
  }

  // Checkbox change -> update summary
  document.addEventListener("change", (e) => {
    if (e.target.classList.contains("item-select")) {
      updateCartSummary();
    }
  });

  // --- Modal open/close helpers ---
  function openOverlay() {
    if (!overlay) return;
    overlay.classList.add("show");
    document.body.style.overflow = "hidden";
  }

  function closeOverlay() {
    if (!overlay) return;
    overlay.classList.remove("show");
    document.body.style.overflow = "";
  }

  async function openCart() {
    await loadCartFromServer();
    openOverlay();
    if (cartModal) cartModal.classList.add("show");
    if (checkoutModal) checkoutModal.classList.remove("show");
    if (variantModal) variantModal.classList.remove("show");
  }

  function openCheckout() {
    openOverlay();
    if (checkoutModal) checkoutModal.classList.add("show");
    if (cartModal) cartModal.classList.remove("show");
    if (variantModal) variantModal.classList.remove("show");
  }

  function closeModals() {
    closeOverlay();
    if (cartModal) cartModal.classList.remove("show");
    if (checkoutModal) checkoutModal.classList.remove("show");
    if (variantModal) variantModal.classList.remove("show");
  }

  function proceedToCheckout() {
    // Build list of selected items + totals
    buildCheckoutSummaryFromSelection();

    // Switch modals
    if (cartModal) cartModal.classList.remove("show");
    if (checkoutModal) checkoutModal.classList.add("show");
    openOverlay();
  }

  // ESC & overlay click close
  if (overlay) overlay.addEventListener("click", closeModals);
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModals();
  });

  // ---------- Variant modal wiring ----------
  const variantNameEl = document.getElementById("variantProductName");
  const variantNameTextEl = document.getElementById("variantProductNameText");
  const variantImgEl = document.getElementById("variantProductImage");
  const variantPriceEl = document.getElementById("variantProductPrice");
  const colorSelect = document.getElementById("variantColorSelect");
  const sizeSelect = document.getElementById("variantSizeSelect");
  const stockEl = document.getElementById("variantStock");

  let currentVariants = [];
  let currentProductId = null;
  let currentBasePrice = 0;

  function rebuildSizeOptions() {
    if (!colorSelect || !sizeSelect || !stockEl) return;
    const colorId = colorSelect.value;
    const variantsForColor = currentVariants.filter(
      (v) => String(v.color_id) === String(colorId)
    );

    const uniqueSizes = [...new Set(variantsForColor.map((v) => v.size))];
    sizeSelect.innerHTML = uniqueSizes
      .map((s) => `<option value="${s}">${s}</option>`)
      .join("");

    if (uniqueSizes.length) {
      sizeSelect.value = uniqueSizes[0];
      updateStockDisplay();
    } else {
      stockEl.textContent = "0";
    }
  }

  function updateStockDisplay() {
    if (!colorSelect || !sizeSelect || !stockEl) return;
    const colorId = colorSelect.value;
    const size = sizeSelect.value;

    const variant = currentVariants.find(
      (v) => String(v.color_id) === String(colorId) && v.size === size
    );

    stockEl.textContent = variant ? variant.quantity : "0";
  }

  if (colorSelect) {
    colorSelect.addEventListener("change", rebuildSizeOptions);
  }

  if (sizeSelect) {
    sizeSelect.addEventListener("change", updateStockDisplay);
  }

  async function openVariantModalInternal(productId) {
    try {
      const resp = await fetch(
        `index.php?page=get_product_variants&PRODUCT_ID=${encodeURIComponent(
          productId
        )}`,
        { credentials: "include" }
      );
      const data = await resp.json();
      if (!data.success) {
        alert(data.message || "Unable to load options.");
        return;
      }

      currentProductId = data.product.id;
      currentBasePrice = Number(data.product.price) || 0;
      currentVariants = data.variants || [];

      if (variantNameEl) variantNameEl.textContent = data.product.name;
      if (variantNameTextEl)
        variantNameTextEl.textContent = data.product.name;
      if (variantPriceEl)
        variantPriceEl.textContent = `₱${currentBasePrice.toFixed(2)}`;
      if (variantImgEl) {
        variantImgEl.src = data.product.image;
        variantImgEl.alt = data.product.name;
      }

      // Build color options
      if (colorSelect) {
        const colorMap = {};
        const colors = [];
        currentVariants.forEach((v) => {
          if (!colorMap[v.color_id]) {
            colorMap[v.color_id] = v.color_name;
            colors.push({ id: v.color_id, name: v.color_name });
          }
        });

        colorSelect.innerHTML = colors
          .map((c) => `<option value="${c.id}">${c.name}</option>`)
          .join("");

        if (colors.length) colorSelect.value = colors[0].id;
      }

      rebuildSizeOptions();

      openOverlay();
      if (variantModal) variantModal.classList.add("show");
      if (cartModal) cartModal.classList.remove("show");
      if (checkoutModal) checkoutModal.classList.remove("show");
    } catch (err) {
      console.error(err);
      alert("Error loading product options.");
    }
  }

  function confirmVariantSelectionInternal() {
    if (!currentProductId || !currentVariants.length) {
      alert("No variant selected.");
      return;
    }
    const colorId = colorSelect ? colorSelect.value : null;
    const size = sizeSelect ? sizeSelect.value : null;

    const variant = currentVariants.find(
      (v) => String(v.color_id) === String(colorId) && v.size === size
    );

    if (!variant) {
      alert("This color/size combination is not available.");
      return;
    }
    if (variant.quantity <= 0) {
      alert("This variant is out of stock.");
      return;
    }

    sendAddToCart(currentProductId, variant.inventory_id, currentBasePrice);

    if (variantModal) variantModal.classList.remove("show");
    closeOverlay();
  }

  // ----- Contact & Shipping Edit Toggle -----
const editBtn    = document.getElementById("addressEditBtn");
const nameInput  = document.getElementById("shippingNameInput");
const emailInput = document.getElementById("shippingEmailInput");
const phoneInput = document.getElementById("shippingPhoneInput");
const addrInput  = document.getElementById("shippingAddressInput");

const editableFields = [nameInput, emailInput, phoneInput, addrInput].filter(Boolean);

if (editBtn && editableFields.length) {
  editableFields.forEach((el) => (el.readOnly = true));

  editBtn.addEventListener("click", () => {
    const willBecomeEditable = editableFields[0].readOnly;

    editableFields.forEach((el) => {
      el.readOnly = !willBecomeEditable;
    });

    editBtn.classList.toggle("active", willBecomeEditable);
    editBtn.textContent = willBecomeEditable
      ? "Save for this order"
      : "Edit";
  });
}


  // --- Quantity + / - and remove buttons (event delegation) ---
  document.addEventListener("click", (e) => {
    // + button
    if (e.target.classList.contains("qty-plus")) {
      const btn = e.target;
      const cartItemId = btn.dataset.id;
      const stock = btn.dataset.stock ? parseInt(btn.dataset.stock, 10) : null;
      const row = btn.closest(".cart-item");
      if (!row) return;

      const qtySpan = row.querySelector(".quantity-value");
      const currentQty = parseInt(qtySpan.textContent, 10) || 0;

      if (stock && currentQty >= stock) {
        showToast("Maximum stock reached");
        return;
      }

      const newQty = currentQty + 1;
      updateCartItemQuantity(cartItemId, newQty);
    }

    // - button
    if (e.target.classList.contains("qty-minus")) {
      const btn = e.target;
      const cartItemId = btn.dataset.id;
      const row = btn.closest(".cart-item");
      if (!row) return;

      const qtySpan = row.querySelector(".quantity-value");
      const currentQty = parseInt(qtySpan.textContent, 10) || 0;

      if (currentQty <= 1) {
        deleteCartItem(cartItemId);
      } else {
        const newQty = currentQty - 1;
        updateCartItemQuantity(cartItemId, newQty);
      }
    }

    // delete button (trash icon)
    const removeBtn = e.target.closest(".remove-btn");
    if (removeBtn) {
      const cartItemId = removeBtn.dataset.id;
      deleteCartItem(cartItemId);
    }
  });

  // Expose globals for HTML onclick=""
  window.openCart = openCart;
  window.openCheckout = openCheckout;
  window.closeModals = closeModals;
  window.proceedToCheckout = proceedToCheckout;
  window._updateCartSummary = updateCartSummary;
  window.openVariantModal = openVariantModalInternal;
  window.confirmVariantSelection = confirmVariantSelectionInternal;
  window.placeOrder = placeOrder; // used in onclick="placeOrder()"

  console.log("cartModal.js initialized");
  refreshCartCount(); // load badge from DB on page load
});
