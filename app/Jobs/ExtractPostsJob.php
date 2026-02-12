<?php

namespace App\Jobs;

use App\Models\Hashtag;
use App\Models\ImportLine;
use App\Models\Post;
use App\Services\ExtractPostsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtractPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(ExtractPostsService $extract_posts): void
    {
        DB::beginTransaction();
        try {

            /**
             * 1 - grab the path of csv 
             * 2 - grab the last line form the BD
             * 3 - grab hashtags 
             * 4 - extract the posts form csv extract(path , hashtags)
             * 5 - save it in the data base 
             * 6 - in another job , classification (sync with post -> Governments)
             */
            Log::info("Extracting post ...");
            $path = storage_path("app/posts/twitter_posts.csv");
            if (!file_exists($path)) {
                throw new Exception("No CSV File");
            }

            $start_line = ImportLine::firstOrCreate(
                ["id" => 1],
                ["line" => 0]
            );
            if (!$start_line) {
                throw new Exception("Error with import line from DB");
            }

            $hashtags = Hashtag::all()->pluck("name", "id")->toArray();
            if (empty($hashtags)) {
                throw new Exception("There is no hashtags");
            }

            Log::info("Reading the csv file ...");

            $extracted_posts = $extract_posts->extractPosts($path, $hashtags, $start_line->line);

            $this->insert($extracted_posts["posts"]);

            $start_line->update([
                'line' => $extracted_posts["current_line"]
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error in extraction posts", [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function insert($collection)
    {
        if (!empty($collection)) {
            Log::info("inserting post ...");
            foreach ($collection as $item) {
                $post = Post::create([
                    "content" =>
                    $item['content']
                ]);
                $post->Hashtags()->attach($item["hashtags"]);
            }
        }
    }
}
