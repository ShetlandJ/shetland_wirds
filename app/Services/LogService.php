<?php

namespace App\Services;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Word;
use App\Models\Comment;
use App\Models\UserLog;
use App\Models\Location;
use App\Models\WordToWord;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Models\WordOfTheDay;
use Illuminate\Http\Request;
use App\Models\WordRecording;
use App\Models\WordDefinition;
use App\Models\WordToLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

class LogService
{
    public function create(Request $request, ?int $wordId): ?UserLog
    {
        $veryRecentLog = UserLog::where('word_id', $wordId)
            ->where('session_id', $request->session()->getId())
            ->where('created_at', '>', Carbon::now()->subMinutes(2))
            ->first();

        if ($veryRecentLog) {
            return null;
        }

        try {
            $sessionId = $request->session()->getId();

            $log = new UserLog();
            $log->uuid = (string) Str::uuid();
            $log->route = $request->route()->getName();
            $log->user_id = Auth::id() ?? null;
            $log->session_id = $sessionId;
            $log->word_id = $wordId ?? null;
            $log->ip_address = $request->ip();
            $log->save();

            return $log;
        } catch (Exception $e) {
            // fail gracefully
            logger($e->getMessage());
            return null;
        }
    }
}
