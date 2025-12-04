<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// basic guard
if (empty($_SESSION['is_admin']) || empty($_SESSION['admin_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$adminRole     = $_SESSION['admin_role']     ?? 'staff';      // 'super_admin' | 'staff'
$adminUsername = $_SESSION['admin_username'] ?? '@admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" />
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" />

  <!-- Your main admin styles -->
  <link rel="stylesheet" href="public/css/admin.css" />

  <!-- Extra styles for analytics cards & charts -->
  <style>
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr)); /* 4 metrics */
      gap: 16px;
      margin-bottom: 24px;
    }

    .metric-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 16px 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .metric-label {
      font-size: 0.9rem;
      color: #666;
    }

    .metric-value {
      font-size: 1.6rem;
      font-weight: 600;
      margin-top: 6px;
    }

    .analytics-charts {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 24px;
      margin-bottom: 24px;
    }

    .chart-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 16px 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    @media (max-width: 900px) {
      .metrics-grid {
        grid-template-columns: 1fr;
      }
      .analytics-charts {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <div class="admin-container">
    <!-- SIDEBAR -->
    <div class="sidebar">
      <div>
        <div class="profile">
          <div class="profile-pic"><i class="bi bi-shield-check"></i></div>
          <div class="profile-info">
            <h4>
              <?php echo $adminRole === 'super_admin' ? 'Super Admin Panel' : 'Staff Panel'; ?>
            </h4>
            <p><i><?php echo htmlspecialchars($adminUsername); ?></i></p>
            <small style="font-size: 0.8rem; color:#ccc;">
              <?php echo $adminRole === 'super_admin' ? 'Super Admin' : 'Staff'; ?>
            </small>
          </div>
        </div>

        <nav class="nav-links">
          <a href="#products" class="nav-btn active" onclick="switchSection('products'); return false;">
            <i class="bi bi-tags"></i> Products
          </a>
          <a href="#inventory" class="nav-btn" onclick="switchSection('inventory'); return false;">
            <i class="bi bi-box-seam"></i> Inventory
          </a>

          <?php if ($adminRole === 'super_admin'): ?>
          <a href="#users" class="nav-btn" onclick="switchSection('users'); return false;">
            <i class="bi bi-people"></i> Users
          </a>
          <?php endif; ?>

          <a href="#orders" class="nav-btn" onclick="switchSection('orders'); return false;">
            <i class="bi bi-cart-check"></i> Orders
          </a>

          <?php if ($adminRole === 'super_admin'): ?>
            <a href="#staff" class="nav-btn" onclick="switchSection('staff'); loadStaff(); return false;">
              <i class="bi bi-person-gear"></i> Staff
            </a>
            <a href="#audit" class="nav-btn" onclick="switchSection('audit'); loadAudit(); return false;">
              <i class="bi bi-clipboard-data"></i> Audit Trail
            </a>
          <?php endif; ?>

          <!-- Analytics nav triggers Flask analytics + section switch -->
          <a href="#analytics" class="nav-btn"
             onclick="switchSection('analytics'); loadFlaskAnalytics(); return false;">
            <i class="bi bi-bar-chart"></i> Analytics
          </a>
        </nav>
      </div>

      <div class="sidebar-footer">
        <div class="logo-section">
          <div class="logo-img">
            <img src="public/image/amarelle.png" alt="Amarelle Logo">
          </div>
          <div class="logo-text-content">
            <div class="logo-text">Amarelle</div>
            <div class="logo-tagline">Admin Portal</div>
          </div>
        </div>
        <button class="logout-btn" onclick="logout()" title="Logout">
          <i class="bi bi-box-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

      <div class="top-utils">
        <button class="icon-btn" id="lowStockBtn" type="button" title="Low stock alerts">
          <i class="bi bi-bell"></i>
          <span class="badge" id="lowStockCount" hidden></span>
        </button>

        <div class="notif-dropdown hidden" id="lowStockDropdown">
          <div class="notif-header">Low stock alerts</div>
          <ul id="lowStockList"></ul>
          <button class="notif-footer-btn" type="button" onclick="switchSection('inventory')">
            View inventory
          </button>
        </div>
      </div>

      <!-- PRODUCTS SECTION -->
      <section id="products" class="content-section active">
        <div class="section-header">
          <div>
            <div class="section-title"><i>Product Management</i></div>
            <div class="section-subtitle">Manage base product details</div>
          </div>
          <button class="btn" onclick="openProductModal()">
            <i class="bi bi-plus-circle"></i> Add Product
          </button>
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Body Shape</th>
                <th>Price</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="productListBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
        </div>
        <div id="productPagination" class="pagination-controls"></div>
      </section>

      <!-- INVENTORY SECTION -->
      <section id="inventory" class="content-section">
        <div class="section-header">
          <div>
            <div class="section-title" id="inventoryProductTitle">Inventory Management</div>
            <div class="section-subtitle" id="inventorySubtitle">Select a product to view its stock variants</div>
          </div>
          <button class="btn" onclick="openInventoryModal()">
            <i class="bi bi-plus-circle"></i> Add Variant
          </button>
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Color</th>
                <th>Quantity</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="inventoryTableBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
        </div>
        <div id="inventoryPagination" class="pagination-controls"></div>
      </section>

      <!-- USERS SECTION – only for super_admin -->
      <?php if ($adminRole === 'super_admin'): ?>
      <section id="users" class="content-section">
        <div class="section-header">
          <div>
            <div class="section-title"><i>User Management</i></div>
            <div class="section-subtitle">View and manage user accounts</div>
          </div>
        </div>

        <div class="filter-bar">
          <input type="text" id="userSearch" placeholder="Search users..." onkeyup="filterUsers()">
          <select id="statusFilter" onchange="filterUsers()">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="locked">Locked</option>
          </select>
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Joined Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
        </div>
        <div id="userPagination" class="pagination-controls"></div>
      </section>
      <?php endif; ?>

      <!-- ORDERS SECTION -->
      <section id="orders" class="content-section">
        <div class="section-header">
          <div>
            <div class="section-title"><i>Order Management</i></div>
            <div class="section-subtitle">Track and manage customer orders</div>
          </div>
        </div>

        <div class="filter-bar">
          <input type="text" id="orderSearch" placeholder="Search orders..." onkeyup="filterOrders()">
          <select id="orderStatusFilter" onchange="filterOrders()">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="delivered">Delivered</option>
          </select>
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="orderTableBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
        </div>
        <div id="orderPagination" class="pagination-controls"></div>
      </section>

      <!-- STAFF SECTION – only for super_admin -->
      <?php if ($adminRole === 'super_admin'): ?>
      <section id="staff" class="content-section">
        <div class="section-header">
          <div>
            <div class="section-title"><i>Staff Management</i></div>
            <div class="section-subtitle">Create and manage admin staff accounts</div>
          </div>
          <button class="btn" type="button" onclick="openStaffModal()">
            <i class="bi bi-person-plus"></i> Add Staff
          </button>
        </div>

        <div class="filter-bar">
          <input 
            type="text" 
            id="staffSearch" 
            placeholder="Search staff by username or role..." 
            onkeyup="filterStaff()"
          >
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="staffTableBody">
              <!-- populated by JS -->
            </tbody>
          </table>
        </div>
        <div id="staffPagination" class="pagination-controls"></div>
      </section>

      <!-- AUDIT SECTION – only for super_admin -->
      <section id="audit" class="content-section">
        <div class="section-header">
          <div>
            <div class="section-title"><i>Audit Trail</i></div>
            <div class="section-subtitle">Track admin actions</div>
          </div>
        </div>

        <div class="filter-bar">
          <input type="text" id="auditSearch" placeholder="Search audit logs..." onkeyup="filterAudit()">
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Admin</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody id="auditTableBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
        </div>
        <div id="auditPagination" class="pagination-controls"></div>
      </section>
      <?php endif; ?>

      <!-- ANALYTICS SECTION -->
      <section id="analytics" class="content-section">
        <div class="section-header">
          <div>
            <div class="section-title"><i>Analytics</i></div>
            <div class="section-subtitle">Insights from body shape dataset</div>
          </div>
        </div>

        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-label">Average Height (cm)</div>
            <div class="metric-value" id="avgHeightValue">--</div>
          </div>
          <div class="metric-card">
            <div class="metric-label">Average Shoulder Width (cm)</div>
            <div class="metric-value" id="avgShoulderValue">--</div>
          </div>
          <div class="metric-card">
            <div class="metric-label">Average Waist Circumference (cm)</div>
            <div class="metric-value" id="avgWaistValue">--</div>
          </div>
          <div class="metric-card">
            <div class="metric-label">Average Hip Circumference (cm)</div>
            <div class="metric-value" id="avgHipsValue">--</div>
          </div>
        </div>

        <div class="analytics-charts">
          <div class="chart-card">
            <h3>Body Shape Distribution</h3>
            <canvas id="bodyShapeChart"></canvas>
          </div>
          <div class="chart-card">
            <h3>Color Season Distribution</h3>
            <canvas id="seasonChart"></canvas>
          </div>
        </div>

        <div class="analytics-charts">
          <div class="chart-card">
            <h3>Analysis Usage by Month</h3>
            <canvas id="usageChart"></canvas>
          </div>
        </div>
      </section>

    </div> <!-- /.main-content -->
  </div> <!-- /.admin-container -->

  <!-- MODALS -->

  <!-- PRODUCT MODAL -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="productModalTitle">Add Product</h2>
        <button class="close-modal" onclick="closeProductModal()">&times;</button>
      </div>

      <form id="productForm" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Product Name *</label>
          <input type="text" id="productName" name="productName" required>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea id="productDescription" name="productDescription" rows="3"></textarea>
        </div>

        <div class="form-group">
          <label>Body Shape *</label>
          <select id="bodyShapeSelect" name="bodyShapeSelect" required>
            <option value="">Select Body Shape</option>
            <!-- filled by JS -->
          </select>
        </div>

        <div class="form-group">
          <label>Price (₱) *</label>
          <input type="text" id="productPrice" name="productPrice" step="0.01" min="0" required>
        </div>

        <div class="form-group" id="currentImageContainer" style="display:none;">
          <label>Current Image</label>
          <img id="currentProductImage" src="" alt="Current Product Image"
               style="max-width: 150px; height: auto; margin-bottom: 10px;">
        </div>

        <div class="form-group">
          <label>Product Image *</label>
          <!-- name must be product_image -->
          <input type="file" id="productImage" name="product_image" accept="image/*" required>
          <small class="form-text text-muted">
            Max file size: 2MB. Accepted formats: JPG, PNG, GIF.
          </small>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
          <button type="submit" class="btn">Save Product</button>
        </div>
      </form>
    </div>
  </div>

  <!-- INVENTORY MODAL -->
  <div id="inventoryModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="inventoryModalTitle">Add Variant</h2>
        <button class="close-modal" onclick="closeInventoryModal()">&times;</button>
      </div>

      <form id="inventoryForm">
        <div class="form-group">
          <label>Size</label>
          <select id="inventorySize" name="inventorySize" required>
            <option value="">Select Size</option>
            <option value="S">Small (S)</option>
            <option value="M">Medium (M)</option>
            <option value="L">Large (L)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Season</label>
          <select id="inventorySeasonFilter">
            <option value="">Select Season</option>
          </select>
        </div>

        <div class="form-group">
          <label>Color</label>
          <div style="display:flex; gap:8px;">
            <select id="inventoryColor" name="inventoryColor" required>
              <!-- populated by JS -->
            </select>
            <button type="button" class="btn btn-secondary" onclick="openAddColorModalFromInventory()">
              Add Color
            </button>
          </div>
        </div>

        <div class="form-group">
          <label>Quantity</label>
          <input type="number" id="inventoryQuantity" name="inventoryQuantity" min="0" required>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="closeInventoryModal()">Cancel</button>
          <button type="submit" class="btn">Save Variant</button>
        </div>
      </form>
    </div>
  </div>

  <!-- STAFF MODAL -->
  <div id="staffModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="staffModalTitle">Add Staff</h2>
        <button class="close-modal" type="button" onclick="closeStaffModal()">&times;</button>
      </div>

      <form id="staffForm">
        <div class="form-group">
          <label>Username *</label>
          <input type="text" id="staffUsername" name="staffUsername" required>
        </div>

        <div class="form-group">
          <label>Email *</label>
          <input type="email" id="staffEmail" name="staffEmail" required>
        </div>

        <div class="form-group">
          <label>Password *</label>
          <input type="password" id="staffPassword" name="staffPassword" required>
        </div>

        <div class="form-group">
          <label>Role</label>
          <select id="staffRole" name="staffRole">
            <option value="staff">Staff</option>
            <option value="super_admin">Super Admin</option>
          </select>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="closeStaffModal()">Cancel</button>
          <button type="submit" class="btn">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ORDER MODAL -->
  <div id="orderModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Order Details</h2>
        <button class="close-modal" onclick="closeOrderModal()">&times;</button>
      </div>
      <div id="orderDetails"></div>
      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeOrderModal()">Close</button>
      </div>
    </div>
  </div>

  <!-- DELETE PRODUCT MODAL -->
  <div id="deleteProductModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Confirm Delete</h2>
        <button class="close-modal" onclick="closeDeleteModal()">&times;</button>
      </div>
      <div class="modal-body">
        <p id="deleteProductMessage">Are you sure you want to delete this product?</p>
      </div>
      <div class="modal-actions">
        <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
        <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>

  <!-- ADD COLOR MODAL -->
  <div id="addColorModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New Color</h2>
        <button class="close-modal" onclick="closeAddColorModal()">&times;</button>
      </div>
      <form id="addColorForm">
        <div class="form-group">
          <label>Color Name</label>
          <input type="text" id="newColorName" placeholder="e.g., Red, Navy Blue, Forest Green" required>
        </div>

        <div class="form-group">
          <label>Season *</label>
          <select id="newColorSeason" required>
            <option value="">Select Season</option>
            <!-- filled by JS from seasons[] -->
          </select>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="closeAddColorModal()">Cancel</button>
          <button type="submit" class="btn">Save Color</button>
        </div>
      </form>
    </div>
  </div>

  <!-- SCRIPTS -->
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Your main admin JS -->
  <script src="public/js/admin.js"></script>

  <script>
    let bodyShapeChartInstance = null;
    let seasonChartInstance = null;
    let usageChartInstance = null;

    async function loadFlaskAnalytics() {
      try {
        const res = await fetch("http://localhost:5000/analytics");
        const data = await res.json();

        if (data.status !== "success") {
          console.error("Analytics error:", data.message);
          return;
        }

        // ------- Update KPI Cards -------
        document.getElementById("avgHeightValue").textContent =
          data.averages.height_cm ? data.averages.height_cm.toFixed(1) : "--";

        document.getElementById("avgShoulderValue").textContent =
          data.averages.shoulder_cm ? data.averages.shoulder_cm.toFixed(1) : "--";

        document.getElementById("avgWaistValue").textContent =
          data.averages.waist_circ_cm ? data.averages.waist_circ_cm.toFixed(1) : "--";

        document.getElementById("avgHipsValue").textContent =
          data.averages.hip_circ_cm ? data.averages.hip_circ_cm.toFixed(1) : "--";

        // ------- Body Shape Chart -------
        const shapes = data.bodyShapeCounts || {};
        const shapeLabels = Object.keys(shapes);
        const shapeValues = Object.values(shapes);

        if (bodyShapeChartInstance) bodyShapeChartInstance.destroy();
        bodyShapeChartInstance = new Chart(
          document.getElementById("bodyShapeChart"),
          {
            type: "doughnut",
            data: {
              labels: shapeLabels,
              datasets: [{ data: shapeValues }]
            },
            options: { responsive: true }
          }
        );

        // ------- Season Chart -------
        const seasons = data.seasonCounts || {};
        const seasonLabels = Object.keys(seasons);
        const seasonValues = Object.values(seasons);

        if (seasonChartInstance) seasonChartInstance.destroy();
        seasonChartInstance = new Chart(
          document.getElementById("seasonChart"),
          {
            type: "bar",
            data: {
              labels: seasonLabels,
              datasets: [{
                label: "Users",
                data: seasonValues
              }]
            },
            options: {
              responsive: true,
              scales: {
                y: { beginAtZero: true }
              }
            }
          }
        );

        // ------- Usage by Month Chart -------
        const usageLabels = (data.usageByMonth && data.usageByMonth.labels) || [];
        const usageValues = (data.usageByMonth && data.usageByMonth.values) || [];

        if (usageChartInstance) usageChartInstance.destroy();
        usageChartInstance = new Chart(
          document.getElementById("usageChart"),
          {
            type: "line",
            data: {
              labels: usageLabels,
              datasets: [{
                label: "Analysis",
                data: usageValues,
                tension: 0.3
              }]
            },
            options: { responsive: true }
          }
        );

      } catch (err) {
        console.error("Analytics load failed:", err);
      }
    }
  </script>

</body>
</html>
