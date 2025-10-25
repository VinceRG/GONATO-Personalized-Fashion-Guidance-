<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>System Features</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"/>
  <link rel="stylesheet" href="public/system_features.css"/>

  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>
  <div class="results-container">
    <div class="sidebar">
      <div>
        <div class="profile">
          <div class="profile-pic"></div>
          <div class="profile-info">

          <div class="header">
        <a href="index.php?page=user_info" class="logo-link">
             <h4>Guest User</h4>
        </a>
    </div>
           
            <p><i>@username</i></p>
          </div>
        </div>

        <nav class="nav-links">
          <a href="#catalog-shop" class="nav-btn active"><i class="bi bi-basket"></i> Shop</a>
          <a href="#features" class="nav-btn"><i class="bi bi-house"></i> Features</a>
        </nav>
      </div>
    </div>

    <div class="main-content">
        <section id="catalog-shop" class="content-section active">
            <div class="shop-header">
              <div>
                <div class="section-title"><i>Welcome to your Style Catalog!</i></div>
                <div class="section-subtitle">Browse our collection tailored just for you.</div>
              </div>
              <button class="cart-button">
                <i class="bi bi-cart3"></i>
                <span class="cart-count">0</span>
              </button>
            </div>

            <div class="subsection">
              <h2 class="section-title" style="font-size:1.6rem; margin:0 0 1rem 0;">Recommendations</h2>
              <p class="section-subtitle" style="margin:0 0 1.25rem 0;">Based on your recent interactions, here are pieces we think you'll love. Tap any item to add it to your cart or view details.</p>
              <div class="clothes-grid">
                <div class="clothes-item">
                  <img src="source/hourglass/autumn/AIRism Cotton Flare Midi Dress brown.avif" alt="Outfit 1" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Rustic Wrap Dress</div>
                    <div class="price">$79</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="source/hourglass/Autumn/Smart Ankle Pants.avif" alt="Outfit 1" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Rustic Wrap Dress</div>
                    <div class="price">$79</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="source/hourglass/autumn/Souffle Yarn Dress olive.avif" alt="Outfit 1" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Rustic Wrap Dress</div>
                    <div class="price">$79</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="source/hourglass/spring/Cotton Ribbed Long-Sleeve Cropped Cardigan Olive.avif" alt="Outfit 1" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Rustic Wrap Dress</div>
                    <div class="price">$79</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="source/Inverted Triangle/winter/Rayon Long Sleeve Blouse dark brown.avif" alt="Outfit 2" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Sage Linen Blouse</div>
                    <div class="price">$49</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="source/Inverted Triangle/winter/Smart Wide Pants body.webp" alt="Outfit 2" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Sage Linen Blouse</div>
                    <div class="price">$49</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="source/Inverted Triangle/winter/Volume Sleeve Short Sleeve Dress black.jfif" alt="Outfit 2" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Sage Linen Blouse</div>
                    <div class="price">$49</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="source/hourglass/winter/Flare Dress.avif" alt="Outfit 3" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Midnight Tailored Coat</div>
                    <div class="price">$129</div>
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

              <!-- Hidden Color Analysis Result -->
              <div id="colorSeasons" class="color-seasons-container">
                <h3>Your Color Palette</h3>
                <p>Your best palette is <strong>Soft Autumn</strong>.</p>
                <p>Recommended tones: warm beige, muted green, soft coral.</p>
                <button class="close-btn" onclick="toggleAnalysis('colorSeasons', false)">Close Analysis</button>
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

              <!-- Hidden Body Shape Result -->
              <div id="bodyShapes" class="body-shapes-container">
                <h3>Your Body Shape</h3>
                <p>Your shape appears to be <strong>Hourglass</strong>.</p>
                <p>Suggested styles: wrap dresses, high-waist skirts, fitted tops.</p>
                <button class="close-btn" onclick="toggleAnalysis('bodyShapes', false)">Close Analysis</button>
              </div>
            </div>
          </div>
        </section>
    </div>
  </div>


  <script>
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

    const mainContent = document.querySelector('.main-content');
    const observerOptions = {
      root: mainContent,
      rootMargin: '0px 0px -60% 0px', 
      threshold: 0
    };

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        const id = '#' + entry.target.id;
        const correspondingLink = document.querySelector(`.nav-links a[href="${id}"]`);
        if (entry.isIntersecting) {
          navLinks.forEach(l => l.classList.remove('active'));
          if (correspondingLink) correspondingLink.classList.add('active');
          sections.forEach(s => s.classList.remove('active'));
          entry.target.classList.add('active');
        }
      });
    }, observerOptions);

    sections.forEach(s => observer.observe(s));

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

    /* ---- NEW ANALYSIS FUNCTIONALITY ---- */
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
  </script>
</body>
</html>