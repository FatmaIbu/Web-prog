
<?php

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "FurniEra REST API",
    version: "1.0.0",
    description: "API documentation for the FurniEra furniture e-commerce application."
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local development server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class OpenApiSpec
{
}
