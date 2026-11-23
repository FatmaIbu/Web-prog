
<?php

use OpenApi\Attributes as OA;

class ProductPaths
{
    #[OA\Get(
        path: '/products',
        summary: 'Get all furniture products',
        description: 'Retrieve all available products from FurniEra.',
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'id',
                                type: 'integer'
                            ),
                            new OA\Property(
                                property: 'name',
                                type: 'string'
                            ),
                            new OA\Property(
                                property: 'price',
                                type: 'number',
                                format: 'float'
                            ),
                            new OA\Property(
                                property: 'stock',
                                type: 'integer'
                            )
                        ]
                    )
                )
            )
        ]
    )]
    public function getProducts(): void
    {
    }
}
