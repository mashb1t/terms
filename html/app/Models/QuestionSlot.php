<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\Models\QuestionSlot
 *
 * @property int $question_id
 * @property int $slot_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Question $question
 * @property-read Slot $slot
 * @method static Builder|QuestionSlot newModelQuery()
 * @method static Builder|QuestionSlot newQuery()
 * @method static Builder|QuestionSlot query()
 * @method static Builder|QuestionSlot whereCreatedAt($value)
 * @method static Builder|QuestionSlot whereId($value)
 * @method static Builder|QuestionSlot whereQuestionId($value)
 * @method static Builder|QuestionSlot whereSlotId($value)
 * @method static Builder|QuestionSlot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionSlot extends Pivot
{
    protected $fillable = [
        'question_id',
        'slot_id',
        # TODO add owner
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }
}
