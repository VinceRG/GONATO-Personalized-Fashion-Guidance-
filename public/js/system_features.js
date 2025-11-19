document.addEventListener("DOMContentLoaded", () => {
  // ============================================
  // CRITICAL: ENSURE OVERLAY IS HIDDEN ON LOAD
  // ============================================
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
    userProfileOverlay.style.display = 'none';
    userProfileOverlay.classList.remove('show');
    document.body.style.overflow = '';
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
  // NOTIFICATION HANDLING
  // ============================================
  const notification = document.querySelector('.notification');
  if (notification) {
    setTimeout(() => {
      if (userProfileOverlay && userProfileOverlay.classList.contains('show')) {
        closeUserProfile();
      }
    }, 100);
  }

  // ============================================
  // [UPDATED] RECOMMENDATIONS VISIBILITY
  // Checks if PHP has rendered the results visible
  // ============================================
  const recommendationsSection = document.getElementById("recommendations");
  const colorResults = document.getElementById("colorSeasons");
  const bodyResults = document.getElementById("bodyShapes");

  // Check if the containers have the 'show-results' class added by PHP
  const hasColor = colorResults && colorResults.classList.contains('show-results');
  const hasBody = bodyResults && bodyResults.classList.contains('show-results');

  if (recommendationsSection) {
    // Show recommendations if EITHER analysis is done
    if (hasColor || hasBody) {
      recommendationsSection.style.display = "block";
    } else {
      recommendationsSection.style.display = "none";
    }
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
  // SEARCH BAR FILTER
  // ============================================
  const searchInput = document.getElementById("shopSearch");
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const query = searchInput.value.toLowerCase();
      document.querySelectorAll(".catalog-item").forEach(item => {
        const titleElement = item.querySelector(".title");
        if (titleElement) {
          const title = titleElement.textContent.toLowerCase();
          item.style.display = title.includes(query) ? "block" : "none";
        }
      });
    });
  }

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
  // USER PROFILE MODAL
  // ============================================
  window.openUserProfile = function () {
    if (userProfileOverlay) {
      userProfileOverlay.style.display = 'flex';
      userProfileOverlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeUserProfile = function () {
    if (userProfileOverlay) {
      userProfileOverlay.style.display = 'none';
      userProfileOverlay.classList.remove('show');
      document.body.style.overflow = '';
    }
  };

  window.goToFeatures = function () {
    closeUserProfile();
    navigateToSection('#features');
  };

  if (userProfileOverlay) {
    userProfileOverlay.addEventListener('click', function (e) {
      if (e.target === this) closeUserProfile();
    });
  }

  // ============================================
  // TABS
  // ============================================
  const tabBtns = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetTab = btn.getAttribute('data-tab');
      tabBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      tabContents.forEach(content => {
        content.classList.remove('active');
        if (content.id === targetTab) content.classList.add('active');
      });
    });
  });

  const subTabBtns = document.querySelectorAll('.sub-tab-btn');
  const subTabContents = document.querySelectorAll('.sub-tab-content');

  subTabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetSubTab = btn.getAttribute('data-subtab');
      subTabBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      subTabContents.forEach(content => {
        content.classList.remove('active');
        if (content.id === targetSubTab) content.classList.add('active');
      });
    });
  });

  // ============================================
  // PROFILE IMAGE PREVIEW
  // ============================================
  const fileInput = document.getElementById('profile_image');
  const avatarImg = document.getElementById('avatar-img');
  const avatarInitials = document.querySelector('.profile-avatar .avatar-initials');
  const editAvatarBtn = document.getElementById('edit-avatar-btn');

  if (editAvatarBtn && fileInput) {
    editAvatarBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          if (avatarImg) {
            avatarImg.src = e.target.result;
            avatarImg.style.display = 'block';
          }
          if (avatarInitials) avatarInitials.style.display = 'none';
          const sidebarPic = document.getElementById('sidebar-profile-pic');
          if (sidebarPic) {
            sidebarPic.innerHTML = `<img src="${e.target.result}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // ============================================
  // EDIT PROFILE UI
  // ============================================
  const editProfileBtn = document.getElementById('editProfileBtn');
  const saveProfileBtn = document.getElementById('saveProfileBtn');
  const cancelEditBtn = document.getElementById('cancelEditBtn');
  const formInputs = document.querySelectorAll('#profileForm input[type="text"], #profileForm input[type="email"]');
  let originalValues = {};

  if (editProfileBtn && saveProfileBtn && cancelEditBtn) {
    editProfileBtn.addEventListener('click', () => {
      formInputs.forEach(input => originalValues[input.id] = input.value);
      formInputs.forEach(input => {
        input.disabled = false;
        input.style.borderColor = '#A68763';
      });
      editProfileBtn.style.display = 'none';
      saveProfileBtn.style.display = 'inline-flex';
      cancelEditBtn.style.display = 'inline-flex';
      if (editAvatarBtn) editAvatarBtn.style.display = 'block';
    });

    cancelEditBtn.addEventListener('click', () => {
      formInputs.forEach(input => {
        input.value = originalValues[input.id];
        input.disabled = true;
        input.style.borderColor = '';
      });
      if (fileInput) fileInput.value = '';
      window.location.reload();
    });
  }
});

// ============================================
// GLOBAL FUNCTIONS
// ============================================

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

// Note: simulateAnalysis is likely not needed anymore since we use real Forms,
// but keeping it to prevent errors if old buttons still reference it.
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

window.logout = function() {
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.style.cssText = `display: flex !important; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.7); z-index: 999999; align-items: center; justify-content: center;`;
    document.body.style.overflow = 'hidden';
  }
};

window.closeLogoutOverlay = function() {
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.style.display = 'none';
    document.body.style.overflow = '';
  }
};

window.confirmLogout = function() {
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.innerHTML = `<div class="modal" style="max-width: 400px; background:white; padding:2rem; border-radius:15px; text-align:center;">Logging out...</div>`;
    setTimeout(() => {
      // Update this URL to match your actual logout logic
      window.location.href = "index.php?page=login&action=logout"; 
    }, 800);
  }
};