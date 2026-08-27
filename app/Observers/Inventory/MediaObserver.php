<?php

namespace App\Observers\Inventory;

use App\Models\Product\Media;

class MediaObserver
{
    public function createData(String $image, String $imageId, String $type)
    {
        return Media::create([
            'path'              => $image,
            'imageable_type'    => $type,
            'imageable_id'      => $imageId
        ]);
    }
}
