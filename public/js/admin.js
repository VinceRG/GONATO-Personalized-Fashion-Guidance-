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
let currentEditId = null;
let currentEditInventoryId = null; // track edit mode


// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  loadColors();
  loadBodyShapes();
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
    products = await response.json();

    const tbody = document.getElementById('productListBody');
    tbody.innerHTML = '';

    if (!Array.isArray(products) || products.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No products found</td></tr>';
      return;
    }

    products.forEach(p => {
      const row = document.createElement('tr');

      const safeName = encodeURIComponent(p.PRODUCT_NAME);

      row.innerHTML = `
        <td><img src="public/image/${p.IMAGE_FILE || 'placeholder.png'}" style="width:50px;height:50px;object-fit:cover;border-radius:4px;"></td>
        <td>${p.PRODUCT_NAME}</td>
        <td>${p.DESCRIPTION || 'No description'}</td>
        <td>${p.CATEGORY || 'N/A'}</td>
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
  } catch (error) {
    console.error('Error loading products:', error);
    const tbody = document.getElementById('productListBody');
    tbody.innerHTML = `<tr><td colspan="6" style="color:red;text-align:center;">Failed to load products: ${error.message}</td></tr>`;
  }
}


function openProductModal(productId = null) {
  const modal = document.getElementById('productModal');
  const form = document.getElementById('productForm');
  const title = document.getElementById('productModalTitle');

  form.reset();
  currentEditId = productId;

  if (productId) {
    // Edit mode
    title.textContent = 'Edit Product';
    const product = products.find(p => p.PRODUCT_ID === productId);
    if (product) {
      document.getElementById('productName').value = product.PRODUCT_NAME;
      document.getElementById('productDescription').value = product.DESCRIPTION || '';
      document.getElementById('productPrice').value = product.PRICE;
      document.getElementById('bodyShapeSelect').value = product.BODY_SHAPE_ID || '';
    }
  } else {
    // Add mode
    title.textContent = 'Add Product';
  }

  modal.style.display = 'block';
}

function closeProductModal() {
  document.getElementById('productModal').style.display = 'none';
  currentEditId = null;
}

async function saveProduct() {
  const formData = {
    productName: document.getElementById('productName').value,
    description: document.getElementById('productDescription').value,
    price: parseFloat(document.getElementById('productPrice').value),
    bodyShapeId: parseInt(document.getElementById('bodyShapeSelect').value)
  };

  console.log('Saving product:', formData);

  try {
    let url, method;

    if (currentEditId) {
      url = `${API_BASE}&action=updateProduct&id=${currentEditId}`;
      method = 'PUT';
    } else {
      url = `${API_BASE}&action=addProduct`;
      method = 'POST';
    }

    const response = await fetch(url, {
      method: method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData),
      credentials: 'include'
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error('Error response:', errorData);
      throw new Error('Failed to save product');
    }

    showNotification(currentEditId ? 'Product updated successfully' : 'Product added successfully', 'success');
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
  const product = products.find(p => p.PRODUCT_ID === productId);
  if (!product) return;

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
    const res = await fetch(`index.php?api=admin&action=users&id=${userId}&toggle-lock`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ isLocked: !isLocked })
    });

    const result = await res.json();
    if (result.success) {
      alert(result.message); // shows "User account unlocked successfully."
      await loadUsers();
    } else {
      alert('Failed to update user status: ' + result.message);
    }
  } catch (err) {
    console.error(err);
    alert('Error updating user status');
  }
}

// ==================== INVENTORY TAB ====================
async function openInventoryManager(productId, productName) {
  selectedProductId = productId;
  selectedProductName = productName;
  document.getElementById('inventoryProductTitle').textContent = `Inventory - ${decodeURIComponent(productName)}`;
  document.getElementById('inventorySubtitle').textContent = `Managing stock variants for ${productName}`;
  switchSection('inventory');
  await loadInventory(productId);
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

    const data = await res.json();
    console.log('Inventory data:', data);
    tbody.innerHTML = '';

    if (!Array.isArray(data) || data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No variants found. Click "Add Variant" to create one.</td></tr>';
      return;
    }

    data.forEach(v => {
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
      <button class="btn-icon" onclick="openDeleteModal(${v.INVENTORY_ID})" title="Delete">
        <i class="bi bi-trash"></i>
      </button>
    </td>
  `;
      tbody.appendChild(row);
    });

  } catch (err) {
    console.error('Error loading inventory:', err);
    tbody.innerHTML = `<tr><td colspan="5" style="color:red;text-align:center;">Failed to load inventory: ${err.message}</td></tr>`;
  }
}

async function openInventoryModal(editItem = null) {
  const modal = document.getElementById('inventoryModal');
  const title = document.getElementById('inventoryModalTitle');
  const colorSelect = document.getElementById('inventoryColor');

  // Populate color dropdown
  colorSelect.innerHTML = '<option value="">Select Color</option>';
  colors.forEach(c => {
    colorSelect.innerHTML += `<option value="${c.COLOR_ID}">${c.COLOR_VALUE}</option>`;
  });

  if (editItem) {
    // EDIT mode
    currentEditInventoryId = editItem.INVENTORY_ID;
    title.textContent = 'Edit Variant';
    document.getElementById('inventorySize').value = editItem.SIZE;
    document.getElementById('inventoryQuantity').value = editItem.QUANTITY;
    colorSelect.value = editItem.COLOR_ID; // select correct color
  } else {
    // ADD mode
    currentEditInventoryId = null;
    title.textContent = 'Add Variant';
    document.getElementById('inventoryForm').reset();
  }

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
    await loadInventory(selectedProductId);
  } catch (err) {
    console.error(err);
    showNotification('Failed to save variant: ' + err.message, 'error');
  }
}

async function editInventory(inventoryId) {
  // Find the item in current product's inventory
  const res = await fetch(`${API_BASE}&action=inventoryByProduct&product_id=${selectedProductId}`, {
    credentials: 'include'
  });
  const data = await res.json();
  const item = data.find(i => i.INVENTORY_ID === inventoryId);
  if (!item) return;

  openInventoryModal(item);
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
    await loadInventory(selectedProductId);
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
  document.getElementById('addColorModal').style.display = 'block';
  document.getElementById('addColorForm').reset();
}

function closeAddColorModal() {
  document.getElementById('addColorModal').style.display = 'none';
}

async function saveColor() {
  const colorName = document.getElementById('newColorName').value.trim();

  try {
    const response = await fetch(`${API_BASE}&action=colors`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ colorValue: colorName })
    });

    if (!response.ok) throw new Error('Failed to add color');

    showNotification('Color added successfully', 'success');
    closeAddColorModal();
    await loadColors();
  } catch (error) {
    console.error('Error saving color:', error);
    showNotification('Failed to add color', 'error');
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

function populateBodyShapeDropdown() {
  const select = document.getElementById('bodyShapeSelect');
  if (!select) return;
  select.innerHTML = '<option value="">Select Body Shape</option>';
  bodyShapes.forEach(shape => {
    select.innerHTML += `<option value="${shape.BODY_SHAPE_ID}">${shape.BODY_TYPE}</option>`;
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