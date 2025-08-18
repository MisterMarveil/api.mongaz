<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;


#[ORM\Entity]
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
            routeName: 'system_webhook_get',
            uriTemplate: '/system/webhook1',
            security: "is_granted('PUBLIC_ACCESS')",
            openapi: new Model\Operation(
                summary: "Facebook Webhook Verification",
                description: "GET endpoint used by Facebook Cloud API to verify your webhook with a challenge token.",
                parameters: [
                    [
                        'name' => 'hub.mode',
                        'in' => 'query',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                        'description' => 'Expected value: subscribe'
                    ],
                    [
                        'name' => 'hub.challenge',
                        'in' => 'query',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                        'description' => 'Challenge string returned to Facebook if verification succeeds'
                    ],
                    [
                        'name' => 'hub.verify_token',
                        'in' => 'query',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                        'description' => 'Your server verify token to validate the request'
                    ]
                ],
                responses: [
                    '200' => new Model\Response(
                        description: 'Verification successful, challenge string returned'
                    ),
                    '403' => new Model\Response(
                        description: 'Verification failed, invalid token'
                    )
                ]
            )
        ),
        new Post(
            name: 'system_webhook_post',
            routeName: 'system_webhook_post',
            uriTemplate: '/system/webhook',
            security: "is_granted('PUBLIC_ACCESS')",
            openapi: new Model\Operation(
                summary: "Facebook Webhook Receiver",
                description: "POST endpoint to receive events from Facebook API Cloud (messages, delivery receipts, etc).",
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'object' => ['type' => 'string'],
                                    'entry' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string'],
                                                'time' => ['type' => 'integer'],
                                                'messaging' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'sender' => [
                                                                'type' => 'object',
                                                                'properties' => ['id' => ['type' => 'string']]
                                                            ],
                                                            'recipient' => [
                                                                'type' => 'object',
                                                                'properties' => ['id' => ['type' => 'string']]
                                                            ],
                                                            'message' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'mid' => ['type' => 'string'],
                                                                    'text' => ['type' => 'string']
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'example' => [
                                'object' => 'page',
                                'entry' => [[
                                    'id' => 'PAGE_ID',
                                    'time' => 1458692752478,
                                    'messaging' => [[
                                        'sender' => ['id' => 'USER_ID'],
                                        'recipient' => ['id' => 'PAGE_ID'],
                                        'message' => [
                                            'mid' => 'mid.123456',
                                            'text' => 'Hello!'
                                        ]
                                    ]]
                                ]]
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(
                        description: 'Webhook event received successfully',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string']
                                    ]
                                ],
                                'example' => ['status' => 'ok']
                            ]
                        ])
                    ),
                    '400' => new Model\Response(
                        description: 'Invalid request format'
                    )
                ]
            )        
        )
    ]
)]
class System {}
