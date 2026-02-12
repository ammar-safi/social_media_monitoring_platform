<?php

namespace App\Services;

use App\Enums\ApproveUserStatusEnum;
use App\Enums\InviteStatusEnum;
use App\Enums\PolicyRequestEnum;
use App\Models\ApproveUser;
use App\Models\Invite;
use App\Models\PolicyRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ExpirationService
{
    public function CheckExpirationForGovRequest()
    {
        try {
            $requests = ApproveUser::query()
                ->where("expired_at", "<", Carbon::now())
                ->where("status", ApproveUserStatusEnum::PENDING->value)
                ->get();
            foreach ($requests as $request) {
                $request->update([
                    "expired" => 1,
                ]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
    public function CheckExpirationForPolicyInvites()
    {
        try {
            $requests = Invite::query()
                ->where("expired_at", "<", Carbon::now())
                ->where("status", InviteStatusEnum::PENDING->value)
                ->get();
            foreach ($requests as $request) {
                $request->update([
                    "status" => InviteStatusEnum::EXPIRED->value,
                ]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
    public function CheckExpirationForPolicyRequest()
    {
        try {
            $requests = PolicyRequest::query()
                ->where("expired_at", "<", Carbon::now())
                ->where("status", PolicyRequestEnum::PENDING->value)
                ->get();
            foreach ($requests as $request) {
                $request->update([
                    "status" => PolicyRequestEnum::EXPIRED->value,
                ]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
