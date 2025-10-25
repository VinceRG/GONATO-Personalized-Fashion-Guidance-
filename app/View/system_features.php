<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>System Features</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
  />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap");

:root {
  --font-family: 'Lexend', sans-serif;
  --font-serif: 'Minion', 'Times New Roman', serif;
  --color-primary: #D7C9AE;
  --color-primary-light: #eae0d2;
  --color-accent: #A68763;
  --color-accent-dark: #2D2D2D;
  --spacing-xsmall: 0.5rem;
  --spacing-small: 1rem;
  --spacing-medium: 1.5rem;
  --spacing-large: 2rem;
  --spacing-xlarge: 3rem;
  --shadow-soft: 0 2px 20px rgba(0, 0, 0, 0.04);
  --shadow-medium: 0 4px 30px rgba(0, 0, 0, 0.08);
  --shadow-elegant: 0 8px 40px rgba(0, 0, 0, 0.12);
  --radius-small: 0.5rem;
  --radius-medium: 0.75rem;
  --radius-xlarge: 1.5rem;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: var(--font-family);
  background: #fafafa;
  padding: var(--spacing-xlarge) var(--spacing-large);
}

.results-container {
  display: flex;
  min-height: 100vh;
  position: relative;
}

/* SIDEBAR */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 280px;
  height: 100vh;
  background: rgba(255, 252, 252, 0.1);
  backdrop-filter: blur(20px);
  border-right: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  border-radius: 0 1.5rem 1.5rem 0;
  padding: 2rem 1.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  z-index: 100;
}

.profile {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.profile-pic {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #d7c9ae;
}

.nav-links {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.nav-links a {
  text-decoration: none;
  color: var(--color-accent-dark);
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0.9rem;
  border-radius: 0.75rem;
  transition: all 0.3s;
}

.nav-links a:hover,
.nav-links a.active {
  background: var(--color-accent-dark);
  color: white;
}

/* MAIN CONTENT */
.main-content {
  flex: 1;
  margin-left: 300px;
  padding: var(--spacing-xlarge) var(--spacing-large);
  min-height: 100vh;
  background: rgba(255, 252, 252, 0.1);
  backdrop-filter: blur(20px);
  border-radius: 0 1.5rem 1.5rem 0;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
}

.content-section {
  display: none;
  animation: fadeIn 0.4s ease-in;
}

.content-section.active {
  display: block;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* TEXT STYLES */

#overview #catalog-shop {
  padding: 3rem 3rem;
}
.section-title {
  font-family: var(--font-serif);
  font-weight: 900;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  background: -webkit-linear-gradient(#A68763, #2D2D2D);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  color: #666;
  margin-bottom: 2rem;
}


.btn {
  background-color: var(--color-accent-dark);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 0.95rem;
  cursor: pointer;
  transition: 0.3s ease;
}

.btn:hover {
  background-color: var(--color-accent);
  transform: scale(1.03);
}

.action-buttons {
  display: flex;
  gap: 12px;
  justify-content: center;
  margin-bottom: 2rem;
}

/* CARD LAYOUT */
.options-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.option-card {
  background: #fff;
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: var(--shadow-soft);
  transition: all 0.4s ease;
  display: flex;
  flex-direction: column;
  gap: 1.8rem;
  position: relative;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.option-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
}

.option-icon {
  font-size: 2.5rem;
  color: var(--color-accent);
  display: flex;
  align-items: center;
  height: 40px;
}

.option-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--color-accent-dark);
  margin: 0;
}

.option-card > p {
  color: #666;
  font-size: 0.95rem;
  line-height: 1.5;
  margin: -1rem 0 0 0;
}

.upload-section {
  display: flex;
  gap: 1rem;
  margin: 0.5rem 0;
}

.upload-section .btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  background: var(--color-accent-dark);
  color: white;
  border-radius: 12px;
  font-size: 0.95rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.upload-section .btn:hover {
  background: var(--color-accent);
  transform: translateY(-2px);
  box-shadow: var(--shadow-medium);
}

.upload-section .btn i {
  font-size: 1.1rem;
}

.feature-list {
  margin: 0;
  padding: 0;
}

.feature-list li {
  list-style: none;
  padding-left: 1.25rem;
  position: relative;
  color: #555;
  font-size: 0.9rem;
  line-height: 1.6;
  margin-bottom: 0.75rem;
}

.feature-list li::before {
  content: "✓";
  position: absolute;
  left: 0;
  color: var(--color-accent);
  font-size: 1rem;
  font-weight: bold;
}

.feature-btn {
  background: var(--color-accent-dark);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 1rem 1.5rem;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: auto;
}

.feature-btn:hover {
  background: var(--color-accent);
  transform: translateY(-2px);
  box-shadow: var(--shadow-medium);
}

.feature-btn i {
  transition: transform 0.3s ease;
}

.feature-btn:hover i {
  transform: translateX(4px);
}

/* Enhance option card hover state */
.option-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-elegant);
  border-color: var(--color-accent);
}

.option-card:hover .option-icon {
  color: var(--color-accent-dark);
  transform: scale(1.1);
}

/* SHOP GRID */

#catalog-shop {
  width: 100%;
  max-width: 100%;
  padding: 0;
  margin: 0;
}

.shop-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 2rem;
  position: relative;
}

.cart-button {
  position: relative;
  background: var(--color-accent-dark);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 1rem;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.cart-button:hover {
  background: var(--color-accent);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.cart-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--color-primary);
  color: var(--color-accent-dark);
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 0.9rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
}

.content-section.active {
  display: block;
  animation: fadeIn 0.4s ease;
}


.clothes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2.5rem;
  padding: 1.5rem;
}

.clothes-item {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  position: relative;
  width: 100%;
  height: 500px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.clothes-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.clothes-item img {
  width: 100%;
  height: 400px;
  object-fit: cover;
}

.clothes-caption {
  padding: 1.5rem;
  text-align: left;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  flex: 1;
}

.clothes-caption .title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--color-accent-dark);
}

.clothes-caption .price {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--color-accent);
}

.add-to-cart {
  position: absolute;
  top: 20px;
  right: 20px;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
}

.add-to-cart i {
  font-size: 1.5rem;
  color: var(--color-accent-dark);
}

.add-to-cart:hover {
  background: var(--color-primary);
  transform: scale(1.1);
  box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

.add-to-cart:hover i {
  color: var(--color-accent-dark);
}

.add-to-cart.added {
  background: var(--color-accent);
  transform: scale(1.1);
}

.add-to-cart.added i {
  color: white;
}

@media (max-width: 1024px) {
  .sidebar {
    width: 240px;
  }
  
  .main-content {
    margin-left: 240px;
  }
  
  .options-container {
    padding: 0 1rem;
  }
}

@media (max-width: 768px) {
  .results-container {
    flex-direction: column;
  }
  
  .sidebar {
    position: static;
    width: 100%;
    height: auto;
    border-radius: 0;
  }
  
  .main-content {
    margin-left: 0;
    border-radius: 0;
  }
  
  .option-card {
    flex: 1 1 100%;
    min-width: 100%;
  }
  
  .clothes-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
  }
  
  .section-title {
    font-size: 2rem;
  }
}
  </style>
</head>

<body>
  <div class="results-container">
    <!-- SIDEBAR -->
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
          <a href="#catalog-shop" class="nav-btn"><i class="bi bi-basket"></i> Shop</a>
          <a href="#features" class="nav-btn active"><i class="bi bi-house"></i> Features</a>

        </nav>
      </div>

      
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
       <!-- SHOP SECTION -->
          <section id="catalog-shop" class="content-section">
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
                  <img src="https://via.placeholder.com/400x300" alt="Outfit 1" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Rustic Wrap Dress</div>
                    <div class="price">$79</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="https://via.placeholder.com/400x300" alt="Outfit 2" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Sage Linen Blouse</div>
                    <div class="price">$49</div>
                  </div>
                </div>

                <div class="clothes-item">
                  <img src="https://via.placeholder.com/400x300" alt="Outfit 3" />
                  <button class="add-to-cart"><i class="bi bi-cart-plus"></i></button>
                  <div class="clothes-caption">
                    <div class="title">Midnight Tailored Coat</div>
                    <div class="price">$129</div>
                  </div>
                </div>
              </div>
            </div>
          </section>

      <!-- FEATURES SECTION -->
      <section id="features" class="content-section active">
        <div class="section-title"><i>Personalized Fashion Features</i></div>
        <div class="section-subtitle">Discover our advanced tools designed to enhance your style journey</div>

        <div class="options-container">
          <!-- COLOR ANALYSIS CARD -->
          <div class="option-card">
            <div class="option-icon"><i class="bi bi-palette2"></i></div>
            <div>
              <h2 class="option-title">Color Analysis</h2>
              <p>Choose how you'd like to proceed with your color analysis</p>
            </div>

            <div class="upload-section">
              <button class="btn"><i class="bi bi-camera"></i> Use Camera</button>
              <button class="btn"><i class="bi bi-upload"></i> Upload Image</button>
            </div>

            <ul class="feature-list">
              <li>Real-time guidance and lighting tips</li>
              <li>Instant capture and analysis</li>
              <li>Personalized seasonal color palette</li>
              <li>Complementary color recommendations</li>
            </ul>
          </div>

          <!-- BODY SHAPE CARD -->
          <div class="option-card">
            <div class="option-icon"><i class="bi bi-person-standing"></i></div>
            <div>
              <h2 class="option-title">Body Shape Analysis</h2>
              <p>Get accurate measurements for personalized style recommendations</p>
            </div>

            <div class="upload-section">
              <button class="btn"><i class="bi bi-camera"></i> Use Camera</button>
              <button class="btn"><i class="bi bi-upload"></i> Upload Image</button>
            </div>

            <ul class="feature-list">
              <li>AI-assisted body recognition</li>
              <li>Measurement-based style insights</li>
              <li>Shape-specific outfit recommendations</li>
              <li>Personalized fit suggestions</li>
            </ul>
          </div>
      </section>  
    </div>
  </div>

  <script>
    // Improved sidebar navigation logic: smooth scroll + active-link syncing using IntersectionObserver
    const navLinks = Array.from(document.querySelectorAll('.nav-btn'));
    const sections = Array.from(document.querySelectorAll('.content-section'));

    // Smooth scroll on click and update hash without jumping
    navLinks.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        const targetSelector = link.getAttribute('href');
        const target = document.querySelector(targetSelector);
        if (!target) return;

        // Close any open active states and set clicked link active immediately
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        // Activate target section visually
        sections.forEach(s => s.classList.remove('active'));
        target.classList.add('active');

        // Smooth scroll to the top of the section within the main-content container
        const mainContent = document.querySelector('.main-content');
        const targetTop = target.getBoundingClientRect().top - mainContent.getBoundingClientRect().top + mainContent.scrollTop - 16;
        mainContent.scrollTo({ top: targetTop, behavior: 'smooth' });

        // Update the URL hash without page jump
        history.replaceState(null, '', targetSelector);
      });
    });

    // IntersectionObserver to keep sidebar links in sync while scrolling
    const mainContent = document.querySelector('.main-content');
    const observerOptions = {
      root: mainContent,
      rootMargin: '0px 0px -60% 0px', // trigger when section is mostly in view
      threshold: 0
    };

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        const id = '#' + entry.target.id;
        const correspondingLink = document.querySelector(`.nav-links a[href="${id}"]`);
        if (entry.isIntersecting) {
          // set active styles
          navLinks.forEach(l => l.classList.remove('active'));
          if (correspondingLink) correspondingLink.classList.add('active');

          // ensure only one section has .active class
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
  </script>
</body>
</html>
