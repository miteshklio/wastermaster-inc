<?php namespace App\Http\Controllers;

/**
 * Dynamically crop images and cache the result
 *
 * Class ImageController
 * @package App\Http\Controllers
 */

class ImageController extends Controller {

    /**
     * Crop images
     *
     * @param $width
     * @param $height
     * @param $img
     * @param null $position
     * @return mixed
     */
    protected function crop($width, $height, $img, $position = null)
    {
        // Decode image
        $img = base64_decode(str_replace('.jpg', '', $img));

        // Check if local
        if(stristr($img, 'http') == false)
        {
            $img = base_path()."/public$img";
        }

        return \Image::cache(function($image) use ($img, $width, $height, $position)
        {
            $image->make($img)
                ->fit($width, $height, null, $position);
        });

    }
}