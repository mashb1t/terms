<?php

namespace App\Helpers;

use App\Models\Quiz;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * @return Collection|Slot[]
     */
    public static function getCachedSlotsCollection(): Collection
    {
        return Cache::rememberForever('slots', function () {
            return Slot::all();
        });
    }

    public static function getCachedQuizzesCollection(): Collection
    {
        return Cache::remember('quizzes', 1, function () {
            return Quiz::whereOwner(auth()->id())->get();
        });
    }
}
