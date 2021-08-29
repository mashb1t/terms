<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Slot
 *
 * @property int $id
 * @property int $repeat_after_days
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\Answer[] $answers
 * @property-read int|null $answers_count
 * @property-read Collection|\App\Models\Question[] $questions
 * @property-read int|null $questions_count
 * @method static Builder|Slot newModelQuery()
 * @method static Builder|Slot newQuery()
 * @method static Builder|Slot query()
 * @method static Builder|Slot whereCreatedAt($value)
 * @method static Builder|Slot whereId($value)
 * @method static Builder|Slot whereRepeatAfterDays($value)
 * @method static Builder|Slot whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Slot extends Model
{
    const MAX_SLOT_ID = 5;

    protected $fillable = [
        'repeat_after_days',
    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class)->using(QuestionSlot::class)->withTimestamps();
    }
}
