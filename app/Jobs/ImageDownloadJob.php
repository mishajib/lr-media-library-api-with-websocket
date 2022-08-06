<?php

namespace App\Jobs;

use App\Events\ImageDownloadedEvent;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImageDownloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url, $previousImage, $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $user_id, $previousImage = null)
    {
        $this->url           = $url;
        $this->user_id       = $user_id;
        $this->previousImage = $previousImage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Database transaction start
            DB::beginTransaction();

            // Extract file info from url - (dirname, basename, filename)
            $info = pathinfo($this->url);

            // get contents of url
            $contents = file_get_contents($this->url);

            // create a temporary file
            $file = '/tmp/' . $info['basename'];

            // write contents to temporary file
            file_put_contents($file, $contents);

            // create a new uploaded file
            $uploaded_file = new UploadedFile($file, $info['basename']);

            // upload image and get image path
            $path = imageUploadHandler($uploaded_file, 'images', '1024x1024', $this->previousImage?->path);

            // delete temporary file
            unlink($file);

            $image = $this->previousImage;
            if ($this->previousImage) {
                // update image in database
                $this->previousImage->update([
                    'name' => $info['filename'],
                    'path' => $path
                ]);
            } else {
                // store image in database
                $image = Image::create([
                    'name'    => $info['filename'],
                    'path'    => $path,
                    'user_id' => $this->user_id,
                ]);
            }

            // Database transaction commit
            DB::commit();

            event(new ImageDownloadedEvent('Image downloaded & uploaded successfully.', $this->user_id));

            Log::info('Image downloaded & uploaded successfully.', $image->toArray());
        } catch (\Exception $e) {
            // Database transaction rollback
            DB::rollBack();
            Log::error("Image upload error - " . $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }
}
