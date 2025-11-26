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

    /* Small helper text for upload rules */
    .upload-rules {
      font-size: 0.8rem;
      color: #666;
      margin-top: 4px;
      line-height: 1.4;
    }
  </style>
</head>

<body>
<?php
  // Flash messages from analysis controllers (body + color)
  $flashSuccess = $_SESSION['successMessage']      ?? '';
  $flashError   = $_SESSION['errorMessage']        ?? '';

  // Stored analysis results (used in cards + modals)
  $bodyShapeResult = $_SESSION['bodyShapeResult']     ?? null;
  $colorResult     = $_SESSION['colorAnalysisResult'] ?? null;

  // One-time modal triggers (set by controllers after analysis)
  $showBodyModal  = $_SESSION['show_body_modal']  ?? false;
  $showColorModal = $_SESSION['show_color_modal'] ?? false;

  // NEW: keep account modal open after profile update
  $keepProfileOpen = $_SESSION['keep_profile_open'] ?? false;

  // Map to variables used below
  $successMessage = $flashSuccess;
  $errorMessage   = $flashError;

  // Clear only flash + flags (results stay for inline display)
  unset($_SESSION['successMessage'], $_SESSION['errorMessage']);
  unset($_SESSION['show_body_modal'], $_SESSION['show_color_modal']);
  unset($_SESSION['keep_profile_open']); // clear this one-time flag
?>


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
              <?= htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?>
            </div>
            <p>@<?= htmlspecialchars($user['USERNAME']); ?></p>
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
<button class="cart-button" type="button" onclick="openCart()">
  <i class="bi bi-cart"></i>
  <span class="cart-count" id="cartCount"></span>
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

      <?php include 'catalog.php'; ?>

      </section>

      <section id="features" class="content-section">
        <div class="section-title"><i>Personalized Fashion Features</i></div>
        <div class="section-subtitle">Discover our advanced tools designed to enhance your style journey</div>

        <div class="options-container">
          <!-- COLOR ANALYSIS CARD -->
          <div class="option-card">
            <div class="option-icon"><i class="bi bi-palette2"></i></div>
            <div>
              <h2 class="option-title">Color Analysis</h2>
              <p>
                Upload a selfie with good lighting to find your season.<br>
                <span class="upload-rules">
                  • Max file size: 5 MB<br>
                  • Allowed formats: JPG, PNG, WEBP<br>
                  • For your safety, images are scanned for viruses before analysis.
                </span>
              </p>
            </div>

            <?php 
              $hasColorSession   = isset($_SESSION['colorAnalysisResult']);
              $hasColorDb        = !empty($user['SEASON_TYPE']); 
              $showColorResults  = ($hasColorSession || $hasColorDb);
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

          <!-- BODY SHAPE ANALYSIS CARD -->
          <div class="option-card">
            <div class="option-icon"><i class="bi bi-person-standing"></i></div>
            <div>
              <h2 class="option-title">Body Shape Analysis</h2>
              <p>
                Get accurate measurements for personalized style recommendations.<br>
                <span class="upload-rules">
                  • Max file size per image: 5 MB<br>
                  • Allowed formats: JPG, PNG, WEBP<br>
                  • Both images are scanned for viruses before we analyze your body shape.
                </span>
              </p>
            </div>

            <?php 
              $hasSessionResult     = isset($_SESSION['bodyShapeResult']);
              $hasDbResult          = !empty($user['BODY_TYPE']); 
              $showResultsByDefault = ($hasSessionResult || $hasDbResult);
            ?>

            <div class="upload-section <?= $showResultsByDefault ? 'hidden' : '' ?>">
              <form id="bodyShapeForm" method="POST" action="index.php?page=process_body_shape" enctype="multipart/form-data">
                <label>Front Image:</label>
                <input type="file" name="front_image" accept="image/*" required>
                <br>
                <label>Side Image:</label>
                <input type="file" name="side_image" accept="image/*" required>
                <br>
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

  <!-- Toast Notification -->
<div id="toast" class="toast hidden">
  <div class="toast-icon"><i class="bi bi-check2-circle"></i></div>
  <span id="toastMessage"></span>
</div>


  <!-- USER PROFILE MODAL -->
  <div class="overlay"
       id="userProfileOverlay"
       data-keep-open="<?= $keepProfileOpen ? '1' : '0' ?>">

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
        <!-- ACCOUNT TAB -->
        <div class="tab-content active" id="account">
          <div class="section-header">
            <p class="section-title">Account Information</p>
          </div>

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

                <button type="button"
                        class="edit-avatar-btn"
                        id="edit-avatar-btn"
                        title="Change Profile Picture"
                        style="display:none;">
                  <i class="bi bi-camera"></i>
                </button>

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

              <div class="form-group address-group">
                <label>Province</label>
                <select
                  name="province"
                  id="province"
                  class="address-select"
                  disabled
                  required
                  data-current-province="<?= htmlspecialchars($user['PROVINCE'] ?? '', ENT_QUOTES) ?>"
                >
                  <option value="" disabled>Select Province</option>
                  <option value="Metro Manila">Metro Manila</option>
                  <option value="Cavite">Cavite</option>
                  <option value="Laguna">Laguna</option>
                  <option value="Bulacan">Bulacan</option>
                  <option value="Rizal">Rizal</option>
                </select>
                <small class="field-error">Province is required.</small>
              </div>

              <div class="form-group address-group">
                <label>City / Town</label>
                <select
                  name="city"
                  id="city"
                  class="address-select"
                  disabled
                  required
                  data-current-city="<?= htmlspecialchars($user['CITY'] ?? '', ENT_QUOTES) ?>"
                >
                  <option value="" disabled>Select City</option>
                </select>
                <small class="field-error">City/Town is required.</small>
              </div>

              <div class="form-group address-group">
                <label>Barangay</label>
                <select
                  name="barangay"
                  id="barangay"
                  class="address-select"
                  disabled
                  required
                  data-current-barangay="<?= htmlspecialchars($user['BARANGAY'] ?? '', ENT_QUOTES) ?>"
                >
                  <option value="" disabled>Select Barangay</option>
                </select>
                <small class="field-error">Barangay is required.</small>
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

        <!-- PURCHASES TAB -->
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
        </div> <!-- end purchases tab -->
      </div> <!-- end modal-body -->
    </div> <!-- end modal -->
  </div> <!-- end userProfileOverlay -->

  <!-- ===================== NEW ANALYSIS MODALS ===================== -->

  <!-- 1) ERROR MODAL (shared for body + color) -->
  <div
    class="overlay <?= $flashError ? '' : 'hidden' ?>"
    id="analysisErrorOverlay"
    style="
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      z-index: 9999;
    "
  >
    <div
      class="modal"
      style="
        width: 100%;
        max-width: 520px;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 60px rgba(0,0,0,0.35);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
      "
    >
      <div
        class="modal-header"
        style="
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 1rem 1.5rem;
          background: #D7C9AE;
        "
      >
        <h2 style="font-size: 1.05rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
          <i class="bi bi-exclamation-triangle-fill"></i>
          Image / Analysis Error
        </h2>
        <button
          class="close-btn"
          onclick="closeAnalysisErrorModal()"
          style="
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 999px;
          "
        >
          <i class="bi bi-x" style="font-size: 1.2rem;"></i>
        </button>
      </div>

      <div
        class="modal-body"
        style="
          padding: 1.5rem 1.75rem 1.25rem;
          overflow-y: auto;
          font-size: 0.95rem;
          color: #111827;
        "
      >
        <p style="margin-bottom: 0.75rem; line-height: 1.5;">
          <?= htmlspecialchars($flashError) ?>
        </p>
        <p style="margin-top: 0.25rem; font-size: 0.85rem; color: #6b7280; line-height: 1.5;">
          Please make sure your photo meets these requirements:
        </p>
        <ul style="margin: 0.35rem 0 0 1.1rem; padding: 0; font-size: 0.85rem; color: #6b7280; line-height: 1.5;">
          <li>Good, even lighting and only one person in each photo.</li>
          <li>File type is JPG, PNG, or WEBP and size is under 5 MB.</li>
        </ul>
      </div>

      <div
        class="modal-footer"
        style="
          padding: 0.9rem 1.75rem 1.1rem;
          display: flex;
          justify-content: flex-end;
          border-top: 1px solid #e5e7eb;
          background: #f9fafb;
        "
      >
        <button
          class="btn"
          onclick="closeAnalysisErrorModal()"
          style="
            margin-top: 0;
            padding: 0.5rem 1.3rem;
            font-size: 0.9rem;
          "
        >
          Got it
        </button>
      </div>
    </div>
  </div>

  <!-- 2) BODY SHAPE RESULT MODAL (one-time trigger) -->
  <div
    class="overlay <?= $showBodyModal ? '' : 'hidden' ?>"
    id="bodyShapeResultOverlay"
    style="
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      z-index: 9999;
    "
  >
    <div
      class="modal"
      style="
        width: 100%;
        max-width: 520px;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 60px rgba(0,0,0,0.35);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
      "
    >
      <div
        class="modal-header"
        style="
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 1rem 1.5rem;
          background: #D7C9AE;
        "
      >
        <h2 style="font-size: 1.05rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
          <i class="bi bi-person-standing"></i>
          Body Shape Result
        </h2>
        <button
          class="close-btn"
          onclick="closeBodyShapeResultModal()"
          style="
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 999px;
          "
        >
          <i class="bi bi-x" style="font-size: 1.2rem;"></i>
        </button>
      </div>

      <div
        class="modal-body"
        style="
          padding: 1.5rem 1.75rem 1.25rem;
          overflow-y: auto;
          font-size: 0.95rem;
          color: #111827;
        "
      >
        <?php if ($bodyShapeResult): ?>
          <p style="margin-bottom: 0.75rem; line-height: 1.5;">
            Your body shape is
            <strong><?= htmlspecialchars($bodyShapeResult['prediction']['body_shape']) ?></strong>.
          </p>

          <?php if (!empty($bodyShapeResult['measurements'])): ?>
            <ul style="margin-top: 0.25rem; padding-left: 1.2rem; font-size: 0.9rem; color: #374151; line-height: 1.5;">
              <li>Shoulder width: <?= htmlspecialchars($bodyShapeResult['measurements']['ShoulderWidth']) ?> cm</li>
              <li>Waist: <?= htmlspecialchars($bodyShapeResult['measurements']['Waist']) ?> cm</li>
              <li>Hips: <?= htmlspecialchars($bodyShapeResult['measurements']['Hips']) ?> cm</li>
            </ul>
          <?php endif; ?>
        <?php else: ?>
          <p>No body shape result available.</p>
        <?php endif; ?>
      </div>

      <div
        class="modal-footer"
        style="
          padding: 0.9rem 1.75rem 1.1rem;
          display: flex;
          justify-content: flex-end;
          gap: 0.5rem;
          border-top: 1px solid #e5e7eb;
          background: #f9fafb;
        "
      >
        <button
          class="btn btn-outline"
          onclick="closeBodyShapeResultModal()"
          style="
            margin-top: 0;
            padding: 0.5rem 1.2rem;
            font-size: 0.9rem;
          "
        >
          Close
        </button>
        <button
          class="btn"
          onclick="document.getElementById('recommendations')?.scrollIntoView({behavior:'smooth'})"
          style="
            margin-top: 0;
            padding: 0.5rem 1.2rem;
            font-size: 0.9rem;
          "
        >
          See Outfit Recommendations
        </button>
      </div>
    </div>
  </div>

  <!-- 3) COLOR ANALYSIS RESULT MODAL (one-time trigger) -->
  <div
    class="overlay <?= $showColorModal ? '' : 'hidden' ?>"
    id="colorResultOverlay"
    style="
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      z-index: 9999;
    "
  >
    <div
      class="modal"
      style="
        width: 100%;
        max-width: 520px;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 60px rgba(0,0,0,0.35);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
      "
    >
      <div
        class="modal-header"
        style="
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 1rem 1.5rem;
          background: #D7C9AE;
        "
      >
        <h2 style="font-size: 1.05rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
          <i class="bi bi-palette2"></i>
          Color Analysis Result
        </h2>
        <button
          class="close-btn"
          onclick="closeColorResultModal()"
          style="
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 999px;
          "
        >
          <i class="bi bi-x" style="font-size: 1.2rem;"></i>
        </button>
      </div>

      <div
        class="modal-body"
        style="
          padding: 1.5rem 1.75rem 1.25rem;
          overflow-y: auto;
          font-size: 0.95rem;
          color: #111827;
        "
      >
        <?php if ($colorResult): ?>
          <p style="margin-bottom: 0.75rem; line-height: 1.5;">
            Your color season is
            <strong><?= htmlspecialchars($colorResult['season']) ?></strong>.
          </p>

          <?php if (!empty($colorResult['palette'])): ?>
            <p style="margin-top: 0.25rem; font-size: 0.9rem; color: #374151;">
              Suggested colors:
              <?= htmlspecialchars(implode(', ', $colorResult['palette'])) ?>
            </p>
          <?php endif; ?>
        <?php else: ?>
          <p>No color analysis result available.</p>
        <?php endif; ?>
      </div>

      <div
        class="modal-footer"
        style="
          padding: 0.9rem 1.75rem 1.1rem;
          display: flex;
          justify-content: flex-end;
          gap: 0.5rem;
          border-top: 1px solid #e5e7eb;
          background: #f9fafb;
        "
      >
        <button
          class="btn btn-outline"
          onclick="closeColorResultModal()"
          style="
            margin-top: 0;
            padding: 0.5rem 1.2rem;
            font-size: 0.9rem;
          "
        >
          Close
        </button>
        <button
          class="btn"
          onclick="document.getElementById('recommendations')?.scrollIntoView({behavior:'smooth'})"
          style="
            margin-top: 0;
            padding: 0.5rem 1.2rem;
            font-size: 0.9rem;
          "
        >
          See Outfit Recommendations
        </button>
      </div>
    </div>
  </div>

  <!-- LOGOUT MODAL -->
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
        if (results) results.classList.remove('show-results');
        if (uploadSection) uploadSection.classList.remove('hidden');
        const form = document.getElementById('colorForm');
        if (form) form.reset();
      } else {
        if (results) results.classList.add('show-results');
        if (uploadSection) uploadSection.classList.add('hidden');
      }
    }

    function closeAnalysisErrorModal() {
      const overlay = document.getElementById('analysisErrorOverlay');
      if (overlay) overlay.classList.add('hidden');
    }

    function closeBodyShapeResultModal() {
      const overlay = document.getElementById('bodyShapeResultOverlay');
      if (overlay) overlay.classList.add('hidden');
    }

    function closeColorResultModal() {
      const overlay = document.getElementById('colorResultOverlay');
      if (overlay) overlay.classList.add('hidden');
    }
  </script>
<?php include __DIR__ . '/cartModal.php'; ?>

<link rel="stylesheet" href="public/css/cartModal.css">
<script>
  // base64("pk_test_xxx:") — encode on the server
  window.PAYMONGO_PUBLIC_KEY_B64 = "<?= base64_encode($_ENV['PAYMONGO_PUBLIC_KEY'] . ':') ?>";
</script>
<script src="public/js/cartModal.js"></script>
<script src="https://js.paymongo.com/v1/paymongo.js"></script>



</body>
</html>