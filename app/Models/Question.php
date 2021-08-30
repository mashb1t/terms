<?php

namespace App\Models;

use App\Helpers\CacheHelper;
use Database\Factories\QuestionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $quiz_id
 * @property string $question
 * @property string $answer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Answer[] $answers
 * @property-read int|null $answers_count
 * @property-read Quiz $quiz
 * @property-read Collection|Slot[] $slot
 * @property-read int|null $slot_count
 * @method static QuestionFactory factory(...$parameters)
 * @method static Builder|Question newModelQuery()
 * @method static Builder|Question newQuery()
 * @method static Builder|Question query()
 * @method static Builder|Question unansweredOrDueQuestions()
 * @method static Builder|Question whereAnswer($value)
 * @method static Builder|Question whereCreatedAt($value)
 * @method static Builder|Question whereId($value)
 * @method static Builder|Question whereQuestion($value)
 * @method static Builder|Question whereQuizId($value)
 * @method static Builder|Question whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Question extends Model
{
    use HasFactory;

    public function answerQuestion(bool $correct, bool $skipped = false): void
    {
        $slotId = $this->slot->first()->id ?? 1;

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

        $this->slot()->sync([$newSlotId]);

        // update timestamp on pivot table to delay question answer (doesn't skip for today)
        QuestionSlot::whereQuestionId($this->id)
            ->whereSlotId($newSlotId)
            ->update(['updated_at' => Carbon::now()]);
    }

    public function skipQuestion(): void
    {
        $this->answerQuestion(false, true);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function slot(): BelongsToMany
    {
        return $this->belongsToMany(Slot::class)->using(QuestionSlot::class)->withTimestamps();
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function scopeUnansweredOrDueQuestions(Builder $builder)
    {
        $currentDate = Carbon::now();
        $slots = CacheHelper::getCachedSlotsCollection();

        $builder->leftJoin('question_slot', 'question_slot.question_id', 'questions.id');

        // check if older than or equal to slot days
        foreach ($slots as $slot) {
            $builder->orWhere(function (Builder $builder) use ($slot, $currentDate) {
                $date = Carbon::parse($currentDate)->subDays($slot->repeat_after_days);
                $builder->where('question_slot.slot_id', '=', $slot->id);
                $builder->whereDate('question_slot.updated_at', '<=', $date);
            });
        }

        // also include non-answered questions
        $builder->orDoesntHave('slot');

        return $builder;
    }
}
