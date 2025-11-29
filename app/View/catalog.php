<?php 
// We assume $catalogProducts is provided by FeaturesController

$imageBasePath = 'public/image/';
?>

<div class="subsection">
  <div class="filter-bar">
    <input type="text" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()">

    <select id="categoryFilter" onchange="filterProducts()">
      <option value="">All Categories</option>
    </select>
  </div>

  <div class="clothes-grid">

    <?php if (!empty($catalogProducts)): ?>
      <?php foreach ($catalogProducts as $product): ?>
      
        <div 
          class="clothes-item catalog-item"
          data-product-id="<?= $product['PRODUCT_ID'] ?>"
          data-name="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>"
          data-price="<?= htmlspecialchars($product['PRICE']) ?>"
        >

          <img
            src="<?= $imageBasePath . htmlspecialchars($product['IMAGE_FILE']) ?>"
            alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>"
          >

          <button class="add-to-cart"
        onclick="openVariantModal(<?= (int)$product['PRODUCT_ID'] ?>)">
  <i class="bi bi-bag-plus"></i>
</button>


          <div class="clothes-caption">
            <span class="title"><?= htmlspecialchars($product['PRODUCT_NAME']) ?></span>
            <span class="price">₱<?= number_format($product['PRICE'], 2) ?></span>
          </div>

        </div>

      <?php endforeach; ?>
    <?php else: ?>
      <p>No products available.</p>
    <?php endif; ?>

  </div>
</div>
