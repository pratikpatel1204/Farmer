<?php

/*
|--------------------------------------------------------------------------
| KisanMandi front settings
|--------------------------------------------------------------------------
| Use: config('kisanmandi.categories'), config('kisanmandi.per_page.products')
|
| categories: header dropdown, Category page aur filters sab yahin se chalte hain.
|   key   = URL slug  (category/vegetables)
|   name  = products.category column me jo naam save hai, wahi likhein
|   match = agar products.category me koi aur value bhi save hai (purana naam ya id)
|           to yahan daal dein, wo bhi isi category me ginega
|   icon  = Font Awesome icon,  color = green | red | yellow | lime | brown | dark-green
*/

return [

    'categories' => [
        'vegetables'     => ['name' => 'Vegetables',      'icon' => 'fa-carrot',      'color' => 'green',      'match' => ['Sabzi', 'Sabziyan']],
        'fruits'         => ['name' => 'Fruits',          'icon' => 'fa-apple-whole', 'color' => 'red',        'match' => ['Fal']],
        'grains-pulses'  => ['name' => 'Grains & Pulses', 'icon' => 'fa-wheat-awn',   'color' => 'yellow',     'match' => ['Anaaj', 'Anaaj & Dal']],
        'seeds'          => ['name' => 'Seeds',           'icon' => 'fa-seedling',    'color' => 'lime',       'match' => ['Beej']],
        'fertilizers'    => ['name' => 'Fertilizers',     'icon' => 'fa-flask',       'color' => 'brown',      'match' => ['Khad', 'Khad & Urvarak']],
        'farm-machinery' => ['name' => 'Farm Machinery',  'icon' => 'fa-tractor',     'color' => 'dark-green', 'match' => ['Krishi Yantra']],
        'dairy'          => ['name' => 'Milk & Dairy',    'icon' => 'fa-cow',         'color' => 'yellow',     'match' => ['Doodh & Dairy']],
        'animal-feed'    => ['name' => 'Animal Feed',     'icon' => 'fa-bowl-food',   'color' => 'red',        'match' => ['Pashu Aahar']],
    ],

    // Phone, email, address, map ab admin ke contact_settings table se aate hain (ContactSetting model).
    'contact' => [
        // Contact form ka message is email par jayega (khali ho to contact_settings ka email, phir MAIL_FROM_ADDRESS)
        'form_to' => env('CONTACT_FORM_TO'),
    ],

    // Product enquiry (product_inquiries.status) naya record kis status se save ho
    'inquiry_status' => env('INQUIRY_STATUS', 'Pending'),

    // Listing page par ek baar me kitne product / blog
    'per_page' => [
        'products' => 12,
        'blogs'    => 9,
    ],
];
