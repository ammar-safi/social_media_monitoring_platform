<?php

namespace App\Jobs\Analysis;

use App\Enums\AnalystStanceEnum;
use App\Models\Analyst;
use App\Services\AnalysisModelService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StanceAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AnalysisModelService $analysis): void
    {

        Log::info("Starting stance analysis job.");

        $data = $this->prepareData();

        Log::info("Prepared data for stance analysis: " . count($data["items"]) . " items.");

        $response = $analysis->post("stance-analysis/batch", $data);

        if (isset($response["error"]) && $response["success"] == false) {
            Log::error("Sentiment analysis API error: " . $response["error"]);
            throw new Exception("connection error with ai serves ");
        }

        $data = $this->validateResponse($response);

        Log::info("Validated stance analysis response: " . count($data) . " valid items.");

        Analyst::upsert(
            $data,
            ["id", "post_id", "gov_id"],
            ["stance", "stance_confidence"]
        );
    }

    private function prepareData()
    {
        $query = DB::table("analysis")
            ->join("posts", "posts.id", "=", "analysis.post_id")
            ->join("gov_orgs", "gov_orgs.id", "=", "analysis.gov_id")
            ->whereNull("analysis.deleted_at")
            ->whereNull("posts.deleted_at")
            ->whereNull("gov_orgs.deleted_at")
            ->select([
                "analysis.id as analysis_id",
                "posts.content as post_content",
                "gov_orgs.name as gov_name",
            ]);
        $result = $query->get()->map(function ($item) {
            return [
                "id" => $item->analysis_id,
                "text" => $item->post_content,
                'target' => $item->gov_name,
            ];
        })->toArray();

        $data = [
            "items" => $result,
        ];
        return $data;
    }

    private function validateResponse($response): array
    {
        $data = [];
        $ids = array_map(function ($item) {
            return $item["id"];
        }, $response["results"]);
        $data_db = Analyst::whereIn("id", $ids)->get()->keyBy("id");
        foreach ($response["results"] as $item) {
            if ($item["status"] != "success") {
                Log::warning("Stance analysis failed for item ID " . $item["id"] . ": " . ($item["error"] ?? "Unknown error"));
                continue;
            }
            $id = $item["id"] ?? null;
            if (!$id) {
                Log::warning("Missing ID in stance result: " . json_encode($item));
                continue;
            }
            try {
                $stance = AnalystStanceEnum::from($item["stance"])->value;
            } catch (\InvalidArgumentException $e) {
                Log::warning("Invalid stance value for item ID " . $item["id"] . ": " . $item["stance"]);
                continue;
            }
            if ($data_db->has($id)) {
                $data[] = [
                    "id" => $id,
                    "stance" => $stance,
                    "stance_confidence" => $item["confidence"],
                    "post_id" => $data_db->get($id)->post_id,
                    "gov_id" => $data_db->get($id)->gov_id,
                ];
            }
        }
        return $data;
    }
}
