<?php
session_start();
require_once __DIR__ . '/../Model/features.php'; // ✅ Corrected path

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE USER_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Helper function to get profile image path
function getProfileImagePath($user) {
    if (!empty($user['PROFILE_IMAGE'])) {
        // Remove any query parameters first for file existence check
        $imagePath = "uploads/profile_images/" . $user['PROFILE_IMAGE'];
        
        // Check if file exists
        if (file_exists($imagePath)) {
            return $imagePath;
        } else {
            // Try alternative path
            $altPath = "../uploads/profile_images/" . $user['PROFILE_IMAGE'];
            if (file_exists($altPath)) {
                return $altPath;
            }
        }
    }
    return "assets/default-avatar.png";
}

// Helper function to get initials
function getInitials($user) {
    $first = !empty($user['FIRST_NAME']) ? substr($user['FIRST_NAME'], 0, 1) : '';
    $last = !empty($user['LAST_NAME']) ? substr($user['LAST_NAME'], 0, 1) : '';
    return strtoupper($first . $last);
}

$profileImagePath = getProfileImagePath($user);
$userInitials = getInitials($user);

// Debug: Check what's in the database
error_log("DEBUG - User PROFILE_IMAGE from DB: " . ($user['PROFILE_IMAGE'] ?? 'EMPTY'));
error_log("DEBUG - Profile Image Path: " . $profileImagePath);
error_log("DEBUG - File exists check: " . (file_exists($profileImagePath) ? 'YES' : 'NO'));

// Add cache-busting parameter only if it's not the default avatar
if (!empty($user['PROFILE_IMAGE']) && $user['PROFILE_IMAGE'] !== 'default-avatar.png') {
    $profileImagePath .= '?v=' . time();
}
?>

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
</head>

<body>
  <!-- Notification Messages -->
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

  <!-- Main Page -->
  <div class="results-container">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="appSidebar" aria-expanded="true">
      <div>
        <div class="profile" onclick="openUserProfile()">
          <div class="profile-pic" id="sidebar-profile-pic">
            <?php if (!empty($user['PROFILE_IMAGE'])): ?>
              <img src="<?= $profileImagePath ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
              <?= $userInitials ?>
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

    <!-- MAIN CONTENT -->
    <div class="main-content">
      <!-- 🛍️ SHOP SECTION -->
      <section id="catalog-shop" class="content-section active">
        <div class="shop-header">
          <div>
            <h2 class="section-title"><i>Shopping Catalog</i></h2>
            <p class="section-subtitle">Find your perfect outfit below or explore personalized picks.</p>
          </div>

          <div class="shop-controls">
            <button class="cart-button">
              <i class="bi bi-bag"></i>
              <span class="cart-count">0</span>
            </button>
          </div>
        </div>

        <!-- Recommendations Section -->
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

        <!-- Shop Catalog -->
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

            <div class="clothes-grid">
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

      <!-- ✨ FEATURES -->
      <section id="features" class="content-section">
        <div class="section-title"><i>Personalized Fashion Features</i></div>
        <div class="section-subtitle">Discover our advanced tools designed to enhance your style journey</div>

        <div class="options-container">
          <!-- Color Analysis -->
          <div class="option-card">
            <div class="option-icon"><i class="bi bi-palette2"></i></div>
            <div>
              <h2 class="option-title">Color Analysis</h2>
              <p>Choose how you'd like to proceed with your color analysis</p>
            </div>

            <div class="upload-section">
              <button class="btn" onclick="simulateAnalysis('colorSeasons')"><i class="bi bi-camera"></i> Use Camera</button>
              <button class="btn" onclick="simulateAnalysis('colorSeasons')"><i class="bi bi-upload"></i> Upload Image</button>
            </div>

            <ul class="feature-list">
              <li>Real-time guidance and lighting tips</li>
              <li>Instant capture and analysis</li>
              <li>Personalized seasonal color palette</li>
              <li>Complementary color recommendations</li>
            </ul>

            <div id="colorSeasons" class="color-seasons-container">
              <h3>Your Color Palette</h3>
              <p>Your best palette is <strong>Soft Autumn</strong>.</p>
              <p>Recommended tones: warm beige, muted green, soft coral.</p>
              <button class="close-btn-analysis" onclick="toggleAnalysis('colorSeasons', false)">Close Analysis</button>
            </div>
          </div>

          <div class="option-card">
            <div class="option-icon"><i class="bi bi-person-standing"></i></div>
            <div>
              <h2 class="option-title">Body Shape Analysis</h2>
              <p>Get accurate measurements for personalized style recommendations</p>
            </div>

            <div class="upload-section">
              <button class="btn" onclick="simulateAnalysis('bodyShapes')"><i class="bi bi-camera"></i> Use Camera</button>
              <button class="btn" onclick="simulateAnalysis('bodyShapes')"><i class="bi bi-upload"></i> Upload Image</button>
            </div>

            <ul class="feature-list">
              <li>AI-assisted body recognition</li>
              <li>Measurement-based style insights</li>
              <li>Shape-specific outfit recommendations</li>
              <li>Personalized fit suggestions</li>
            </ul>

            <div id="bodyShapes" class="body-shapes-container">
              <h3>Your Body Shape</h3>
              <p>Your shape appears to be <strong>Hourglass</strong>.</p>
              <p>Suggested styles: wrap dresses, high-waist skirts, fitted tops.</p>
              <button class="close-btn-analysis" onclick="toggleAnalysis('bodyShapes', false)">Close Analysis</button>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- 👤 User Profile Modal -->
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
        <!-- Account Tab -->
        <div class="tab-content active" id="account">
          <div class="section-header">
            <p class="section-title">Account Information</p>
          </div>

          <form method="POST" action="" enctype="multipart/form-data" class="profile-form" id="profileForm">
            <div class="profile-section">
              <div class="profile-avatar">
                <?php if (!empty($user['PROFILE_IMAGE']) && file_exists("uploads/profile_images/" . $user['PROFILE_IMAGE'])): ?>
                  <img id="avatar-img" src="<?= $profileImagePath ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                <?php else: ?>
                  <img id="avatar-img" src="<?= $profileImagePath ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: <?= empty($user['PROFILE_IMAGE']) ? 'none' : 'block' ?>;">
                  <div class="avatar-initials" style="<?= empty($user['PROFILE_IMAGE']) ? '' : 'display:none' ?>"><?= $userInitials ?></div>
                <?php endif; ?>
                <button type="button" class="edit-avatar-btn" id="edit-avatar-btn" title="Change Profile Picture" style="display:none;">
                  <i class="bi bi-camera"></i>
                </button>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display:none;">
              </div>

              <div class="profile-details">
                <h2 id="display-name"><?= htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?></h2>
                <div class="username" id="display-username">@<?= htmlspecialchars($user['USERNAME']); ?></div>
                <p class="profile-tagline">Welcome to Amarelle — your personal style space.</p>
              </div>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['FIRST_NAME']); ?>" disabled required>
              </div>
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['LAST_NAME']); ?>" disabled required>
              </div>
              <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['USERNAME']); ?>" disabled required>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['EMAIL']); ?>" disabled required>
              </div>
              <div class="form-group">
                <label>Home Address</label>
                <input type="text" name="address" id="address" value="<?= htmlspecialchars($user['ADDRESS']); ?>" disabled>
              </div>
              <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="contacts" id="contacts" value="<?= htmlspecialchars($user['CONTACTS']); ?>" disabled>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" id="editProfileBtn" class="btn btn-secondary">
                <i class="bi bi-pencil-square"></i> Edit Profile
              </button>
              <button type="submit" name="update_profile" id="saveProfileBtn" class="btn btn-primary" style="display:none;">
                <i class="bi bi-save"></i> Save Changes
              </button>
              <button type="button" id="cancelEditBtn" class="btn btn-outline" style="display:none;">
                <i class="bi bi-x-circle"></i> Cancel
              </button>
            </div>
          </form>

          <div class="info-card">
            <div class="info-card-header">
              <i class="bi bi-palette"></i>
              <h3>Style Profile</h3>
            </div>
            <?php if (empty($user['COLOR_SEASON']) && empty($user['BODY_SHAPE'])): ?>
              <div class="info-empty">
                <p>You haven't completed your style analysis yet.</p>
                <button class="btn" onclick="goToFeatures()">
                  <i class="bi bi-magic"></i> Start Style Analysis
                </button>
              </div>
            <?php else: ?>
              <?php if (!empty($user['COLOR_SEASON'])): ?>
                <div class="info-item">
                  <span class="info-label">Color Season</span>
                  <span class="info-value">
                    <?php echo htmlspecialchars($user['COLOR_SEASON']); ?>
                  </span>
                </div>
              <?php endif; ?>

              <?php if (!empty($user['BODY_SHAPE'])): ?>
                <div class="info-item">
                  <span class="info-label">Body Shape</span>
                  <span class="info-value">
                    <?php echo htmlspecialchars($user['BODY_SHAPE']); ?>
                  </span>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
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

  <script>
    // Navigation functionality
    const navLinks = Array.from(document.querySelectorAll('.nav-btn'));
    const sections = Array.from(document.querySelectorAll('.content-section'));

    navLinks.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        const targetSelector = link.getAttribute('href');
        const target = document.querySelector(targetSelector);
        if (!target) return;

        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        sections.forEach(s => s.classList.remove('active'));
        target.classList.add('active');

        const mainContent = document.querySelector('.main-content');
        const targetTop = target.offsetTop; 
        mainContent.scrollTo({ top: targetTop, behavior: 'smooth' });

        history.replaceState(null, '', targetSelector);
      });
    });

    // Cart functionality
    let cartCount = 0;
    const cartCountElement = document.querySelector('.cart-count');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
      button.addEventListener('click', () => {
        cartCount++;
        cartCountElement.textContent = cartCount;
        button.classList.add('added');
        setTimeout(() => button.classList.remove('added'), 1000);
      });
    });

    document.querySelector('.cart-button').addEventListener('click', () => {
      alert('Shopping cart feature coming soon!');
    });

    // Analysis functionality
    function toggleAnalysis(targetId, show) {
      const targetElement = document.getElementById(targetId);
      if (!targetElement) return;
      const card = targetElement.closest('.option-card');
      const uploadSection = card.querySelector('.upload-section');
      const featureList = card.querySelector('.feature-list');
      if (show) {
        targetElement.classList.add('show-results');
        uploadSection.classList.add('hidden');
        featureList.classList.add('hidden');
        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } else {
        targetElement.classList.remove('show-results');
        uploadSection.classList.remove('hidden');
        featureList.classList.remove('hidden');
      }
    }

    function simulateAnalysis(targetId) {
      const button = event.target;
      button.disabled = true;
      const originalText = button.textContent;
      button.textContent = "Analyzing...";
      setTimeout(() => {
        button.disabled = false;
        button.textContent = originalText;
        toggleAnalysis(targetId, true);
      }, 1500);
    }

    // User Profile Modal Functions
    function openUserProfile() {
      document.getElementById('userProfileOverlay').classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeUserProfile() {
      document.getElementById('userProfileOverlay').classList.remove('show');
      document.body.style.overflow = '';
    }

    function goToFeatures() {
      closeUserProfile();
      const featuresLink = document.querySelector('a[href="#features"]');
      if (featuresLink) {
        featuresLink.click();
      }
    }

    // Close modal when clicking outside
    document.getElementById('userProfileOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
        closeUserProfile();
      }
    });

    // Tab functionality for modal
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetTab = btn.getAttribute('data-tab');
        
        tabBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        tabContents.forEach(content => {
          content.classList.remove('active');
          if (content.id === targetTab) {
            content.classList.add('active');
          }
        });
      });
    });

    // Sub-tab functionality
    const subTabBtns = document.querySelectorAll('.sub-tab-btn');
    const subTabContents = document.querySelectorAll('.sub-tab-content');

    subTabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetSubTab = btn.getAttribute('data-subtab');
        
        subTabBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        subTabContents.forEach(content => {
          content.classList.remove('active');
          if (content.id === targetSubTab) {
            content.classList.add('active');
          }
        });
      });
    });

    // Profile image upload preview
    const fileInput = document.getElementById('profile_image');
    const avatarImg = document.getElementById('avatar-img');
    const editAvatarBtn = document.getElementById('edit-avatar-btn');

    editAvatarBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          avatarImg.src = e.target.result;
          // Also update sidebar profile pic if it has an image
          const sidebarPic = document.getElementById('sidebar-profile-pic');
          if (sidebarPic.querySelector('img')) {
            sidebarPic.querySelector('img').src = e.target.result;
          } else {
            // Replace initials with image
            sidebarPic.innerHTML = `<img src="${e.target.result}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
          }
        };
        reader.readAsDataURL(file);
      }
    });

    // Edit Profile functionality
    const editProfileBtn = document.getElementById('editProfileBtn');
    const saveProfileBtn = document.getElementById('saveProfileBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const formInputs = document.querySelectorAll('#profileForm input[type="text"], #profileForm input[type="email"]');
    
    // Store original values for cancel functionality
    let originalValues = {};

    editProfileBtn.addEventListener('click', () => {
      // Store original values
      formInputs.forEach(input => {
        originalValues[input.id] = input.value;
      });

      // Enable all inputs
      formInputs.forEach(input => {
        input.disabled = false;
      });

      // Show/hide buttons
      editProfileBtn.style.display = 'none';
      saveProfileBtn.style.display = 'inline-flex';
      cancelEditBtn.style.display = 'inline-flex';
      editAvatarBtn.style.display = 'block';

      // Add visual feedback
      formInputs.forEach(input => {
        input.style.borderColor = '#A68763';
      });
    });

    cancelEditBtn.addEventListener('click', () => {
      // Restore original values
      formInputs.forEach(input => {
        input.value = originalValues[input.id];
        input.disabled = true;
        input.style.borderColor = '';
      });

      // Reset file input
      fileInput.value = '';

      // Restore original avatar (reload from server)
      avatarImg.src = '<?= $profileImagePath ?>';
      
      // Restore sidebar pic
      const sidebarPic = document.getElementById('sidebar-profile-pic');
      <?php if (!empty($user['PROFILE_IMAGE'])): ?>
        if (sidebarPic.querySelector('img')) {
          sidebarPic.querySelector('img').src = '<?= $profileImagePath ?>';
        } else {
          sidebarPic.innerHTML = '<img src="<?= $profileImagePath ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
        }
      <?php else: ?>
        sidebarPic.innerHTML = '<?= $userInitials ?>';
      <?php endif; ?>

      // Show/hide buttons
      editProfileBtn.style.display = 'inline-flex';
      saveProfileBtn.style.display = 'none';
      cancelEditBtn.style.display = 'none';
      editAvatarBtn.style.display = 'none';
    });

    // Form submission handler to update displayed name
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      // Update display name in modal (for preview before page reload)
      const firstName = document.getElementById('first_name').value;
      const lastName = document.getElementById('last_name').value;
      const username = document.getElementById('username').value;
      
      document.getElementById('display-name').textContent = firstName + ' ' + lastName;
      document.getElementById('display-username').textContent = '@' + username;
      
      // Update sidebar name
      const sidebarName = document.querySelector('.profile-info .form-group');
      const sidebarUsername = document.querySelector('.profile-info p');
      if (sidebarName) sidebarName.textContent = firstName + ' ' + lastName;
      if (sidebarUsername) sidebarUsername.textContent = '@' + username;
    });

    // On page load, check if notification exists and close modal
    window.addEventListener('DOMContentLoaded', function() {
      const notification = document.querySelector('.notification');
      if (notification) {
        // Automatically close modal if notification is present
        setTimeout(() => {
          const overlay = document.getElementById('userProfileOverlay');
          if (overlay && overlay.classList.contains('show')) {
            closeUserProfile();
          }
        }, 100);
      }
    });

    function openUserProfile() {
  const overlay = document.getElementById("userProfileOverlay");
  if (overlay) {
    overlay.style.display = "flex";
    overlay.classList.add("show");
  }
}

function closeUserProfile() {
  const overlay = document.getElementById("userProfileOverlay");
  if (overlay) {
    overlay.style.display = "none";
    overlay.classList.remove("show");
  }
}
  </script>
</body>
</html>