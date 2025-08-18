<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;

#[ApiResource(
    operations: [
        new Get(
            name: 'system_health',
            uriTemplate: '/_health',            
            routeName: 'system_health', 
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
        ),
        new Get(
            name: 'system_webhook_get',
            routeName: 'system_webhook',
            uriTemplate: '/system/webhook',
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            name: 'system_webhook_post',
            routeName: 'system_webhook',
            uriTemplate: '/system/webhook',
            security: "is_granted('PUBLIC_ACCESS')"
        )
    ]
)]
class System {}
