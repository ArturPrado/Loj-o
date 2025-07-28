<?php
session_start();

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

// Carrega os produtos
$productsJson = file_get_contents('products.json');
$products = json_decode($productsJson, true);

// Calcula estatísticas
$totalProducts = count($products);
$categories = array_unique(array_column($products, 'category'));
$totalCategories = count($categories);
$totalValue = array_sum(array_column($products, 'price'));

// Função para adicionar novo produto (simplificado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $newProduct = [
        'id' => count($products) + 1,
        'name' => $_POST['name'],
        'price' => floatval($_POST['price']),
        'category' => $_POST['category'],
        'image' => 'images/products/' . basename($_POST['image']),
        'description' => $_POST['description']
    ];
    
    $products[] = $newProduct;
    file_put_contents('products.json', json_encode($products));
    header('Location: dashboard.php');
    exit;
}

// Função para remover produto
if (isset($_GET['remove'])) {
    $idToRemove = intval($_GET['remove']);
    $products = array_filter($products, function($product) use ($idToRemove) {
        return $product['id'] !== $idToRemove;
    });
    
    // Reindexa os IDs
    $products = array_values($products);
    foreach ($products as $index => $product) {
        $products[$index]['id'] = $index + 1;
    }
    
    file_put_contents('products.json', json_encode($products));
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - TechSmart Solutions</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            width: 30%;
            text-align: center;
        }
        .stat-card h3 {
            margin-top: 0;
            color: #40e0d0;
        }
        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #40e0d0;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .action-link {
            color: #40e0d0;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-link.delete {
            color: #f17d7d;
        }
        .action-link:hover {
            text-decoration: underline;
        }
        .add-product-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #40e0d0;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-submit:hover {
            background-color: #30cfcf;
        }
        .logout-btn {
            float: right;
            background-color: #f17d7d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-btn:hover {
            background-color: #e06d6d;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo" onclick="window.location.href='index.php'">TechSmart</div>
        <div class="nav-links">
            <button onclick="window.location.href='logout.php'" class="logout-btn">Logout</button>
            <a href="index.php">Voltar para a Loja</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Dashboard Administrativo</h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Produtos</h3>
                <div class="value"><?php echo $totalProducts; ?></div>
            </div>
            <div class="stat-card">
                <h3>Categorias</h3>
                <div class="value"><?php echo $totalCategories; ?></div>
            </div>
            <div class="stat-card">
                <h3>Valor Total</h3>
                <div class="value">R$ <?php echo number_format($totalValue, 2, ',', '.'); ?></div>
            </div>
        </div>

        <h2>Lista de Produtos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Categoria</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="action-link">Editar</a>
                            <a href="dashboard.php?remove=<?php echo $product['id']; ?>" class="action-link delete" onclick="return confirm('Tem certeza que deseja remover este produto?')">Remover</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="add-product-form">
            <h2>Adicionar Novo Produto</h2>
            <form method="post" action="dashboard.php">
                <div class="form-group">
                    <label for="name">Nome do Produto:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="price">Preço:</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="category">Categoria:</label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="image">Imagem (caminho relativo):</label>
                    <input type="text" id="image" name="image" required>
                </div>
                <div class="form-group">
                    <label for="description">Descrição:</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <button type="submit" name="add_product" class="btn-submit">Adicionar Produto</button>
            </form>
        </div>
    </div>
</body>
</html>