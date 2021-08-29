<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $quiz_id
 * @property string $question
 * @property string $answer
 * @property int $slot_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\Answer[] $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\Quiz $quiz
 * @property-read \App\Models\Slot $slot
 * @method static QuestionFactory factory(...$parameters)
 * @method static Builder|Question newModelQuery()
 * @method static Builder|Question newQuery()
 * @method static Builder|Question query()
 * @method static Builder|Question whereAnswer($value)
 * @method static Builder|Question whereCreatedAt($value)
 * @method static Builder|Question whereId($value)
 * @method static Builder|Question whereQuestion($value)
 * @method static Builder|Question whereQuizId($value)
 * @method static Builder|Question whereSlotId($value)
 * @method static Builder|Question whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Question extends Model
{
    use HasFactory;

    public function answerQuestion(bool $correct, bool $skipped = false): void
    {
        $slotId = Answer::whereQuestionId($this->id)->orderByDesc('id')->first()->slot_id_new ?? $this->slot_id;

        if ($correct) {
            $newSlotId = min($slotId + 1, Slot::MAX_SLOT_ID);
        } else {
            $newSlotId = $skipped ? $slotId : 1;
        }

        Answer::create([
            'question_id' => $this->id,
            'slot_id_old' => $slotId,
            'slot_id_new' => $newSlotId,
            'correct' => $correct,
            'skipped' => $skipped
        ]);

        $this->slot_id = $newSlotId;
        $this->save();
    }

    public function skipQuestion(): void
    {
        $this->answerQuestion(false, true);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
