document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById('appSidebar');
  const logoutBtn = document.querySelector('.logout-btn');
  const navLinks = document.querySelectorAll('.nav-links a');
  const sections = document.querySelectorAll('.content-section');
  const mainContent = document.querySelector('.main-content');
  const cartCountElement = document.querySelector('.cart-count');
  const addToCartButtons = document.querySelectorAll('.add-to-cart');
  const recommendationsSection = document.getElementById("recommendations");
  const catalogHeader = document.querySelector(".catalog-grid");
  const colorDone = localStorage.getItem("colorAnalysisDone") === "true";
  const bodyDone = localStorage.getItem("bodyAnalysisDone") === "true";

  /* --- Start collapsed --- */
  sidebar.classList.add('collapsed');
  document.body.classList.add('collapsed-layout');
  toggleSidebarTopAndFooter(false); // hide toggle + logout

  /* --- Expand sidebar on hover --- */
  sidebar.addEventListener('mouseenter', () => {
    sidebar.classList.remove('collapsed');
    document.body.classList.remove('collapsed-layout');
    toggleSidebarTopAndFooter(true);
  });

  /* --- Collapse sidebar on mouse leave --- */
  sidebar.addEventListener('mouseleave', () => {
    sidebar.classList.add('collapsed');
    document.body.classList.add('collapsed-layout');
    toggleSidebarTopAndFooter(false);
  });

  /* --- Helper: hide/show toggle and logout --- */
  function toggleSidebarTopAndFooter(show) {
    if (logoutBtn) logoutBtn.style.display = show ? 'block' : 'none';
  }

  /* --- Navigation logic --- */
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
      mainContent.scrollTo({ top: target.offsetTop, behavior: 'smooth' });
      history.replaceState(null, '', targetSelector);
    });
  });

  /* --- Cart --- */
  let cartCount = 0;
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

  /* --- Logout --- */
  window.logout = function () {
    if (confirm('Are you sure you want to logout?')) {
      window.location.href = 'logout.php';
    }
  };

  if (colorDone || bodyDone) {
    // show recommendations
    recommendationsSection.style.display = "block";
  } else {
    // hide recommendations, show catalog instead
    recommendationsSection.style.display = "none";
    window.location.hash = "#catalog-shop"; // optional: ensure user lands in shop
  }

  // Search bar filter functionality
  const searchInput = document.getElementById("shopSearch");
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const query = searchInput.value.toLowerCase();
      document.querySelectorAll(".catalog-item").forEach(item => {
        const title = item.querySelector(".title").textContent.toLowerCase();
        item.style.display = title.includes(query) ? "block" : "none";
      });
    });
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