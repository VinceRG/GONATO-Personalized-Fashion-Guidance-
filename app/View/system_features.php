<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Amarelle</title>
  <link rel="icon" type="image/png" href="public/image/amarelle.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="public/css/system_features.css">
  <link rel="stylesheet" href="public/css/cartModal.css"> 
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="public/js/system_features.js" defer></script>
  <script src="public/js/cartModal.js" defer></script>
  <script>
    window.PAYMONGO_PUBLIC_KEY_B64 = "<?= base64_encode($_ENV['PAYMONGO_PUBLIC_KEY'] . ':') ?>";
  </script>
  <script src="https://js.paymongo.com/v1/paymongo.js"></script>

</head>

<body>
<?php
require_once './app/Helpers/Csrf.php';
$csrfToken = Csrf::getToken();

// ==========================================
// HELPER FUNCTIONS
// ==========================================
if (!function_exists('mapStatusLabel')) {
    function mapStatusLabel($status) {
        switch (strtolower($status)) {
            case 'pending':   return 'Processing';
            case 'confirmed': return 'Confirmed';
            case 'shipped':   return 'Shipped';
            case 'delivered': return 'Delivered';
            case 'cancelled': return 'Cancelled';
            default:          return ucfirst($status);
        }
    }
}

if (!function_exists('mapStatusClass')) {
    function mapStatusClass($status) {
        switch (strtolower($status)) {
            case 'pending':   return 'status-processing';
            case 'delivered': return 'status-completed';
            case 'cancelled': return 'status-cancelled';
            default:          return '';
        }
    }
}

if (empty($user['SEASON_TYPE'])) {
    unset($_SESSION['colorAnalysisResult']);
}

$hasColorSession   = isset($_SESSION['colorAnalysisResult']);
$hasColorDb        = !empty($user['SEASON_TYPE']);
$showColorResults  = ($hasColorSession || $hasColorDb);

// ==========================================

  // Flash messages
  $flashSuccess = $_SESSION['successMessage']      ?? '';
  $flashError   = $_SESSION['errorMessage']        ?? '';

  // Stored analysis results
  $bodyShapeResult = $_SESSION['bodyShapeResult']     ?? null;
  $colorResult     = $_SESSION['colorAnalysisResult'] ?? null;

  // One-time modal triggers
  $showBodyModal  = $_SESSION['show_body_modal']  ?? false;
  $showColorModal = $_SESSION['show_color_modal'] ?? false;

  // NEW: keep account modal open after profile update
  $keepProfileOpen = $_SESSION['keep_profile_open'] ?? false;

  $successMessage = $flashSuccess;
  $errorMessage   = $flashError;

  unset($_SESSION['successMessage'], $_SESSION['errorMessage']);
  unset($_SESSION['show_body_modal'], $_SESSION['show_color_modal']);
  unset($_SESSION['keep_profile_open']); 
?>

  <?php if (isset($successMessage) && $successMessage): ?>
    <div class="notification success"><?= htmlspecialchars($successMessage) ?></div>
  <?php endif; ?>

  <?php if (isset($errorMessage) && $errorMessage): ?>
    <div class="notification error"><?= htmlspecialchars($errorMessage) ?></div>
  <?php endif; ?>
  
  <!-- HEADER / NAVBAR -->
  <header class="site-header">
    <div class="header-left">
      <nav class="nav-links">
        <a href="#catalog-shop" class="nav-btn active">SHOP</a>
        <a href="#features" class="nav-btn">FEATURES</a>
      </nav>
    </div>

    <div class="header-center">
      <div class="logo-img">
        <img src="public/image/amarelle.png" alt="Amarelle logo">
      </div>
    </div>

    <div class="header-right">
      <button class="icon-btn" onclick="toggleSearchBar()">
        <i class="bi bi-search"></i>
      </button>

      <!-- Cart Trigger -->
      <button class="icon-btn cart-trigger" onclick="openCart()">
        <i class="bi bi-bag"></i>
        <span class="cart-count" id="cartCount">0</span>
      </button>

      <button class="icon-btn" onclick="openUserProfile()">
        <i class="bi bi-person"></i>
      </button>

       <button class="icon-btn" onclick="logout()" title="Logout">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </div>
  </header>

  <!-- SEARCH BAR CONTAINER -->
  <div id="searchContainer" class="search-container hidden">
      <input type="text" id="productSearch" placeholder="Search for products..." oninput="filterProducts()">
      <button class="close-search" onclick="toggleSearchBar()"><i class="bi bi-x"></i></button>
  </div>

  <div class="results-container">
    <div class="main-content">
      
      <!-- SHOP SECTION -->
      <section id="catalog-shop" class="content-section active">
        <div class="shop-header-banner">
          <h3 class="section-title"><i style="font-size: 5rem; font-weight: 300;">The Shopping Catalog</i></h3>
          <img src="public/image/banner.png" alt="Shop Banner" class="banner-image">
        </div>

        <?php $imageBasePath = 'public/image/'; ?>

        <?php if (!empty($bodyShape) && !empty($season) && !empty($recommendedProducts)): ?>
          <div id="recommendations" class="subsection">
            <h3 class="section-title" style="font-size: 1.8rem;">Recommended for You</h3>
            <p class="section-subtitle">Based on your color and body analysis results.</p>
            <div class="clothes-grid">
              <?php foreach ($recommendedProducts as $product): ?>
                <div class="clothes-item catalog-item"
                  data-product-id="<?= (int)$product['PRODUCT_ID'] ?>"
                  data-name="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>"
                  data-price="<?= htmlspecialchars($product['PRICE']) ?>"
                >
                  <img src="<?= $imageBasePath . htmlspecialchars($product['IMAGE_FILE']) ?>" alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>">
                  <button class="add-to-cart" onclick="openVariantModal(<?= (int)$product['PRODUCT_ID'] ?>)">
                    <i class="bi bi-bag-plus"></i>
                  </button>
                  <div class="clothes-caption">
                    <span class="title"><?= htmlspecialchars($product['PRODUCT_NAME']) ?></span>
                    <span class="price">₱<?= number_format($product['PRICE'], 2) ?></span>
                    
                    <!-- PILL TAGS -->
                    <div class="recommendation-tags">
                        <?php if (!empty($bodyShape)): ?>
                            <span class="tag tag-yellow"><?= htmlspecialchars($bodyShape) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($season)): ?>
                            <span class="tag tag-green"><?= htmlspecialchars($season) ?></span>
                        <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php include 'catalog.php'; ?>
      </section>

      <!-- FEATURES SECTION -->
      <section id="features" class="content-section">
        <div class="section-title text-center"><i style="font-size: 5rem; font-weight: 300;">Personalized Fashion Features</i></div>
        <div class="section-subtitle text-center">Discover our advanced tools designed to enhance your style journey</div>

        <div class="options-container">
          
          <!-- COLOR ANALYSIS CARD -->
          <div class="option-card">
            <div class="option-header">
                <div class="option-icon"><i class="bi bi-palette2"></i></div>
                <h2 class="option-title">Color Analysis</h2>
            </div>
            <p>Upload a selfie with good lighting to find your season.
                <span class="upload-rules">
                  • Max file size: 5 MB<br>
                  • Allowed formats: JPG, PNG, WEBP<br>
                  • Your image is scanned for viruses before we analyze your season.
                </span>
            </p>

            <?php
              $hasColorSession   = isset($_SESSION['colorAnalysisResult']);
              $hasColorDb        = !empty($user['SEASON_TYPE']);
              $showColorResults  = ($hasColorSession || $hasColorDb);
            ?>

            <div id="colorUploadSection" class="upload-wrapper <?= $showColorResults ? 'hidden' : '' ?>">
                <form id="colorForm" method="POST" action="index.php?page=process_color_analysis" enctype="multipart/form-data">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                  
                  <div class="drag-drop-area" id="colorDropArea">
                      <div class="icon-container">
                          <i class="bi bi-file-earmark-image"></i>
                      </div>
                      <h3>Drag & drop your selfie</h3>
                      <p>or <span class="browse-btn">browse files</span> on your computer</p>
                      <input type="file" name="face_image" id="face_image_input" accept="image/*" required class="file-input-hidden">
                      <div class="file-preview" id="colorFilePreview"></div>
                  </div>

                  <button type="submit" class="btn btn-upload">
                    Upload & Analyze
                  </button>
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
             
              <div class="result-actions">
                <button class="btn" onclick="toggleColorAnalysis(true)">
                    <i class="bi bi-arrow-repeat"></i> Try Again
                </button>
              </div>
            </div>
          </div>

          <!-- BODY SHAPE ANALYSIS CARD -->
          <div class="option-card">
            <div class="option-header">
                <div class="option-icon"><i class="bi bi-person-standing"></i></div>
                <h2 class="option-title">Body Shape Analysis</h2>
            </div>
            <p>Get accurate measurements for personalized style recommendations.
                <span class="upload-rules">
                  • Max file size per image: 5 MB<br>
                  • Allowed formats: JPG, PNG, WEBP<br>
                  • Both images are scanned for viruses before we analyze your body shape.
                </span>
            </p>

            <?php
              $hasSessionResult     = isset($_SESSION['bodyShapeResult']);
              $hasDbResult          = !empty($user['BODY_TYPE']);
              $showResultsByDefault = ($hasSessionResult || $hasDbResult);
            ?>

            <div class="upload-wrapper <?= $showResultsByDefault ? 'hidden' : '' ?>">
              <form id="bodyShapeForm" method="POST" action="index.php?page=process_body_shape" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="dual-upload-container">
                    <div class="upload-item">
                        <label class="input-label">Front Image</label>
                        <div class="drag-drop-area small" id="frontDropArea">
                             <div class="icon-container"><i class="bi bi-file-image"></i></div>
                             <p>Drag front image</p>
                             <input type="file" name="front_image" id="front_image_input" accept="image/*" required class="file-input-hidden">
                             <div class="file-preview" id="frontFilePreview"></div>
                        </div>
                    </div>

                    <div class="upload-item">
                        <label class="input-label">Side Image</label>
                        <div class="drag-drop-area small" id="sideDropArea">
                             <div class="icon-container"><i class="bi bi-file-image"></i></div>
                             <p>Drag side image</p>
                             <input type="file" name="side_image" id="side_image_input" accept="image/*" required class="file-input-hidden">
                             <div class="file-preview" id="sideFilePreview"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group custom-input-group">
                    <label>Height (cm)</label>
                    <input type="number" name="height_cm" class="styled-input" placeholder="e.g. 170" required>
                </div>

                <button type="submit" class="btn btn-upload">
                  Upload & Analyze
                </button>

                <?php if ($showResultsByDefault): ?>
                    <button type="button" class="btn btn-outline" style="margin-top:10px; width:100%" onclick="toggleAnalysis('bodyShapes', true)">
                      Cancel
                    </button>
                <?php endif; ?>
              </form>
            </div>

            <div id="bodyShapes" class="body-shapes-container <?= $showResultsByDefault ? 'show-results' : '' ?>">
              <h3>Your Body Shape</h3>
              <?php if ($hasSessionResult): ?>
                  <?php $result = $_SESSION['bodyShapeResult']; ?>
                  <p>Your shape appears to be <strong><?= htmlspecialchars($result['prediction']['body_shape']) ?></strong>.</p>
              <?php elseif ($hasDbResult): ?>
                  <p>Your saved shape is <strong><?= htmlspecialchars($user['BODY_TYPE']) ?></strong>.</p>
              <?php endif; ?>

              <div class="result-actions">
                <button class="btn" onclick="toggleAnalysis('bodyShapes', false)">
                  <i class="bi bi-arrow-repeat"></i> Try Again
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FOOTER -->
      <footer style="background: #1C1917; color: #E7E5E4; padding: 2.5rem 2rem; font-family: 'Lexend', sans-serif; width: 100%; text-align: center; margin-top: 5rem; border-top: 1px solid #333;">
        
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3rem; max-width: 100%; margin: 0 auto;">

            <div class="footer-brand" style="display: flex; align-items: center; gap: 12px; justify-content: center;">
                <img src="public/image/amarelle.png" alt="Amarelle Logo" style="height: 65px; width: auto; filter: brightness(0) invert(1);">
                <h2 style="font-family: 'Lexend', sans-serif; font-size: 1.8rem; font-weight: 500; margin: 0; color: #F5F5F4; letter-spacing: 1px;">Amarelle</h2>
            </div>

            <div class="footer-links" style="display: flex; flex-direction: row; gap: 3rem; justify-content: center; align-items: center; width: 100%; flex-wrap: wrap;">
                <a href="#features" style="color: #A8A29E; text-decoration: none; font-size: 0.8rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; transition: color 0.3s; white-space: nowrap;">FEATURES</a>
                <a href="index.php?page=policy#cookies" 
                style="color: #A8A29E; text-decoration: none; font-size: 0.8rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; transition: color 0.3s; white-space: nowrap;">
                COOKIES
                </a>

                <a href="index.php?page=policy#privacy"
                style="color: #A8A29E; text-decoration: none; font-size: 0.8rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; transition: color 0.3s; white-space: nowrap;">
                PRIVACY POLICY
                </a>
            </div>

            <div class="footer-payments" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                <span style="font-size: 0.75rem; color: #78716C; letter-spacing: 0.5px;">We accept various credit & debit cards</span>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <img src="public/image/ub.svg" alt="Union Bank" style="height: 30px; width: 30; opacity: 0.8;">
                    <svg viewBox="0 0 32 20" width="45" height="25" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.8;">
                        <circle cx="11" cy="10" r="8" fill="#EB001B"/>
                        <circle cx="21" cy="10" r="8" fill="#F79E1B"/>
                        <path d="M16 10a7.9 7.9 0 0 1 2.3 5.7 7.9 7.9 0 0 1-2.3-5.7 7.9 7.9 0 0 1 2.3 5.7A7.9 7.9 0 0 1 16 10z" fill="#FF5F00"/>
                    </svg>
                    <img src="public/image/pnb.svg" alt="Union Bank" style="height: 30px; width: 30; opacity: 0.8;">
                </div>
            </div>

            <div style="font-size: 0.7rem; color: #57534E; margin-top: 1rem; font-weight: 300;">
                <p>© 2025 Amarelle. All rights reserved.</p>
            </div>
        </div>
      </footer>

    </div>
  </div>

  <!-- Toast & Overlays -->
  <div id="toast" class="toast hidden">
    <div class="toast-icon"><i class="bi bi-check2-circle"></i></div>
    <span id="toastMessage"></span>
  </div>

  <!-- User Profile Overlay -->
  <div class="overlay" id="userProfileOverlay" data-keep-open="<?= $keepProfileOpen ? '1' : '0' ?>">
    <div class="modal">
      <div class="modal-header">
        <div class="header-main">
          <div class="header-avatar">
            <?php if (!empty($user['PROFILE_IMAGE']) && file_exists("uploads/profile_images/" . $user['PROFILE_IMAGE'])): ?>
              <img src="<?= htmlspecialchars($profileImagePath) ?>" alt="Profile">
            <?php else: ?>
              <div class="header-initials"><?= htmlspecialchars($userInitials) ?></div>
            <?php endif; ?>
          </div>
          <div class="header-text">
            <h3 class="header-name"><?= htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?></h3>
            <span class="header-username">@<?= htmlspecialchars($user['USERNAME']); ?></span>
          </div>
        </div>
        <div class="header-right">
          <button type="button" class="btn-view-orders-header" onclick="toggleViews('purchases')">View Orders</button>
          <button class="close-btn" onclick="closeUserProfile()"><i class="bi bi-x-circle"></i></button>
        </div>
      </div>
      
      <div class="modal-body">
         <div id="account-view">
             <form method="POST" action="" enctype="multipart/form-data" class="profile-form" id="profileForm">
                 <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display:none;">
                 
                <!-- Form Fields -->
                <div class="form-group">
                    <label>Username</label>
                    <div class="username-display">
                        <span class="url-domain"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['USERNAME']); ?>" required disabled>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>First name</label>
                        <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['FIRST_NAME']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label>Last name</label>
                        <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['LAST_NAME']); ?>" required disabled>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['EMAIL']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="contacts" id="contacts" value="<?= htmlspecialchars($user['CONTACTS']); ?>" disabled>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Street Address</label>
                        <input type="text" name="street_address" id="street_address" value="<?= htmlspecialchars($user['STREET_ADDRESS'] ?? ''); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Apartment / Unit</label>
                        <input type="text" name="apartment" id="apartment" value="<?= htmlspecialchars($user['APARTMENT'] ?? ''); ?>" disabled>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Region</label>
                        <select id="regionSelect" name="region" class="address-select">
                            <option value="">Select Region</option>
                            <option value="Metro Manila" <?= ($user['REGION'] ?? '') === 'Metro Manila' ? 'selected' : '' ?>>Metro Manila</option>
                            <option value="North Luzon" <?= ($user['REGION'] ?? '') === 'North Luzon' ? 'selected' : '' ?>>North Luzon</option>
                            <option value="South Luzon" <?= ($user['REGION'] ?? '') === 'South Luzon' ? 'selected' : '' ?>>South Luzon</option>
                            <option value="Visayas" <?= ($user['REGION'] ?? '') === 'Visayas' ? 'selected' : '' ?>>Visayas</option>
                            <option value="Mindanao" <?= ($user['REGION'] ?? '') === 'Mindanao' ? 'selected' : '' ?>>Mindanao</option>
                        </select>
                        <span class="error-message" id="region-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Province</label>
                        <select id="province" name="province" class="address-select" data-current-province="<?= htmlspecialchars($user['PROVINCE'] ?? '') ?>">
                            <option value="">Select Province</option>
                        </select>
                        <span class="error-message" id="province-error"></span>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>City / Municipality</label>
                        <select id="city" name="city" class="address-select" data-current-city="<?= htmlspecialchars($user['CITY'] ?? '') ?>">
                            <option value="">Select City / Municipality</option>
                        </select>
                        <span class="error-message" id="city-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Barangay</label>
                        <select id="barangay" name="barangay" class="address-select" data-current-barangay="<?= htmlspecialchars($user['BARANGAY'] ?? '') ?>">
                            <option value="">Select Barangay</option>
                        </select>
                        <span class="error-message" id="barangay-error"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" value="<?= htmlspecialchars($user['POSTAL_CODE'] ?? ''); ?>" disabled>
                </div>

                <!-- STYLE PROFILE CARD -->
                <div class="info-card-rect">
                    <div class="info-card-header">
                        <i class="bi bi-palette"></i>
                        <h3>Style Profile</h3>
                    </div>
                    <?php if (empty($user['SEASON_TYPE']) && empty($user['BODY_TYPE'])): ?>
                        <div class="info-empty">
                            <p>You haven't completed your style analysis yet.</p>
                            <button type="button" class="btn-rect-secondary" onclick="goToFeatures()">
                                <i class="bi bi-magic"></i> Start Style Analysis
                            </button>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($user['SEASON_TYPE'])): ?>
                            <div class="info-item-clean">
                                <span class="info-label">Color Season</span>
                                <span class="info-value"><?= htmlspecialchars($user['SEASON_TYPE']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($user['BODY_TYPE'])): ?>
                            <div class="info-item-clean">
                                <span class="info-label">Body Shape</span>
                                <span class="info-value"><?= htmlspecialchars($user['BODY_TYPE']); ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                 
                 <div class="edit-row">
                    <button type="button" class="btn-edit-profile" onclick="enableEditing(this)">Edit Profile</button>
                 </div>
                 <div class="modal-footer" id="profileFooter">
                    <button type="button" class="btn-cancel" onclick="cancelEditing()">Cancel</button>
                    <button type="submit" name="update_profile" class="btn-save" id="saveProfileBtn" disabled>Save changes</button>
                 </div>
             </form>
         </div>

         <!-- PURCHASES VIEW WRAPPER -->
         <div id="purchases-view" style="display: none;">
            <!-- Back Button -->
            <div class="view-header">
                <button class="btn-back" onclick="toggleViews('account')">
                    <i class="bi bi-arrow-left"></i> Back to Profile
                </button>
            </div>

            <!-- Tab Navigation Buttons -->
            <div class="sub-tabs">
          <button class="sub-tab-btn active" data-subtab="orders">
            <i class="bi bi-box-seam"></i> Orders
          </button>

          <button class="sub-tab-btn" data-subtab="history">
            <i class="bi bi-clock-history"></i> Order History
          </button>
            </div>

            <!-- SUB TAB CONTENT: ORDERS -->
        <div class="sub-tab-content active" id="orders">
          <?php if (!empty($ordersByTab['orders'])): ?>
            <?php foreach ($ordersByTab['orders'] as $order): ?>
              <?php
                $orderId    = (int)$order['ORDER_ID'];
                $statusText = mapStatusLabel($order['STATUS']);   // e.g. "Processing", "Confirmed"
                $statusCls  = mapStatusClass($order['STATUS']);   // e.g. "status-processing"
              ?>
              <div class="order-card">
                <div class="order-header">
                  <span class="order-number">
                    <?= htmlspecialchars($order['ORDER_NUMBER']); ?>
                  </span>
                  <span class="order-status <?= htmlspecialchars($statusCls); ?>">
                    <?= htmlspecialchars($statusText); ?>
                  </span>
                </div>

                <div class="order-body">
                  <?php if (!empty($orderItems[$orderId])): ?>
                    <?php foreach ($orderItems[$orderId] as $item): ?>
                      <div class="order-item">
                        <span class="item-name">
                          <?= htmlspecialchars($item['PRODUCT_NAME']); ?>
                          <?php if (!empty($item['COLOR_NAME'])): ?>
                            (<?= htmlspecialchars($item['COLOR_NAME']); ?>,
                            <?= htmlspecialchars($item['SIZE']); ?>)
                          <?php else: ?>
                            (<?= htmlspecialchars($item['SIZE']); ?>)
                          <?php endif; ?>
                        </span>
                        <span class="item-price">
                          ₱<?= number_format($item['UNIT_PRICE'], 2); ?>
                          × <?= (int)$item['QUANTITY']; ?>
                        </span>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="empty-state">No items found for this order.</p>
                  <?php endif; ?>
                </div>

                <div class="order-footer">
                  <span class="order-date">
                    Ordered: <?= date('M d, Y H:i', strtotime($order['ORDER_DATE'])); ?>
                  </span>
                  <span class="order-total">
                    Total: ₱<?= number_format($order['TOTAL_AMOUNT'], 2); ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="empty-state">You don’t have active orders yet.</p>
          <?php endif; ?>
        </div>

        <!-- SUB TAB CONTENT: HISTORY -->
        <div class="sub-tab-content" id="history">
          <?php if (!empty($ordersByTab['history'])): ?>
            <?php foreach ($ordersByTab['history'] as $order): ?>
              <?php
                $orderId    = (int)$order['ORDER_ID'];
                $statusText = mapStatusLabel($order['STATUS']);
                $statusCls  = mapStatusClass($order['STATUS']);
              ?>
              <div class="order-card">
                <div class="order-header">
                  <span class="order-number">
                    <?= htmlspecialchars($order['ORDER_NUMBER']); ?>
                  </span>
                  <span class="order-status <?= htmlspecialchars($statusCls); ?>">
                    <?= htmlspecialchars($statusText); ?>
                  </span>
                </div>

                <div class="order-body">
                  <?php if (!empty($orderItems[$orderId])): ?>
                    <?php foreach ($orderItems[$orderId] as $item): ?>
                      <div class="order-item">
                        <span class="item-name">
                          <?= htmlspecialchars($item['PRODUCT_NAME']); ?>
                          <?php if (!empty($item['COLOR_NAME'])): ?>
                            (<?= htmlspecialchars($item['COLOR_NAME']); ?>,
                            <?= htmlspecialchars($item['SIZE']); ?>)
                          <?php else: ?>
                            (<?= htmlspecialchars($item['SIZE']); ?>)
                          <?php endif; ?>
                        </span>
                        <span class="item-price">
                          ₱<?= number_format($item['UNIT_PRICE'], 2); ?>
                          × <?= (int)$item['QUANTITY']; ?>
                        </span>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="empty-state">No items found for this order.</p>
                  <?php endif; ?>
                </div>

                <div class="order-footer">
                  <?php
                    // Optional: show "Delivered" instead of "Ordered" for completed orders
                    $dateLabel = ($order['STATUS'] === 'delivered') ? 'Delivered:' : 'Ordered:';
                  ?>
                  <span class="order-date">
                    <?= $dateLabel ?> <?= date('M d, Y H:i', strtotime($order['ORDER_DATE'])); ?>
                  </span>
                  <span class="order-total">
                    Total: ₱<?= number_format($order['TOTAL_AMOUNT'], 2); ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="empty-state">You don’t have any past orders yet.</p>
          <?php endif; ?>
        </div>
      </div>
      </div> <!-- End Modal Body -->
    </div> <!-- End Modal -->
  </div> <!-- End User Profile Overlay -->

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
      </div>
    </div>
  </div>

  <!-- LOGOUT MODAL -->
  <div class="overlay" id="logoutOverlay" style="display: none;">
    <div class="modal" style="max-width: 450px;">
        <div class="modal-header modal-header-dark">
            <h3>Confirm Logout</h3>
            <button class="close-btn close-btn-light" onclick="closeLogoutOverlay()"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body modal-body-center">
            <p>Are you sure you want to logout?</p>
            <div class="modal-actions-center">
                <button onclick="closeLogoutOverlay()" class="btn btn-outline">Cancel</button>
                <button onclick="confirmLogout()" class="btn btn-dark-confirm">Logout</button>
            </div>
        </div>
    </div>
  </div>

  <!-- Analysis Loading Overlay -->
  <div id="analysisLoadingOverlay" class="loading-overlay hidden">
    <div class="loading-box">
        <div class="loading-spinner"></div>
        <div class="loading-title">Analyzing...</div>
    </div>
  </div>

  <?php include __DIR__ . '/cartModal.php'; ?>
</body>
</html>