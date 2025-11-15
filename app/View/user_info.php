<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Profile - Amarelle</title>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="public/css/user_info.css">
  <script src="public/js/user_info.js"></script>
</head>

<body>
  <div class="overlay">
    <div class="modal" role="dialog" aria-label="User Profile">
      <div class="modal-header">
        <button class="header-back" id="headerBackBtn" aria-label="Back">
          <i class="bi bi-arrow-left"></i> Back
        </button>

        <button class="close-btn" aria-label="Close">
          <i class="bi bi-x"></i>
        </button>
      </div>

    </div>

    <div class="modal-body">
      <!-- Account Tab -->
      <div class="view-container active" id="accountView">
        <div class="profile-top">
          <div class="profile-left-column">
            <div class="profile-section">
              <div class="profile-avatar">
                <span class="initials">GV</span>
                <button class="edit-avatar-btn" title="Change Profile Picture">
                  <i class="bi bi-camera"></i>
                </button>
              </div>
            </div>

            <div class="profile-meta-below">
              <h2 class="profile-name">Gabrielle Villamor</h2>
              <div class="username">@rielleir24</div>
            </div>
          </div>

          <div class="section-header">
            <button class="view-orders-btn" id="viewOrdersBtn">
              <i class="bi bi-box-seam"></i> View orders
            </button>
          </div>
        </div>
      </div>

      <form id="profileForm">
        <div class="form-grid">
          <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" id="name" value="Sophia" disabled>
          </div>

          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" value="Dela Cruz" disabled>
          </div>

          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" value="@sophiadlcrz" disabled>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" value="sophia.martinez@email.com" disabled>
          </div>
          <div class="form-group full-width">
            <label for="homeAddress">Home Address</label>
            <input type="text" id="homeAddress" value="123 Main St, Pasig City" disabled>
          </div>
          <div class="form-group full-width">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" value="+63 912 345 6789" disabled>
          </div>
        </div>

        <div class="info-card">
          <div class="info-card-header">
            <i class="bi bi-palette"></i>
            <h3>Style Profile</h3>
          </div>
          <div class="info-item">
            <span class="info-label">Color Season</span>
            <span class="info-value">Autumn</span>
          </div>
          <div class="info-item">
            <span class="info-label">Body Shape</span>
            <span class="info-value">Hourglass</span>
          </div>
        </div>
      </form>

      <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
        <button type="button" class="edit-profile-btn" id="editProfileBtn">
          <i class="bi bi-pencil-square"></i> Edit Profile
        </button>
      </div>

    </div>

    <!-- Orders View -->
    <div class="view-container" id="ordersView">

      <div class="sub-tabs">
        <button class="sub-tab-btn active" data-subtab="orders">
          <i class="bi bi-box-seam"></i> Orders
        </button>
        <button class="sub-tab-btn" data-subtab="to-receive">
          <i class="bi bi-truck"></i> To Receive
        </button>
        <button class="sub-tab-btn" data-subtab="history">
          <i class="bi bi-clock-history"></i> Order History
        </button>
      </div>

      <!-- Orders Sub Tab -->
      <div class="sub-tab-content active" id="orders">
        <div class="order-card">
          <div class="order-header">
            <span class="order-number">#001234</span>
            <span class="order-status status-processing">Processing</span>
          </div>
          <div class="order-body">
            <div class="order-item">
              <span class="item-name">Elegant Silk Blouse</span>
              <span class="item-price">₱2,500</span>
            </div>
            <div class="order-item">
              <span class="item-name">Classic Denim Jeans</span>
              <span class="item-price">₱1,800</span>
            </div>
          </div>
          <div class="order-footer">
            <span class="order-date">Ordered: Oct 20, 2025</span>
            <span class="order-total">Total: ₱4,300</span>
          </div>
        </div>

        <div class="order-card">
          <div class="order-header">
            <span class="order-number">#001235</span>
            <span class="order-status status-processing">Processing</span>
          </div>
          <div class="order-body">
            <div class="order-item">
              <span class="item-name">Summer Dress</span>
              <span class="item-price">₱3,200</span>
            </div>
          </div>
          <div class="order-footer">
            <span class="order-date">Ordered: Oct 22, 2025</span>
            <span class="order-total">Total: ₱3,200</span>
          </div>
        </div>
      </div>

      <!-- To Receive Sub Tab -->
      <div class="sub-tab-content" id="to-receive">
        <div class="order-card">
          <div class="order-header">
            <span class="order-number">#001230</span>
            <span class="order-status status-shipping">Shipping</span>
          </div>
          <div class="order-body">
            <div class="order-item">
              <span class="item-name">Leather Handbag</span>
              <span class="item-price">₱4,500</span>
            </div>
          </div>
          <div class="order-footer">
            <span class="order-date">Est. Arrival: Oct 26, 2025</span>
            <span class="order-total">Total: ₱4,500</span>
          </div>
        </div>

        <div class="order-card">
          <div class="order-header">
            <span class="order-number">#001228</span>
            <span class="order-status status-shipping">Shipping</span>
          </div>
          <div class="order-body">
            <div class="order-item">
              <span class="item-name">Casual Sneakers</span>
              <span class="item-price">₱2,800</span>
            </div>
            <div class="order-item">
              <span class="item-name">Cotton T-Shirt</span>
              <span class="item-price">₱890</span>
            </div>
          </div>
          <div class="order-footer">
            <span class="order-date">Est. Arrival: Oct 28, 2025</span>
            <span class="order-total">Total: ₱3,690</span>
          </div>
        </div>
      </div>

      <!-- Order History Sub Tab -->
      <div class="sub-tab-content" id="history">
        <div class="order-card">
          <div class="order-header">
            <span class="order-number">#001220</span>
            <span class="order-status status-completed">Completed</span>
          </div>
          <div class="order-body">
            <div class="order-item">
              <span class="item-name">Designer Sunglasses</span>
              <span class="item-price">₱1,500</span>
            </div>
          </div>
          <div class="order-footer">
            <span class="order-date">Delivered: Oct 15, 2025</span>
            <span class="order-total">Total: ₱1,500</span>
          </div>
        </div>

        <div class="order-card">
          <div class="order-header">
            <span class="order-number">#001215</span>
            <span class="order-status status-completed">Completed</span>
          </div>
          <div class="order-body">
            <div class="order-item">
              <span class="item-name">Wool Scarf</span>
              <span class="item-price">₱1,200</span>
            </div>
            <div class="order-item">
              <span class="item-name">Winter Coat</span>
              <span class="item-price">₱5,500</span>
            </div>
          </div>
          <div class="order-footer">
            <span class="order-date">Delivered: Oct 10, 2025</span>
            <span class="order-total">Total: ₱6,700</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>

</body>

</html>