<?php

namespace App\Jobs;

use App\Models\Upload;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUpload implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The podcast instance.
     *
     * @var \App\Models\Upload
     */
    protected $upload;
    protected $request;

    /**
    * The number of seconds after which the job's unique lock will be released.
    *
    * @var int
    */
    public $uniqueFor = 3600;

    /**
    * The unique ID of the job.
    *
    * @return string
    */
    public function uniqueId()
    {
        return $this->upload->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       $validator = Validator::make(Request::all(), [
            'file' => 'required|mimes:pdf,xlx,csv|max:2048',
        ]);
  
        if(!$validator->fails()) {
            $fileName = time().'.'.Request::file()->extension();  
            Request::file()->move(public_path('uploads'), $fileName);
        }
    }
}
