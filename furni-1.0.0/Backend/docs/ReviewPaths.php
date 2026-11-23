
<?php

use OpenApi\Attributes as OA;

class ReviewPaths
{
    #[OA\Get(
        path: '/reviews',
        summary: 'Get all product reviews',
        tags: ['Reviews'],
        responses: [
            new OA\Response(response: 200, description: 'Reviews retrieved successfully')
        ]
    )]
    public function getAll(): void {}

    #[OA\Get(
        path: '/reviews/{id}',
        summary: 'Get a review by ID',
        tags: ['Reviews'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Review found'),
            new OA\Response(response: 404, description: 'Review not found')
        ]
    )]
    public function getById(): void {}

    #[OA\Post(
        path: '/reviews',
        summary: 'Create a product review',
        tags: ['Reviews'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id', 'rating'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer'),
                    new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5),
                    new OA\Property(property: 'comment', type: 'string', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Review created'),
            new OA\Response(response: 400, description: 'Invalid review data'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function create(): void {}

    #[OA\Put(
        path: '/reviews/{id}',
        summary: 'Update a product review',
        tags: ['Reviews'],
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
                properties: [
                    new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5),
                    new OA\Property(property: 'comment', type: 'string', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Review updated'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Review not found')
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/reviews/{id}',
        summary: 'Delete a product review',
        tags: ['Reviews'],
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
            new OA\Response(response: 200, description: 'Review deleted'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Review not found')
        ]
    )]
    public function delete(): void {}
}