
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Amarelle</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="public/css/system_features.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="public/js/system_features.js" defer></script>
  <style>
    /* Ensure hidden utility class exists for the toggling logic */
    .hidden { display: none !important; }
  </style>
</head>

<body>
  <?php if (isset($successMessage) && $successMessage): ?>
    <div class="notification success"><?= htmlspecialchars($successMessage) ?></div>
    <script>
      setTimeout(() => {
        const notif = document.querySelector('.notification.success');
        if (notif) {
          notif.classList.add('hiding');
          setTimeout(() => notif.remove(), 300);
        }
      }, 3000);
    </script>
  <?php endif; ?>

  <?php if (isset($errorMessage) && $errorMessage): ?>
    <div class="notification error"><?= htmlspecialchars($errorMessage) ?></div>
    <script>
      setTimeout(() => {
        const notif = document.querySelector('.notification.error');
        if (notif) {
          notif.classList.add('hiding');
          setTimeout(() => notif.remove(), 300);
        }
      }, 3000);
    </script>
  <?php endif; ?>

  <div class="results-container">
      <aside class="sidebar" id="appSidebar" aria-expanded="true">
        <div>
          <div class="profile" onclick="openUserProfile()">
            <div class="profile-pic" id="sidebar-profile-pic">
              <?php if (!empty($user['PROFILE_IMAGE'])): ?>
                <img src="<?= htmlspecialchars($profileImagePath) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
              <?php else: ?>
                <?= htmlspecialchars($userInitials) ?>
              <?php endif; ?>
            </div>
            <div class="profile-info">
              <div class="form-group">
                <?php echo htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?>
              </div>
              <p>@<?php echo htmlspecialchars($user['USERNAME']); ?></p>
            </div>
          </div>

          <nav class="nav-links" role="navigation" aria-label="Main navigation">
            <a href="#catalog-shop" class="nav-btn active nav-item" data-label="Shop">
              <i class="bi bi-basket nav-icon" aria-hidden="true"></i>
              <span class="nav-label">Shop</span>
              <span class="label-tooltip" aria-hidden="true"></span>
            </a>
            <a href="#features" class="nav-btn nav-item" data-label="Features">
              <img src="public/image/features.png" class="nav-icon" alt="Features icon">
              <span class="nav-label">Features</span>
              <span class="label-tooltip" aria-hidden="true"></span>
            </a>
          </nav>
        </div>

        <div class="sidebar-footer" aria-hidden="true">
          <div class="logo-section">
            <div class="logo-img" style="width:40px; height:40px;">
              <img src="public/image/amarelle.png" alt="Amarelle logo">
            </div>
          </div>
          <button class="logout-btn" onclick="logout()" title="Logout" aria-label="Logout">
            <i class="bi bi-box-arrow-right"></i>
          </button>
          
        </div>
      </aside>


    <div class="main-content">
      <section id="catalog-shop" class="content-section active">
        <div class="shop-header">
          <div>
            <h2 class="section-title"><i>Shopping Catalog</i></h2>
            <p class="section-subtitle">Find your perfect outfit below or explore personalized picks.</p>
          </div>

          <div class="shop-controls">
            <button class="cart-button" onclick="openCart()">
              <i class="bi bi-bag"></i>
              <span class="cart-count">0</span>
            </button>
          </div>
        </div>

        <div id="recommendations" class="subsection">
          <h3 class="section-title" style="font-size: 1.8rem;">Recommended for You</h3>
          <p class="section-subtitle">Based on your color and body analysis results.</p>

          <div class="clothes-grid">
            <div class="clothes-item">
              <img src="images/recommend1.jpg" alt="Recommended Outfit 1">
              <button class="add-to-cart"><i class="bi bi-heart"></i></button>
              <div class="clothes-caption">
                <span class="title">Soft Beige Blazer</span>
                <span class="price">₱1,899</span>
              </div>
            </div>

            <div class="clothes-item">
              <img src="images/recommend2.jpg" alt="Recommended Outfit 2">
              <button class="add-to-cart"><i class="bi bi-heart"></i></button>
              <div class="clothes-caption">
                <span class="title">Classic White Dress</span>
                <span class="price">₱2,150</span>
              </div>
            </div>
          </div>
        </div>

        <div class="subsection">

         <div class="filter-bar">
          <input type="text" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()">
          <select id="categoryFilter" onchange="filterProducts()">
            <option value="">All Categories</option>
            <option value="Dresses">Dresses</option>
            <option value="Tops">Tops</option>
            <option value="Bottoms">Bottoms</option>
          </select>
        </div>

          <div class="clothes-grid">
            <div class="clothes-item catalog-item">
              <img src="images/item1.jpg" alt="Clothing Item 1">
              <button class="add-to-cart"><i class="bi bi-bag-plus"></i></button>
              <div class="clothes-caption">
                <span class="title">Summer Linen Top</span>
                <span class="price">₱999</span>
              </div>
            </div>

            <div class="clothes-item catalog-item">
              <img src="images/item2.jpg" alt="Clothing Item 2">
              <button class="add-to-cart"><i class="bi bi-bag-plus"></i></button>
              <div class="clothes-caption">
                <span class="title">Flowy Midi Skirt</span>
                <span class="price">₱1,250</span>
              </div>
            </div>

            <div class="clothes-item catalog-item">
              <img src="images/item3.jpg" alt="Clothing Item 3">
              <button class="add-to-cart"><i class="bi bi-bag-plus"></i></button>
              <div class="clothes-caption">
                <span class="title">Tan Trousers</span>
                <span class="price">₱1,799</span>
              </div>
            </div>

            <div class="clothes-item catalog-item">
              <img src="images/item1.jpg" alt="Clothing Item 4">
              <button class="add-to-cart"><i class="bi bi-bag-plus"></i></button>
              <div class="clothes-caption">
                <span class="title">Summer Linen Top</span>
                <span class="price">₱999</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="features" class="content-section">
        <div class="section-title"><i>Personalized Fashion Features</i></div>
        <div class="section-subtitle">Discover our advanced tools designed to enhance your style journey</div>

        <div class="options-container">
          
          <div class="option-card">
            <div class="option-icon"><i class="bi bi-palette2"></i></div>
            <div>
              <h2 class="option-title">Color Analysis</h2>
              <p>Upload a selfie with good lighting to find your season.</p>
            </div>

            <?php 
              $hasColorSession = isset($_SESSION['colorAnalysisResult']);
              $hasColorDb = !empty($user['SEASON_TYPE']); 
              $showColorResults = ($hasColorSession || $hasColorDb);
            ?>

            <div id="colorUploadSection" class="upload-section <?= $showColorResults ? 'hidden' : '' ?>">
              <form id="colorForm" method="POST" action="index.php?page=process_color_analysis" enctype="multipart/form-data">
                <label>Face Image (Selfie):</label>
                <input type="file" name="face_image" accept="image/*" required>
                
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                  <button type="submit" class="btn" style="flex: 1;">
                    <i class="bi bi-magic"></i> Analyze Color
                  </button>
                  
                  <?php if ($showColorResults): ?>
                    <button type="button" class="btn btn-outline" style="flex: 1; justify-content: center;" onclick="toggleColorAnalysis(false)">
                      Cancel
                    </button>
                  <?php endif; ?>
                </div>
              </form>
            </div>

            <div id="colorSeasons" class="color-seasons-container <?= $showColorResults ? 'show-results' : '' ?>">
              <h3>Your Color Palette</h3>
              <?php if ($hasColorSession): ?>
                  <p>Your season is <strong><?= htmlspecialchars($_SESSION['colorAnalysisResult']['season']) ?></strong>.</p>
                  <p>Recommended tones: <?= implode(", ", $_SESSION['colorAnalysisResult']['palette']) ?></p>
              <?php elseif ($hasColorDb): ?>
                  <p>Your saved season is <strong><?= htmlspecialchars($user['SEASON_TYPE']) ?></strong>.</p>
              <?php endif; ?>
              
              <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button class="btn" onclick="toggleColorAnalysis(true)" style="background: #2D2D2D; color: white; flex: 1; justify-content: center;">
                    <i class="bi bi-arrow-repeat"></i> Try Again
                </button>
              </div>
            </div>
          </div>
          <div class="option-card">
            <div class="option-icon"><i class="bi bi-person-standing"></i></div>
            <div>
              <h2 class="option-title">Body Shape Analysis</h2>
              <p>Get accurate measurements for personalized style recommendations</p>
            </div>

            <?php 
              $hasSessionResult = isset($_SESSION['bodyShapeResult']);
              $hasDbResult = !empty($user['BODY_TYPE']); 
              $showResultsByDefault = ($hasSessionResult || $hasDbResult);
            ?>

            <div class="upload-section <?= $showResultsByDefault ? 'hidden' : '' ?>">
              <form id="bodyShapeForm" method="POST" action="index.php?page=process_body_shape" enctype="multipart/form-data">
                <label>Front Image:</label>
                <input type="file" name="front_image" accept="image/*" required>

                <label>Side Image:</label>
                <input type="file" name="side_image" accept="image/*" required>

                <label>Height (cm):</label>
                <input type="number" name="height_cm" placeholder="Enter your height in cm" required>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                  <button type="submit" class="btn" style="flex: 1;">
                    <i class="bi bi-upload"></i> Analyze
                  </button>
                  
                  <?php if ($showResultsByDefault): ?>
                    <button type="button" class="btn btn-outline" style="flex: 1; justify-content: center;" onclick="toggleAnalysis('bodyShapes', true)">
                      Cancel
                    </button>
                  <?php endif; ?>
                </div>
              </form>
            </div>

            <ul class="feature-list <?= $showResultsByDefault ? 'hidden' : '' ?>">
              <li>AI-assisted body recognition</li>
              <li>Measurement-based style insights</li>
              <li>Shape-specific outfit recommendations</li>
              <li>Personalized fit suggestions</li>
            </ul>

            <div id="bodyShapes" class="body-shapes-container <?= $showResultsByDefault ? 'show-results' : '' ?>">
              <h3>Your Body Shape</h3>
              
              <?php if ($hasSessionResult): ?>
                  <?php $result = $_SESSION['bodyShapeResult']; ?>
                  <p>Your shape appears to be <strong><?= htmlspecialchars($result['prediction']['body_shape']) ?></strong>.</p>
                  <?php elseif ($hasDbResult): ?>
                  <p>Your saved shape is <strong><?= htmlspecialchars($user['BODY_TYPE']) ?></strong>.</p>
                  <p>We have your measurements saved.</p>
              <?php endif; ?>

              <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button class="btn" onclick="toggleAnalysis('bodyShapes', false)" style="background: #2D2D2D; color: white; flex: 1; justify-content: center;">
                  <i class="bi bi-arrow-repeat"></i> Try Again
                </button>
                
               
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <div class="overlay" id="userProfileOverlay">
  <div class="modal">
    <div class="modal-header">
      <div class="tabs">
        <button class="tab-btn active" data-tab="account">
          <i class="bi bi-person"></i> Account
        </button>
        <button class="tab-btn" data-tab="purchases">
          <i class="bi bi-bag"></i> Purchases
        </button>
      </div>
      <button class="close-btn" onclick="closeUserProfile()">
        <i class="bi bi-x"></i>
      </button>
    </div>

    <div class="modal-body">
      <!-- ========== ACCOUNT TAB ========== -->
      <div class="tab-content active" id="account">
        <div class="section-header">
          <p class="section-title">Account Information</p>
        </div>

        <!-- IMPORTANT: method POST + enctype for image upload -->
        <form method="POST" action="" enctype="multipart/form-data" class="profile-form" id="profileForm">
          <div class="profile-section">
            <div class="profile-avatar">
              <?php if (!empty($user['PROFILE_IMAGE']) && file_exists("uploads/profile_images/" . $user['PROFILE_IMAGE'])): ?>
                <img id="avatar-img"
                     src="<?= htmlspecialchars($profileImagePath) ?>"
                     alt="Profile Picture"
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
              <?php else: ?>
                <img id="avatar-img"
                     src="<?= htmlspecialchars($profileImagePath) ?>"
                     alt="Profile Picture"
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: <?= empty($user['PROFILE_IMAGE']) ? 'none' : 'block' ?>;">
                <div class="avatar-initials" style="<?= empty($user['PROFILE_IMAGE']) ? '' : 'display:none' ?>">
                  <?= htmlspecialchars($userInitials) ?>
                </div>
              <?php endif; ?>

              <!-- camera button shown when editing (handled in JS) -->
              <button type="button"
                      class="edit-avatar-btn"
                      id="edit-avatar-btn"
                      title="Change Profile Picture"
                      style="display:none;">
                <i class="bi bi-camera"></i>
              </button>

              <!-- hidden file input for profile image -->
              <input type="file"
                     id="profile_image"
                     name="profile_image"
                     accept="image/*"
                     style="display:none;">
            </div>

            <div class="profile-details">
              <h2 id="display-name">
                <?= htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?>
              </h2>
              <div class="username" id="display-username">
                @<?= htmlspecialchars($user['USERNAME']); ?>
              </div>
              <p class="profile-tagline">
                Welcome to Amarelle — your personal style space.
              </p>
            </div>
          </div>

          <!-- ========== FORM FIELDS ========== -->
          <div class="form-grid">
            <div class="form-group">
              <label>First Name</label>
              <input type="text"
                     name="first_name"
                     id="first_name"
                     value="<?= htmlspecialchars($user['FIRST_NAME']); ?>"
                     disabled
                     required>
            </div>

            <div class="form-group">
              <label>Last Name</label>
              <input type="text"
                     name="last_name"
                     id="last_name"
                     value="<?= htmlspecialchars($user['LAST_NAME']); ?>"
                     disabled
                     required>
            </div>

            <div class="form-group">
              <label>Username</label>
              <input type="text"
                     name="username"
                     id="username"
                     value="<?= htmlspecialchars($user['USERNAME']); ?>"
                     disabled
                     required>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email"
                     name="email"
                     id="email"
                     value="<?= htmlspecialchars($user['EMAIL']); ?>"
                     disabled
                     required>
            </div>

            <!-- NEW: split address fields based on your users table -->

            <div class="form-group">
              <label>Street Address</label>
              <input type="text"
                     name="street_address"
                     id="street_address"
                     value="<?= htmlspecialchars($user['STREET_ADDRESS'] ?? ''); ?>"
                     disabled>
            </div>

            <div class="form-group">
              <label>Apartment / Unit (optional)</label>
              <input type="text"
                     name="apartment"
                     id="apartment"
                     value="<?= htmlspecialchars($user['APARTMENT'] ?? ''); ?>"
                     disabled>
            </div>

            <div class="form-group">
              <label>Province</label>
              <input type="text"
                     name="province"
                     id="province"
                     value="<?= htmlspecialchars($user['PROVINCE'] ?? ''); ?>"
                     disabled>
            </div>

            <div class="form-group">
              <label>City / Municipality</label>
              <input type="text"
                     name="city"
                     id="city"
                     value="<?= htmlspecialchars($user['CITY'] ?? ''); ?>"
                     disabled>
            </div>

            <div class="form-group">
              <label>Barangay</label>
              <input type="text"
                     name="barangay"
                     id="barangay"
                     value="<?= htmlspecialchars($user['BARANGAY'] ?? ''); ?>"
                     disabled>
            </div>

            <div class="form-group">
              <label>Postal Code</label>
              <input type="text"
                     name="postal_code"
                     id="postal_code"
                     value="<?= htmlspecialchars($user['POSTAL_CODE'] ?? ''); ?>"
                     disabled>
            </div>

            <div class="form-group">
              <label>Phone Number</label>
              <input type="text"
                     name="contacts"
                     id="contacts"
                     value="<?= htmlspecialchars($user['CONTACTS']); ?>"
                     disabled>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" id="editProfileBtn" class="btn btn-secondary">
              <i class="bi bi-pencil-square"></i> Edit Profile
            </button>

            <button type="submit"
                    name="update_profile"
                    id="saveProfileBtn"
                    class="btn btn-primary"
                    style="display:none;">
              <i class="bi bi-save"></i> Save Changes
            </button>

            <button type="button"
                    id="cancelEditBtn"
                    class="btn btn-outline"
                    style="display:none;">
              <i class="bi bi-x-circle"></i> Cancel
            </button>
          </div>
        </form>

        <!-- ========== STYLE PROFILE CARD ========== -->
        <div class="info-card">
          <div class="info-card-header">
            <i class="bi bi-palette"></i>
            <h3>Style Profile</h3>
          </div>

          <?php if (empty($user['SEASON_TYPE']) && empty($user['BODY_TYPE'])): ?>
            <div class="info-empty">
              <p>You haven't completed your style analysis yet.</p>
              <button class="btn" onclick="goToFeatures()">
                <i class="bi bi-magic"></i> Start Style Analysis
              </button>
            </div>
          <?php else: ?>
            <?php if (!empty($user['SEASON_TYPE'])): ?>
              <div class="info-item">
                <span class="info-label">Color Season</span>
                <span class="info-value">
                  <?= htmlspecialchars($user['SEASON_TYPE']); ?>
                </span>
              </div>
            <?php endif; ?>

            <?php if (!empty($user['BODY_TYPE'])): ?>
              <div class="info-item">
                <span class="info-label">Body Shape</span>
                <span class="info-value">
                  <?= htmlspecialchars($user['BODY_TYPE']); ?>
                </span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>

      

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

  <div class="overlay" id="logoutOverlay" style="display: none;">
    <div class="modal" style="max-width: 450px;">
      <div class="modal-header" style="background: #2D2D2D;">
        <div style="flex: 1;">
          <h3 style="color: white; font-size: 1.3rem; margin: 0; font-weight: 600;">
            <i class="bi bi-box-arrow-right" style="margin-right: 0.5rem;"></i>
            Confirm Logout
          </h3>
        </div>
        <button class="close-btn" onclick ="closeLogoutOverlay()" style="color: white;">
          <i class="bi bi-x"></i>
        </button>
      </div>

      <div class="modal-body" style="text-align: center; padding: 2.5rem 2rem;">
        <div style="
          width: 80px;
          height: 80px;
          background: linear-gradient(135deg, #D7C9AE, #A68763);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto 1.5rem;
          box-shadow: 0 8px 20px rgba(166, 135, 99, 0.3);
        ">
          <i class="bi bi-box-arrow-right" style="font-size: 2.5rem; color: white;"></i>
        </div>

        <h3 style="color: #2D2D2D; font-size: 1.4rem; margin-bottom: 0.75rem; font-weight: 600;">
          Are you sure?
        </h3>
        <p style="color: #666; font-size: 1rem; margin-bottom: 2rem; line-height: 1.6;">
          You will be logged out of your account and redirected to the login page.
        </p>

        <div style="display: flex; gap: 1rem; justify-content: center;">
          <button onclick="closeLogoutOverlay()" style="flex:1; padding:0.85rem 1.5rem; border:2px solid #e0e0e0; background:white; color:#666; border-radius:10px; font-size:1rem; font-weight:600; cursor:pointer; transition:all 0.3s ease;">
            <i class="bi bi-x-circle" style="margin-right: 0.5rem;"></i>
            Cancel
          </button>
          <button onclick="confirmLogout()" style="flex:1; padding:0.85rem 1.5rem; border:none; background:#2D2D2D; color:white; border-radius:10px; font-size:1rem; font-weight:600; cursor:pointer; transition:all 0.3s ease;">
            <i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i>
            Yes, Logout
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleColorAnalysis(showUpload) {
        const results = document.getElementById('colorSeasons');
        const uploadSection = document.getElementById('colorUploadSection');
        
        if (showUpload) {
            // User clicked "Try Again"
            if (results) results.classList.remove('show-results');
            if (uploadSection) uploadSection.classList.remove('hidden');
            // Optional: Clear form
            const form = document.getElementById('colorForm');
            if (form) form.reset();
        } else {
            // User clicked "Cancel"
            if (results) results.classList.add('show-results');
            if (uploadSection) uploadSection.classList.add('hidden');
        }
    }
  </script>

</body>
</html>