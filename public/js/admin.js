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
});

// ==================== NAVIGATION ====================
function initNavigation() {
  const navBtns = document.querySelectorAll('.nav-btn');
  navBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const target = btn.getAttribute('href').substring(1);
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
      <td>$${parseFloat(p.PRICE).toFixed(2)}</td>
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
  const container = document.getElementById('productPagination');
  if (!container) return;

  const totalItems  = products.length;
  const totalPages  = Math.ceil(totalItems / productPageSize) || 1;

  container.innerHTML = '';

  if (totalPages <= 1) {
    // If you still want to show "Page 1 of 1", uncomment next lines:
    // const span = document.createElement('span');
    // span.textContent = `Page 1 of 1 (${totalItems} products)`;
    // container.appendChild(span);
    return;
  }

  const createBtn = (label, disabled, onClick, isActive = false) => {
    const btn = document.createElement('button');
    btn.textContent = label;
    btn.disabled = disabled;
    btn.className = 'btn btn-secondary btn-sm';
    if (isActive) btn.classList.add('active-page');
    if (!disabled && onClick) btn.onclick = onClick;
    return btn;
  };

  const total = totalPages;
  const current = productPage;
  const maxButtons = 5;

  // Prev
  container.appendChild(
    createBtn('Prev', current <= 1, () => {
      productPage--;
      renderProductsTable();
      renderProductPagination();
    })
  );

  let start = Math.max(1, current - Math.floor(maxButtons / 2));
  let end   = start + maxButtons - 1;

  if (end > total) {
    end = total;
    start = Math.max(1, end - maxButtons + 1);
  }

  // First + ...
  if (start > 1) {
    container.appendChild(
      createBtn('1', false, () => {
        productPage = 1;
        renderProductsTable();
        renderProductPagination();
      }, current === 1)
    );
    if (start > 2) {
      const dots = document.createElement('span');
      dots.textContent = '...';
      dots.style.margin = '0 4px';
      container.appendChild(dots);
    }
  }

  // Main window
  for (let p = start; p <= end; p++) {
    container.appendChild(
      createBtn(
        String(p),
        false,
        () => {
          productPage = p;
          renderProductsTable();
          renderProductPagination();
        },
        p === current
      )
    );
  }

  // ... + Last
  if (end < total) {
    if (end < total - 1) {
      const dots = document.createElement('span');
      dots.textContent = '...';
      dots.style.margin = '0 4px';
      container.appendChild(dots);
    }
    container.appendChild(
      createBtn(
        String(total),
        false,
        () => {
          productPage = total;
          renderProductsTable();
          renderProductPagination();
        },
        current === total
      )
    );
  }

  // Next
  container.appendChild(
    createBtn('Next', current >= total, () => {
      productPage++;
      renderProductsTable();
      renderProductPagination();
    })
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
async function openInventoryManager(productId, productName) {
  selectedProductId = productId;
  selectedProductName = productName;
  document.getElementById('inventoryProductTitle').textContent = `Inventory - ${decodeURIComponent(productName)}`;
  document.getElementById('inventorySubtitle').textContent = `Managing stock variants for ${productName}`;
  switchSection('inventory');
  await loadInventory(productId, inventoryPage);
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
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No variants found. Click "Add Variant" to create one.</td></tr>';
    return;
  }

  const start = (inventoryPage - 1) * inventoryPageSize;
  const end   = start + inventoryPageSize;
  const pageItems = inventoryItems.slice(start, end);

  pageItems.forEach(v => {
    const row = document.createElement('tr');
    const stockClass = v.QUANTITY === 0 ? 'out-of-stock' : v.QUANTITY < 10 ? 'low-stock' : 'in-stock';
    row.innerHTML = `
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
  const container = document.getElementById('inventoryPagination');
  if (!container) return;

  const totalItems = inventoryItems.length;
  const totalPages = Math.ceil(totalItems / inventoryPageSize) || 1;

  container.innerHTML = '';

  if (totalPages <= 1) {
    return;
  }

  const createBtn = (label, disabled, onClick, isActive = false) => {
    const btn = document.createElement('button');
    btn.textContent = label;
    btn.disabled = disabled;
    btn.className = 'btn btn-secondary btn-sm';
    if (isActive) btn.classList.add('active-page');
    if (!disabled && onClick) btn.onclick = onClick;
    return btn;
  };

  const current = inventoryPage;
  const total   = totalPages;
  const maxButtons = 5;

  // Prev
  container.appendChild(
    createBtn('Prev', current <= 1, () => {
      inventoryPage--;
      renderInventoryTable();
      renderInventoryPagination();
    })
  );

  let start = Math.max(1, current - Math.floor(maxButtons / 2));
  let end   = start + maxButtons - 1;

  if (end > total) {
    end = total;
    start = Math.max(1, end - maxButtons + 1);
  }

  // First + ...
  if (start > 1) {
    container.appendChild(
      createBtn('1', false, () => {
        inventoryPage = 1;
        renderInventoryTable();
        renderInventoryPagination();
      }, current === 1)
    );
    if (start > 2) {
      const dots = document.createElement('span');
      dots.textContent = '...';
      dots.style.margin = '0 4px';
      container.appendChild(dots);
    }
  }

  // Main range
  for (let p = start; p <= end; p++) {
    container.appendChild(
      createBtn(
        String(p),
        false,
        () => {
          inventoryPage = p;
          renderInventoryTable();
          renderInventoryPagination();
        },
        p === current
      )
    );
  }

  // ... + Last
  if (end < total) {
    if (end < total - 1) {
      const dots = document.createElement('span');
      dots.textContent = '...';
      dots.style.margin = '0 4px';
      container.appendChild(dots);
    }
    container.appendChild(
      createBtn(
        String(total),
        false,
        () => {
          inventoryPage = total;
          renderInventoryTable();
          renderInventoryPagination();
        },
        current === total
      )
    );
  }

  // Next
  container.appendChild(
    createBtn('Next', current >= total, () => {
      inventoryPage++;
      renderInventoryTable();
      renderInventoryPagination();
    })
  );
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

// ==================== USERS TAB ====================
async function loadUsers() {
  try {
    const res = await fetch(`${API_BASE}&action=users`, {
      credentials: 'include', // send session cookies for admin auth
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch users (HTTP ${res.status})`);
    }

    allUsers = await res.json();
    renderUsers(allUsers);
  } catch (err) {
    console.error('loadUsers error:', err);
    alert('Error loading users. Make sure you are logged in as admin.');
  }
}

function renderUsers(users) {
  const tbody = document.getElementById('userTableBody');
  tbody.innerHTML = '';

  if (!Array.isArray(users) || users.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" style="text-align:center;">No users found</td>
      </tr>
    `;
    return;
  }

  users.forEach(user => {
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

  renderUsers(filtered);
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
    renderOrders();
  } catch (error) {
    console.error('Error loading orders:', error);
  }
}

function renderOrders() {
  const tbody = document.getElementById('orderTableBody');
  tbody.innerHTML = '';

  if (orders.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No orders found</td></tr>';
    return;
  }

  orders.forEach(order => {
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

async function viewOrder(orderId) {
  try {
    const response = await fetch(`${API_BASE}&action=orderDetails&id=${orderId}`);
    if (!response.ok) throw new Error('Failed to load order details');
    const orderDetails = await response.json();

    const modal = document.getElementById('orderModal');
    const detailsDiv = document.getElementById('orderDetails');

    detailsDiv.innerHTML = `
      <div class="order-details">
        <div class="detail-row"><strong>Order ID:</strong> #${orderDetails.ORDER_ID}</div>
        <div class="detail-row"><strong>Customer:</strong> ${orderDetails.USERNAME || 'N/A'}</div>
        <div class="detail-row"><strong>Email:</strong> ${orderDetails.EMAIL || 'N/A'}</div>
        <div class="detail-row"><strong>Status:</strong> <span class="badge ${orderDetails.STATUS.toLowerCase()}">${orderDetails.STATUS}</span></div>
        <div class="detail-row"><strong>Total:</strong> $${parseFloat(orderDetails.TOTAL_AMOUNT).toFixed(2)}</div>
        <div class="detail-row"><strong>Order Date:</strong> ${new Date(orderDetails.CREATED_AT).toLocaleString()}</div>
        <hr>
        <h3>Order Items</h3>
        <table class="items-table">
          <thead>
            <tr><th>Product</th><th>Size</th><th>Color</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
          </thead>
          <tbody>
            ${orderDetails.items.map(item => `
              <tr>
                <td>${item.PRODUCT_NAME}</td>
                <td>${item.SIZE}</td>
                <td>${item.COLOR_VALUE}</td>
                <td>${item.QUANTITY}</td>
                <td>$${parseFloat(item.PRICE).toFixed(2)}</td>
                <td>$${(parseFloat(item.PRICE) * item.QUANTITY).toFixed(2)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;

    modal.style.display = 'block';
  } catch (error) {
    console.error('Error viewing order:', error);
    showNotification('Failed to load order details', 'error');
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