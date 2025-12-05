// ============================================
// GLOBAL FUNCTIONS (Defined immediately)
// ============================================

window.toggleSearchBar = function() {
    const searchContainer = document.getElementById('searchContainer');
    if (searchContainer) {
        searchContainer.classList.toggle('hidden');
        if (!searchContainer.classList.contains('hidden')) {
            const input = document.getElementById('productSearch');
            if (input) input.focus();
        }
    }
};

window.filterProducts = function() {
    const input = document.getElementById('productSearch');
    if (!input) return;
    
    const filter = input.value.toLowerCase().trim();
    // Select both catalog items and generic clothes items to ensure we catch everything
    const items = document.querySelectorAll('.catalog-item, .clothes-item'); 
    
    let matchCount = 0;
    // Try to find the specific grid first, then fallbacks
    let resultsContainer = document.querySelector('.clothes-grid') || document.querySelector('#catalog-shop') || document.querySelector('.main-content');

    items.forEach(item => {
        // 1. Try data-name attribute
        let name = item.dataset.name ? item.dataset.name.toLowerCase() : '';
        
        // 2. Try finding a title element inside
        if (!name) {
            const titleEl = item.querySelector('.title, .product-name, h3, h4');
            if (titleEl) {
                name = titleEl.textContent.trim().toLowerCase();
            }
        }

        // 3. Fallback: Search all text in the card
        if (!name) {
            name = item.textContent.trim().toLowerCase();
        }

        // Show/Hide logic
        if (name.includes(filter)) {
            item.style.display = ''; // Revert to CSS default (block/flex)
            matchCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Manage "No Results" message
    let noResultsMsg = document.getElementById('search-no-results');
    
    if (matchCount === 0 && filter !== '') {
        // Create message if it doesn't exist
        if (!noResultsMsg && resultsContainer) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'search-no-results';
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.style.textAlign = 'center';
            noResultsMsg.style.width = '100%';
            noResultsMsg.style.padding = '3rem';
            noResultsMsg.style.color = '#78716C';
            noResultsMsg.style.fontSize = '1.1rem';
            noResultsMsg.innerHTML = `<i class="bi bi-search" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i><p>No products found matching "<strong>${filter}</strong>"</p>`;
            
            // Insert safely
            if (resultsContainer.classList.contains('clothes-grid')) {
                // Insert after the grid so it doesn't mess up grid layout
                resultsContainer.parentNode.insertBefore(noResultsMsg, resultsContainer.nextSibling);
            } else {
                resultsContainer.appendChild(noResultsMsg);
            }
        } else if (noResultsMsg) {
            // Update existing message
            const p = noResultsMsg.querySelector('p');
            if (p) p.innerHTML = `No products found matching "<strong>${filter}</strong>"`;
            noResultsMsg.style.display = 'block';
        }
    } else {
        // Hide message if results found or search cleared
        if (noResultsMsg) noResultsMsg.style.display = 'none';
    }
    
    // Auto-scroll to shop if user is searching
    if (filter.length > 0) {
       const shopSection = document.getElementById('catalog-shop');
       if (shopSection) {
            const rect = shopSection.getBoundingClientRect();
            // Scroll if the shop section is not currently well-visible (e.g. user is at the very top)
            // Using a threshold of 150px to prevent jumping if already viewing the section
            if (rect.top > 150 || rect.bottom < 0) {
                shopSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
       }
    }
};

document.addEventListener("DOMContentLoaded", () => {
  // ============================================
  // USER PROFILE OVERLAY INITIAL STATE
  // ============================================
  // Logic disabled to prevent auto-opening on login/load
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
      // Ensure it starts hidden regardless of session flags
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
  initNavigation();

  function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('.content-section');
    
    function navigateTo(targetId) {
      if (!targetId) return;
      const targetSection = document.querySelector(targetId);
      if (!targetSection) return;
  
      // Update Links
      navLinks.forEach(l => {
          l.classList.toggle('active', l.getAttribute('href') === targetId);
      });
  
      // Update Sections
      sections.forEach(s => s.classList.remove('active'));
      targetSection.classList.add('active');
  
      window.scrollTo({ top: 0, behavior: 'smooth' });
      // update URL without page reload
      if(history.pushState) {
          history.pushState(null, null, targetId);
      } else {
          location.hash = targetId;
      }
    }
  
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        navigateTo(link.getAttribute('href'));
      });
    });
  
    // Handle Hash on Load
    const hash = window.location.hash;
    try {
        if (hash && document.querySelector(hash)) {
          navigateTo(hash);
        } else {
          navigateTo('#catalog-shop');
        }
    } catch(e) {
        console.warn("Invalid hash, defaulting to shop");
        navigateTo('#catalog-shop');
    }
  }

  // ============================================
  // CART FUNCTIONALITY (UPDATED FOR NEW CARD)
  // ============================================
  let cartCount = 0;
  const cartCountElement = document.querySelector('.cart-count');
  // Listen for clicks on document to handle dynamically added buttons
  document.addEventListener('click', (e) => {
    // Check if clicked element or parent is add-to-cart
    const button = e.target.closest('.add-to-cart') || e.target.closest('.card-add-btn');
    
    if (button) {
      e.stopPropagation(); // Prevent bubbling
      cartCount++;
      if (cartCountElement) cartCountElement.textContent = cartCount;
      
      // Animation
      button.classList.add('added');
      const icon = button.querySelector('i');
      const originalClass = icon ? icon.className : '';
      if(icon) icon.className = 'bi bi-check-lg';

      setTimeout(() => {
        button.classList.remove('added');
        if(icon) icon.className = originalClass;
      }, 1500);
    }
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
      
      // Update content (with Display Toggle for robustness)
      subTabContents.forEach(content => {
        content.classList.remove('active'); 
        content.style.display = 'none'; // Force hide

        if (content.id === targetSubTab) {
            content.classList.add('active');
            content.style.display = 'block'; // Force show
        }
      });
    });
  });

  // ============================================
  // PROFILE IMAGE PREVIEW & CLICK
  // ============================================
  const fileInput    = document.getElementById('profile_image');
  const headerAvatar = document.querySelector('.header-avatar');
  const sidebarPic   = document.getElementById('sidebar-profile-pic');

  if (headerAvatar && fileInput) {
    headerAvatar.addEventListener('click', () => {
      if (headerAvatar.classList.contains('editing')) {
        fileInput.click();
      }
    });
  }

  if (fileInput) {
    fileInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        const imgInsideAvatar = headerAvatar.querySelector('img');
        if (imgInsideAvatar) {
          imgInsideAvatar.src = e.target.result;
        } else {
          headerAvatar.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
        }
        if (sidebarPic) {
          sidebarPic.innerHTML = `<img src="${e.target.result}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        }
      };
      reader.readAsDataURL(file);
    });
  }

  // ============================================
  // ADDRESS DATA & CASCADING DROPDOWNS (PSGC LOGIC)
  // ============================================
  const PSGC_API = "https://psgc.gitlab.io/api";
  const regionSelect = document.getElementById("regionSelect");
  const provinceSelect = document.getElementById("province");
  const citySelect = document.getElementById("city");
  const barangaySelect = document.getElementById("barangay");

  const APP_REGION_MAP = {
      "Metro Manila": ["130000000"],
      "North Luzon": ["010000000","020000000","030000000","140000000"],
      "South Luzon": ["040000000","170000000","050000000"],
      "Visayas": ["060000000","070000000","080000000"],
      "Mindanao": ["090000000","100000000","110000000","120000000","160000000","150000000"]
  };

  function resetSelect(sel, placeholder) {
      if (!sel) return;
      sel.innerHTML = "";
      const opt = document.createElement("option");
      opt.value = "";
      opt.textContent = placeholder;
      sel.appendChild(opt);
  }

  async function handleAppRegionChange(appRegion) {
      resetSelect(provinceSelect, "Select Province");
      resetSelect(citySelect, "Select City / Municipality");
      resetSelect(barangaySelect, "Select Barangay");

      if (!appRegion) {
          provinceSelect.disabled = false; citySelect.disabled = true; barangaySelect.disabled = true;
          return;
      }

      const regionCodes = APP_REGION_MAP[appRegion] || [];

      if (appRegion === "Metro Manila") {
          resetSelect(provinceSelect, "Province");
          const opt = document.createElement("option");
          opt.value = "Metro Manila"; opt.textContent = "Metro Manila"; opt.selected = true;
          provinceSelect.appendChild(opt);
          provinceSelect.disabled = false;
          citySelect.disabled = false; barangaySelect.disabled = true;
          await loadCitiesForNCR();
          return;
      }

      provinceSelect.disabled = true; citySelect.disabled = true; barangaySelect.disabled = true;
      const allProvinces = [];
      for (const rCode of regionCodes) {
          try {
              const res = await fetch(`${PSGC_API}/regions/${rCode}/provinces/`);
              if (!res.ok) continue;
              const provinces = await res.json();
              provinces.forEach(p => allProvinces.push(p));
          } catch (e) { console.error("Error loading provinces", e); }
      }
      allProvinces.sort((a,b) => a.name.localeCompare(b.name));
      allProvinces.forEach(p => {
          const opt = document.createElement("option");
          opt.value = p.name; opt.textContent = p.name; opt.dataset.code = p.code;
          provinceSelect.appendChild(opt);
      });
      provinceSelect.disabled = allProvinces.length === 0 ? true : false;
  }

  async function loadCitiesFromProvince() {
      resetSelect(citySelect, "Select City / Municipality");
      resetSelect(barangaySelect, "Select Barangay");
      const selected = provinceSelect.selectedOptions[0];
      if (!selected || !selected.dataset.code) { citySelect.disabled = true; return; }
      try {
          const res = await fetch(`${PSGC_API}/provinces/${selected.dataset.code}/cities-municipalities/`);
          const cities = await res.json();
          cities.sort((a,b) => a.name.localeCompare(b.name));
          cities.forEach(c => {
              const opt = document.createElement("option");
              opt.value = c.name; opt.textContent = c.name; opt.dataset.code = c.code;
              citySelect.appendChild(opt);
          });
          citySelect.disabled = cities.length === 0;
      } catch (e) { console.error("Error loading cities:", e); }
  }

  async function loadCitiesForNCR() {
      resetSelect(citySelect, "Select City / Municipality");
      resetSelect(barangaySelect, "Select Barangay");
      try {
          const res = await fetch(`${PSGC_API}/regions/130000000/cities-municipalities/`);
          const cities = await res.json();
          cities.sort((a,b) => a.name.localeCompare(b.name));
          cities.forEach(c => {
              const opt = document.createElement("option");
              opt.value = c.name; opt.textContent = c.name; opt.dataset.code = c.code;
              citySelect.appendChild(opt);
          });
          citySelect.disabled = cities.length === 0;
      } catch (e) { console.error("Error loading NCR cities:", e); }
  }

  async function loadBarangaysFromCity() {
      resetSelect(barangaySelect, "Select Barangay");
      const selected = citySelect.selectedOptions[0];
      if (!selected || !selected.dataset.code) { barangaySelect.disabled = true; return; }
      try {
          const res = await fetch(`${PSGC_API}/cities-municipalities/${selected.dataset.code}/barangays/`);
          const barangays = await res.json();
          barangays.sort((a,b) => a.name.localeCompare(b.name));
          barangays.forEach(b => {
              const opt = document.createElement("option");
              opt.value = b.name; opt.textContent = b.name;
              barangaySelect.appendChild(opt);
          });
          barangaySelect.disabled = barangays.length === 0;
      } catch (e) { console.error("Error loading barangays:", e); }
  }

  if (regionSelect) {
      regionSelect.disabled = true; provinceSelect.disabled = true; citySelect.disabled = true; barangaySelect.disabled = true;
      regionSelect.addEventListener("change", function () { handleAppRegionChange(this.value); });
      provinceSelect.addEventListener("change", function () { loadCitiesFromProvince(); });
      citySelect.addEventListener("change", function () { loadBarangaysFromCity(); });
  }

  // Loading Overlay Logic
  const loadingOverlay = document.getElementById('analysisLoadingOverlay');
  const colorForm = document.getElementById('colorForm');
  const bodyShapeForm = document.getElementById('bodyShapeForm');
  function showAnalysisLoading() {
      if (loadingOverlay) { loadingOverlay.classList.remove('hidden'); loadingOverlay.style.display = 'flex'; }
  }
  if (colorForm) colorForm.addEventListener('submit', showAnalysisLoading);
  if (bodyShapeForm) bodyShapeForm.addEventListener('submit', showAnalysisLoading);

  // Initialize Drag & Drop
  initDragAndDrop();
});

// ============================================
// GLOBAL FUNCTIONS (For onclick attributes)
// ============================================

let originalProfileValues = {};
window.enableEditing = function(btn) {
    const form = document.getElementById('profileForm');
    if (!form) return;
    const fields = form.querySelectorAll('input:not([type="hidden"]):not(#profile_image), select');
    const saveBtn = document.getElementById('saveProfileBtn');
    const footer = document.getElementById('profileFooter');
    const editRow = document.querySelector('.edit-row');
    const headerAvatar = document.querySelector('.header-avatar');
    originalProfileValues = {};
    fields.forEach(el => {
        if (el.id) originalProfileValues[el.id] = el.value;
        el.disabled = false; el.style.borderColor = '#A68763';
    });
    if (editRow) editRow.style.display = 'none';
    if (footer) footer.style.display = 'flex';
    if (saveBtn) saveBtn.disabled = false;
    if (headerAvatar) headerAvatar.classList.add('editing');
    
    // Enable address selects for editing
    const regionSelect = document.getElementById('regionSelect');
    if(regionSelect) regionSelect.disabled = false;
};

window.cancelEditing = function() {
    const form = document.getElementById('profileForm');
    if (!form) return;
    const fields = form.querySelectorAll('input:not([type="hidden"]):not(#profile_image), select');
    const saveBtn = document.getElementById('saveProfileBtn');
    const footer = document.getElementById('profileFooter');
    const editRow = document.querySelector('.edit-row');
    const headerAvatar = document.querySelector('.header-avatar');
    fields.forEach(el => {
        if (el.id && originalProfileValues.hasOwnProperty(el.id)) el.value = originalProfileValues[el.id];
        el.disabled = true; el.style.borderColor = '';
    });
    const fileInput = document.getElementById('profile_image');
    if (fileInput) fileInput.value = '';
    if (editRow) editRow.style.display = 'flex';
    if (footer) footer.style.display = 'none';
    if (saveBtn) saveBtn.disabled = true;
    if (headerAvatar) headerAvatar.classList.remove('editing');
    
    const regionSelect = document.getElementById('regionSelect');
    if(regionSelect) regionSelect.disabled = true;
};

window.toggleViews = function(viewName) {
    const accountView = document.getElementById('account-view');
    const purchasesView = document.getElementById('purchases-view');
    if(!accountView || !purchasesView) return;
    if (viewName === 'purchases') {
        accountView.style.display = 'none'; purchasesView.style.display = 'block';
    } else {
        accountView.style.display = 'block'; purchasesView.style.display = 'none';
    }
};

window.openUserProfile = function () {
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
    userProfileOverlay.style.display = 'flex'; userProfileOverlay.classList.add('show'); document.body.style.overflow = 'hidden';
  }
};

window.closeUserProfile = function () {
  const userProfileOverlay = document.getElementById('userProfileOverlay');
  if (userProfileOverlay) {
    userProfileOverlay.style.display = 'none'; userProfileOverlay.classList.remove('show'); document.body.style.overflow = '';
    toggleViews('account'); cancelEditing();
  }
};

window.openCart = function() {
    // Placeholder for cart logic, OR handled by cartModal.js
    console.log("Open Cart Clicked");
};

window.goToFeatures = function () {
  closeUserProfile();
  const featuresLink = document.querySelector('a[href="#features"]');
  if(featuresLink) featuresLink.click();
};

window.toggleAnalysis = function (targetId, show) {
  const targetElement = document.getElementById(targetId);
  if (!targetElement) return;
  const card = targetElement.closest('.option-card');
  const uploadSection = card.querySelector('.upload-wrapper') || card.querySelector('.upload-section'); // Support both class names
  if (show) { // Show Upload
    targetElement.classList.remove('show-results');
    if (uploadSection) uploadSection.classList.remove('hidden');
  } else { // Show Results
    targetElement.classList.add('show-results');
    if (uploadSection) uploadSection.classList.add('hidden');
  }
};

window.toggleColorAnalysis = function(show) {
    const results = document.getElementById('colorSeasons');
    const uploadSection = document.getElementById('colorUploadSection');
    if (!results || !uploadSection) return;
    
    if (show) { // "Try Again" -> Show Upload
      results.classList.remove('show-results');
      uploadSection.classList.remove('hidden');
      const form = document.getElementById('colorForm');
      if (form) form.reset();
    } else { // "Cancel" or Show Results
      results.classList.add('show-results');
      uploadSection.classList.add('hidden');
    }
};

window.closeAnalysisErrorModal = function() {
    const overlay = document.getElementById('analysisErrorOverlay');
    if (overlay) overlay.classList.add('hidden');
};
window.closeBodyShapeResultModal = function() {
    const overlay = document.getElementById('bodyShapeResultOverlay');
    if (overlay) overlay.classList.add('hidden');
};
window.closeColorResultModal = function() {
    const overlay = document.getElementById('colorResultOverlay');
    if (overlay) overlay.classList.add('hidden');
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
  if (logoutOverlay) { logoutOverlay.style.display = 'none'; document.body.style.overflow = ''; }
};

window.confirmLogout = function () {
  const logoutOverlay = document.getElementById('logoutOverlay');
  if (logoutOverlay) {
    logoutOverlay.innerHTML = `<div class="modal" style="max-width: 400px; background:white; padding:2rem; border-radius:15px; text-align:center;">Logging out...</div>`;
    setTimeout(() => { window.location.href = "index.php?page=login&action=logout"; }, 800);
  }
};

// ============================================
// DRAG & DROP UPLOAD LOGIC
// ============================================
function initDragAndDrop() {
    setupDragDrop('colorDropArea', 'face_image_input', 'colorFilePreview');
    setupDragDrop('frontDropArea', 'front_image_input', 'frontFilePreview');
    setupDragDrop('sideDropArea', 'side_image_input', 'sideFilePreview');
}

function setupDragDrop(areaId, inputId, previewId) {
    const area = document.getElementById(areaId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (!area || !input) return;

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        area.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Highlight drop area
    ['dragenter', 'dragover'].forEach(eventName => {
        area.addEventListener(eventName, () => area.classList.add('drag-over'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        area.addEventListener(eventName, () => area.classList.remove('drag-over'), false);
    });

    // Handle dropped files
    area.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        input.files = files;
        updatePreview(files[0]);
    });

    // Handle file input change (browse)
    input.addEventListener('change', () => {
        if(input.files.length > 0) {
            updatePreview(input.files[0]);
        }
    });

    function updatePreview(file) {
        if(preview && file) {
            preview.innerHTML = `<i class="bi bi-check-circle-fill" style="color:green;"></i> ${file.name} selected`;
        }
    }
}