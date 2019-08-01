<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Transformers\ImageTransformer;

class ImagesController extends Controller
{
    //
    public function store(ImageRequest $imageRequest, ImageUploadHandler $imageUploadHandler, Image $image)
    {
        $user = $this->user();

        $size = $imageRequest->type == 'avatar' ? 362 : 1024;

        $result = $imageUploadHandler->save($imageRequest->image, str_plural($imageRequest->type), $user->id, $size);

        $image->path = $result['path'];
        $image->type = $imageRequest->type;
        $image->user_id = $user->id;
        $image->save();

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }
}
