<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Action\Facebook\WebhookVerify;
use App\Action\Facebook\WebhookReceive;
use App\Action\Facebook\SendMessage;
use App\Action\Facebook\CreateTemplate;
use App\Action\Facebook\ListTemplates;
use App\Action\Facebook\LinkAccount;
use App\Action\Facebook\ExchangeToken;
use App\Action\Facebook\ListAssets;


#[ApiResource(
    shortName: 'Facebook',
    operations: [
        new Get(
            name: 'facebook_webhook_verify',
            uriTemplate: '/facebook/webhook',
            controller: WebhookVerify::class,
            openapi: new Model\Operation(
                summary: 'Webhook verification (GET)',
                description: 'Facebook Cloud API verification endpoint (hub.mode, hub.challenge, hub.verify_token).',
                parameters: [
                    ['name'=>'hub.mode','in'=>'query','required'=>true,'schema'=>['type'=>'string']],
                    ['name'=>'hub.challenge','in'=>'query','required'=>true,'schema'=>['type'=>'string']],
                    ['name'=>'hub.verify_token','in'=>'query','required'=>true,'schema'=>['type'=>'string']],
                ],
                responses: ['200'=> new Model\Response(description: 'Echo challenge string')]
            ),
            paginationEnabled: false
        ),
        new Post(
            name: 'facebook_webhook_receive',
            uriTemplate: '/facebook/webhook',
            controller: WebhookReceive::class,
                openapi: new Model\Operation(
                summary: 'Webhook ingress (POST)',
                description: 'Receives message status/events from Facebook Cloud API.',
                responses: ['200'=> new Model\Response(description: 'Ack')]
            ),
            security: "is_granted('PUBLIC_ACCESS')",
            paginationEnabled: false
        ),
        new Post(
            name: 'facebook_link_account',
            uriTemplate: '/facebook/link-account',
            controller: LinkAccount::class,
            openapi: new Model\Operation(
                summary: 'Link a customer Meta account/WABA',
                description: 'Store WABA id, phone ids, and short-lived token provided by the customer (from Embedded Signup or their app).',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema'=>[
                                'type'=>'object',
                                'required'=>['waba_id','access_token'],
                                'properties'=>[
                                    'waba_id'=>['type'=>'string'],
                                    'access_token'=>['type'=>'string','description'=>'Customer-provided system user token'],
                                    'phone_ids'=>['type'=>'array','items'=>['type'=>'string']]
                                ]
                            ]
                        ]
                    ])
                ),
                responses: ['201'=> new Model\Response(description: 'Linked')]
            ),
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            name: 'facebook_exchange_token',
            uriTemplate: '/facebook/exchange-token',
            controller: ExchangeToken::class,
            openapi: new Model\Operation(
                summary: 'Exchange short-lived token to long-lived',
                description: 'Exchanges and stores long-lived token reference for server-to-server operations.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json'=>[
                            'schema'=>[
                                'type'=>'object',
                                'required'=>['access_token'],
                                'properties'=>['access_token'=>['type'=>'string']]
                            ]
                        ]
                    ])
                ),
                responses: ['200'=> new Model\Response(description: 'Long-lived token stored')]
            ),
            security: "is_granted('ROLE_USER')"
        ),
        new Get(
            name: 'facebook_list_assets',
            uriTemplate: '/facebook/assets',
            controller: ListAssets::class,
            openapi: new Model\Operation(
                summary: 'List connected business assets',
                description: 'Returns WABA id, phone numbers, template quota status.',
                responses: ['200'=> new Model\Response(description: 'Assets list')]
            ),
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            name: 'facebook_create_template',
            uriTemplate: '/facebook/templates',
            controller: CreateTemplate::class,
            openapi: new Model\Operation(
                summary: 'Create a message template',
                description: 'Proxies a template creation request to Meta Graph on behalf of the customer.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json'=>[
                            'schema'=>[
                                'type'=>'object',
                                'required'=>['name','category','components','language'],
                                'properties'=>[
                                    'name'=>['type'=>'string'],
                                    'category'=>['type'=>'string','enum'=>['UTILITY','AUTHENTICATION','MARKETING']],
                                    'language'=>['type'=>'string','example'=>'en_US'],
                                    'components'=>['type'=>'array','items'=>['type'=>'object']]
                                ]
                            ]
                        ]
                    ])
                ),
                responses: ['201'=> new Model\Response(description: 'Template created (pending review)')]
            ),
            security: "is_granted('ROLE_USER')"
        ),
        new Get(
            name: 'facebook_list_templates',
            uriTemplate: '/facebook/templates',
            controller: ListTemplates::class,
            openapi: new Model\Operation(
                summary: 'List message templates',
                description: 'Lists templates for the connected WABA (status, categories, languages).',
                responses: ['200'=> new Model\Response(description: 'Templates list')]
            ),
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            name: 'facebook_send_message',
            uriTemplate: '/facebook/messages',
            controller: SendMessage::class,
            openapi: new Model\Operation(
                summary: 'Send WhatsApp message',
                description: 'Sends approved template or session message to a recipient number.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json'=>[
                            'schema'=>[
                                'type'=>'object',
                                'required'=>['to','type'],
                                'properties'=>[
                                    'to'=>['type'=>'string','example'=>'+14155551234'],
                                    'type'=>['type'=>'string','enum'=>['template','text']],
                                    'template'=>['type'=>'object','properties'=>[
                                        'name'=>['type'=>'string'],
                                        'language'=>['type'=>'string','example'=>'en_US'],
                                        'components'=>['type'=>'array','items'=>['type'=>'object']]
                                    ]],
                                    'text'=>['type'=>'object','properties'=>['body'=>['type'=>'string']]]
                                ]
                            ]
                        ]
                    ])
                ),
                responses: ['200'=> new Model\Response(description: 'Message sent')]
            ),
            security: "is_granted('ROLE_USER')"
        ),
    ],
    provider: null,
    processor: null
)]
final class FacebookSystem
{
    public string $id = 'facebook-system';
}
