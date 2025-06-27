<?php
if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$id = intval($_GET['id']);
$productsJson = file_get_contents('../products.json');
$products = json_decode($productsJson, true);

$product = null;
foreach ($products as $p) {
    if ($p['id'] === $id) {
        $product = $p;
        break;
    }
}

if (!$product) {
    echo "<h1>Produto não encontrado</h1>";
    echo "<p><a href='../index.php'>Voltar para a loja</a></p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($product['name']); ?> - TechSmart Solutions</title>
    <link rel="stylesheet" href="../styles.css" />
</head>
<body>
    <nav class="navbar">
        <div class="logo" onclick="window.location.href='../index.php'">TechSmart</div>
        <div class="nav-links">
            <a href="../pages/login.php">Login / Cadastro</a>
            <a href="../pages/carrinho.php">Carrinho</a>
        </div>
    </nav>
    <main style="padding: 20px; max-width: 900px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1 1 300px;">
                <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; max-width: 400px; object-fit: contain; border-radius: 8px;" />
            </div>
            <div style="flex: 1 1 300px;">
                <p style="font-size: 1em; color: #4b0082; font-weight: bold;">Categoria: <?php echo htmlspecialchars($product['category']); ?></p>
                <p style="font-size: 1.2em; color: #4b0082; font-weight: bold;">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <label for="quantity" style="font-weight: bold;">Quantidade:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" style="width: 60px; margin-left: 10px;"/>
                <button class="add-to-cart-btn" style="margin-top: 20px; font-size: 1.1em;" onclick="addToCart()">Adicionar ao Carrinho</button>
            </div>
        </div>
        <p style="margin-top: 20px;"><a href="../index.php" style="color: #40e0d0;">&larr; Voltar para a loja</a></p>
    </main>
    <script>
        function addToCart() {
            const quantity = document.getElementById('quantity').value;
            alert('Adicionado ' + quantity + ' unidade(s) do produto ao carrinho.');
            // Aqui você pode adicionar a lógica para adicionar o produto ao carrinho real
        }
    </script>
</body>
</html>
