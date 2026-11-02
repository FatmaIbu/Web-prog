
<?php

require_once __DIR__ . '/BaseDAO.php';

class ProductDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('products');
    }

    // CREATE - Add a new product
    public function create($data) {
        $sql = "INSERT INTO products
                (category_id, name, description, price, stock, image_url)
                VALUES
                (:category_id, :name, :description, :price, :stock, :image_url)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'] ?? 0,
            'image_url' => $data['image_url'] ?? null
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // READ - Get all products
    // getAll() is inherited from BaseDAO

    // READ - Get one product
    // getById() is inherited from BaseDAO

    // UPDATE - Edit a product
    public function update($id, $data) {
        $sql = "UPDATE products SET
                    category_id = :category_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    stock = :stock,
                    image_url = :image_url
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'] ?? 0,
            'image_url' => $data['image_url'] ?? null
        ]);
    }

    // DELETE - Remove a product
    // delete() is inherited from BaseDAO
}
