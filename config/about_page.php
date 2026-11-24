<?php

/*
|--------------------------------------------------------------------------
| About Us content (App API: GET /api/v1/pages/about)
|--------------------------------------------------------------------------
| Website ka About page (resources/views/front/pages/about.blade.php) jaisa hi text.
| Text badlo to website ka view bhi same badalna (dono jagah same rahe).
| ":brand" ki jagah config('app.name') lagta hai (abhi RGM Mart).
| Icons Font Awesome 6 ke naam hain (bina "fa-"), app apne icon set me map kare.
| Badalne ke baad: php artisan config:clear
*/

return [
    'hero' => [
        'badge'    => 'About us',
        'title'    => 'Much More Than an E-Commerce Portal!',
        'subtitle' => 'Connecting Farmers, Businesses & Consumers – For Mutual Growth',
    ],

    'intro' => [
        'eyebrow'          => 'Who we are',
        'title'            => 'Connecting Farmers, Businesses & Consumers',
        'text'             => ':brand is a nationwide digital networking platform that directly connects Farmers, Agricultural Businesses and Consumers, creating opportunities to exchange products, services, resources, knowledge and business opportunities.',
        'philosophy_label' => 'Our philosophy is simple',
        'philosophy'       => 'Connect people directly so they can help each other, save costs, generate business and grow together.',
        'nodes'            => [
            ['key' => 'farmer', 'label' => 'Farmers', 'icon' => 'wheat-awn', 'color' => 'green'],
            ['key' => 'business', 'label' => 'Businesses', 'icon' => 'industry', 'color' => 'brown'],
            ['key' => 'consumer', 'label' => 'Consumers', 'icon' => 'users', 'color' => 'red'],
        ],
        'center_label'     => 'Mutual Growth',
    ],

    'connections_title' => [
        'eyebrow' => 'How we connect',
        'title'   => 'Three connections, one network',
    ],

    'connections' => [
        [
            'no'         => '01',
            'title'      => 'Farmer ↔ Business',
            'subtitle'   => 'Connecting Farmers with Companies & Businesses',
            'color'      => 'green',
            'paragraphs' => [
                'Farmers can directly connect with manufacturers, dealers, suppliers and service providers to access everything they need for agriculture:',
            ],
            'chips'      => [
                'Seeds', 'Saplings', 'Fertilizers', 'Pesticides', 'Manure', 'Machinery', 'Farming tools',
                'Solar energy', 'Drip irrigation', 'Biogas', 'Modern farming technologies', 'Equipment repair',
                'Soil testing', 'Crop consultancy', 'Insurance', 'Farm loans', 'Transportation', 'Warehousing',
                'Cold storage', 'Processing', 'Packaging',
            ],
            'chips_more' => 'and other agricultural services',
            'examples_intro' => 'Similarly, companies and industries can directly connect with farmers to procure agricultural produce and raw materials.',
            'examples'   => [
                ['icon' => 'bottle-droplet', 'text' => 'A ketchup manufacturer can find tomato growers'],
                ['icon' => 'wheat-awn', 'text' => 'A flour mill can connect with wheat farmers'],
                ['icon' => 'apple-whole', 'text' => 'A food processing company can source fruits, vegetables and other ingredients directly from producers'],
            ],
            'highlight'  => null,
            'cards'      => [],
            'footer'     => null,
            'result'     => 'Direct connections between agricultural suppliers, farmers and industries – creating opportunities for both sides.',
        ],
        [
            'no'         => '02',
            'title'      => 'Farmer ↔ Farmer',
            'subtitle'   => 'Farmers Helping Farmers',
            'color'      => 'yellow',
            'paragraphs' => [
                'Farmers can search and connect directly with other farmers in nearby villages, districts or anywhere across India to support each other in farming and related activities.',
                'They can buy, sell, rent, exchange or share seeds, saplings, manure, crops, machinery, tractors, tools, labour, land, water, storage and other agricultural resources.',
            ],
            'chips'      => [],
            'chips_more' => null,
            'examples_intro' => null,
            'examples'   => [
                ['icon' => 'tractor', 'text' => 'Rent another farmer’s tractor'],
                ['icon' => 'map-location-dot', 'text' => 'Lease available farmland'],
                ['icon' => 'droplet', 'text' => 'Access water from a nearby farm through mutually agreed arrangements'],
                ['icon' => 'right-left', 'text' => 'Exchange produce and resources instead of making cash payments'],
            ],
            'highlight'  => null,
            'cards'      => [],
            'footer'     => 'Farmers can also share farming techniques, practical experiences, innovative ideas, cost-saving methods, market information and knowledge about cultivation, irrigation, soil health, pest management, harvesting, storage and marketing.',
            'result'     => 'Reduced farming costs, better utilisation of resources, knowledge sharing and stronger farmer-to-farmer cooperation.',
        ],
        [
            'no'         => '03',
            'title'      => 'Farmer ↔ Consumer',
            'subtitle'   => 'Bringing Farmers Closer to the General Public',
            'color'      => 'red',
            'paragraphs' => [
                'Farmers can directly connect with households, families, residential societies and consumers to sell fresh vegetables, fruits, grains, pulses, dairy products, spices, honey and other agricultural produce.',
            ],
            'chips'      => [],
            'chips_more' => null,
            'examples_intro' => null,
            'examples'   => [],
            'highlight'  => 'But this relationship goes beyond buying and selling!',
            'cards'      => [
                ['icon' => 'handshake-angle', 'title' => 'Consumers help farmers', 'text' => 'By providing business leads, connecting them with potential buyers, sharing professional expertise, technical knowledge, marketing skills and business opportunities.'],
                ['icon' => 'children', 'title' => 'Consumers visit farms', 'text' => 'Introduce children to farming, learn agricultural practices, organise community visits and events, participate in educational and social programmes, and develop meaningful relationships with farming communities.'],
            ],
            'footer'     => null,
            'result'     => 'Direct farm-to-consumer connections, new business opportunities, agricultural awareness and mutual support.',
        ],
    ],

    'who_can_join' => [
        'eyebrow' => 'Who can join?',
        'title'   => 'Everyone is Welcome!',
        'members' => [
            ['icon' => 'wheat-awn', 'label' => 'Farmers'],
            ['icon' => 'people-group', 'label' => 'FPOs'],
            ['icon' => 'industry', 'label' => 'Manufacturers'],
            ['icon' => 'scale-balanced', 'label' => 'Traders'],
            ['icon' => 'boxes-stacked', 'label' => 'Wholesalers'],
            ['icon' => 'store', 'label' => 'Retailers'],
            ['icon' => 'screwdriver-wrench', 'label' => 'Agricultural service providers'],
            ['icon' => 'microchip', 'label' => 'Technology companies'],
            ['icon' => 'lightbulb', 'label' => 'Entrepreneurs'],
            ['icon' => 'user-tie', 'label' => 'Professionals'],
            ['icon' => 'house-chimney', 'label' => 'Families'],
            ['icon' => 'basket-shopping', 'label' => 'Consumers'],
        ],
    ],

    'how_it_works' => [
        'eyebrow' => 'How does it work?',
        'title'   => 'List, post, connect',
        // action: app kis screen par le jaye (have / want listing, register)
        'steps'   => [
            ['step' => 1, 'key' => 'have', 'icon' => 'seedling', 'color' => 'brown', 'title' => 'People Have', 'text' => 'List your available products, produce, machinery, land, services, expertise and resources.', 'action' => ['label' => 'See People Have', 'screen' => 'products', 'type' => 'have']],
            ['step' => 2, 'key' => 'want', 'icon' => 'cart-shopping', 'color' => 'red', 'title' => 'People Want', 'text' => 'Post your requirements, specifications, quantity, location and other details.', 'action' => ['label' => 'See People Want', 'screen' => 'products', 'type' => 'want']],
            ['step' => 3, 'key' => 'connect', 'icon' => 'handshake', 'color' => 'green', 'title' => 'Connect', 'text' => 'Discover relevant members, connect directly, discuss requirements and establish mutually beneficial relationships.', 'action' => ['label' => 'Join free', 'screen' => 'register', 'type' => null]],
        ],
    ],

    'vision' => [
        'label'   => 'Our Vision',
        'text'    => 'To build India’s largest connected agricultural ecosystem where Farmers, Businesses and Consumers can find each other, help each other and create opportunities together.',
        'words'   => ['Connect.', 'Collaborate.', 'Trade.', 'Share.', 'Grow Together!'],
        'tagline' => 'One Network. Endless Possibilities. Mutual Growth.',
    ],

    'cta' => [
        'title'   => 'Ready to join :brand?',
        'text'    => 'Farmers, businesses and consumers – everyone is welcome. Register free and start connecting today.',
        'buttons' => [
            ['label' => 'Register free', 'screen' => 'register'],
            ['label' => 'Talk to us', 'screen' => 'contact'],
        ],
    ],

    // App ke liye website ke rang (hex)
    'colors' => [
        'green'      => '#1d7a3a',
        'green_dark' => '#135c2a',
        'lime'       => '#8cc63f',
        'yellow'     => '#f5b324',
        'brown'      => '#7b4a23',
        'red'        => '#e2552d',
        'mist'       => '#f1f8ee',
        'sun_bg'     => '#fff8e6',
        'tomato_bg'  => '#fff3ef',
        'ink'        => '#1d2a1f',
        'ink_soft'   => '#55665a',
        'line'       => '#d9e8d3',
    ],
];
