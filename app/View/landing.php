


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">    
    <title>Amarelle - Your Personal Fashion Intelligence</title>
    <link rel="stylesheet" href="public/css/landing.css">


</head>
<body>
     <?php if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1'): ?>
        <div class="side-alert" id="logoutAlert">
            <i class="bi bi-check-circle"></i>
            <span>You have successfully logged out.</span>
        </div>

        <script>
            // Show animation after slight delay
            setTimeout(() => {
                document.getElementById('logoutAlert').classList.add('show');
            }, 200);

            // Auto-hide after 4 seconds
            setTimeout(() => {
                document.getElementById('logoutAlert').classList.remove('show');
            }, 4200);
        </script>
    <?php endif; ?>
    
    <nav>
        <div class="nav-brand">
            <img src="public/image/amarelle.png" alt="Amarelle Logo" class="brand-logo" style="height: 50px; width: auto; margin-right: 10px; vertical-align: middle;">
            Amarelle
        </div>
        <div class="nav-links">
            <div class="nav-auth">
                <a href="index.php?page=login" class="nav-login">Logins</a>
                <a href="index.php?page=register" class="nav-cta">Sign Up</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <h1>Fashion that <br> <span class="accent"> understands</span> you</h1>
        <p class="hero-description">
            Discover your best look with AI that understands your colors, shape, and personal style.
        </p>
        
        <p>
            <button class="discover-button" onclick="window.location.href='index.php?page=login'">Discover your Style</button>
        </p>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2 class="section-title">Intelligence that knows your style</h2>
            <p class="section-description">
                Experience AI that understands every layer of your style to find pieces that feel made for you.
            </p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-image">
                    <img src="public/image/coloranalysis.jpg" alt="Color Analysis">
                </div>
                <h3><b>Color Analysis</b></h3>
                <p>Advanced camera technology that reads your palette and finds pieces that naturally complement your tones.</p>
                <ul class="feature-list">
                    <li>Snap a photo or upload an image</li>
                    <li>Real-time color extraction and analysis</li>
                    <li>Complementary color and inventory matching</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-image">
                    <img src="public/image/bodyshape.jpg" alt="Body Shape Intelligence">
                </div>
                <h3><b>Body-Shape Intelligence</b></h3>
                <p>Computer vision that understands your proportions and recommends cuts that complement your silhouette.</p>
                <ul class="feature-list">
                    <li>Use your camera or upload a photo</li>
                    <li>Landmark detection and shape classification</li>
                    <li>Flattering cut and pattern recommendations</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-image">
                    <img src="public/image/clothes.jpg" alt="Smart Recommendations">
                </div>
                <h3><b>Smart Recommendations</b></h3>
                <p>Unified intelligence that combines color and fit data to curate outfits tailored exclusively to you.</p>
                <ul class="feature-list">
                    <li>Personalized outfit</li>
                    <li>Style-aware suggestions</li>
                    <li>Dynamic wardrobe curation</li>
                </ul>
            </div>
        </div>
    </section>

    <footer style="background: #1C1917; color: #E7E5E4; padding: 5rem 2rem; font-family: 'Lexend', sans-serif; width: 100%; text-align: center;">
        
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
    
</body>
</html>