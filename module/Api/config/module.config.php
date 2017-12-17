<?php
return [
    'doctrine' => [
        'driver' => [
            'xml_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Api/Model/Entity' => 'Api\Model\Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    'Api\Model\Entity' =>  'xml_driver'
                ],
            ],
        ],
    ],
	'errors' => [
		'post_processor' => 'json-pp',
		'show_exceptions' => [
			'message' => true,
			'trace'   => true
		]
	],
	'di' => [
		'instance' => [
			'alias' => [
				'json-pp'  => 'Api\PostProcessor\Json',
				'xml-pp'   => 'Api\PostProcessor\Xml'
			]
		]
	],
	'controllers' => [
		'invokables' => [
			'api' => 'Api\Controller\ApiController',
		]
	],
	'router' => [
		'routes' => [
			'restful' => [
				'type'    => 'Zend\Mvc\Router\Http\Segment',
				'options' => [
					'route'       => '/:controller[.:formatter][/:id]',
					'constraints' => [
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'formatter'  => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'         => '[a-zA-Z0-9_-]*'
					],
				],
			],
		],
	]
];
