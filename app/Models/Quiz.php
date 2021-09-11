<?php

namespace App\Models;

use Database\Factories\QuizFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Quiz
 *
 * @property int $id
 * @property User $owner
 * @property string $title
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Question[] $questions
 * @property-read int|null $questions_count
 * @method static QuizFactory factory(...$parameters)
 * @method static Builder|Quiz newModelQuery()
 * @method static Builder|Quiz newQuery()
 * @method static Builder|Quiz query()
 * @method static Builder|Quiz whereCreatedAt($value)
 * @method static Builder|Quiz whereDescription($value)
 * @method static Builder|Quiz whereId($value)
 * @method static Builder|Quiz whereOwner($value)
 * @method static Builder|Quiz whereTitle($value)
 * @method static Builder|Quiz whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Quiz extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function(Quiz $quiz) {
            $quiz->questions()->get()->each->delete();
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
