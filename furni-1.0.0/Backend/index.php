
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/ProductService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/OrderService.php';
require_once __DIR__ . '/services/ReviewService.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';


$userService = new UserService(new UserDao());
$productService = new ProductService(new ProductDao());
$categoryService = new CategoryService(new CategoryDao());
$orderService = new OrderService(new OrderDao());
$reviewService = new ReviewService(new ReviewDao());
$authService = new AuthService(new UserDao());



Flight::route('GET /', function () {
    Flight::json([
        'success' => true,
        'message' => 'Welcome to FurniEra API',
        'milestone' => 3
    ]);
});

Flight::route('GET /products', function () use ($productService) {
    try {
        Flight::json($productService->getAll());
    } catch (Throwable $e) {
        error_log($e);
        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});

Flight::route('GET /products/@id:[0-9]+', function ($id) use ($productService) {
    try {
        Flight::json($productService->getById((int)$id));
    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('POST /products', function () use ($productService) {
    try {
        $data = Flight::request()->data->getData();
        $product = $productService->create($data);

        Flight::json($product, 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('PUT /products/@id:[0-9]+', function ($id) use ($productService) {
    try {
        $data = Flight::request()->data->getData();
        $product = $productService->update((int)$id, $data);

        Flight::json($product, 200);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('DELETE /products/@id:[0-9]+', function ($id) use ($productService) {
    try {
        $productService->delete((int)$id);

        Flight::json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('GET /categories', function () use ($categoryService) {
    try {
        Flight::json($categoryService->getAll());
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('GET /categories/@id:[0-9]+', function ($id) use ($categoryService) {
    try {
        Flight::json($categoryService->getById((int)$id));
    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('POST /categories', function () use ($categoryService) {
    try {
        $data = Flight::request()->data->getData();
        Flight::json($categoryService->create($data), 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('PUT /categories/@id:[0-9]+', function ($id) use ($categoryService) {
    try {
        $data = Flight::request()->data->getData();
        Flight::json($categoryService->update((int)$id, $data));
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('DELETE /categories/@id:[0-9]+', function ($id) use ($categoryService) {
    try {
        $categoryService->delete((int)$id);
        Flight::json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});
Flight::route('GET /orders', function () use ($orderService) {
    try {
        $user = AuthMiddleware::requireUser();

        if ($user['role'] !== 'admin') {
            Flight::json(['error' => 'Forbidden: Admin access required'], 403);
            return;
        }

        Flight::json($orderService->getAll());

    } catch (UnexpectedValueException $e) {
        Flight::json(['error' => 'Unauthorized: Invalid or missing token'], 401);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});


/**
 * GET /orders/@id
 * Only the order owner or an administrator can view an order.
 */
Flight::route('GET /orders/@id:[0-9]+', function ($id) use ($orderService) {
    try {
        $user = AuthMiddleware::requireUser();

        $order = $orderService->getById((int)$id);

        if ($user['role'] !== 'admin' &&
            (int)$order['user_id'] !== (int)$user['id']) {

            Flight::json(['error' => 'Forbidden: This is not your order'], 403);
            return;
        }

        Flight::json($order);

    } catch (UnexpectedValueException $e) {
        Flight::json(['error' => 'Unauthorized: Invalid or missing token'], 401);

    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => 'Order not found'], 404);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('POST /orders', function () use ($orderService) {
    try {
        // Get authenticated customer
        $user = AuthMiddleware::requireUser();

        // Read request data
        $data = Flight::request()->data->getData();

        // Only accept product IDs and quantities
        $items = $data['items'] ?? [];

        if (!is_array($items) || empty($items)) {
            throw new InvalidArgumentException(
                'At least one order item is required'
            );
        }

        // User ID comes from JWT, not the request
        $order = $orderService->createWithItems(
            (int)$user['id'],
            $items
        );

        Flight::json($order, 201);

    } catch (UnexpectedValueException $e) {
        Flight::json([
            'error' => 'Unauthorized: Invalid or missing token'
        ], 401);

    } catch (InvalidArgumentException $e) {
        Flight::json([
            'error' => $e->getMessage()
        ], 400);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});




Flight::route('GET /reviews', function () use ($reviewService) {
    try {
        Flight::json($reviewService->getAll());
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('GET /reviews/@id:[0-9]+', function ($id) use ($reviewService) {
    try {
        Flight::json($reviewService->getById((int)$id));
    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('POST /reviews', function () use ($reviewService) {
    try {
        $user = AuthMiddleware::requireUser();

        $data = Flight::request()->data->getData();

        // Always use the authenticated user's ID.
        $data['user_id'] = (int)$user['id'];

        $review = $reviewService->create($data);

        Flight::json($review, 201);

    } catch (UnexpectedValueException $e) {
        Flight::json([
            'error' => 'Unauthorized: Invalid or missing token'
        ], 401);

    } catch (InvalidArgumentException $e) {
        Flight::json([
            'error' => $e->getMessage()
        ], 400);

    } catch (PDOException $e) {
        error_log($e);
        Flight::json([
            'error' => 'Unable to create review'
        ], 500);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});


Flight::route('PUT /reviews/@id:[0-9]+', function ($id) use ($reviewService) {
    try {
        $user = AuthMiddleware::requireUser();

        $review = $reviewService->getById((int)$id);

        // Only the review owner or admin can update.
        if (
            $user['role'] !== 'admin' &&
            (int)$review['user_id'] !== (int)$user['id']
        ) {
            Flight::json([
                'error' => 'Forbidden: This is not your review'
            ], 403);
            return;
        }

        $data = Flight::request()->data->getData();

        $updatedReview = $reviewService->update((int)$id, $data);

        Flight::json($updatedReview, 200);

    } catch (UnexpectedValueException $e) {
        Flight::json([
            'error' => 'Unauthorized: Invalid or missing token'
        ], 401);

    } catch (OutOfBoundsException $e) {
        Flight::json([
            'error' => 'Review not found'
        ], 404);

    } catch (InvalidArgumentException $e) {
        Flight::json([
            'error' => $e->getMessage()
        ], 400);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});

Flight::route('DELETE /reviews/@id:[0-9]+', function ($id) use ($reviewService) {
    try {
        $user = AuthMiddleware::requireUser();

        $review = $reviewService->getById((int)$id);

        // Only the owner or an admin can delete.
        if (
            $user['role'] !== 'admin' &&
            (int)$review['user_id'] !== (int)$user['id']
        ) {
            Flight::json([
                'error' => 'Forbidden: This is not your review'
            ], 403);
            return;
        }

        $reviewService->delete((int)$id);

        Flight::json([
            'message' => 'Review deleted successfully'
        ], 200);

    } catch (UnexpectedValueException $e) {
        Flight::json([
            'error' => 'Unauthorized: Invalid or missing token'
        ], 401);

    } catch (OutOfBoundsException $e) {
        Flight::json([
            'error' => 'Review not found'
        ], 404);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});






Flight::route('POST /auth/login', function () use ($authService) {
    try {
        $data = Flight::request()->data->getData();

        $result = $authService->login($data);

        Flight::json($result, 200);

    } catch (InvalidArgumentException $e) {
        Flight::json([
            'error' => 'Invalid email or password'
        ], 401);

    } catch (Throwable $e) {
        error_log($e);

        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});


Flight::route('POST /auth/register', function () use ($userService) {
    try {
        $data = Flight::request()->data->getData();

        $user = $userService->create($data);

        Flight::json([
            'message' => 'Registration successful',
            'user' => $user
        ], 201);

    } catch (InvalidArgumentException $e) {
        Flight::json([
            'error' => $e->getMessage()
        ], 400);

    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            Flight::json([
                'error' => 'Email is already registered'
            ], 409);
            return;
        }

        error_log($e);

        Flight::json([
            'error' => 'Internal server error'
        ], 500);

    } catch (Throwable $e) {
        error_log($e);

        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});


Flight::route('GET /auth/me', function () {
    try {
        $user = AuthMiddleware::requireUser();

        Flight::json([
            'user' => $user
        ], 200);

    } catch (UnexpectedValueException $e) {
        Flight::json([
            'error' => 'Unauthorized: Invalid or missing token'
        ], 401);

    } catch (Throwable $e) {
        error_log($e);

        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});


Flight::route('PUT /orders/@id:[0-9]+', function ($id) use ($orderService) {
    try {
        $user = AuthMiddleware::requireUser();

        if ($user['role'] !== 'admin') {
            Flight::json(['error' => 'Forbidden: Admin access required'], 403);
            return;
        }

        $data = Flight::request()->data->getData();

        $order = $orderService->update((int)$id, $data);

        Flight::json($order, 200);

    } catch (UnexpectedValueException $e) {
        Flight::json(['error' => 'Unauthorized: Invalid or missing token'], 401);

    } catch (OutOfBoundsException $e) {
        Flight::json(['error' => 'Order not found'], 404);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json(['error' => 'Internal server error'], 500);
    }
});

Flight::route('DELETE /orders/@id:[0-9]+', function ($id) use ($orderService) {
    try {
        $user = AuthMiddleware::requireUser();

        if ($user['role'] !== 'admin') {
            Flight::json([
                'error' => 'Forbidden: Admin access required'
            ], 403);
            return;
        }

        $orderService->delete((int)$id);

        Flight::json([
            'message' => 'Order deleted successfully'
        ], 200);

    } catch (UnexpectedValueException $e) {
        Flight::json([
            'error' => 'Unauthorized: Invalid or missing token'
        ], 401);

    } catch (OutOfBoundsException $e) {
        Flight::json([
            'error' => 'Order not found'
        ], 404);

    } catch (Throwable $e) {
        error_log($e);
        Flight::json([
            'error' => 'Internal server error'
        ], 500);
    }
});








Flight::start();
