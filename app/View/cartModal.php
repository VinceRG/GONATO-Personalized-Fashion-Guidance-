<!-- cart-modal.php -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="public/css/cartModal.css">

<!-- Overlay -->
<div class="overlay" id="overlay" onclick="closeModals()"></div>

<!-- Shopping Cart Modal -->
<div class="modal" id="cartModal">
  <div class="modal-header">
    <h2 class="modal-title"><i>Shopping Cart</i></h2>
    <button class="close-btn" onclick="closeModals()">×</button>
  </div>

  <div class="modal-content">
    <div class="cart-items">
      <!-- Sample Cart Items -->
      <div class="cart-item">
          <input type="checkbox" class="item-select">
        <div class="item-image">
          <img src="https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400" alt="Product">
        </div>
        <div class="item-details">
          <div>
            <div class="item-name">Classic Cotton T-Shirt</div>
            <div class="item-meta">
              <div style="display: flex; align-items: center; gap: 0.25rem;">
                <div class="color-swatch" style="background: #3b82f6;"></div>
                <span>Blue</span>
              </div>
              <span>Size M</span>
            </div>
          </div>
          <div class="item-actions">
            
            <div class="quantity-control">
              <button class="quantity-btn">−</button>
              <span class="quantity-value">2</span>
              <button class="quantity-btn">+</button>
            </div>
            <div class="item-price">$59.98</div>
            <button class="remove-btn"><i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>

      <!-- Additional Cart Item -->
      <div class="cart-item">
                  <input type="checkbox" class="item-select">

        <div class="item-image">
          <img src="https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400" alt="Product">
        </div>
        <div class="item-details">
          <div>
            <div class="item-name">Denim Jeans</div>
            <div class="item-meta">
              <div style="display: flex; align-items: center; gap: 0.25rem;">
                <div class="color-swatch" style="background: #1e3a8a;"></div>
                <span>Dark Blue</span>
              </div>
              <span>Size 32</span>
            </div>
          </div>
          <div class="item-actions">
            <div class="quantity-control">
              <button class="quantity-btn">−</button>
              <span class="quantity-value">1</span>
              <button class="quantity-btn">+</button>
            </div>
            <div class="item-price">$79.99</div>
            <button class="remove-btn"><i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="summary-section">
  <div class="summary-total">
    <span class="total-label">Selected Items:</span>
    <span id="selectedCount">0</span>
  </div>
  <div class="summary-total">
    <span class="total-label">Total Price:</span>
    <span id="selectedTotal">$0.00</span>
  </div>

  <button class="action-btn" onclick="proceedToCheckout()">
    <i class="bi bi-bag-check"></i> Proceed to Checkout
  </button>
  <button class="action-btn secondary-btn" onclick="closeModals()">
    <i class="bi bi-arrow-left"></i> Continue Shopping
  </button>
</div>

  </div>
</div>

<!-- Checkout Modal -->
<div class="modal" id="checkoutModal">
  <div class="modal-header">
    <h2 class="modal-title"><i>Checkout</i></h2>
    <button class="close-btn" onclick="closeModals()">×</button>
  </div>

  <div class="modal-content">
    <!-- Shipping Address Section -->
    <div class="section-divider">
      <div class="section-header">
        <div class="section-header-left">
          <div class="section-icon">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <h3 class="section-title">Shipping Address</h3>
        </div>
      </div>
      <div class="address-display">
        <div class="address-line">
          <i class="bi bi-person-fill"></i>
          <div><strong>John Doe</strong></div>
        </div>
        <div class="address-line">
          <i class="bi bi-house-fill"></i>
          <div>123 Main Street, Taguig City</div>
        </div>
        <div class="address-line">
          <i class="bi bi-telephone-fill"></i>
          <div>+63 912 345 6789</div>
        </div>
      </div>
    </div>

    <!-- Order Items Section -->
    <div class="section-divider">
      <div class="section-header">
        <div class="section-header-left">
          <div class="section-icon">
            <i class="bi bi-bag-fill"></i>
          </div>
          <h3 class="section-title">Order Items</h3>
        </div>
      </div>
      <div class="checkout-items">
        <div class="checkout-item">
          <img src="https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400" alt="Product">
          <div class="checkout-item-details">
            <div class="checkout-item-name">Classic Cotton T-Shirt</div>
            <div class="checkout-item-meta">Blue • Size M • Qty: 2</div>
          </div>
          <div class="checkout-item-price">$59.98</div>
        </div>
        <div class="checkout-item">
          <img src="https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400" alt="Product">
          <div class="checkout-item-details">
            <div class="checkout-item-name">Denim Jeans</div>
            <div class="checkout-item-meta">Dark Blue • Size 32 • Qty: 1</div>
          </div>
          <div class="checkout-item-price">$79.99</div>
        </div>
      </div>
    </div>

    <!-- Payment Method Section -->
<div class="section-divider">
  <div class="section-header">
    <div class="section-header-left">
      <div class="section-icon">
        <i class="bi bi-credit-card-fill"></i>
      </div>
      <h3 class="section-title">Payment Method</h3>
    </div>
  </div>
  <div class="payment-methods">
    <label class="payment-option">
      <input type="radio" name="payment" value="card" checked>
      <div class="payment-option-content">
        <i class="bi bi-credit-card"></i>
        <span>Credit/Debit Card</span>
      </div>
    </label>
    <label class="payment-option">
      <input type="radio" name="payment" value="gcash">
      <div class="payment-option-content">
        <i class="bi bi-wallet2"></i>
        <span>GCash</span>
      </div>
    </label>
  </div>
    <!-- Card Input Fields -->
  <div id="card-fields" style="margin-top: 1rem;">
    <input type="text" id="card-number" placeholder="Card Number" maxlength="19" />
    <input type="text" id="card-exp-month" placeholder="MM" maxlength="2" style="width: 50px;" />
    <input type="text" id="card-exp-year" placeholder="YYYY" maxlength="4" style="width: 70px;" />
    <input type="text" id="card-cvc" placeholder="CVC" maxlength="4" style="width: 50px;" />
  </div>
</div>


    <!-- Order Summary -->
    <div class="order-summary">
      <div class="summary-row">
        <span>Subtotal</span>
        <span>$139.97</span>
      </div>
      <div class="summary-row">
        <span>Shipping</span>
        <span>$5.00</span>
      </div>
      <div class="summary-total">
        <span class="total-label">Total</span>
        <span class="total-value">$144.97</span>
      </div>
    </div>

    <button class="action-btn" onclick="placeOrder()">
      <i class="bi bi-check-circle-fill"></i> Place Order
    </button>
  </div>
</div>

<script src="public/js/cartModal.js"></script>
<script src="https://js.paymongo.com/v1/paymongo.js"></script>
