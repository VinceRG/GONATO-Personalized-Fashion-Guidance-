document.addEventListener("DOMContentLoaded", () => {
  // ============================================
  // CRITICAL: ENSURE OVERLAY IS HIDDEN ON LOAD
  // ============================================
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
    // Force hide overlay on page load
    userProfileOverlay.style.display = 'none';
    userProfileOverlay.classList.remove('show');
    document.body.style.overflow = ''; // Ensure body scroll is enabled
  }

  // ============================================
  // SIDEBAR FUNCTIONALITY
  // ============================================
  const sidebar = document.getElementById('appSidebar');
  const logoutBtn = document.querySelector('.logout-btn');

  if (sidebar) {
    /* Start collapsed */
    sidebar.classList.add('collapsed');
    document.body.classList.add('collapsed-layout');
    toggleSidebarTopAndFooter(false);

    /* Expand sidebar on hover */
    sidebar.addEventListener('mouseenter', () => {
      sidebar.classList.remove('collapsed');
      document.body.classList.remove('collapsed-layout');
      toggleSidebarTopAndFooter(true);
    });

    /* Collapse sidebar on mouse leave */
    sidebar.addEventListener('mouseleave', () => {
      sidebar.classList.add('collapsed');
      document.body.classList.add('collapsed-layout');
      toggleSidebarTopAndFooter(false);
    });
  }

  /* Helper: hide/show logout button */
  function toggleSidebarTopAndFooter(show) {
    if (logoutBtn) logoutBtn.style.display = show ? 'block' : 'none';
  }

  // ============================================
  // NAVIGATION FUNCTIONALITY WITH HASH HANDLING
  // ============================================
  const navLinks = Array.from(document.querySelectorAll('.nav-btn'));
  const sections = Array.from(document.querySelectorAll('.content-section'));
  const mainContent = document.querySelector('.main-content');

  // Function to navigate to a specific section
  function navigateToSection(sectionId) {
    const target = document.querySelector(sectionId);
    if (!target) {
      console.warn('Section not found:', sectionId);
      return;
    }

    console.log('Navigating to section:', sectionId);

    // Update active states
    navLinks.forEach(l => l.classList.remove('active'));
    const correspondingLink = document.querySelector(`a[href="${sectionId}"]`);
    if (correspondingLink) {
      correspondingLink.classList.add('active');
    }

    // Show target section, hide others
    sections.forEach(s => s.classList.remove('active'));
    target.classList.add('active');

    // Scroll to section
    if (mainContent) {
      const targetTop = target.offsetTop;
      mainContent.scrollTo({ top: targetTop, behavior: 'smooth' });
    }

    // Update URL without triggering page reload
    history.replaceState(null, '', sectionId);
  }

  // Handle navigation link clicks
  navLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const targetSelector = link.getAttribute('href');
      navigateToSection(targetSelector);
    });
  });

  // ============================================
  // PAGE LOAD: CHECK HASH AND NAVIGATE
  // THIS RUNS FIRST - BEFORE ANY MODAL CAN OPEN
  // ============================================
  const hash = window.location.hash;

  console.log('Page loaded with hash:', hash);

  if (hash && (hash === '#catalog-shop' || hash === '#features')) {
    // Navigate to the section specified in hash
    console.log('Hash detected, navigating to:', hash);
    navigateToSection(hash);
  } else {
    // Default to shop section if no valid hash
    console.log('No valid hash, defaulting to #catalog-shop');
    navigateToSection('#catalog-shop');
  }

  // ============================================
  // HANDLE NOTIFICATIONS (Don't open modal)
  // ============================================
  const notification = document.querySelector('.notification');
  if (notification) {
    console.log('Notification detected');
    // Just ensure modal stays closed if notification exists
    setTimeout(() => {
      if (userProfileOverlay && userProfileOverlay.classList.contains('show')) {
        console.log('Closing any open modal due to notification');
        closeUserProfile();
      }
    }, 100);
  }

  // ============================================
  // RECOMMENDATIONS VISIBILITY
  // ============================================
  const recommendationsSection = document.getElementById("recommendations");
  const colorDone = localStorage.getItem("colorAnalysisDone") === "true";
  const bodyDone = localStorage.getItem("bodyAnalysisDone") === "true";

  if (recommendationsSection) {
    if (colorDone || bodyDone) {
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
      if (cartCountElement) {
        cartCountElement.textContent = cartCount;
      }
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
  // LOGOUT FUNCTIONALITY (Overlay Modal)
  // ============================================

  window.logout = function () {
    const logoutOverlay = document.getElementById('logoutOverlay');
    if (logoutOverlay) logoutOverlay.style.display = 'flex';
  };

  window.closeLogoutOverlay = function () {
    const logoutOverlay = document.getElementById('logoutOverlay');
    if (logoutOverlay) logoutOverlay.style.display = 'none';
  };

  window.confirmLogout = function () {
    const logoutOverlay = document.getElementById('logoutOverlay');
    if (logoutOverlay) {
      // Show loading animation
      logoutOverlay.innerHTML = `
      <div class="modal" style="max-width: 400px;">
        <div class="modal-body" style="text-align: center; padding: 3rem 2rem;">
          <div style="
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #A68763;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
          "></div>
          <p style="color: #2D2D2D; font-size: 1.1rem; font-weight: 500; margin: 0;">
            Logging out...
          </p>
        </div>
      </div>
      <style>
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      </style>
    `;

      // Redirect to landing/login page after short delay
      setTimeout(() => {
        window.location.href = "?logout=true";
      }, 800);
    }
  };

  // ============================================
  // Close overlay when clicking outside modal
  // ============================================
  document.addEventListener('click', function (event) {
    const overlay = document.getElementById('logoutOverlay');
    if (overlay && event.target === overlay) {
      closeLogoutOverlay();
    }
  });

  // ============================================
  // Close overlay with Escape key
  // ============================================
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      const overlay = document.getElementById('logoutOverlay');
      if (overlay && overlay.style.display === 'flex') {
        closeLogoutOverlay();
      }
    }
  });

  // ============================================
  // Prevent back navigation after logout
  // ============================================
  if (window.history && window.history.pushState) {
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = function () {
      window.history.go(1);
    };
  }



  // ============================================
  // USER PROFILE MODAL - ONLY OPEN WHEN CLICKED
  // ============================================
  window.openUserProfile = function () {
    console.log('Opening user profile modal');
    if (userProfileOverlay) {
      userProfileOverlay.style.display = 'flex';
      userProfileOverlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeUserProfile = function () {
    console.log('Closing user profile modal');
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

  // Close modal when clicking outside
  if (userProfileOverlay) {
    userProfileOverlay.addEventListener('click', function (e) {
      if (e.target === this) {
        closeUserProfile();
      }
    });
  }

  // ============================================
  // TAB FUNCTIONALITY FOR MODAL
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

  // ============================================
  // PROFILE IMAGE UPLOAD PREVIEW
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
          // Show image in modal
          if (avatarImg) {
            avatarImg.src = e.target.result;
            avatarImg.style.display = 'block';
          }
          if (avatarInitials) {
            avatarInitials.style.display = 'none';
          }

          // Update sidebar profile pic
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
  // EDIT PROFILE FUNCTIONALITY
  // ============================================
  const editProfileBtn = document.getElementById('editProfileBtn');
  const saveProfileBtn = document.getElementById('saveProfileBtn');
  const cancelEditBtn = document.getElementById('cancelEditBtn');
  const formInputs = document.querySelectorAll('#profileForm input[type="text"], #profileForm input[type="email"]');

  // Store original values for cancel functionality
  let originalValues = {};

  if (editProfileBtn && saveProfileBtn && cancelEditBtn) {
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
      if (editAvatarBtn) {
        editAvatarBtn.style.display = 'block';
      }

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
      if (fileInput) {
        fileInput.value = '';
      }

      // Reload page to restore original avatar state
      window.location.reload();
    });
  }

  // ============================================
  // FORM SUBMISSION HANDLER
  // ============================================
  const profileForm = document.getElementById('profileForm');
  if (profileForm) {
    profileForm.addEventListener('submit', function (e) {
      // Update display name in modal (for preview before page reload)
      const firstName = document.getElementById('first_name');
      const lastName = document.getElementById('last_name');
      const username = document.getElementById('username');
      const displayName = document.getElementById('display-name');
      const displayUsername = document.getElementById('display-username');

      if (firstName && lastName && displayName) {
        displayName.textContent = firstName.value + ' ' + lastName.value;
      }
      if (username && displayUsername) {
        displayUsername.textContent = '@' + username.value;
      }

      // Update sidebar name
      const sidebarName = document.querySelector('.profile-info .form-group');
      const sidebarUsername = document.querySelector('.profile-info p');
      if (sidebarName && firstName && lastName) {
        sidebarName.textContent = firstName.value + ' ' + lastName.value;
      }
      if (sidebarUsername && username) {
        sidebarUsername.textContent = '@' + username.value;
      }
    });
  }
});

// ============================================
// ANALYSIS FUNCTIONALITY (Global functions)
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