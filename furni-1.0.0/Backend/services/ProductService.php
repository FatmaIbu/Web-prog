
<?php

require_once __DIR__ . '/../dao/ProductDao.php';

class ProductService {
    private ProductDao $dao;

    public function __construct(ProductDao $dao) {
        $this->dao = $dao;
    }

    public function getAll(): array {
        return $this->dao->getAll();
    }

    public function getById(int $id): array {
        $product = $this->dao->getById($id);

        if (!$product) {
            throw new OutOfBoundsException(
                "Product not found"
            );
        }

        return $product;
    }

    public function create(array $data): array {
        $name = trim((string)($data['name'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException(
                "Product name is required"
            );
        }

        if (
            !isset($data['price']) ||
            !is_numeric($data['price']) ||
            $data['price'] <= 0
        ) {
            throw new InvalidArgumentException(
                "Price must be greater than zero"
            );
        }

        if (
            !isset($data['stock']) ||
            filter_var(
                $data['stock'],
                FILTER_VALIDATE_INT
            ) === false ||
            $data['stock'] < 0
        ) {
            throw new InvalidArgumentException(
                "Stock must be a non-negative integer"
            );
        }

        if (
            !isset($data['category_id']) ||
            filter_var(
                $data['category_id'],
                FILTER_VALIDATE_INT
            ) === false ||
            (int)$data['category_id'] <= 0
        ) {
            throw new InvalidArgumentException(
                "Valid category ID is required"
            );
        }

        $data['name'] = $name;

        $id = $this->dao->create($data);

        return $this->getById((int)$id);
    }

    public function update(int $id, array $data): array {
        $this->getById($id);

        if (array_key_exists('name', $data)) {
            if (
                !is_string($data['name']) ||
                trim($data['name']) === ''
            ) {
                throw new InvalidArgumentException(
                    "Product name cannot be empty"
                );
            }

            $data['name'] = trim($data['name']);
        }

        if (array_key_exists('price', $data)) {
            if (
                !is_numeric($data['price']) ||
                $data['price'] <= 0
            ) {
                throw new InvalidArgumentException(
                    "Price must be greater than zero"
                );
            }
        }

        if (array_key_exists('stock', $data)) {
            if (
                filter_var(
                    $data['stock'],
                    FILTER_VALIDATE_INT
                ) === false ||
                $data['stock'] < 0
            ) {
                throw new InvalidArgumentException(
                    "Stock must be non-negative"
                );
            }
        }

        if (empty($data)) {
            throw new InvalidArgumentException(
                "No fields to update"
            );
        }

        $this->dao->update($id, $data);

        return $this->getById($id);
    }

    public function delete(int $id): void {
        $this->getById($id);
        $this->dao->delete($id);
    }
}
