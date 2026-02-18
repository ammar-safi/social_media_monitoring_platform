<?php

namespace App\Jobs\Analysis;

use App\Models\Analyst;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = DB::table("hashtag_post")
            ->join("posts", function ($join) {
                $join->on("posts.uuid", "=", "hashtag_post.post_uuid")
                    ->whereNull("posts.deleted_at");
            })
            ->join("hashtags", function ($join) {
                $join->on("hashtags.uuid", "=", "hashtag_post.hashtag_uuid")
                    ->whereNull("hashtags.deleted_at");
            })
            ->join("gov_orgs", function ($join) {
                $join->on("gov_orgs.id", "=", "hashtags.gov_id")
                    ->whereNull("gov_orgs.deleted_at");
            })
            ->leftJoin("analysis", function ($join) {
                $join->on("analysis.post_id", "=", "posts.id")
                    ->on("analysis.gov_id", "=", "gov_orgs.id");
            })
            ->whereNull("analysis.post_id")
            ->whereNull("analysis.deleted_at")
            ->select([
                "posts.id as post_id",
                "gov_orgs.id as gov_id",
            ]);

        $results = $query->get()->map(function ($item) {
            return [
                "post_id" => $item->post_id,
                "gov_id" => $item->gov_id,
            ];
        })->toArray();
        Log::info("Dispatching analysis for " . count($results) . " post-gov pairs.");
        Analyst::upsert($results, ["post_id", "gov_id"], ["post_id", "gov_id"]);


        SentimentAnalysisJob::dispatch();
        StanceAnalysisJob::dispatch();
    }
}
