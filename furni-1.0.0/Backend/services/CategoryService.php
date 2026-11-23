
<?php

require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService {
    private CategoryDao $dao;

    public function __construct(CategoryDao $dao) {
        $this->dao = $dao;
    }

    public function getAll(): array {
        return $this->dao->getAll();
    }

    public function getById(int $id): array {
        $category = $this->dao->getById($id);

        if (!$category) {
            throw new OutOfBoundsException(
                "Category not found"
            );
        }

        return $category;
    }

    public function create(array $data): array {
        $name = trim((string)($data['name'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException(
                "Category name is required"
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
                    "Category name cannot be empty"
                );
            }

            $data['name'] = trim($data['name']);
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
