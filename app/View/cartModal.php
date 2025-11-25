<!-- app/View/cartModal.php -->

<!-- CART OVERLAY -->
<div id="cartOverlay" class="cart-overlay"></div>

<!-- CART DRAWER -->
<div id="cartModal" class="cart-modal">
  <div class="cart-modal-header">
    <h2 class="cart-modal-title">Your Cart</h2>
    <button type="button" class="cart-close-btn" onclick="closeModals()">&times;</button>
  </div>

  <div class="cart-modal-content">
    <!-- Cart items rendered by JS -->
    <div id="cartItemsContainer" class="cart-items">
      <!-- JS will render items here -->
    </div>

    <!-- Summary -->
    <div class="summary-section">
      <div class="summary-row">
        <span>Selected Items</span>
        <span id="selectedCount">0</span>
      </div>
      <div class="summary-total">
        <span class="total-label">Total</span>
        <span class="total-value" id="selectedTotal">₱0.00</span>
      </div>
      <button type="button" class="action-btn" onclick="proceedToCheckout()">
        Proceed to Checkout
      </button>
    </div>
  </div>
</div>


<!-- CHECKOUT DRAWER -->
<div id="checkoutModal" class="cart-modal">
  <div class="cart-modal-header">
    <h2 class="cart-modal-title">Checkout</h2>
    <button type="button" class="cart-close-btn" onclick="closeModals()">&times;</button>
  </div>

  <div class="cart-modal-content">
   <?php
$shipName = '';
$shipAddress = '';
$shipPhone = '';

if (isset($user) && is_array($user)) {
    $firstName = $user['FIRST_NAME'] ?? '';
    $lastName  = $user['LAST_NAME'] ?? '';
    $shipName  = trim("$firstName $lastName");

    // Prefer SHIPPING_ADDRESS if set, otherwise build from components
    if (!empty($user['SHIPPING_ADDRESS'])) {
        $shipAddress = $user['SHIPPING_ADDRESS'];
    } else {
        $parts = [
            $user['STREET_ADDRESS'] ?? '',
            $user['APARTMENT']      ?? '',
            $user['BARANGAY']       ?? '',
            $user['CITY']           ?? '',
            $user['PROVINCE']       ?? '',
            $user['POSTAL_CODE']    ?? '',
        ];
        $shipAddress = trim(implode(', ', array_filter($parts)));
    }

    $shipPhone = $user['CONTACTS'] ?? '';
}
?>

<section class="section-divider">
  <div class="section-header">
    <div class="section-header-left">
      <div class="section-icon"><i class="bi bi-geo-alt"></i></div>
      <h3 class="section-title">Contact & Shipping Details</h3>
    </div>
    <button type="button" class="address-edit-btn" id="addressEditBtn">Edit</button>
  </div>

  <div class="address-display">
    <div class="address-row">
      <span class="address-label">Name</span>
      <input
        type="text"
        id="shippingNameInput"
        class="address-input"
        value="<?= htmlspecialchars($shipName ?: 'Your name') ?>"
        readonly
      />
    </div>

    <div class="address-row">
      <span class="address-label">Email</span>
      <input
        type="email"
        id="shippingEmailInput"
        class="address-input"
        value="<?= htmlspecialchars($user['EMAIL'] ?? '') ?>"
        readonly
      />
    </div>

    <div class="address-row">
      <span class="address-label">Contact</span>
      <input
        type="text"
        id="shippingPhoneInput"
        class="address-input"
        value="<?= htmlspecialchars($shipPhone ?: '') ?>"
        readonly
      />
    </div>

    <div class="address-row">
      <span class="address-label">Address</span>
      <textarea
        id="shippingAddressInput"
        class="address-input"
        readonly
      ><?= htmlspecialchars($shipAddress ?? '') ?></textarea>
    </div>
  </div>
</section>


    <!-- Order items summary -->
<section class="section-divider">
  <div class="section-header">
    <div class="section-header-left">
      <div class="section-icon"><i class="bi bi-bag"></i></div>
      <h3 class="section-title">Order Summary</h3>
    </div>
  </div>

  <!-- Line items go here -->
  <div id="checkoutItemsList" class="checkout-items-list">
    <!-- JS will render selected items here -->
  </div>

  <div class="order-summary">
    <div class="summary-row">
      <span>Items</span>
      <span id="checkoutItemCount">0</span>
    </div>
    <div class="summary-total">
      <span class="total-label">Total</span>
      <span class="total-value" id="cartTotal">₱0.00</span>
    </div>
  </div>
</section>


    <!-- Payment methods + card form -->
    <!-- Payment methods + card form -->
<section>
  <div class="section-header">
    <div class="section-header-left">
      <div class="section-icon"><i class="bi bi-credit-card"></i></div>
      <h3 class="section-title">Payment Method</h3>
    </div>
  </div>

  <div class="payment-methods">
    <label class="payment-option">
      <input type="radio" name="payment" value="card" checked />
      <div class="payment-option-content">
        <i class="bi bi-credit-card-2-front"></i>
        Credit / Debit Card
      </div>
    </label>
  </div>

  <!-- Simple card form -->
  <div class="card-form" style="margin-top:1.25rem;">
    <div class="form-group">
      <label>Card Number</label>
      <input type="text" id="card-number" placeholder="4242 4242 4242 4242" />
    </div>
    <div style="display:flex; gap:0.75rem; margin-top:0.75rem;">
      <div class="form-group" style="flex:1;">
        <label>Exp. Month</label>
        <input type="text" id="card-exp-month" placeholder="12" />
      </div>
      <div class="form-group" style="flex:1;">
        <label>Exp. Year</label>
        <input type="text" id="card-exp-year" placeholder="2030" />
      </div>
      <div class="form-group" style="flex:1;">
        <label>CVC</label>
        <input type="text" id="card-cvc" placeholder="123" />
      </div>
    </div>
  </div>

  <button
    type="button"
    class="action-btn"
    style="margin-top:1.5rem;"
    onclick="placeOrder()"
  >
    Place Order
  </button>
</section>

  </div>
</div>

<!-- VARIANT / OPTIONS MODAL -->
<div id="variantModal" class="cart-modal">
  <div class="cart-modal-header">
    <h2 class="cart-modal-title" id="variantProductName">Select Options</h2>
    <button type="button" class="cart-close-btn" onclick="closeModals()">&times;</button>
  </div>

  <div class="cart-modal-content">
    <!-- Product info -->
    <section class="section-divider">
      <div class="section-header">
        <div class="section-header-left">
          <div class="section-icon"><i class="bi bi-bag"></i></div>
          <h3 class="section-title">Product Details</h3>
        </div>
      </div>

      <div class="variant-product-info">
        <img
          id="variantProductImage"
          src=""
          alt=""
          style="width:80px; height:80px; object-fit:cover; border-radius:8px;"
        >
        <div>
          <p class="variant-name" id="variantProductNameText"></p>
          <p class="variant-price" id="variantProductPrice" style="font-weight:600;"></p>
        </div>
      </div>
    </section>

    <!-- Options -->
    <section class="section-divider">
      <div class="section-header">
        <div class="section-header-left">
          <div class="section-icon"><i class="bi bi-sliders"></i></div>
          <h3 class="section-title">Choose Color & Size</h3>
        </div>
      </div>

      <div class="variant-options">
        <div class="form-group">
          <label for="variantColorSelect">Color</label>
          <select id="variantColorSelect"></select>
        </div>

        <div class="form-group">
          <label for="variantSizeSelect">Size</label>
          <select id="variantSizeSelect"></select>
        </div>

        <div class="stock-info">
          Available stock: <strong id="variantStock">0</strong>
        </div>
      </div>
    </section>

    <!-- Actions -->
    <section>
      <button
        type="button"
        class="action-btn"
        style="margin-top:1rem;"
        onclick="confirmVariantSelection()"
      >
        Add to cart
      </button>
    </section>
  </div>
</div>

<!-- TOAST NOTIFICATION (overlay-level, not inside any modal) -->
<div id="toast" class="toast hidden">
  <div class="toast-icon"><i class="bi bi-check2-circle"></i></div>
  <span id="toastMessage"></span>
</div>
