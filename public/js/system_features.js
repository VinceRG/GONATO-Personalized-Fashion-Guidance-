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