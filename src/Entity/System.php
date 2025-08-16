<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;

#[ApiResource(
    operations: [
        new Get(
            name: 'system_health',
            uriTemplate: '/_health',
            controller: App\Action\System\HealthCheck::class,
            openapi: new Model\Operation(
                summary: 'Health check',
                description: 'Returns API health status.',
                responses: [
                    '200' => new Model\Response(
                        description: 'OK',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string'],
                                        'time' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ])
                    )
                ]
            )
        )
    ]
)]
class System {}
