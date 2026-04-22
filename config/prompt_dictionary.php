<?php

return [
    'categories' => [
        'subject' => [
            'монгол гэр' => 'traditional Mongolian ger (yurt)',
            'гэр' => 'ger / yurt',
            'уламжлалт монгол гэр' => 'traditional Mongolian ger',
            'орчин үеийн монгол гэр' => 'modernized Mongolian ger',
            'байшин' => 'house',
            'орон сууц' => 'residential building',
            'вилла' => 'villa',
            'кабин' => 'cabin house',
            'зочид буудал' => 'hotel',
            'амралтын газар' => 'resort',
            'ресторан' => 'restaurant interior',
            'гал тогоо' => 'kitchen interior',
            'унтлагын өрөө' => 'bedroom interior',
            'зочны өрөө' => 'living room interior',
            'оффис' => 'office interior',
            'кафе' => 'cafe interior',
        ],

        'environment' => [
            'дотор' => 'interior',
            'дотор орчин' => 'interior environment',
            'гадаа' => 'exterior',
            'гадна орчин' => 'exterior environment',
            'тал хээр' => 'steppe landscape',
            'уулын бүс' => 'mountain landscape',
            'ойн бүс' => 'forest environment',
            'хөдөө орчин' => 'rural environment',
            'хотын орчин' => 'urban environment',
            'жуулчны бааз' => 'tourist camp setting',
        ],

        'structure' => [
            'тооно' => 'central crown opening',
            'уняа' => 'wooden roof poles',
            'багана' => 'support columns',
            'хана' => 'lattice wall structure',
            'хаалга' => 'entrance door',
            'дээвэр' => 'roof structure',
            'дотоод зохион байгуулалт' => 'interior layout',
            'дугуй хэлбэр' => 'circular form',
        ],

        'material' => [
            'модон' => 'wooden',
            'байгалийн мод' => 'natural wood',
            'эсгий' => 'felt',
            'эсгий бүрээс' => 'felt covering',
            'чулуу' => 'stone',
            'шилэн' => 'glass material',
            'металл' => 'metal material',
            'арьсан' => 'leather material',
            'даавуун' => 'fabric material',
        ],

        'furniture' => [
            'тавилга' => 'furniture',
            'модон тавилга' => 'handcrafted wooden furniture',
            'ширээ' => 'table',
            'сандал' => 'chair',
            'ор' => 'bed',
            'буйдан' => 'sofa',
            'авдар' => 'traditional storage chest',
            'шүүгээ' => 'cabinet',
            'хивс' => 'carpet',
        ],

        'style' => [
            'уламжлалт' => 'traditional',
            'орчин үеийн' => 'modern',
            'минимал' => 'minimalist',
            'тансаг' => 'luxurious',
            'энгийн' => 'simple',
            'тухтай' => 'cozy',
            'гар урлалын' => 'handcrafted',
            'соёлын өв' => 'culturally authentic',
        ],

        'lighting' => [
            'өглөөний нар' => 'soft morning sunlight',
            'өдрийн гэрэл' => 'daylight',
            'оройн нар' => 'warm sunset light',
            'сарны гэрэл' => 'moonlight',
            'байгалийн гэрэл' => 'natural light',
            'зөөлөн гэрэл' => 'soft light',
            'дулаан гэрэл' => 'warm lighting',
            'хүйтэн гэрэл' => 'cool lighting',
        ],

        'camera' => [
            'урдаас харсан' => 'front view',
            'хажуугаас харсан' => 'side view',
            'дээрээс харсан' => 'top view',
            'ойрын зураг' => 'close-up view',
            'өргөн өнцөг' => 'wide-angle view',
            'нүдний түвшин' => 'eye-level view',
            'дотоод перспектив' => 'interior perspective',
            'гадна перспектив' => 'exterior perspective',
        ],

        'quality' => [
            'өндөр деталчлал' => 'highly detailed',
            'реалист' => 'realistic',
            'фотореалист' => 'photorealistic',
            'архитектурын визуалчлал' => 'architectural visualization',
            'интерьер рендер' => 'interior render',
            'өндөр чанартай' => 'high quality',
            'мэргэжлийн гэрэлтүүлэг' => 'professional lighting',
            '4к' => '4k',
        ],

        'negative' => [
            'бүдгэрсэн' => 'blurry',
            'муу чанар' => 'low quality',
            'гажсан бүтэц' => 'distorted structure',
            'илүүдэл объект' => 'extra objects',
            'муу пропорц' => 'bad proportions',
            'усны тэмдэг' => 'watermark',
            'текст' => 'text',
        ],
    ],

    'quality_tags' => [
        'realistic',
        'highly detailed',
        'professional lighting',
        'architectural visualization',
        '4k',
    ],

    'negative_tags' => [
        'blurry',
        'low quality',
        'distorted structure',
        'extra objects',
        'bad proportions',
        'watermark',
        'text',
    ],

    'templates' => [
        'general' => '[subject], [environment], [structure], [materials], [style], [lighting], [camera], [quality]',
        'architecture' => '[subject], [architectural_style], [materials], [facade_details], [lighting], [camera], [quality]',
        'interior' => '[subject], [layout], [furniture], [materials], [style], [lighting], [camera], [quality]',
        'ger' => '[subject], [environment], [structure], [furniture], [materials], [style], [lighting], [camera], [quality]',
        'furniture' => '[subject], [form], [materials], [style], [color], [lighting], [camera], [quality]',
    ],
];
