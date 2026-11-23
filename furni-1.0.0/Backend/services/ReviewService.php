
<?php

require_once __DIR__ . '/../dao/reviewDao.php';

class ReviewService {
    private ReviewDao $dao;

    public function __construct(ReviewDao $dao) {
        $this->dao = $dao;
    }

    public function getAll(): array {
        return $this->dao->getAll();
    }

    public function getById(int $id): array {
        $review = $this->dao->getById($id);

        if (!$review) {
            throw new OutOfBoundsException(
                "Review not found"
            );
        }

        return $review;
    }

    public function create(array $data): array {
        $userId = filter_var(
            $data['user_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $productId = filter_var(
            $data['product_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$userId || $userId <= 0) {
            throw new InvalidArgumentException(
                "Valid user ID is required"
            );
        }

        if (!$productId || $productId <= 0) {
            throw new InvalidArgumentException(
                "Valid product ID is required"
            );
        }

        $rating = filter_var(
            $data['rating'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($rating === false || $rating === null ||
            $rating < 1 || $rating > 5) {
            throw new InvalidArgumentException(
                "Rating must be between 1 and 5"
            );
        }

        if (
            array_key_exists('comment', $data) &&
            $data['comment'] !== null &&
            !is_string($data['comment'])
        ) {
            throw new InvalidArgumentException(
                "Comment must be text"
            );
        }

        $id = $this->dao->create([
            'user_id' => (int)$userId,
            'product_id' => (int)$productId,
            'rating' => $rating,
            'comment' => $data['comment'] ?? null
        ]);

        return $this->getById($id);
    }

    public function update(int $id, array $data): array {
        $this->getById($id);

        $updates = [];

        if (array_key_exists('rating', $data)) {
            $rating = filter_var(
                $data['rating'],
                FILTER_VALIDATE_INT
            );

            if ($rating === false || $rating === null ||
                $rating < 1 || $rating > 5) {
                throw new InvalidArgumentException(
                    "Rating must be between 1 and 5"
                );
            }

            $updates['rating'] = $rating;
        }

        if (array_key_exists('comment', $data)) {
            if (
                $data['comment'] !== null &&
                !is_string($data['comment'])
            ) {
                throw new InvalidArgumentException(
                    "Comment must be text"
                );
            }

            $updates['comment'] = $data['comment'];
        }

        if (empty($updates)) {
            throw new InvalidArgumentException(
                "No valid fields to update"
            );
        }

        $this->dao->update($id, $updates);

        return $this->getById($id);
    }

    public function delete(int $id): void {
        $this->getById($id);
        $this->dao->delete($id);
    }
}
