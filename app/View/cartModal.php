<!-- app/View/cartModal.php -->

<!-- CART OVERLAY -->
<div id="cartOverlay" class="cart-overlay"></div>

<!-- CART DRAWER -->
<div id="cartModal" class="cart-modal">
  <div class="cart-modal-header">
    <h2 class="cart-modal-title">Cart</h2>
    <button type="button" class="cart-close-btn" onclick="closeModals()">&times;</button>
  </div>

  <div class="cart-modal-content">
    <!-- Cart items rendered by JS -->
    <div id="cartItemsContainer" class="cart-items">
      <!-- JS will render items here -->
    </div>

    <!-- Summary -->
    <div class="summary-section">
      <!-- Hidden summary row for JS logic, visually removed per design -->
      <div class="summary-row" style="display:none;">
        <span>Selected Items</span>
        <span id="selectedCount">0</span>
      </div>
      
      <div class="summary-total">
        <span class="total-label">Total</span>
        <span class="total-value" id="selectedTotal">₱0.00</span>
      </div>
      <button type="button" class="action-btn" onclick="proceedToCheckout()">
        Checkout
      </button>
    </div>
  </div>
</div>


<!-- CHECKOUT DRAWER -->
<div id="checkoutModal" class="cart-modal">
  <div class="cart-modal-header">
    <h2 class="cart-modal-title" style="font-family: var(--font-family); font-size:1.4rem;">Checkout</h2>
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
    <h3 class="section-title">Shipping Details</h3>
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
    <h3 class="section-title">Order Summary</h3>
  </div>

  <div id="checkoutItemsList" class="checkout-items-list" style="background:#f9f9f9; padding:1rem; border-radius:12px;">
    <!-- JS will render selected items here -->
  </div>

  <div class="order-summary" style="margin-top:1rem;">
    <div class="summary-total">
      <span class="total-label">Total Payment</span>
      <span class="total-value" id="cartTotal">₱0.00</span>
    </div>
  </div>
</section>

<!-- Payment methods + card form -->
<section>
  <div class="section-header">
    <h3 class="section-title">Payment</h3>
  </div>

  <div class="payment-methods">
    <label class="payment-option" style="padding:1rem; border:1px solid #eee; border-radius:12px; display:flex; align-items:center;">
      <input type="radio" name="payment" value="card" checked style="accent-color:var(--color-btn-checkout); margin-right:1rem;">
      <div class="payment-option-content" style="font-weight:600; font-size:0.95rem;">
        Credit / Debit Card
      </div>
    </label>
  </div>

  <div class="card-form" style="background:#f9f9f9; padding:1.25rem; border-radius:12px; margin-top:1rem;">
    <div class="form-group" style="margin-bottom:1rem;">
      <label style="display:block; font-size:0.8rem; margin-bottom:0.4rem; color:#666; font-weight:600;">Card Number</label>
      <input type="text" id="card-number" placeholder="0000 0000 0000 0000" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px; outline:none;" />
    </div>
    <div style="display:flex; gap:0.75rem;">
      <div class="form-group" style="flex:1;">
        <label style="display:block; font-size:0.8rem; margin-bottom:0.4rem; color:#666; font-weight:600;">Exp. Month</label>
        <input type="text" id="card-exp-month" placeholder="MM" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px; outline:none;" />
      </div>
      <div class="form-group" style="flex:1;">
        <label style="display:block; font-size:0.8rem; margin-bottom:0.4rem; color:#666; font-weight:600;">Exp. Year</label>
        <input type="text" id="card-exp-year" placeholder="YYYY" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px; outline:none;" />
      </div>
      <div class="form-group" style="flex:1;">
        <label style="display:block; font-size:0.8rem; margin-bottom:0.4rem; color:#666; font-weight:600;">CVC</label>
        <input type="text" id="card-cvc" placeholder="123" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px; outline:none;" />
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
    <section class="section-divider">
      <div class="variant-product-info">
        <img
          id="variantProductImage"
          src=""
          alt=""
          style="width:80px; height:80px; object-fit:cover; border-radius:8px;"
        >
        <div>
          <p class="variant-name" id="variantProductNameText" style="font-weight:700; color:#333; margin:0;"></p>
          <p class="variant-price" id="variantProductPrice" style="font-weight:600; color:#c68a4c; margin-top:0.25rem;"></p>
        </div>
      </div>
    </section>

    <section class="section-divider">
      <div class="variant-options">
        <div class="form-group" style="margin-bottom:1rem;">
          <label for="variantColorSelect">Color</label>
          <select id="variantColorSelect"></select>
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
          <label for="variantSizeSelect">Size</label>
          <select id="variantSizeSelect"></select>
        </div>

        <div class="stock-info" style="text-align:center; padding:0.8rem; background:#f9f9f7; border-radius:8px; font-size:0.9rem; color:#666;">
          Available stock: <strong id="variantStock" style="color:#333;">0</strong>
        </div>
      </div>
    </section>

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

<!-- TOAST (Outside) -->
<div id="toast" class="toast hidden">
  <span id="toastMessage"></span>
<<<<<<< HEAD
</div>
<!-- Payment Loading Overlay -->
<div id="paymentLoadingOverlay" class="payment-loading-overlay hidden">
  <div class="payment-loading-box">
    <div class="payment-spinner"></div>
    <p class="payment-loading-message">Processing your payment...</p>
    <small>Please don’t close or refresh this page.</small>
  </div>
</div>


=======
</div>
>>>>>>> feat/item_reco
