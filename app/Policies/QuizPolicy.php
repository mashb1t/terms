<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizPolicy
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
     * @param User $user
     * @param Quiz $quiz
     *
     * @return bool
     */
    public function view(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->owner;
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
     * @param User $user
     * @param Quiz $quiz
     *
     * @return bool
     */
    public function update(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->owner;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Quiz $quiz
     *
     * @return bool
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->owner;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Quiz $quiz
     *
     * @return bool
     */
    public function restore(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->owner;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Quiz $quiz
     *
     * @return bool
     */
    public function forceDelete(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->owner;
    }
}
