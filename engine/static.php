<?php


ignore_user_abort(true);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$watermarks = [
    'default' => [
        'file' => 'static/content/logo.png',
        'position' => 'bottom-right',
        'offsets' => [
            'x' => 10,
            'y' => 10,
        ],
    ],
];

/*
|--------------------------------------------------------------------------
| Create static files miner
|--------------------------------------------------------------------------
|
| Handles requests to nonexistent static files,
| analyzes and modifies the URL file before he was sent to http response,
| and stores the processed file and next time have
| to give the finished file is not causing this script
|
 */

$miner = new \Ideil\ImageMiner\Miner(
    Symfony\Component\HttpFoundation\Request::createFromGlobals(),
    '~/[a-z\d]{1}/[a-z\d]{2}/.+$~',
    [
        'external_tokens' => [
            env(
                'FILE_MANAGER_TOKEN',
                env('APP_KEY')
            ),
        ],
    ]
);

// var_dump('here');
// die();

$miner->setRequestFilePathResolver(function (string $path) {
    $parts = explode(
        '/',
        trim($path, '/')
    );
    $fileNameParts = explode('--', $parts[2]);

    $fileName = end($fileNameParts);

    return "/{$parts[0]}/{$parts[1]}/{$fileName}";
});

/*
|--------------------------------------------------------------------------
| Configure
|--------------------------------------------------------------------------
|
| Setup basic settings
|
 */

$miner->setDevModeActivity(env('APP_DEBUG'));

$miner->setUriRoot('/content/thumbs/');

$miner->setHandledFilesRoot(__DIR__ . '/../static');

$miner->setOriginalFilesRoot(__DIR__ . '/../static/content/files');

$miner->setCleanUriResolver(function ($uri) {
    return str_replace(
        env('APP_STATIC_URL_PREFIX', ''),
        '',
        $uri
    );
});

/*
|--------------------------------------------------------------------------
| Register thumb handlers
|--------------------------------------------------------------------------
|
| Register handlers for processing files
|
 */

// Crop processing
// Example: URL must contain a fragment /0x0-100x100/

$miner->addConversion('default', '/.*/', function ($image, $matches) use ($watermarks) {
    $uri = $matches[0];

    [$payload, $folder1, $folder2, $fullFileName] = explode(
        '/',
        trim($uri, '/')
    );

    $fileNameParts = explode(
        '--',
        preg_replace('/^\w{6}\-/', '', $fullFileName)
    );

    $selectedConversions = [
        'fit' => false,
        'background_color' => null,
        'watermark' => null,
        'crop' => null,
    ];

    if (count($fileNameParts) === 2) {
        $conversionString = $fileNameParts[0];

        foreach (explode('-', $conversionString) as $conversion) {
            $conversionMatches = [];

            if ($conversion === 'f') {
                $selectedConversions['fit'] = true;
            }

            if (preg_match('/bc([0-9a-f]{6})/', $conversion, $conversionMatches)) {
                $selectedConversions['background_color'] = $conversionMatches[1];
            }

            if ($conversion === 'bcnone') {
                $selectedConversions['background_color'] = 'none';
            }

            if ($conversion === 'w') {
                $selectedConversions['watermark'] = true;
            }

            if (preg_match('/w(\w+)/', $conversion, $conversionMatches)) {
                $selectedConversions['watermark'] = $conversionMatches[1];
            }

            if ($conversion === 'c') {
                $selectedConversions['crop'] = true;
            }

            if ($conversion === 'cp') {
                $selectedConversions['crop'] = [
                    'width' => null,
                    'height' => null,
                    'x' => null,
                    'y' => null,
                    'postpone' => true,
                ];
            }

            if (preg_match('/c[p]?(\d+)x(\d+)/', $conversion, $conversionMatches)) {
                $isPostponeCrop = starts_with($conversionMatches[0], 'cp');

                $selectedConversions['crop'] = [
                    'width' => $conversionMatches[1],
                    'height' => $conversionMatches[2],
                    'x' => null,
                    'y' => null,
                    'postpone' => $isPostponeCrop,
                ];
            }

            if (preg_match('/^c[p]?(\d+x\d+|\*x\*){1}(x\d+(\.\d+)?[p]?){0,2}$/', $conversion, $conversionMatches)) {
                $isPostponeCrop = starts_with($conversionMatches[0], 'cp');

                $cropParams = explode(
                    'x',
                    str_replace(['cp', 'c'], '', $conversionMatches[0])
                );

                $selectedConversions['crop'] = [
                    'width' => $cropParams[0] !== '*' ? $cropParams[0] : null,
                    'height' => $cropParams[1] !== '*' ? $cropParams[1] : null,
                    'x' => $cropParams[2],
                    'y' => $cropParams[3],
                    'postpone' => $isPostponeCrop,
                ];
            }
        }
    }

    $payloadMatches = [];

    $cropIfNeed = function (
        $image,
        $width = null,
        $height = null,
        Closure $convert
    ) use ($selectedConversions) {
        /** @var \Intervention\Image\Image $image */
        if (! $selectedConversions['crop']) {
            $convert($image);

            return $image;
        }

        $crop = $selectedConversions['crop'];

        if ($crop['postpone']) {
            $convert($image);
        }

        $cropWidth = $crop['width'] ?? $width;
        $cropHeight = $crop['height'] ?? $height;

        $rawX = $crop['x'] ?? '50p';
        $rawY = $crop['y'] ?? '50p';

        if (ends_with($rawX, 'p')) {
            $xPercent = str_replace('p', '', $rawX);

            $x = $xPercent * $image->width() / 100;
        } else {
            $x = $rawX;
        }

        if (ends_with($rawY, 'p')) {
            $yPercent = str_replace('p', '', $rawY);

            $y = $yPercent * $image->height() / 100;
        } else {
            $y = $rawY;
        }

        $x = max(0, $x - $cropWidth / 2);
        $y = max(0, $y - $cropHeight / 2);

        if (! isset($cropWidth, $cropHeight)) {
            return $image;
        }

        $overWidth = ($cropWidth + $x) - $image->width();
        $overHeight = ($cropHeight + $y) - $image->height();

        if ($overWidth > 0) {
            $x -= $overWidth;
        }

        if ($overHeight > 0) {
            $y -= $overHeight;
        }

        $image->crop((int)$cropWidth, (int)$cropHeight, (int)$x, (int)$y);

        if (! $crop['postpone']) {
            $convert($image);
        }

        return $image;
    };

    // Fit image to size
    if (preg_match('/(\d+)\*(\d+)/', $payload, $payloadMatches)) {
        $cropIfNeed(
            $image,
            $payloadMatches[1],
            $payloadMatches[2],
            function ($image) use ($payloadMatches) {
                $image->fit($payloadMatches[1], $payloadMatches[2]);
            }
        );
    }

    // Resize image with background
    if (preg_match('/(\d+)\-(\d+)/', $payload, $payloadMatches)) {
        $cropIfNeed(
            $image,
            $payloadMatches[1],
            $payloadMatches[2],
            function ($image) use ($payloadMatches, $selectedConversions) {
                $image->resize(
                    $payloadMatches[1],
                    $payloadMatches[2],
                    function ($constraint) use ($selectedConversions) {
                        $constraint->aspectRatio();

                        if (! $selectedConversions['fit']) {
                            $constraint->upsize();
                        }
                    }
                );

                $backgroundColor = $selectedConversions['background_color'] ?? 'ffffff';

                $image->resizeCanvas(
                    $payloadMatches[1],
                    $payloadMatches[2] ?: null,
                    'center',
                    false,
                    $backgroundColor === 'none' ? [255, 255, 255, 0] : "#{$backgroundColor}"
                );
            }
        );
    }

    // Resize image
    if (preg_match('/(\d*)x(\d*)/', $payload, $payloadMatches)) {
        $cropIfNeed(
            $image,
            $payloadMatches[1] ?? null,
            $payloadMatches[2] ?? null,
            function ($image) use ($selectedConversions, $payloadMatches) {
                if (empty($payloadMatches[1])) {
                    $image->heighten($payloadMatches[2], function ($constraint) use ($selectedConversions) {
                        if (! $selectedConversions['fit']) {
                            $constraint->upsize();
                        }
                    });
                } elseif (empty($payloadMatches[2])) {
                    $image->widen($payloadMatches[1], function ($constraint) use ($selectedConversions) {
                        if (! $selectedConversions['fit']) {
                            $constraint->upsize();
                        }
                    });
                } else {
                    $image->fit($payloadMatches[1], $payloadMatches[2],
                        function ($constraint) use ($selectedConversions) {
                            if (! $selectedConversions['fit']) {
                                $constraint->upsize();
                            }
                        });
                }
            }
        );
    }

    if ($selectedConversions['watermark'] || isset($watermarks[$selectedConversions['watermark']])) {
        $watermarkName = $selectedConversions['watermark'] === true ? 'default' : $selectedConversions['watermark'];
        $watermarkData = $watermarks[$watermarkName];
        $watermarkFilePath = dirname(__DIR__) . '/' . $watermarkData['file'];

        if (file_exists($watermarkFilePath)) {
            $image->insert(
                $watermarkFilePath,
                $watermarkData['position'],
                $watermarkData['offsets']['x'],
                $watermarkData['offsets']['y']
            );
        }
    }

    return $image;
});

/*
|--------------------------------------------------------------------------
| Run
|--------------------------------------------------------------------------
|
| Execute all handlers and send response
|
 */

$miner->handle() ? $miner->handle()->send() : null;

// if (file_exists('optimize.php')) {
//     require_once('optimize.php');

//     if ($prefix = env('APP_STATIC_URL_PREFIX')) {
//         $requestUri = str_replace($prefix, '', $_SERVER['REQUEST_URI']);
//     } else {
//         $requestUri = $_SERVER['REQUEST_URI'];
//     }

//     $filepath = sprintf(
//         '%s/%s',
//         rtrim(__DIR__ . '/../static', '/'),
//         ltrim($requestUri, '/')
//     );

//     optimize($filepath);
// }
