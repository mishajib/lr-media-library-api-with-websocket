<?php

use Intervention\Image\Facades\Image;


// Get file from storage folder
if (!function_exists('storageLink')) {
    function storageLink($url, $type = 'default')
    {
        $url = $url ?? '';
        if (Storage::disk('public')->exists($url)) {
            return Storage::disk('public')->url($url);
        } else {
            if ($type == 'user') {
                return Storage::disk('public')->url(config('settings.default_user_image'));
            }
            return Storage::disk('public')->url(config('settings.default_image'));
        }
    }
}

// Upload image and return the uploaded path
function imageUploadHandler($image, $request_path = 'default', $size = null, $old_image = null)
{
    if (isset($old_image)) {
        if (Storage::disk('public')->exists($old_image)) {
            Storage::disk('public')->delete($old_image);
        }
    }

    $path = $image->store($request_path, 'public');

    if (isset($size)) {
        $request_size = explode('x', $size);
        $width        = $request_size[0];
        $height       = $request_size[1];
    } else {
        $width  = 80;
        $height = 80;
    }

    $image = Image::make(Storage::disk('public')->get($path))->fit($width, $height)->encode();

    Storage::disk('public')->put($path, $image);

    return $path;
}
