<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>User Profile - Amarelle</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"/>
  <link rel="stylesheet" href="public/css/user_info.css">
  <script src="public/js/user_info.js"></script>
</head>
<body>
<div class="overlay">
  <div class="modal">
    <div class="modal-header">
      <button class="close-btn" onclick="window.history.back()">
        <i class="bi bi-x"></i>
      </button>
      <div class="tabs">
        <button class="tab-btn active" data-tab="account">
          <i class="bi bi-person"></i> Account
        </button>
        <button class="tab-btn" data-tab="purchases">
          <i class="bi bi-bag"></i> Purchases
        </button>
      </div>
    </div>
    
    <div class="modal-body">
      <!-- Account Tab -->
      <div class="tab-content active" id="account">
        <div class="section-header">
          <h2 class="section-title">Account Information</h2>
          <button class="edit-btn" onclick="toggleEdit()">
            <i class="bi bi-pencil-square"></i> Edit Profile
          </button>
        </div>
        <div class="profile-section">
  <div class="profile-avatar">
    <span class="initials">GU</span>
    <button class="edit-avatar-btn" title="Change Profile Picture">
      <i class="bi bi-camera"></i>
    </button>
  </div>

  <div class="profile-details">
    <h2 class="profile-name">Guest User</h2>
    <div class="username">@username</div>
    <p class="profile-tagline">Welcome to Amarelle — your personal style space.</p>
    <button class="edit-avatar-text"><i class="bi bi-pencil"></i> Edit Photo</button>
  </div>
</div>
        <form id="profileForm">
          <div class="form-grid">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" id="name" value="Sophia Dela Cruz" disabled>
            </div>
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" id="username" value="@sophiadlcrz" disabled>
            </div>
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" value="sophia.martinez@email.com" disabled>
            </div>
            <div class="form-group">
              <label for="homeAddress">Home Address</label>
              <input type="text" id="homeAddress" value="123 Main St, Pasig City" disabled>
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" value="+63 912 345 6789" disabled>
            </div>
          </div>
          <div class="info-card">
            <div class="info-card-header"><i class="bi bi-palette"></i><h3>Style Profile</h3></div>
            <div class="info-item"><span class="info-label">Color Season</span><span class="info-value">Autumn</span></div>
            <div class="info-item"><span class="info-label">Body Shape</span><span class="info-value">Hourglass</span></div>
          </div>
        </div>
        </form>
      </div>
      
      <!-- Purchases Tab -->
      <div class="tab-content" id="purchases">
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