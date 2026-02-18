<?php

namespace App\Jobs;

use App\Models\Hashtag;
use App\Models\HashtagPost;
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

            $hashtags = Hashtag::all()->pluck("name", "uuid")->toArray();
            if (empty($hashtags)) {
                throw new Exception("There is no hashtags");
            }

            Log::info("Reading the csv file ...");

            $extracted_posts = $extract_posts->extractPosts($path, $hashtags, $start_line->line);

            DB::transaction(function () use ($extracted_posts, $start_line) {

                $this->insert($extracted_posts["data"]);

                $start_line->update([
                    'line' => $extracted_posts["current_line"]
                ]);
            });
        } catch (Exception $e) {
            Log::error("Error in extraction posts", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    private function insert($collection)
    {
        if (!empty($collection["posts"])) {
            Log::info("inserting post ...");
            Post::upsert(
                $collection['posts'],
                ["hash"],
                ['content', 'updated_at']
            );

            $hashes = array_unique(array_column($collection["posts"], 'hash'));
            $post_from_db = Post::whereIn("hash", $hashes)->get()->keyBy("hash");

            $pivot = [];

            foreach ($collection["post_hashtag"] as $relation_with_hash) {

                $hash = $relation_with_hash["post_hash"];

                if (!isset($post_from_db[$hash])) {
                    continue;
                }
                $pivot[] = [
                    "post_uuid" => $post_from_db[$hash]->uuid,
                    "hashtag_uuid" => $relation_with_hash["hashtag_uuid"]
                ];
            }

            HashtagPost::upsert(
                $pivot,
                ["post_uuid", "hashtag_uuid"]
            );
        }
    }
}