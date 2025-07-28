<?php
session_start();

// Inicializa o carrinho na sessão se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Função para remover produto do carrinho
if (isset($_GET['remove'])) {
    $removeId = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
    }
    header('Location: carrinho.php');
    exit;
}

// Função para atualizar quantidades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $id = intval($id);
        $qty = intval($qty);
        if ($qty > 0) {
            $_SESSION['cart'][$id] = $qty;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }
    header('Location: carrinho.php');
    exit;
}

// Carrega produtos do JSON
$productsJson = file_get_contents('../products.json');
$products = json_decode($productsJson, true);

// Filtra produtos que estão no carrinho
$cartProducts = [];
foreach ($products as $product) {
    if (isset($_SESSION['cart'][$product['id']])) {
        $product['quantity'] = $_SESSION['cart'][$product['id']];
        $cartProducts[] = $product;
    }
}

// Calcula total
$total = 0;
foreach ($cartProducts as $product) {
    $total += $product['price'] * $product['quantity'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Carrinho - TechSmart Solutions</title>
    <link rel="stylesheet" href="../styles.css" />
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }
        img.product-img {
            max-width: 80px;
            height: auto;
            border-radius: 6px;
        }
        .remove-link {
            color: #f17d7d;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }
        .remove-link:hover {
            text-decoration: underline;
        }
        .total-row td {
            font-weight: bold;
            font-size: 1.2em;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #40e0d0;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        /* Estilo para o botão de finalizar compra */
        .btn-checkout {
            background-color: #40e0d0;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            font-size: 1.1em;
        }
        .btn-checkout:hover {
            background-color: #30cfcf;
        }
        /* Estilo para o input de quantidade */
        .quantity-input {
            width: 60px;
            padding: 6px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo" onclick="window.location.href='../index.php'">TechSmart</div>
        <div class="nav-links">
            <a href="../pages/login.php">Login / Cadastro</a>
            <a href="carrinho.php">Carrinho</a>
        </div>
    </nav>
    <main style="max-width: 900px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h1>Seu Carrinho</h1>
        <?php if (count($cartProducts) === 0): ?>
            <p>Seu carrinho está vazio.</p>
            <p><a href="../index.php" class="back-link">&larr; Voltar para a loja</a></p>
        <?php else: ?>
            <form method="post" action="carrinho.php" id="cartForm">
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço Unitário</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartProducts as $product): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img" />
                                    <br />
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </td>
                                <td>R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $product['id']; ?>]" value="<?php echo $product['quantity']; ?>" min="1" max="99" class="quantity-input" />
                                </td>
                                <td>R$ <?php echo number_format($product['price'] * $product['quantity'], 2, ',', '.'); ?></td>
                                <td><a href="carrinho.php?remove=<?php echo $product['id']; ?>" class="remove-link">X</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3">Total</td>
                            <td colspan="2">R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn-checkout" onclick="window.location.href='checkout.php'">Finalizar Compra</button>
            </form>
            <p><a href="../index.php" class="back-link">&larr; Continuar comprando</a></p>
        <?php endif; ?>
    </main>

    <script>
        // Adiciona event listeners para todos os inputs de quantidade
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                // Envia o formulário automaticamente quando a quantidade é alterada
                document.getElementById('cartForm').submit();
            });
        });
    </script>
</body>
</html>