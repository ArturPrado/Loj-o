<?php
// Ler produtos do arquivo JSON
$productsJson = file_get_contents('products.json');
$products = json_decode($productsJson, true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TechSmart Solutions - Loja</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <nav class="navbar">
        <div class="logo" onclick="window.location.href='index.php'">TechSmart</div>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Pesquisar produtos..." />
        </div>
        <div class="nav-links">
            <a href="login.php">Login / Cadastro</a>
            <a href="pages/carrinho.php">Carrinho <span id="cartCount" class="cart-count">0</span></a>
        </div>
    </nav>
    <main>
        <div class="top-bar">
            <select id="categoryFilter">
                <option value="">Todas as categorias</option>
                <?php
                $categories = array_unique(array_column($products, 'category'));
                foreach ($categories as $category) {
                    echo '<option value="' . htmlspecialchars($category) . '">' . htmlspecialchars($category) . '</option>';
                }
                ?>
            </select>
        </div>
        <div id="productList">
            <?php foreach ($products as $product): ?>
                <div class="product" data-category="<?php echo htmlspecialchars($product['category']); ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-price">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></div>
                    <a href="pages/produto.php?id=<?php echo $product['id']; ?>" class="add-to-cart-btn" style="display: inline-block; text-decoration: none; text-align: center;">Comprar</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script>
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const productList = document.getElementById('productList');

        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            Array.from(productList.children).forEach(product => {
                const name = product.querySelector('.product-name').textContent.toLowerCase();
                const category = product.getAttribute('data-category');

                const matchesSearch = name.includes(searchTerm);
                const matchesCategory = selectedCategory === '' || category === selectedCategory;

                if (matchesSearch && matchesCategory) {
                    product.style.display = 'inline-block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterProducts);
        categoryFilter.addEventListener('change', filterProducts);
    </script>
</body>
</html>
