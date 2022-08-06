<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Jobs\ImageDownloadJob;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // Get authenticated user
        $user = $request->user()->load('images');

        return success_response(
            'Images retrieved successfully.',
            Response::HTTP_OK,
            ImageResource::collection($user->images()->latest()->paginate(5))->withQuery($request->query())->response()->getData(true)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ImageRequest $request
     * @return JsonResponse
     */
    public function store(ImageRequest $request)
    {
        try {
            // Get image from request
            $url = $request->input('url');

            // Dispatch job to download and upload image
            ImageDownloadJob::dispatch($url, $request->user());

            return success_response(
                'Image upload processing, after completion you will notified!',
                Response::HTTP_CREATED,
            );

        } catch (\Exception $e) {
            return error_response(
                'Could not upload image, please try again!',
                Response::HTTP_BAD_REQUEST,
                $e->getMessage()
            );
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Image $image
     * @return JsonResponse
     */
    public function destroy(Image $image)
    {
        try {
            // Database transaction start
            DB::beginTransaction();

            // delete image from storage
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            // delete image from database
            $image->delete();

            // Database transaction commit
            DB::commit();

            return success_response(
                'Image deleted successfully.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            // Database transaction rollback
            DB::rollBack();
            return error_response(
                'Could not delete image, please try again!',
                Response::HTTP_BAD_REQUEST,
                $e->getMessage()
            );
        }
    }
}
