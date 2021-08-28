<?php

namespace App\Models;

use Database\Factories\AnswerFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Answer
 *
 * @property int $id
 * @property int $question_id
 * @property int $skipped
 * @property int $slot_id
 * @property int $correct
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Question $question
 * @property-read \App\Models\Slot $slot
 * @method static AnswerFactory factory(...$parameters)
 * @method static Builder|Answer newModelQuery()
 * @method static Builder|Answer newQuery()
 * @method static Builder|Answer query()
 * @method static Builder|Answer whereCorrect($value)
 * @method static Builder|Answer whereCreatedAt($value)
 * @method static Builder|Answer whereId($value)
 * @method static Builder|Answer whereQuestionId($value)
 * @method static Builder|Answer whereSkipped($value)
 * @method static Builder|Answer whereSlotId($value)
 * @method static Builder|Answer whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'slot_id',
        'correct',
        'skipped',
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
