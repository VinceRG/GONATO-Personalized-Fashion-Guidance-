// Admin Dashboard JavaScript
// Absolute path from root (preferred)
// admin.js
//const API_BASE = '../../public/admin-api.php?api=admin';

// Or relative path from current page
const API_BASE = 'index.php?api=admin';

let selectedProductId = null;
let selectedProductName = null;
let products = [];
let users = [];
let orders = [];
let colors = [];
let bodyShapes = [];
let seasons = []; 
let currentEditId = null;
let currentEditInventoryId = null; // track edit mode
let openedColorFromInventory = false;
let productPage = 1;
let productPageSize = 5;   // how many products per page
let allUsers = [];
let inventoryPage = 1;
let inventoryPageSize = 5; // variants per page
let inventoryItems = [];   // holds current product's inventory
let userPage = 1;
let userPageSize = 5;
let currentUsers = [];
let orderPage = 1;
let orderPageSize = 5;
let currentOrders = [];
let auditLogs = [];
let displayAuditLogs = [];
let auditPage = 1;
let auditPageSize = 5;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  loadColors();
  loadBodyShapes();
  loadSeasons();
  loadProducts();
  loadUsers();
  loadOrders();
  initFormHandlers();
  loadAudit();
  initLowStockNotification();   // 🔔 set up bell + fetch
});

// ==================== NAVIGATION ====================
function initNavigation() {
  const navBtns = document.querySelectorAll('.nav-btn');
  navBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const target = btn.getAttribute('href').substring(1);

      if (target === 'inventory') {
        // Coming from the nav: always show ALL inventory
        selectedProductId   = null;
        selectedProductName = null;

        switchSection('inventory');
        loadAllInventory();      // 🔹 always "All Products" from nav
        return;
      }

      switchSection(target);
    });
  });
}

function switchSection(sectionId) {
  document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.classList.remove('active');
    if (btn.getAttribute('href') === `#${sectionId}`) {
      btn.classList.add('active');
    }
  });

  document.querySelectorAll('.content-section').forEach(section => {
    section.classList.remove('active');
  });
  document.getElementById(sectionId).classList.add('active');

  // no inventory logic here anymore
}

// ==================== PRODUCTS TAB ====================
async function loadProducts() {
  try {
    const response = await fetch(`${API_BASE}&action=products`, {
      credentials: 'include'
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const result = await response.json();
    console.log('Products API result:', result);

    // Handle both shapes:
    // 1) [ {...}, {...} ]
    // 2) { data: [ {...}, {...} ], total: ..., page: ... }
    if (Array.isArray(result)) {
      products = result;
    } else if (result && Array.isArray(result.data)) {
      products = result.data;
    } else {
      products = [];
    }

    productPage = 1; // reset to first page when reloading

    renderProductsTable();
    renderProductPagination();
  } catch (error) {
    console.error('Error loading products:', error);
    const tbody = document.getElementById('productListBody');
    tbody.innerHTML = `<tr><td colspan="6" style="color:red;text-align:center;">Failed to load products: ${error.message}</td></tr>`;
    const pag = document.getElementById('productPagination');
    if (pag) pag.innerHTML = '';
  }
}

function renderProductsTable() {
  const tbody = document.getElementById('productListBody');
  tbody.innerHTML = '';

  if (!Array.isArray(products) || products.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No products found</td></tr>';
    return;
  }

  const start = (productPage - 1) * productPageSize;
  const end   = start + productPageSize;
  const pageItems = products.slice(start, end);

  pageItems.forEach(p => {
    const row = document.createElement('tr');
    const safeName = encodeURIComponent(p.PRODUCT_NAME);

    row.innerHTML = `
      <td><img src="public/image/${p.IMAGE_FILE || 'placeholder.png'}" style="width:50px;height:50px;object-fit:cover;border-radius:4px;"></td>
      <td>${p.PRODUCT_NAME}</td>
      <td>${p.DESCRIPTION || 'No description'}</td>
      <td>${p.BODY_SHAPE_NAME || 'N/A'}</td>
      <td>₱${parseFloat(p.PRICE).toFixed(2)}</td>
      <td>
        <button class="btn-icon" onclick="openInventoryManager(${p.PRODUCT_ID}, '${safeName}')">
          <i class="bi bi-box-seam"></i>
        </button>
        <button class="btn-icon" onclick="editProduct(${p.PRODUCT_ID})" title="Edit">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn-icon" onclick="deleteProduct(${p.PRODUCT_ID})" title="Delete">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function renderProductPagination() {
    renderPagination(
        "productPagination",
        productPage,
        products.length,
        productPageSize,
        (p) => {
            productPage = p;
            renderProductsTable();
            renderProductPagination();
        }
    );
}



function openProductModal(productId = null) {
  const modal = document.getElementById('productModal');
  const form = document.getElementById('productForm');
  const title = document.getElementById('productModalTitle');
  const imageInput = document.getElementById('productImage');
  const imageContainer = document.getElementById('currentImageContainer');
  const currentImage = document.getElementById('currentProductImage');

  form.reset();
  currentEditId = productId;
  imageInput.required = !productId; // Require image only for new product

if (productId) {
  // Edit mode
  title.textContent = 'Edit Product';

  // 🔧 Make sure we match even if one is string and one is number
  const product = products.find(p => String(p.PRODUCT_ID) === String(productId));

  console.log('Editing productId:', productId, 'Found product:', product);

  if (product) {
    document.getElementById('productName').value = product.PRODUCT_NAME || '';
    document.getElementById('productDescription').value = product.DESCRIPTION || '';
    document.getElementById('productPrice').value = product.PRICE || '';
    document.getElementById('bodyShapeSelect').value = product.BODY_SHAPE_ID || '';


    if (product.IMAGE_FILE) {
      currentImage.src = `public/image/${product.IMAGE_FILE}`;
      imageContainer.style.display = 'block';
    } else {
      imageContainer.style.display = 'none';
    }

    imageInput.required = false;
  } else {
    console.warn('No product found for id:', productId);
  }
}
else {
    // Add mode
    title.textContent = 'Add Product';
    imageContainer.style.display = 'none';
    imageInput.required = true;
  }

  modal.style.display = 'block';
}


function closeProductModal() {
  document.getElementById('productModal').style.display = 'none';
  currentEditId = null;
}

async function saveProduct() {
  const form = document.getElementById('productForm');
  const formData = new FormData(form);

  // Build the correct URL with action (and id for update)
  let url;
  if (currentEditId) {
    url = `${API_BASE}&action=updateProduct&id=${currentEditId}`;
  } else {
    url = `${API_BASE}&action=addProduct`;
  }

  try {
    const response = await fetch(url, {        // ✅ use url here
      method: 'POST',
      body: formData,
      credentials: 'include'
    });

    if (!response.ok) {
      let errorData = {};
      try {
        errorData = await response.json();
      } catch (e) {}
      console.error('Error response:', errorData);
      throw new Error(errorData.error || 'Failed to save product');
    }

    showNotification(
      currentEditId ? 'Product updated successfully' : 'Product added successfully',
      'success'
    );
    closeProductModal();
    await loadProducts();
  } catch (error) {
    console.error('Error saving product:', error);
    showNotification('Failed to save product: ' + error.message, 'error');
  }
}


async function editProduct(productId) {
  openProductModal(productId);
}

async function deleteProduct(productId) {
  openDeleteModal(productId);
}

let productToDeleteId = null;

function openDeleteModal(productId) {
   const product = products.find(p => String(p.PRODUCT_ID) === String(productId));
  if (!product) {
    console.warn('No product found for delete id:', productId);
    return;
  }

  productToDeleteId = productId;
  document.getElementById('deleteProductMessage').textContent =
    `Are you sure you want to delete "${product.PRODUCT_NAME}"?\nThis will also delete all inventory variants.`;

  document.getElementById('deleteProductModal').style.display = 'block';
}

function closeDeleteModal() {
  productToDeleteId = null;
  document.getElementById('deleteProductModal').style.display = 'none';
}

// Confirm deletion
document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
  if (!productToDeleteId) return;

  try {
    const response = await fetch(`${API_BASE}&action=deleteProduct&id=${productToDeleteId}`, {
      method: 'DELETE',
      credentials: 'include'
    });

    if (!response.ok) throw new Error('Failed to delete product');

    showNotification('Product deleted successfully', 'success');
    closeDeleteModal();
    await loadProducts();
  } catch (error) {
    console.error('Error deleting product:', error);
    showNotification('Failed to delete product', 'error');
  }
});
async function toggleUserStatus(userId, isLocked) {
    try {
        const res = await fetch(
            `index.php?api=admin&action=users&id=${userId}&toggle-lock=1`,
            {
                method: "PATCH",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ isLocked })
            }
        );

        const result = await res.json();

        if (result.success) {
            await loadUsers();
        } else {
            alert(result.message || "Failed to update user status");
        }
    } catch (err) {
        console.error(err);
        alert("Error toggling lock status");
    }
}


// ==================== INVENTORY TAB ====================
function openInventoryManager(productId, productName) {
  selectedProductId   = productId;
  selectedProductName = encodeURIComponent(productName);

  const title    = document.getElementById('inventoryProductTitle');
  const subtitle = document.getElementById('inventorySubtitle');
  if (title)    title.textContent    = `Inventory - ${productName}`;
  if (subtitle) subtitle.textContent = 'Manage size & color stock for this product';

  // Go to inventory section
  switchSection('inventory');

  // And explicitly load only this product’s inventory
  loadInventory(productId);
}


async function loadAllInventory() {
  const title    = document.getElementById('inventoryProductTitle');
  const subtitle = document.getElementById('inventorySubtitle');
  const tbody    = document.getElementById('inventoryTableBody');

  // Clear selected product so we know we’re in “all inventory” mode
  selectedProductId   = null;
  selectedProductName = null;

  if (title)    title.textContent    = 'Inventory - All Products';
  if (subtitle) subtitle.textContent = 'Showing stock variants for all products';

  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>';
  }

  try {
    const res = await fetch(`${API_BASE}&action=inventoryAll`, {
      credentials: 'include'
    });

    if (!res.ok) {
      throw new Error(`Failed to load inventory (HTTP ${res.status})`);
    }

    const result = await res.json();

    // Expecting a plain array; if you ever wrap it with {data: [...]}, this still works:
    if (Array.isArray(result)) {
      inventoryItems = result;
    } else if (result && Array.isArray(result.data)) {
      inventoryItems = result.data;
    } else {
      inventoryItems = [];
    }

    inventoryPage = 1;
    renderInventoryTable();
    renderInventoryPagination();
  } catch (err) {
    console.error('loadAllInventory error:', err);
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" style="color:red;text-align:center;">
            Failed to load inventory: ${err.message}
          </td>
        </tr>
      `;
    }
    const pag = document.getElementById('inventoryPagination');
    if (pag) pag.innerHTML = '';
  }
}


async function loadInventory(productId) {
  const tbody = document.getElementById('inventoryTableBody');
  tbody.innerHTML = '<tr><td colspan="5">Loading...</td></tr>';

  try {
    const res = await fetch(`${API_BASE}&action=inventoryByProduct&product_id=${productId}`, {
      credentials: 'include'
    });

    if (!res.ok) {
      throw new Error(`Failed to load inventory (${res.status})`);
    }

    const result = await res.json();
    console.log('Inventory API result:', result);

    // Handle both array and {data: [...]}
    if (Array.isArray(result)) {
      inventoryItems = result;
    } else if (result && Array.isArray(result.data)) {
      inventoryItems = result.data;
    } else {
      inventoryItems = [];
    }

    inventoryPage = 1; // reset to first page when changing product

    renderInventoryTable();
    renderInventoryPagination();
  } catch (err) {
    console.error('Error loading inventory:', err);
    tbody.innerHTML = `<tr><td colspan="5" style="color:red;text-align:center;">Failed to load inventory: ${err.message}</td></tr>`;
    const pag = document.getElementById('inventoryPagination');
    if (pag) pag.innerHTML = '';
  }
}

function renderInventoryTable() {
  const tbody = document.getElementById('inventoryTableBody');
  tbody.innerHTML = '';

  if (!Array.isArray(inventoryItems) || inventoryItems.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No variants found.</td></tr>';
    return;
  }

  const start = (inventoryPage - 1) * inventoryPageSize;
  const end   = start + inventoryPageSize;
  const pageItems = inventoryItems.slice(start, end);

  pageItems.forEach(v => {
    const row = document.createElement('tr');
    const stockClass =
      v.QUANTITY === 0 ? 'out-of-stock'
      : v.QUANTITY < 10 ? 'low-stock'
      : 'in-stock';

    const productLabel =
      v.PRODUCT_NAME || (selectedProductName ? decodeURIComponent(selectedProductName) : '');

    row.innerHTML = `
      <td>${productLabel}</td>
      <td>${v.SIZE}</td>
      <td>${v.COLOR_VALUE}</td>
      <td><span class="badge ${stockClass}">${v.QUANTITY}</span></td>
      <td>${new Date(v.CREATED_AT).toLocaleDateString()}</td>
      <td>
        <button class="btn-icon" onclick="editInventory(${v.INVENTORY_ID})" title="Edit">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn-icon" onclick="deleteInventory(${v.INVENTORY_ID})" title="Delete">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });
}


function renderInventoryPagination() {
  renderPagination(
    "inventoryPagination",
    inventoryPage,
    inventoryItems.length,
    inventoryPageSize,
    (p) => {
      inventoryPage = p;
      renderInventoryTable();
      renderInventoryPagination();
    }
  );
}

async function fetchLowStock() {
  try {
const res = await fetch(`${API_BASE}&action=criticalInventory`, {
  credentials: 'include'
});
    const text = await res.text(); // read raw body first
    console.log('low stock raw response:', text);

    if (!res.ok) {
      console.error('Low stock HTTP error', res.status);
      return;
    }

    if (!text) {
      console.error('Low stock: empty response body');
      return;
    }

    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error('Low stock: failed to parse JSON', e);
      return;
    }

    const count = data.count || 0;
    const badge = document.getElementById('lowStockCount');
    const list  = document.getElementById('lowStockList');

    if (!badge || !list) return;

    list.innerHTML = '';

    if (count === 0) {
      badge.hidden = true;
      const li = document.createElement('li');
      li.classList.add('empty');
      li.textContent = 'All good, no critical items.';
      list.appendChild(li);
      return;
    }

    badge.hidden = false;
    badge.textContent = count > 9 ? '9+' : count;

    data.items.forEach(item => {
  const li = document.createElement('li');
  li.textContent =
    `${item.product_name} (${item.color}, ${item.size}) – only ${item.quantity} left`;

  li.addEventListener('click', () => {
    // Go straight to the inventory tab for this product,
    // same behaviour as the box icon in the Products table
    const safeName = encodeURIComponent(item.product_name);
    openInventoryManager(item.product_id, safeName);
  });

  list.appendChild(li);

    });
  } catch (err) {
    console.error('Failed to fetch low stock', err);
  }
}
function initLowStockNotification() {
  const bell = document.getElementById('lowStockBtn');
  const dropdown = document.getElementById('lowStockDropdown');

  if (!bell || !dropdown) {
    console.warn('Low stock notification elements not found in DOM');
    return;
  }

  // Load data once at startup
  fetchLowStock();

  // Refresh + toggle when clicking the bell
  bell.addEventListener('click', async (e) => {
    e.stopPropagation();
    await fetchLowStock();                 // make sure we have latest counts
    dropdown.classList.toggle('hidden');
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
}


async function openInventoryModal(editItem = null) {
  const modal = document.getElementById('inventoryModal');
  const title = document.getElementById('inventoryModalTitle');
  const seasonSelect = document.getElementById('inventorySeasonFilter');
  const colorSelect = document.getElementById('inventoryColor');

  let selectedSeasonId = '';
  let selectedColorId  = '';

  if (editItem) {
    // 🔹 EDIT mode
    currentEditInventoryId = editItem.INVENTORY_ID;
    title.textContent = 'Edit Variant';

    console.log('openInventoryModal editItem:', editItem);

    // Set size & quantity from item
    const sizeInput = document.getElementById('inventorySize');
    const qtyInput  = document.getElementById('inventoryQuantity');

    if (sizeInput) sizeInput.value = editItem.SIZE || '';
    if (qtyInput)  qtyInput.value  = editItem.QUANTITY || '';

    selectedColorId = editItem.COLOR_ID;

    // Look up season from colors[]
    const colorObj = colors.find(c => String(c.COLOR_ID) === String(editItem.COLOR_ID));
    if (colorObj) {
      selectedSeasonId = colorObj.SEASON_ID;
    }
  } else {
    // 🔹 ADD mode
    currentEditInventoryId = null;
    title.textContent = 'Add Variant';
    document.getElementById('inventoryForm').reset();
  }

  // Populate season dropdown (pre-select if editing)
  populateInventorySeasonFilter(selectedSeasonId);

  // Populate colors based on selected season (or placeholder)
  populateInventoryColorDropdown(selectedSeasonId, selectedColorId);

  // When season changes, update colors in the dropdown
  seasonSelect.onchange = function () {
    const newSeasonId = seasonSelect.value;
    populateInventoryColorDropdown(newSeasonId, '');
  };

  modal.style.display = 'block';
}



function closeInventoryModal() {
  document.getElementById('inventoryModal').style.display = 'none';
  currentEditInventoryId = null;
}

async function saveInventoryVariant() {
  const payload = {
    productId: selectedProductId,
    colorId: parseInt(document.getElementById('inventoryColor').value),
    size: document.getElementById('inventorySize').value,
    quantity: parseInt(document.getElementById('inventoryQuantity').value)
  };

  if (!payload.colorId || !payload.size || isNaN(payload.quantity)) {
    showNotification('All fields are required', 'error');
    return;
  }

  try {
    let url = `${API_BASE}&action=addInventory`;
    let method = 'POST';

    if (currentEditInventoryId) {
      url = `${API_BASE}&action=inventory&id=${currentEditInventoryId}`;
      method = 'PUT';
    }

    const res = await fetch(url, {
      method: method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      credentials: 'include'
    });

    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Failed to save inventory');

    showNotification(currentEditInventoryId ? 'Variant updated' : 'Variant added', 'success');
    closeInventoryModal();
    await loadInventory(selectedProductId, inventoryPage);
  } catch (err) {
    console.error(err);
    showNotification('Failed to save variant: ' + err.message, 'error');
  }
}

async function editInventory(inventoryId) {
  try {
    const res = await fetch(`${API_BASE}&action=inventoryByProduct&product_id=${selectedProductId}`, {
      credentials: 'include'
    });

    if (!res.ok) {
      throw new Error(`Failed to load inventory for editing (${res.status})`);
    }

    const data = await res.json();
    console.log('Inventory list for edit:', data);

    const item = data.find(i => String(i.INVENTORY_ID) === String(inventoryId));
    if (!item) {
      console.warn('No inventory item found for id', inventoryId);
      return;
    }

    console.log('Editing inventory item:', item);
    openInventoryModal(item);
  } catch (err) {
    console.error('Error in editInventory:', err);
  }
}


async function deleteInventory(inventoryId) {
  if (!confirm('Are you sure you want to delete this variant?')) return;

  try {
    const res = await fetch(`${API_BASE}&action=inventory&id=${inventoryId}`, {
      method: 'DELETE',
      credentials: 'include'
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Failed to delete inventory');

    showNotification('Variant deleted', 'success');
    await loadInventory(selectedProductId, inventoryPage);
  } catch (err) {
    console.error(err);
    showNotification('Failed to delete variant', 'error');
  }
}
// ==================== COLORS ====================
async function loadColors() {
  try {
    const response = await fetch(`${API_BASE}&action=colors`);
    if (!response.ok) throw new Error('Failed to load colors');
    colors = await response.json();
  } catch (error) {
    console.error('Error loading colors:', error);
  }
}

function openAddColorModal() {
  openedColorFromInventory = false;
  document.getElementById('addColorForm').reset();
  populateSeasonDropdown(); // no preselect
  document.getElementById('addColorModal').style.display = 'block';
}
function openAddColorModalFromInventory() {
  const seasonSelect = document.getElementById('inventorySeasonFilter');
  const currentSeasonId = seasonSelect ? seasonSelect.value : '';

  if (!currentSeasonId) {
    showNotification('Please select a season first.', 'error');
    return;
  }

  openedColorFromInventory = true;

  document.getElementById('addColorForm').reset();
  // Preselect the same season in Add Color modal
  populateSeasonDropdown(currentSeasonId);

  document.getElementById('addColorModal').style.display = 'block';
}


function closeAddColorModal() {
  document.getElementById('addColorModal').style.display = 'none';
}

async function saveColor() {
  const colorName = document.getElementById('newColorName').value.trim();
  const seasonId  = document.getElementById('newColorSeason').value;

  if (!colorName || !seasonId) {
    showNotification('Color name and season are required', 'error');
    return;
  }

  try {
    const response = await fetch(`${API_BASE}&action=colors`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ colorValue: colorName, seasonId: seasonId })
    });

    const data = await response.json();
    if (!response.ok || data.error) {
      throw new Error(data.error || 'Failed to add color');
    }

    showNotification('Color added successfully', 'success');
    closeAddColorModal();

    // Reload master colors list
    await loadColors();

    // 🔹 If we came from inventory modal, refresh its color dropdown
    if (openedColorFromInventory) {
      const invSeasonSelect = document.getElementById('inventorySeasonFilter');
      const invSeasonId = invSeasonSelect ? invSeasonSelect.value : '';

      if (invSeasonId) {
        populateInventoryColorDropdown(invSeasonId, data.color_id || '');
      }

      openedColorFromInventory = false;
    }
  } catch (error) {
    console.error('Error saving color:', error);
    showNotification('Failed to add color: ' + error.message, 'error');
  }
}

function renderPagination(containerId, currentPage, totalItems, pageSize, onChangePage) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = '';

    const totalPages = Math.ceil(totalItems / pageSize);
    if (totalPages <= 1) return;

    const createBtn = (label, page, disabled = false, active = false) => {
        const btn = document.createElement('button');
        btn.textContent = label;
        btn.disabled = disabled;
        btn.className = 'btn btn-secondary btn-sm';
        if (active) btn.classList.add('active-page');
        if (!disabled) btn.onclick = () => onChangePage(page);
        return btn;
    };

    // Prev
    container.appendChild(createBtn('Prev', currentPage - 1, currentPage === 1));

    let pagesToShow = [];

    if (totalPages <= 3) {
        // Show all pages (max 3)
        for (let i = 1; i <= totalPages; i++) pagesToShow.push(i);
    } else {
        // More than 3 pages
        if (currentPage <= 2) {
            pagesToShow = [1, 2, 3];
            pagesToShow.push('...');
        } else if (currentPage >= totalPages - 1) {
            pagesToShow = ['...', totalPages - 2, totalPages - 1, totalPages];
        } else {
            pagesToShow = ['...', currentPage - 1, currentPage, currentPage + 1, '...'];
        }
    }

    pagesToShow.forEach(p => {
        if (p === '...') {
            const dots = document.createElement('span');
            dots.textContent = '...';
            container.appendChild(dots);
        } else {
            container.appendChild(
                createBtn(p, p, false, currentPage === p)
            );
        }
    });

    // Next
    container.appendChild(createBtn('Next', currentPage + 1, currentPage === totalPages));
}


// ==================== USERS TAB ====================
async function loadUsers() {
  try {
    const res = await fetch(`${API_BASE}&action=users`, {
      credentials: 'include',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch users (HTTP ${res.status})`);
    }

    allUsers = await res.json();
    currentUsers = allUsers.slice();  // copy
    userPage = 1;

    renderUsers();
    renderUserPagination();
  } catch (err) {
    console.error('loadUsers error:', err);
    alert('Error loading users. Make sure you are logged in as admin.');
  }
}

function renderUsers() {
  const tbody = document.getElementById('userTableBody');
  tbody.innerHTML = '';

  const list = currentUsers || [];

  if (!Array.isArray(list) || list.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" style="text-align:center;">No users found</td>
      </tr>
    `;
    return;
  }

  const start = (userPage - 1) * userPageSize;
  const end   = start + userPageSize;
  const pageItems = list.slice(start, end);

  pageItems.forEach(user => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${user.USERNAME}</td>
      <td>${user.EMAIL}</td>
      <td>${new Date(user.CREATED_AT).toLocaleDateString()}</td>
      <td>${user.IS_LOCKED ? 'Locked' : 'Active'}</td>
      <td>
        <button class="btn btn-sm" onclick="toggleUserStatus(${user.USER_ID}, ${user.IS_LOCKED})">
          ${user.IS_LOCKED ? 'Unlock' : 'Lock'}
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function renderUserPagination() {
    renderPagination(
        "userPagination",
        userPage,
        currentUsers.length,
        userPageSize,
        (p) => {
            userPage = p;
            renderUsers();
            renderUserPagination();
        }
    );
}


function filterUsers() {
  const searchTerm   = document.getElementById('userSearch').value.toLowerCase();
  const statusFilter = document.getElementById('statusFilter').value; // "active" | "locked" | ""

  const filtered = allUsers.filter(user => {
    const matchesSearch =
      user.USERNAME.toLowerCase().includes(searchTerm) ||
      user.EMAIL.toLowerCase().includes(searchTerm);

    let matchesStatus = true;

    if (statusFilter === 'active') {
      matchesStatus = !user.IS_LOCKED;
    } else if (statusFilter === 'locked') {
      matchesStatus = !!user.IS_LOCKED;
    }

    return matchesSearch && matchesStatus;
  });

  currentUsers = filtered;
  userPage = 1;

  renderUsers();
  renderUserPagination();
}


async function toggleUserStatus(userId, isLocked) {
  try {
    const res = await fetch(
      `${API_BASE}&action=users&id=${userId}&toggle-lock=1`,
      {
        method: 'PATCH', // matches AdminApiController
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ isLocked }) // send current state; backend flips it
      }
    );

    const result = await res.json();

    if (result.success) {
      await loadUsers(); // refresh table
    } else {
      alert(result.message || 'Failed to update user status');
    }
  } catch (err) {
    console.error('toggleUserStatus error:', err);
    alert('Error toggling lock status');
  }
}



// ==================== BODY SHAPES ====================
async function loadBodyShapes() {
  try {
    const response = await fetch(`${API_BASE}&action=bodyShapes`);
    if (!response.ok) throw new Error('Failed to load body shapes');
    bodyShapes = await response.json();
    populateBodyShapeDropdown();
  } catch (error) {
    console.error('Error loading body shapes:', error);
  }
}

// ==================== SEASONS ====================
async function loadSeasons() {
  try {
    const response = await fetch(`${API_BASE}&action=seasons`);
    if (!response.ok) throw new Error('Failed to load seasons');
    seasons = await response.json();
    populateSeasonDropdown();
  } catch (error) {
    console.error('Error loading seasons:', error);
  }
}

function populateSeasonDropdown(selectedSeasonId = '') {
  const colorSeasonSelect = document.getElementById('newColorSeason');
  if (!colorSeasonSelect) return;

  colorSeasonSelect.innerHTML = '<option value="">Select Season</option>';
  seasons.forEach(season => {
    const isSelected = String(season.SEASON_ID) === String(selectedSeasonId);
    colorSeasonSelect.innerHTML += `
      <option value="${season.SEASON_ID}" ${isSelected ? 'selected' : ''}>
        ${season.SEASON_TYPE}
      </option>
    `;
  });
}


function populateBodyShapeDropdown() {
  const select = document.getElementById('bodyShapeSelect');
  if (!select) return;
  select.innerHTML = '<option value="">Select Body Shape</option>';
  bodyShapes.forEach(shape => {
    select.innerHTML += `<option value="${shape.BODY_SHAPE_ID}">${shape.BODY_TYPE}</option>`;
  });
}

// Populate the "Season" select in the inventory modal
function populateInventorySeasonFilter(selectedSeasonId = '') {
  const seasonSelect = document.getElementById('inventorySeasonFilter');
  if (!seasonSelect) return;

  seasonSelect.innerHTML = '<option value="">Select Season</option>';

  seasons.forEach(season => {
    seasonSelect.innerHTML += `
      <option value="${season.SEASON_ID}" ${String(season.SEASON_ID) === String(selectedSeasonId) ? 'selected' : ''}>
        ${season.SEASON_TYPE}
      </option>
    `;
  });
}

// Populate the "Color" select based on selected season
function populateInventoryColorDropdown(seasonId = '', selectedColorId = '') {
  const colorSelect = document.getElementById('inventoryColor');
  if (!colorSelect) return;

  // If no season selected, show placeholder and disable
  if (!seasonId) {
    colorSelect.innerHTML = '<option value="">Select a season first</option>';
    colorSelect.disabled = true;
    return;
  }

  // Filter colors[] by SEASON_ID
  const filteredColors = colors.filter(c => String(c.SEASON_ID) === String(seasonId));

  if (filteredColors.length === 0) {
    colorSelect.innerHTML = '<option value="">No colors for this season</option>';
    colorSelect.disabled = true;
    return;
  }

  colorSelect.disabled = false;
  colorSelect.innerHTML = '<option value="">Select Color</option>';

  filteredColors.forEach(c => {
    colorSelect.innerHTML += `
      <option value="${c.COLOR_ID}" ${String(c.COLOR_ID) === String(selectedColorId) ? 'selected' : ''}>
        ${c.COLOR_VALUE}
      </option>
    `;
  });
}


// ==================== FORM HANDLERS ====================
function initFormHandlers() {
  // Product Form
  document.getElementById('productForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await saveProduct();
  });

  // Inventory Form
  document.getElementById('inventoryForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await saveInventoryVariant();
  });

  // Add Color Form
  document.getElementById('addColorForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await saveColor();
  });

  // Close modals on outside click
  window.onclick = (event) => {
    if (event.target.classList.contains('modal')) {
      event.target.style.display = 'none';
    }
  };
}


// ==================== ORDERS ====================
async function loadOrders() {
  try {
    const response = await fetch(`${API_BASE}&action=orders`);
    if (!response.ok) throw new Error('Failed to load orders');
    orders = await response.json();
    currentOrders = orders.slice();
    orderPage = 1;

    renderOrders();
    renderOrderPagination();
  } catch (error) {
    console.error('Error loading orders:', error);
  }
}

function renderOrders() {
  const tbody = document.getElementById('orderTableBody');
  tbody.innerHTML = '';

  const list = currentOrders || [];

  if (!Array.isArray(list) || list.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No orders found</td></tr>';
    return;
  }

  const start = (orderPage - 1) * orderPageSize;
  const end   = start + orderPageSize;
  const pageItems = list.slice(start, end);

  pageItems.forEach(order => {
    const row = document.createElement('tr');
    const statusClass = order.STATUS.toLowerCase();

    row.innerHTML = `
      <td>#${order.ORDER_ID}</td>
      <td>${order.USERNAME || 'N/A'}</td>
      <td>${order.ITEM_COUNT || 0} item(s)</td>
      <td>$${parseFloat(order.TOTAL_AMOUNT).toFixed(2)}</td>
      <td><span class="badge ${statusClass}">${order.STATUS}</span></td>
      <td>${order.SHIPPING_REQUIRED ? 'Yes' : 'No'}</td>
      <td>
        <button class="btn-icon" onclick="viewOrder(${order.ORDER_ID})">
          <i class="bi bi-eye"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function renderOrderPagination() {
    renderPagination(
        "orderPagination",
        orderPage,
        currentOrders.length,
        orderPageSize,
        (p) => {
            orderPage = p;
            renderOrders();
            renderOrderPagination();
        }
    );
}


/* Filter hooked from HTML: orderSearch + orderStatusFilter */
function filterOrders() {
  const term = document.getElementById('orderSearch').value.toLowerCase();
  const status = document.getElementById('orderStatusFilter').value; // "", "confirmed", "cancelled", ...

  const filtered = orders.filter(order => {
    const matchesSearch =
      String(order.ORDER_ID).includes(term) ||
      (order.USERNAME && order.USERNAME.toLowerCase().includes(term));

    const matchesStatus =
      !status || (order.STATUS && order.STATUS.toLowerCase() === status.toLowerCase());

    return matchesSearch && matchesStatus;
  });

  currentOrders = filtered;
  orderPage = 1;

  renderOrders();
  renderOrderPagination();
}

async function viewOrder(orderId) {
  try {
    // ✅ Call the correct route: action=orders with an id
    const response = await fetch(`${API_BASE}&action=orders&id=${orderId}`, {
      credentials: 'include',
    });

    const raw = await response.text();
    console.log('Order details raw response:', raw);

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const orderDetails = JSON.parse(raw);

    if (orderDetails.error) {
      // In case PHP returns { error: '...' }
      throw new Error(orderDetails.error);
    }

    const modal = document.getElementById('orderModal');
    const detailsDiv = document.getElementById('orderDetails');

    detailsDiv.innerHTML = `
      <div class="order-details">
        <div class="detail-row"><strong>Order ID:</strong> #${orderDetails.ORDER_ID}</div>
        <div class="detail-row"><strong>Customer:</strong> ${orderDetails.USERNAME || 'N/A'}</div>
        <div class="detail-row"><strong>Email:</strong> ${orderDetails.EMAIL || 'N/A'}</div>
        <div class="detail-row">
          <strong>Status:</strong>
          <span class="badge ${orderDetails.STATUS.toLowerCase()}">${orderDetails.STATUS}</span>
        </div>
        <div class="detail-row"><strong>Total:</strong> $${parseFloat(orderDetails.TOTAL_AMOUNT).toFixed(2)}</div>
        <div class="detail-row"><strong>Order Date:</strong> ${new Date(orderDetails.CREATED_AT).toLocaleString()}</div>
        <hr>
        <h3>Order Items</h3>
        <table class="items-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Size</th>
              <th>Color</th>
              <th>Qty</th>
              <th>Price</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${
              (orderDetails.items || [])
                .map(item => `
                  <tr>
                    <td>${item.PRODUCT_NAME}</td>
                    <td>${item.SIZE}</td>
                    <td>${item.COLOR_VALUE}</td>
                    <td>${item.QUANTITY}</td>
                    <td>₱${parseFloat(item.PRICE || item.UNIT_PRICE).toFixed(2)}</td>
                    <td>₱${(parseFloat(item.PRICE || item.UNIT_PRICE) * item.QUANTITY).toFixed(2)}</td>
                  </tr>
                `)
                .join('')
            }
          </tbody>
        </table>
      </div>
    `;

    modal.style.display = 'block';
  } catch (error) {
    console.error('Error viewing order:', error);
    showNotification('Failed to load order details: ' + error.message, 'error');
  }
}


function closeOrderModal() {
  document.getElementById('orderModal').style.display = 'none';
}

// ==================== UTILITIES ====================
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'}"></i>
    <span>${message}</span>
  `;

  document.body.appendChild(notification);
  setTimeout(() => notification.classList.add('show'), 100);
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

function logout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = '?page=logout';
  }
}

// === AUDIT TRAIL ===


async function loadAudit() {
  try {
    const res = await fetch('index.php?api=admin&action=get_audit');
    const json = await res.json();

    console.log('Audit API response:', json); // for debugging

    if (json.status !== 'success') {
      console.error('Failed to load audit logs:', json);
      return;
    }

    auditLogs = json.data || [];
    displayAuditLogs = auditLogs.slice();
    auditPage = 1;

    renderAudit();
    renderAuditPagination();
  } catch (err) {
    console.error('loadAudit error:', err);
  }
}

function renderAudit() {
  const tbody = document.getElementById('auditTableBody');
  if (!tbody) {
    console.error('auditTableBody not found in DOM');
    return;
  }

  const list = displayAuditLogs || [];

  if (!list.length) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" style="text-align:center; padding: 12px;">
          No audit logs found.
        </td>
      </tr>
    `;
    return;
  }

  const start = (auditPage - 1) * auditPageSize;
  const end   = start + auditPageSize;
  const pageItems = list.slice(start, end);

  tbody.innerHTML = pageItems.map(log => `
    <tr>
      <td>${log.USERNAME || 'N/A'}</td>
      <td>${log.ACTION}</td>
      <td>${log.DESCRIPTION || ''}</td>
      <td>${log.IP_ADDRESS || ''}</td>
      <td>${log.CREATED_AT}</td>
    </tr>
  `).join('');
}

function renderAuditPagination() {
    renderPagination(
        "auditPagination",
        auditPage,
        displayAuditLogs.length,
        auditPageSize,
        (p) => {
            auditPage = p;
            renderAudit();
            renderAuditPagination();
        }
    );
}


function filterAudit() {
  const input = document.getElementById('auditSearch');
  if (!input) return;

  const term = input.value.toLowerCase();

  const filtered = auditLogs.filter(log => {
    return (
      (log.USERNAME && log.USERNAME.toLowerCase().includes(term)) ||
      (log.ACTION && log.ACTION.toLowerCase().includes(term)) ||
      (log.DESCRIPTION && log.DESCRIPTION.toLowerCase().includes(term)) ||
      (log.IP_ADDRESS && log.IP_ADDRESS.toLowerCase().includes(term)) ||
      (log.CREATED_AT && log.CREATED_AT.toLowerCase().includes(term))
    );
  });

  displayAuditLogs = filtered;
  auditPage = 1;

  renderAudit();
  renderAuditPagination();
}

// expose to inline HTML
window.loadAudit = loadAudit;
window.filterAudit = filterAudit;
