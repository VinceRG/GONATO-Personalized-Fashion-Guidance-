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

    /* Overlay */
    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.35);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    /* Modal Container */
    .modal {
      background: #ffffff;
      border-radius: 20px;
      max-width: 640px;
      width: 95%;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      color: #0f172a;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    /* Header */
    .modal-header {
      padding: 1.5rem 2rem;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #ffffff;
    }

    .header-main {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    /* Bigger avatar, clickable (for change photo) */
    .header-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      overflow: hidden;
      background: #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
    }

    .header-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .header-initials {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #A68763;
      color: #f9fafb;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .header-avatar.editing::after {
      content: "Change";
      position: absolute;
      inset: 0;
      background: #A68763;
      color: #f9fafb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 600;
    }

    /* Name + username */
    .header-text {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .header-name {
      font-size: 1.1rem;
      font-weight: 700;
      margin: 0;
      color: #0f172a;
    }

    .header-username {
      font-size: 0.9rem;
      color: #6b7280;
    }

    /* Right side of header */
    .header-right {
      display: flex;
      align-items: first baseline;
      gap: 0.75rem;
    }

    /* View Orders button in header */
    .btn-view-orders-header {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      padding: 0.7rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      color: #111827;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }

    .btn-view-orders-header:hover {
      background: #2D2D2D;
      color: #D7C9AE;
    }

    /* Close button */
    .close-btn {
      background: none;
      border: none;
      font-size: 1.25rem;
      cursor: pointer;
      color: #9ca3af;
      padding: 4px;
      border-radius: 50%;
      transition: 0.15s;
    }

    .close-btn:hover {
      background: #D10000;
      color: #EAE0D2;
    }

    /* Edit row (under header) */
    .edit-row {
      padding: 0.75rem 0 0;
      display: flex;
      justify-content: flex-end;
    }

    .btn-edit-profile {
      padding: 0.5rem 1rem;
      font-size: 0.85rem;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
      background: #f9fafb;
      cursor: pointer;
      font-weight: 600;
      color: #111827;
    }

    .btn-edit-profile:hover:not(:disabled) {
      background: #2D2D2D;
      color: #D7C9AE;
    }

    /* Body */
    .modal-body {
      padding: 2rem;
      max-height: 75vh;
      overflow-y: auto;
    }

    /* Basic form styles */

    .address-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .form-group {
      margin-bottom: 1.25rem;
      display: flex;
      flex-direction: column;
    }

    .form-group label,
    .input-wrapper label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #111827;
      margin-bottom: 0.5rem;
      display: block;
    }

    input[type="text"],
    input[type="email"],
    textarea,
    select {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      font-size: 0.95rem;
      transition: 0.15s;
      color: #111827;
      background: #f9fafb;
    }

    input:disabled,
    textarea:disabled,
    select:disabled {
      background: #f3f4f6;
      color: #9ca3af;
    }

    input:focus,
    textarea:focus,
    select:focus {
      outline: none;
      border-color: #111827;
      background: #ffffff;
    }

    /* Username row */
    .form-row {
      margin-bottom: 1.5rem;
    }

    .username-display {
      position: relative;
    }

    .url-domain {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 0.9rem;
    }

    .username-display input {
      padding-left: 45px;
      font-weight: 600;
    }

    .username-display input i {
      size: 50px;
    }

    /* Grid for first/last name */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    /* Email icon */
    .input-with-icon {
      position: relative;
    }

    .input-with-icon i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
    }

    .input-with-icon input {
      padding-left: 38px;
    }

    /* Address selects & validation */
    .address-select {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      font-size: 0.95rem;
      background: #f9fafb;
      color: #111827;
    }

    .address-group {
      position: relative;
    }

    .field-error {
      display: none;
      font-size: 0.75rem;
      color: #b91c1c;
      margin-top: 0.25rem;
    }

    .address-group.has-error .field-error {
      display: block;
    }

    /* Style profile card */
    .info-card-rect {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 1rem 1.25rem;
      margin-top: 1.25rem;
      background: #eae0d2;
    }

    .info-card-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.75rem;
    }

    .info-card-header h3 {
      margin: 0;
      font-size: 0.95rem;
      font-weight: 700;
    }

    .info-empty {
      font-size: 0.85rem;
      color: #6b7280;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 0.75rem;
    }

    .btn-rect-secondary {
      border: 1px solid #e5e7eb;
      background: #ffffff;
      border-radius: 6px;
      padding: 0.4rem 0.9rem;
      font-size: 0.8rem;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      cursor: pointer;
      font-weight: 600;
    }

    .btn-rect-secondary:hover {
      background: #2D2D2D;
      color: #eae0d2;
    }

    .info-item-clean {
      display: flex;
      justify-content: space-between;
      font-size: 0.85rem;
      padding: 0.2rem 0;
    }

    .info-label {
      color: #6b7280;
    }

    .info-value {
      font-weight: 600;
      color: #111827;
    }

    /* Footer: hidden until editing */
    .modal-footer {
      margin-top: 1.5rem;
      display: none; /* shown via JS in edit mode */
      justify-content: flex-end;
      gap: 0.75rem;
      border-top: 1px solid #e5e7eb;
      padding-top: 1.25rem;
    }

    .btn-cancel {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      color: #4b5563;
      font-weight: 600;
      cursor: pointer;
      padding: 0.7rem 1.5rem;
      border-radius: 6px;
    }

    .btn-cancel:hover {
      background: #f3f4f6;
    }

    .btn-save {
      background: #111827;
      color: #ffffff;
      border: none;
      border-radius: 6px;
      padding: 0.7rem 1.6rem;
      font-weight: 600;
      cursor: pointer;
      transition: 0.15s;
    }

    .btn-save:disabled {
      opacity: 0.5;
      cursor: default;
    }

    .btn-save:not(:disabled):hover {
      background: #000000;
    }

    /* Purchases view */
    .view-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.25rem;
    }

    .btn-back {
      background: none;
      border: none;
      color: #6b7280;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      font-weight: 500;
    }

    .btn-back:hover {
      color: #111827;
    }

    /* Sub-tabs */
    .sub-tabs {
      display: inline-flex;
      gap: 0.5rem;
      border-bottom: 1px solid #e5e7eb;
      margin-bottom: 1rem;
      width: 95%;
    }

    .sub-tab-btn {
      border: none;
      background: transparent;
      padding: 0.65rem 1rem;
      font-size: 0.85rem;
      font-weight: 600;
      color: #6b7280;
      border-radius: 6px 6px 0 0;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      cursor: pointer;
    }

    .sub-tab-btn i {
      font-size: 0.9rem;
    }

    .sub-tab-btn.active {
      color: #111827;
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-bottom-color: #ffffff;
    }

    .sub-tab-content {
      display: none;
    }

    .sub-tab-content.active {
      display: block;
    }

    /* Order cards */
    .order-card {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 1rem 1.1rem;
      background: #ffffff;
      margin-bottom: 1rem;
    }

    .order-header,
    .order-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.85rem;
    }

    .order-header {
      margin-bottom: 0.75rem;
      color: #4b5563;
    }

    .order-footer {
      margin-top: 0.75rem;
      color: #6b7280;
    }

    .order-body {
      border-top: 1px dashed #e5e7eb;
      border-bottom: 1px dashed #e5e7eb;
      padding: 0.75rem 0;
    }

    .order-item {
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
      padding: 0.2rem 0;
    }

    .item-name {
      color: #111827;
    }

    .item-price {
      font-weight: 600;
      color: #111827;
    }

    .order-number {
      font-weight: 600;
    }

    .order-total {
      font-weight: 600;
      color: #111827;
    }

    .order-status {
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 0.75rem;
    }

    .status-processing {
      color: #d97706;
      background: #fffbeb;
    }

    .status-completed {
      color: #059669;
      background: #ecfdf5;
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
  <?php
  // ================= RECOMMENDATIONS LOGIC =================

  // Where your product images are stored (adjust if different)
  $productImageBasePath = "uploads/products/";

  $recommendations = [];

  // Make sure we have a logged-in user & a DB connection ($pdo or whatever you use)
  if (!empty($user['USER_ID']) && isset($pdo)) {

      $userId = (int)$user['USER_ID'];

      $sql = "
          SELECT
              p.PRODUCT_ID,
              p.PRODUCT_NAME,
              p.DESCRIPTION,
              p.PRICE,
              p.IMAGE_FILE,
              bs.BODY_TYPE,
              s.SEASON_TYPE,
              c.COLOR_VALUE,
              i.SIZE,
              i.QUANTITY
          FROM users u
          JOIN products p
              ON p.BODY_SHAPE_ID = u.BODY_SHAPE_ID
          JOIN inventory i
              ON i.PRODUCT_ID = p.PRODUCT_ID
          JOIN colors c
              ON c.COLOR_ID = i.COLOR_ID
          JOIN seasons s
              ON c.SEASON_ID = s.SEASON_ID
          LEFT JOIN body_shapes bs
              ON bs.BODY_SHAPE_ID = u.BODY_SHAPE_ID
          WHERE
              u.USER_ID        = :user_id
              AND u.SEASON_ID IS NOT NULL
              AND u.BODY_SHAPE_ID IS NOT NULL
              AND c.SEASON_ID   = u.SEASON_ID
              AND i.QUANTITY    > 0
          ORDER BY
              p.CREATED_AT DESC,
              p.PRODUCT_NAME
          LIMIT 8
      ";

      $stmt = $pdo->prepare($sql);
      $stmt->execute(['user_id' => $userId]);
      $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }
?>


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
  <?php if (!empty($recommendations)): ?>
    <?php foreach ($recommendations as $item): ?>
      <div class="clothes-item">
        <img
          src="<?= htmlspecialchars($productImageBasePath . $item['IMAGE_FILE']) ?>"
          alt="<?= htmlspecialchars($item['PRODUCT_NAME']) ?>"
        >


        <!-- Heart / add-to-cart button (wired with data-attributes if you want JS to use them) -->
        <button
          class="add-to-cart"
          data-product-id="<?= (int)$item['PRODUCT_ID'] ?>"
          data-product-name="<?= htmlspecialchars($item['PRODUCT_NAME']) ?>"
          data-price="<?= htmlspecialchars($item['PRICE']) ?>"
          data-size="<?= htmlspecialchars($item['SIZE']) ?>"
          data-color="<?= htmlspecialchars($item['COLOR_VALUE'] ?? '') ?>"
        >
          <i class="bi bi-heart"></i>
        </button>


        <div class="clothes-caption">
          <span class="title">
            <?= htmlspecialchars($item['PRODUCT_NAME']) ?>
          </span>
          <span class="price">
            ₱<?= number_format($item['PRICE'], 2) ?>
          </span>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="section-subtitle" style="grid-column: 1 / -1; margin-top: 1rem;">
      No personalized items yet. Complete your
      <a href="#features" style="text-decoration: underline;">color and body shape analysis</a>
      to unlock outfit recommendations.
    </p>
  <?php endif; ?>
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
<div class="overlay" id="userProfileOverlay" data-keep-open="<?= $keepProfileOpen ? '1' : '0' ?>">

  <div class="modal">
    <!-- HEADER -->
    <div class="modal-header">
      <!-- LEFT: Avatar + name + username -->
      <div class="header-main">
        <div class="header-avatar">
          <?php if (!empty($user['PROFILE_IMAGE']) && file_exists("uploads/profile_images/" . $user['PROFILE_IMAGE'])): ?>
            <img src="<?= htmlspecialchars($profileImagePath) ?>" alt="Profile">
          <?php else: ?>
            <div class="header-initials"><?= htmlspecialchars($userInitials) ?></div>
          <?php endif; ?>
        </div>

        <div class="header-text">
          <h3 class="header-name">
            <?= htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?>
          </h3>
          <span class="header-username">@<?= htmlspecialchars($user['USERNAME']); ?></span>
        </div>
      </div>

      <!-- RIGHT: View orders + close -->
      <div class="header-right">
        <button type="button" class="btn-view-orders-header" onclick="toggleViews('purchases')">
          View Orders
        </button>

        <button class="close-btn" onclick="closeUserProfile()">
          <i class="bi bi-x-circle"></i>
        </button>
      </div>

      <!-- Hidden file input for avatar change -->
      <input
        type="file"
        id="profile_image"
        name="profile_image"
        accept="image/*"
        style="display:none;"
      >
    </div>

    <!-- BODY -->
    <div class="modal-body">

      <!-- ACCOUNT VIEW -->
      <div id="account-view">
        <form method="POST" action="" enctype="multipart/form-data" class="profile-form" id="profileForm">

          <!-- USERNAME -->
          <div class="form-row">
            <div class="input-wrapper">
              <label>Username</label>
              <div class="username-display">
                <span class="url-domain"><i class="bi bi-person"></i></span>
                <input
                  type="text"
                  name="username"
                  id="username"
                  value="<?= htmlspecialchars($user['USERNAME']); ?>"
                  required
                  disabled
                >
              </div>
            </div>
          </div>

          <!-- NAMES -->
          <div class="form-grid">
            <div class="form-group">
              <label>First name</label>
              <input
                type="text"
                name="first_name"
                id="first_name"
                value="<?= htmlspecialchars($user['FIRST_NAME']); ?>"
                required
                disabled
              >
            </div>
            <div class="form-group">
              <label>Last name</label>
              <input
                type="text"
                name="last_name"
                id="last_name"
                value="<?= htmlspecialchars($user['LAST_NAME']); ?>"
                required
                disabled
              >
            </div>
          </div>

          <!-- EMAIL -->
          <div class="form-group">
            <label>Email address</label>
            <div class="input-with-icon">
              <i class="bi bi-envelope"></i>
              <input
                type="email"
                name="email"
                id="email"
                value="<?= htmlspecialchars($user['EMAIL']); ?>"
                required
                disabled
              >
            </div>
          </div>

          <!-- ADDRESS BLOCK -->
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

          <!-- PROVINCE | CITY -->
          <div class="address-grid">
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
          </div>

          <!-- BARANGAY | POSTAL CODE -->
          <div class="address-grid">
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
          </div>

          <div class="form-group">
            <label>Phone Number</label>
            <input type="text"
                  name="contacts"
                  id="contacts"
                  value="<?= htmlspecialchars($user['CONTACTS']); ?>"
                  disabled>
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

            <!-- Edit button below -->
          <div class="edit-row">
            <button type="button" class="btn-edit-profile" onclick="enableEditing(this)">
              Edit Profile
            </button>
          </div>

          <!-- FOOTER (only visible in edit mode) -->
          <div class="modal-footer" id="profileFooter">
            <button type="button" class="btn-cancel" onclick="cancelEditing()">Cancel</button>
            <button
              type="submit"
              name="update_profile"
              class="btn-save"
              id="saveProfileBtn"
              disabled
            >
              Save changes
            </button>
          </div>
        </form>
      </div>

      <!-- PURCHASES VIEW -->
      <div id="purchases-view" style="display: none;">
        <div class="view-header">
          <button type="button" class="btn-back" onclick="toggleViews('account')">
            <i class="bi bi-arrow-left"></i> Back to Account
          </button>
        </div>

        <!-- SUB TABS -->
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
        </div>

        <!-- SUB TAB CONTENT: HISTORY -->
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
        </div>
      </div>

    </div> <!-- /modal-body -->
  </div> <!-- /modal -->
</div> <!-- /overlay -->



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

<script>
  document.addEventListener("DOMContentLoaded", () => {
  // ============================================
  // USER PROFILE OVERLAY INITIAL STATE
  // ============================================
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
    const shouldKeepOpen = userProfileOverlay.dataset.keepOpen === '1';

    if (shouldKeepOpen) {
      userProfileOverlay.style.display = 'flex';
      userProfileOverlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    } else {
      userProfileOverlay.style.display = 'none';
      userProfileOverlay.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  // ============================================
  // SIDEBAR FUNCTIONALITY
  // ============================================
  const sidebar = document.getElementById('appSidebar');
  const logoutBtn = document.querySelector('.logout-btn');

  if (sidebar) {
    sidebar.classList.add('collapsed');
    document.body.classList.add('collapsed-layout');
    toggleSidebarTopAndFooter(false);

    sidebar.addEventListener('mouseenter', () => {
      sidebar.classList.remove('collapsed');
      document.body.classList.remove('collapsed-layout');
      toggleSidebarTopAndFooter(true);
    });

    sidebar.addEventListener('mouseleave', () => {
      sidebar.classList.add('collapsed');
      document.body.classList.add('collapsed-layout');
      toggleSidebarTopAndFooter(false);
    });
  }

  function toggleSidebarTopAndFooter(show) {
    if (logoutBtn) logoutBtn.style.display = show ? 'block' : 'none';
  }

  // ============================================
  // NAVIGATION FUNCTIONALITY
  // ============================================
  const navLinks = Array.from(document.querySelectorAll('.nav-btn'));
  const sections = Array.from(document.querySelectorAll('.content-section'));
  const mainContent = document.querySelector('.main-content');

  function navigateToSection(sectionId) {
    const target = document.querySelector(sectionId);
    if (!target) return;

    navLinks.forEach(l => l.classList.remove('active'));
    const correspondingLink = document.querySelector(`a[href="${sectionId}"]`);
    if (correspondingLink) correspondingLink.classList.add('active');

    sections.forEach(s => s.classList.remove('active'));
    target.classList.add('active');

    if (mainContent) {
      mainContent.scrollTo({ top: target.offsetTop, behavior: 'smooth' });
    }
    history.replaceState(null, '', sectionId);
  }

  navLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      navigateToSection(link.getAttribute('href'));
    });
  });

  const hash = window.location.hash;
  if (hash && (hash === '#catalog-shop' || hash === '#features')) {
    navigateToSection(hash);
  } else {
    navigateToSection('#catalog-shop');
  }

  // ============================================
  // RECOMMENDATIONS VISIBILITY
  // ============================================
  const recommendationsSection = document.getElementById("recommendations");
  const colorResults = document.getElementById("colorSeasons");
  const bodyResults = document.getElementById("bodyShapes");

  const hasColor = colorResults && colorResults.classList.contains('show-results');
  const hasBody = bodyResults && bodyResults.classList.contains('show-results');

  if (recommendationsSection) {
    recommendationsSection.style.display = (hasColor || hasBody) ? "block" : "none";
  }

  // ============================================
  // CART FUNCTIONALITY
  // ============================================
  let cartCount = 0;
  const cartCountElement = document.querySelector('.cart-count');
  const addToCartButtons = document.querySelectorAll('.add-to-cart');

  addToCartButtons.forEach(button => {
    button.addEventListener('click', () => {
      cartCount++;
      if (cartCountElement) cartCountElement.textContent = cartCount;
      button.classList.add('added');
      setTimeout(() => button.classList.remove('added'), 1000);
    });
  });

  // ============================================
  // LOGOUT OVERLAY HANDLERS
  // ============================================
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.addEventListener('click', function (event) {
      if (event.target === logoutOverlay) closeLogoutOverlay();
    });
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      const overlay = document.getElementById('logoutOverlay');
      if (overlay && overlay.style.display === 'flex') closeLogoutOverlay();
    }
  });

  // ============================================
  // USER PROFILE MODAL EVENTS
  // ============================================
  if (userProfileOverlay) {
    userProfileOverlay.addEventListener('click', function (e) {
      if (e.target === this) closeUserProfile();
    });
  }

  // ============================================
  // TABS (Orders/History)
  // ============================================
  const subTabBtns = document.querySelectorAll('.sub-tab-btn');
  const subTabContents = document.querySelectorAll('.sub-tab-content');

  subTabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetSubTab = btn.getAttribute('data-subtab');
      
      // Update buttons
      subTabBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      // Update content
      subTabContents.forEach(content => {
        content.classList.remove('active'); // Hide all
        if (content.id === targetSubTab) {
            content.classList.add('active'); // Show matching
        }
      });
    });
  });

  // ============================================
  // PROFILE IMAGE PREVIEW & CLICK
  // ============================================
  const fileInput = document.getElementById('profile_image');
  const headerAvatar = document.querySelector('.header-avatar');
  const sidebarPic = document.getElementById('sidebar-profile-pic');

  // Handle clicking the avatar ONLY when editing
  if(headerAvatar && fileInput) {
      headerAvatar.addEventListener('click', () => {
          if(headerAvatar.classList.contains('editing')) {
              fileInput.click();
          }
      });
  }

  if (fileInput) {
    fileInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          // Update Modal Image
          const imgInsideAvatar = headerAvatar.querySelector('img');
          if (imgInsideAvatar) {
             imgInsideAvatar.src = e.target.result;
          } else {
             // If it was initials, replace with img
             headerAvatar.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
          }
          
          // Update Sidebar Image
          if (sidebarPic) {
            sidebarPic.innerHTML = `<img src="${e.target.result}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // ============================================
  // CONTACT & POSTAL FORMATTING
  // ============================================
  const contactInput = document.getElementById("contacts");
  if (contactInput) {
    contactInput.addEventListener("input", function () {
      let value = this.value.replace(/[^0-9]/g, "");
      if (value === "") value = "0";
      if (value[0] !== "0") value = "0" + value;
      this.value = value;
    });
  }

  const postalInput = document.getElementById("postal_code");
  if (postalInput) {
    postalInput.addEventListener("input", function () {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  }

  // ============================================
  // ADDRESS DATA & CASCADING DROPDOWNS
  // ============================================
  const addressData = {
    "Metro Manila": {
      "Quezon City": ["Commonwealth", "Fairview", "Batasan Hills"],
      "Manila": ["Barangay 1", "Barangay 2", "Barangay 3"],
      "Pasig": ["Rosario", "Ugong", "Manggahan", "Pinagbuhatan", "Malinao"]
    },
    "Cavite": {
      "Bacoor": ["Talaba", "Zapote", "Molino 1", "Molino 2"]
    },
    "Laguna": {
      "Calamba": ["Canlubang", "Real", "Lingga"]
    },
    "Bulacan": {
      "Malolos": ["Tikay", "Mojon", "San Agustin"]
    },
    "Rizal": {
      "Antipolo": ["San Roque", "Dalig", "Cupang"]
    }
  };

  const province = document.getElementById("province");
  const city = document.getElementById("city");
  const barangay = document.getElementById("barangay");

  // Pre-populate logic
  if (province && city && barangay) {
    const currentProvince = province.dataset.currentProvince || "";
    const currentCity = city.dataset.currentCity || "";
    const currentBarangay = barangay.dataset.currentBarangay || "";

    if (currentProvince && addressData[currentProvince]) {
      province.value = currentProvince;

      // Populate Cities
      city.innerHTML = `<option value="" disabled>Select City</option>`;
      Object.keys(addressData[currentProvince]).forEach(c => {
        const opt = document.createElement("option");
        opt.value = c;
        opt.textContent = c;
        city.appendChild(opt);
      });
      if (currentCity) city.value = currentCity;

      // Populate Barangays
      if (currentCity && addressData[currentProvince][currentCity]) {
        barangay.innerHTML = `<option value="" disabled>Select Barangay</option>`;
        addressData[currentProvince][currentCity].forEach(brgy => {
          const opt = document.createElement("option");
          opt.value = brgy;
          opt.textContent = brgy;
          barangay.appendChild(opt);
        });
        if (currentBarangay) barangay.value = currentBarangay;
      }
    }
  }

  function validateSelect(selectEl) {
    if (!selectEl) return true;
    const wrapper = selectEl.closest(".address-group");
    if (!wrapper) return true;

    if (!selectEl.value) {
      wrapper.classList.add("has-error");
      return false;
    } else {
      wrapper.classList.remove("has-error");
      return true;
    }
  }

  if (province) {
    province.addEventListener("change", () => {
      if (!city || !barangay) return;
      city.innerHTML = `<option value="" disabled selected>Select City</option>`;
      barangay.innerHTML = `<option value="" disabled selected>Select Barangay</option>`;
      
      const selectedProv = province.value;
      if (addressData[selectedProv]) {
        Object.keys(addressData[selectedProv]).forEach(c => {
          const opt = document.createElement("option");
          opt.value = c;
          opt.textContent = c;
          city.appendChild(opt);
        });
      }
      validateSelect(province);
    });
  }

  if (city) {
    city.addEventListener("change", () => {
      if (!province || !barangay) return;
      barangay.innerHTML = `<option value="" disabled selected>Select Barangay</option>`;
      
      const selectedProv = province.value;
      const selectedCity = city.value;
      if (addressData[selectedProv] && addressData[selectedProv][selectedCity]) {
        addressData[selectedProv][selectedCity].forEach(brgy => {
          const opt = document.createElement("option");
          opt.value = brgy;
          opt.textContent = brgy;
          barangay.appendChild(opt);
        });
      }
      validateSelect(city);
    });
  }

  if (barangay) {
    barangay.addEventListener("change", () => validateSelect(barangay));
  }

  // Validate on submit
  const profileFormEl = document.getElementById("profileForm");
  if (profileFormEl) {
    profileFormEl.addEventListener("submit", function (e) {
      const ok1 = validateSelect(province);
      const ok2 = validateSelect(city);
      const ok3 = validateSelect(barangay);
      if (!ok1 || !ok2 || !ok3) e.preventDefault();
    });
  }
});

// ============================================
// GLOBAL FUNCTIONS (Necessary for inline onClick)
// ============================================

// 1. EDIT PROFILE LOGIC
let originalProfileValues = {};

window.enableEditing = function(btn) {
    const form = document.getElementById('profileForm');
    if (!form) return;

    // Select inputs to enable
    const fields = form.querySelectorAll('input:not([type="hidden"]):not(#profile_image), select');
    const saveBtn = document.getElementById('saveProfileBtn');
    const footer = document.getElementById('profileFooter');
    const editRow = document.querySelector('.edit-row');
    const headerAvatar = document.querySelector('.header-avatar');

    // Store original values and enable fields
    originalProfileValues = {};
    fields.forEach(el => {
        if (el.id) originalProfileValues[el.id] = el.value;
        el.disabled = false;
        el.style.borderColor = '#A68763';
    });

    // Toggle UI visibility
    if (editRow) editRow.style.display = 'none'; // Hide "Edit Profile" button
    if (footer) footer.style.display = 'flex'; // Show Save/Cancel buttons
    if (saveBtn) saveBtn.disabled = false;
    
    // Allow Avatar editing
    if (headerAvatar) headerAvatar.classList.add('editing');
};

window.cancelEditing = function() {
    const form = document.getElementById('profileForm');
    if (!form) return;

    const fields = form.querySelectorAll('input:not([type="hidden"]):not(#profile_image), select');
    const saveBtn = document.getElementById('saveProfileBtn');
    const footer = document.getElementById('profileFooter');
    const editRow = document.querySelector('.edit-row');
    const headerAvatar = document.querySelector('.header-avatar');

    // Restore values and disable fields
    fields.forEach(el => {
        if (el.id && originalProfileValues.hasOwnProperty(el.id)) {
            el.value = originalProfileValues[el.id];
        }
        el.disabled = true;
        el.style.borderColor = '';
    });

    // Reset file input
    const fileInput = document.getElementById('profile_image');
    if (fileInput) fileInput.value = '';

    // Toggle UI visibility
    if (editRow) editRow.style.display = 'flex';
    if (footer) footer.style.display = 'none';
    if (saveBtn) saveBtn.disabled = true;

    // Disable Avatar editing
    if (headerAvatar) headerAvatar.classList.remove('editing');
};


// 2. TOGGLE VIEWS (Purchases vs Account)
window.toggleViews = function(viewName) {
    const accountView = document.getElementById('account-view');
    const purchasesView = document.getElementById('purchases-view');
    
    if(!accountView || !purchasesView) return;

    if (viewName === 'purchases') {
        accountView.style.display = 'none';
        purchasesView.style.display = 'block';
    } else {
        accountView.style.display = 'block';
        purchasesView.style.display = 'none';
    }
};

// 3. OTHER MODAL FUNCTIONS
window.openUserProfile = function () {
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
    userProfileOverlay.style.display = 'flex';
    userProfileOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
};

window.closeUserProfile = function () {
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
    userProfileOverlay.style.display = 'none';
    userProfileOverlay.classList.remove('show');
    document.body.style.overflow = '';
    // Optional: Reset view to account when closing
    toggleViews('account');
    cancelEditing();
  }
};

window.goToFeatures = function () {
  closeUserProfile();
  // We use the navigateToSection logic via hash or manually
  const featuresLink = document.querySelector('a[href="#features"]');
  if(featuresLink) featuresLink.click();
};

window.toggleAnalysis = function (targetId, show) {
  const targetElement = document.getElementById(targetId);
  if (!targetElement) return;
  const card = targetElement.closest('.option-card');
  const uploadSection = card.querySelector('.upload-section');
  const featureList = card.querySelector('.feature-list');
  if (show) {
    targetElement.classList.add('show-results');
    if (uploadSection) uploadSection.classList.add('hidden');
    if (featureList) featureList.classList.add('hidden');
    targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } else {
    targetElement.classList.remove('show-results');
    if (uploadSection) uploadSection.classList.remove('hidden');
    if (featureList) featureList.classList.remove('hidden');
  }
};

window.simulateAnalysis = function (targetId) {
  const button = event.target;
  button.disabled = true;
  const originalText = button.textContent;
  button.textContent = "Analyzing...";
  setTimeout(() => {
    button.disabled = false;
    button.textContent = originalText;
    window.toggleAnalysis(targetId, true);
  }, 1500);
};

window.filterProducts = function () {
  const searchInput = document.getElementById('productSearch');
  const categorySelect = document.getElementById('categoryFilter');

  const query = searchInput ? searchInput.value.toLowerCase() : '';
  const category = categorySelect ? categorySelect.value : '';

  document.querySelectorAll('.catalog-item').forEach(item => {
    const titleEl = item.querySelector('.title');
    const title = titleEl ? titleEl.textContent.toLowerCase() : '';
    const itemCategory = item.dataset.category || '';

    const matchesName = !query || title.includes(query);
    const matchesCategory = !category || itemCategory === category;

    item.style.display = (matchesName && matchesCategory) ? 'block' : 'none';
  });
};

window.logout = function () {
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.style.cssText = `display: flex !important; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.7); z-index: 999999; align-items: center; justify-content: center;`;
    document.body.style.overflow = 'hidden';
  }
};

window.closeLogoutOverlay = function () {
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.style.display = 'none';
    document.body.style.overflow = '';
  }
};

window.confirmLogout = function () {
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.innerHTML = `<div class="modal" style="max-width: 400px; background:white; padding:2rem; border-radius:15px; text-align:center;">Logging out...</div>`;
    setTimeout(() => {
      window.location.href = "index.php?page=login&action=logout";
    }, 800);
  }
};
</script>


<link rel="stylesheet" href="public/css/cartModal.css">
<script>
  // base64("pk_test_xxx:") — encode on the server
  window.PAYMONGO_PUBLIC_KEY_B64 = "<?= base64_encode($_ENV['PAYMONGO_PUBLIC_KEY'] . ':') ?>";
</script>
<script src="public/js/cartModal.js"></script>
<script src="https://js.paymongo.com/v1/paymongo.js"></script>




</body>
</html>