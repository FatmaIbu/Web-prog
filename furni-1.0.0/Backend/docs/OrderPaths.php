
<?php

use OpenApi\Attributes as OA;

class OrderPaths
{
    #[OA\Get(
        path: '/orders',
        summary: 'Get all orders',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Orders retrieved successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden: Admin access required'
            )
        ]
    )]
    public function getAll(): void {}

    #[OA\Get(
        path: '/orders/{id}',
        summary: 'Get an order by ID',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order found'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Order not found'),
            new OA\Response(response: 403, description: 'Forbidden: Not the order owner')
        ]
    )]
    public function getById(): void {}

    #[OA\Post(
        path: '/orders',
        summary: 'Create a new order',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['items'],
                properties: [
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['product_id', 'quantity'],
                            properties: [
                                new OA\Property(
                                    property: 'product_id',
                                    type: 'integer'
                                ),
                                new OA\Property(
                                    property: 'quantity',
                                    type: 'integer'
                                )
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Order created'),
            new OA\Response(response: 400, description: 'Invalid order data or insufficient stock'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function create(): void {}

    #[OA\Put(
        path: '/orders/{id}',
        summary: 'Update order status',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Order updated'),
            new OA\Response(response: 400, description: 'Invalid status'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Order not found')
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/orders/{id}',
        summary: 'Delete an order',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order deleted'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Order not found')
        ]
    )]
    public function delete(): void {}
}