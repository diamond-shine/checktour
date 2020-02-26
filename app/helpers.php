<?php

/**
 * @param string $path
 * @return string
 */
function static_asset_rev(string $path): string
{
    static $manifest = null;

    if (null === $manifest) {
        $manifestPath = \storage_path('assets/manifest/manifest.json');

        $manifest = \is_file($manifestPath) ?
            json_decode(
                file_get_contents($manifestPath),
                true
            ) :
            [];
    }

    $path = Arr::get($manifest, $path) ?: $path;

    return \static_asset("{$path}");
}
