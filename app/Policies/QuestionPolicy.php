<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuestionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User     $user
     * @param Question $question
     *
     * @return bool
     */
    public function view(User $user, Question $question): bool
    {
        return $user->id === $question->quiz->owner;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User     $user
     * @param Question $question
     *
     * @return bool
     */
    public function update(User $user, Question $question): bool
    {
        return $user->id === $question->quiz->owner;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User     $user
     * @param Question $question
     *
     * @return bool
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->id === $question->quiz->owner;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User     $user
     * @param Question $question
     *
     * @return bool
     */
    public function restore(User $user, Question $question): bool
    {
        return $user->id === $question->quiz->owner;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User     $user
     * @param Question $question
     *
     * @return bool
     */
    public function forceDelete(User $user, Question $question): bool
    {
        return $user->id === $question->quiz->owner;
    }

    /**
     * Determine whether the user can add a question to the model.
     *
     * @param User          $user
     * @param Question|null $question
     *
     * @throws ModelNotFoundException
     *
     * @return bool
     */
    public function changeQuizId(User $user, ?Question $question): bool
    {
        // explicit model load to prevent outdated relation
        $quiz = Quiz::findOrFail($question->quiz_id);

        return $user->id === $quiz->owner;
    }
}
