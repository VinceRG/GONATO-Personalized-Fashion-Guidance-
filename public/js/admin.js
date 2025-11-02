    // Sample Data
    let products = [
      { id: 1, name: "Rustic Wrap Dress", size: "Medium", color: "Black", category: "Dresses", price: 79, stock: 15, image: "🎨" },
      { id: 2, name: "Smart Ankle Pants", size: "Small", color: "Yellow", category: "Bottoms", price: 65, stock: 8, image: "🎨" },
      { id: 3, name: "Souffle Yarn Dress", size: "Small", color: "Yellow", category: "Dresses", price: 89, stock: 0, image: "🎨" },
      { id: 4, name: "Cotton Cropped Cardigan", size: "Small", color: "Yellow", category: "Outerwear", price: 55, stock: 22, image: "🎨" },
      { id: 5, name: "Rayon Long Sleeve Blouse", size: "Small", color: "Yellow", category: "Tops", price: 49, stock: 12, image: "🎨" },
      { id: 6, name: "Smart Wide Pants", size: "Small", color: "Yellow", category: "Bottoms", price: 72, stock: 3, image: "🎨" },
      { id: 7, name: "Volume Sleeve Dress", size: "Small", color: "Yellow", category: "Dresses", price: 95, stock: 18, image: "🎨" },
      { id: 8, name: "Flare Midi Dress", size: "Small", color: "Yellow", category: "Dresses", price: 85, stock: 10, image: "🎨" }
    ];

    let users = [
      { id: 1, username: "alice_wonder", email: "alice@example.com", joined: "2024-01-15", status: "active" },
      { id: 2, username: "bob_smith", email: "bob@example.com", joined: "2024-02-20", status: "active" },
      { id: 3, username: "carol_jones", email: "carol@example.com", joined: "2024-03-10", status: "locked" },
      { id: 4, username: "dave_wilson", email: "dave@example.com", joined: "2024-04-05", status: "active" },
      { id: 5, username: "eve_davis", email: "eve@example.com", joined: "2024-05-12", status: "active" }
    ];

    let orders = [
      { id: "ORD-001", customer: "alice_wonder", items: 3, total: 223, status: "pending", shippingRequired: true },
      { id: "ORD-002", customer: "bob_smith", items: 1, total: 79, status: "shipped", shippingRequired: true },
      { id: "ORD-003", customer: "carol_jones", items: 2, total: 134, status: "delivered", shippingRequired: false },
      { id: "ORD-004", customer: "dave_wilson", items: 4, total: 298, status: "pending", shippingRequired: true },
      { id: "ORD-005", customer: "eve_davis", items: 2, total: 170, status: "shipped", shippingRequired: true }
    ];

    // Colors database
    let availableColors = ["Yellow", "Blue", "Black", "Red", "White", "Green"];

    // Populate color dropdown
    function populateColorDropdown() {
      const colorSelect = document.getElementById('colorCategory');
      const currentValue = colorSelect.value;
      
      colorSelect.innerHTML = '<option value="">Select Color</option>' + 
        availableColors.map(color => `<option value="${color}">${color}</option>`).join('');
      
      if (currentValue && availableColors.includes(currentValue)) {
        colorSelect.value = currentValue;
      }
    }

    // Add Color Modal functions
    function openAddColorModal() {
      document.getElementById('addColorModal').classList.add('show');
      document.getElementById('newColorName').focus();
    }

    function closeAddColorModal() {
      document.getElementById('addColorModal').classList.remove('show');
      document.getElementById('addColorForm').reset();
    }

    // Handle Add Color form submission
    document.getElementById('addColorForm').addEventListener('submit', e => {
      e.preventDefault();
      const newColor = document.getElementById('newColorName').value.trim();
      
      if (newColor && !availableColors.includes(newColor)) {
        availableColors.push(newColor);
        availableColors.sort(); // Keep colors alphabetically sorted
        populateColorDropdown();
        document.getElementById('colorCategory').value = newColor; // Auto-select the new color
        closeAddColorModal();
        alert(`Color "${newColor}" has been added successfully!`);
      } else if (availableColors.includes(newColor)) {
        alert(`Color "${newColor}" already exists!`);
      }
    });

    // Navigation
    const navLinks = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        const targetId = link.getAttribute('href').substring(1);
        
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        
        sections.forEach(s => s.classList.remove('active'));
        document.getElementById(targetId).classList.add('active');
      });
    });

    // Render Products
    function renderProducts(productsToRender = products) {
      const tbody = document.getElementById('productTableBody');
      tbody.innerHTML = productsToRender.map(p => {
        const stockClass = p.stock === 0 ? 'out' : p.stock < 10 ? 'low' : 'instock';
        const stockText = p.stock === 0 ? 'Out of Stock' : p.stock < 10 ? `Low (${p.stock})` : p.stock;
        return `
          <tr>
            <td><div class="product-img">${p.image}</div></td>
            <td>${p.name}</td>
            <td>${p.size}</td>
            <td>${p.color}</td>
            <td>${p.category}</td>
            <td>$${p.price}</td>
            <td>${stockText}</td>
            <td class="action-btns">
              <button class="icon-btn" onclick="editProduct(${p.id})" title="Edit">
                <i class="bi bi-pencil-square"></i>
              </button>
              <button class="icon-btn delete" onclick="deleteProduct(${p.id})" title="Delete">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    // Filter Products
    function filterProducts() {
      const search = document.getElementById('productSearch').value.toLowerCase();
      const category = document.getElementById('categoryFilter').value;
      const stock = document.getElementById('stockFilter').value;

      const filtered = products.filter(p => {
        const matchSearch = p.name.toLowerCase().includes(search);
        const matchCategory = !category || p.category === category;
        let matchStock = true;
        if (stock === 'instock') matchStock = p.stock >= 10;
        if (stock === 'low') matchStock = p.stock > 0 && p.stock < 10;
        if (stock === 'out') matchStock = p.stock === 0;
        return matchSearch && matchCategory && matchStock;
      });

      renderProducts(filtered);
    }

    // Product Modal
    function openProductModal(productId = null) {
      const modal = document.getElementById('productModal');
      const form = document.getElementById('productForm');
      const title = document.getElementById('productModalTitle');

      populateColorDropdown(); // Always populate colors when opening modal

      if (productId) {
        const product = products.find(p => p.id === productId);
        title.textContent = 'Edit Product';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('sizeCategory').value = product.size;
        document.getElementById('colorCategory').value = product.color;
        document.getElementById('productCategory').value = product.category;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productDescription').value = product.description || '';
      } else {
        title.textContent = 'Add Product';
        form.reset();
        document.getElementById('productId').value = '';
      }

      modal.classList.add('show');
    }

    function closeProductModal() {
      document.getElementById('productModal').classList.remove('show');
    }

    document.getElementById('productForm').addEventListener('submit', e => {
      e.preventDefault();
      const id = document.getElementById('productId').value;
      const productData = {
        name: document.getElementById('productName').value,
        size: document.getElementById('sizeCategory').value,
        color: document.getElementById('colorCategory').value,
        category: document.getElementById('productCategory').value,
        price: parseFloat(document.getElementById('productPrice').value),
        stock: parseInt(document.getElementById('productStock').value),
        description: document.getElementById('productDescription').value,
        image: "🎨"
      };

      if (id) {
        const index = products.findIndex(p => p.id === parseInt(id));
        products[index] = { ...products[index], ...productData };
      } else {
        const newId = Math.max(...products.map(p => p.id)) + 1;
        products.push({ id: newId, ...productData });
      }

      renderProducts();
      closeProductModal();
    });

    function editProduct(id) {
      openProductModal(id);
    }

    function deleteProduct(id) {
      if (confirm('Are you sure you want to delete this product?')) {
        products = products.filter(p => p.id !== id);
        renderProducts();
      }
    }

    // Render Users
    function renderUsers(usersToRender = users) {
      const tbody = document.getElementById('userTableBody');
      tbody.innerHTML = usersToRender.map(u => `
        <tr>
          <td>${u.username}</td>
          <td>${u.email}</td>
          <td>${u.joined}</td>
          <td><span class="status-badge status-${u.status}">${u.status.toUpperCase()}</span></td>
          <td class="action-btns">
            <button class="btn ${u.status === 'active' ? 'btn-danger' : 'btn-success'}" 
                    onclick="toggleUserStatus(${u.id})">
              <i class="bi bi-${u.status === 'active' ? 'lock' : 'unlock'}"></i>
              ${u.status === 'active' ? 'Lock' : 'Unlock'}
            </button>
          </td>
        </tr>
      `).join('');
    }

    function filterUsers() {
      const search = document.getElementById('userSearch').value.toLowerCase();
      const status = document.getElementById('statusFilter').value;

      const filtered = users.filter(u => {
        const matchSearch = u.username.toLowerCase().includes(search) || 
                          u.email.toLowerCase().includes(search);
        const matchStatus = !status || u.status === status;
        return matchSearch && matchStatus;
      });

      renderUsers(filtered);
    }

    function toggleUserStatus(id) {
      const user = users.find(u => u.id === id);
      user.status = user.status === 'active' ? 'locked' : 'active';
      renderUsers();
    }

    // Render Orders
    function renderOrders(ordersToRender = orders) {
      const tbody = document.getElementById('orderTableBody');
      tbody.innerHTML = ordersToRender.map(o => `
        <tr>
          <td><strong>${o.id}</strong></td>
          <td>${o.customer}</td>
          <td>${o.items} items</td>
          <td>${o.total}</td>
          <td><span class="status-badge status-${o.status}">${o.status.toUpperCase()}</span></td>
          <td>
            <span class="status-badge ${o.shippingRequired ? 'status-pending' : 'status-delivered'}">
              ${o.shippingRequired ? 'Required' : 'Not Required'}
            </span>
          </td>
          <td class="action-btns">
            <button class="icon-btn" onclick="viewOrderDetails('${o.id}')" title="View Details">
              <i class="bi bi-eye"></i>
            </button>
            <select onchange="updateOrderStatus('${o.id}', this.value)" style="padding: 0.5rem; border-radius: 6px; border: 1px solid #ddd;">
              <option value="">Update Status</option>
              <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
              <option value="confirmed" ${o.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
              <option value="shipped" ${o.status === 'shipped' ? 'selected' : ''}>Shipped</option>
              <option value="delivered" ${o.status === 'delivered' ? 'selected' : ''}>Delivered</option>
              <option value="cancelled" ${o.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
            </select>
          </td>
        </tr>
      `).join('');
    }

    function filterOrders() {
      const search = document.getElementById('orderSearch').value.toLowerCase();
      const status = document.getElementById('orderStatusFilter').value;

      const filtered = orders.filter(o => {
        const matchSearch = o.id.toLowerCase().includes(search) || 
                          o.customer.toLowerCase().includes(search);
        const matchStatus = !status || o.status === status;
        return matchSearch && matchStatus;
      });

      renderOrders(filtered);
    }

    function updateOrderStatus(orderId, newStatus) {
      if (!newStatus) return;
      const order = orders.find(o => o.id === orderId);
      if (order) {
        order.status = newStatus;
        renderOrders();
      }
    }

    function viewOrderDetails(orderId) {
      const order = orders.find(o => o.id === orderId);
      if (!order) return;

      const detailsDiv = document.getElementById('orderDetails');
      detailsDiv.innerHTML = `
        <div style="margin-bottom: 1.5rem;">
          <h3 style="color: var(--color-accent-dark); margin-bottom: 1rem;">Order ${order.id}</h3>
          <div style="background: #f9f9f9; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem;">
            <p style="margin-bottom: 0.75rem;"><strong>Customer:</strong> ${order.customer}</p>
            <p style="margin-bottom: 0.75rem;"><strong>Items:</strong> ${order.items}</p>
            <p style="margin-bottom: 0.75rem;"><strong>Total:</strong> ${order.total}</p>
            <p style="margin-bottom: 0.75rem;"><strong>Status:</strong> <span class="status-badge status-${order.status}">${order.status.toUpperCase()}</span></p>
            <p style="margin-bottom: 0.75rem;"><strong>Shipping Required:</strong> ${order.shippingRequired ? 'Yes' : 'No'}</p>
          </div>
          ${order.shippingRequired ? `
            <div style="background: #fff3cd; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #ffc107;">
              <h4 style="color: #856404; margin-bottom: 0.75rem;"><i class="bi bi-truck"></i> Shipping Information</h4>
              <p style="color: #856404; margin-bottom: 0.5rem;"><strong>Address:</strong> 123 Main Street, City, State 12345</p>
              <p style="color: #856404; margin-bottom: 0.5rem;"><strong>Phone:</strong> +1 (555) 123-4567</p>
              <p style="color: #856404; margin-bottom: 0;"><strong>Delivery Notes:</strong> Please call upon arrival</p>
            </div>
          ` : `
            <div style="background: #d4edda; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #28a745;">
              <h4 style="color: #155724; margin-bottom: 0.5rem;"><i class="bi bi-check-circle"></i> Digital Order</h4>
              <p style="color: #155724; margin: 0;">No shipping required for this order</p>
            </div>
          `}
        </div>
      `;

      document.getElementById('orderModal').classList.add('show');
    }

    function closeOrderModal() {
      document.getElementById('orderModal').classList.remove('show');
    }

    function logout() {
      if (confirm('Are you sure you want to logout?')) {
        alert('Logging out... Redirecting to login page.');
      }
    }

    // Initialize
    populateColorDropdown();
    renderProducts();
    renderUsers();
    renderOrders();

    // Close modals on outside click
    document.getElementById('productModal').addEventListener('click', e => {
      if (e.target.id === 'productModal') closeProductModal();
    });

    document.getElementById('orderModal').addEventListener('click', e => {
      if (e.target.id === 'orderModal') closeOrderModal();
    });

    document.getElementById('addColorModal').addEventListener('click', e => {
      if (e.target.id === 'addColorModal') closeAddColorModal();
    });