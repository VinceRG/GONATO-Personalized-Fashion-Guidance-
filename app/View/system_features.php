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
  <div class="results-container">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="appSidebar" aria-expanded="true">
      <div>
        <div class="sidebar-header">
          <div class="profile" style="margin-top: .9rem;">
            <div class="profile-pic" aria-hidden="true"></div>
            <div class="profile-info">
              <div style="font-weight:700">
                <a href="index.php?page=user_info" class="logo-link">Guest User</a>
              </div>
              <small><i>@username</i></small>
            </div>
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
              <button class="close-btn" onclick="toggleAnalysis('colorSeasons', false)">Close Analysis</button>
            </div>
          </div>

          <!-- Body Shape Analysis -->
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
              <button class="close-btn" onclick="toggleAnalysis('bodyShapes', false)">Close Analysis</button>
            </div>
          </div>
        </div>
      </section>

     <section id="footer" class="footer-section">
        <div class="footer-container">
          <div class="footer-grid">
            <div class="footer-logo">
              <img class="logo" src="public/image/amarelle.png" alt="Amarelle" />
              <p class="tagline"><i>Fashion that understands you.</i></p>
            </div>

            <div>
              <p class="header-text">Company</p>
              <ul class="footer-company">
                <li><a href="#">About</a></li>
                <li><a href="#">Features</a></li>
              </ul>
            </div>

            <div>
              <p class="header-text">Help</p>
              <ul class="footer-help">
                <li><a href="#">Customer Support</a></li>
                <li><a href="#">Delivery Details</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
              </ul>
            </div>
          </div>

          <hr class="footer-divider">
          <p class="footer-license">© Copyright 2025, All Rights Reserved by Amarelle</p>
        </div>
      </section>
    </div>
  </div>
</body>
</html>
<?php include 'cartModal.php'; ?>
