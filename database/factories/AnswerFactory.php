<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $slot = $slotIdOld = $slotIdNew = Slot::all()->random()->id;
        $skipped = $this->faker->boolean;

        if ($skipped) {
            $correct = false;
        } else {
            $correct = $this->faker->boolean;

            if ($correct) {
                $slotIdNew = min($slot + 1, Slot::MAX_SLOT_ID);
            } else {
                $slotIdNew = 1;
            }
        }

        return [
            'question_id' => Question::all()->random()->id,
            'slot_id_old' => $slotIdOld,
            'slot_id_new' => $slotIdNew,
            'correct' => $correct,
            'skipped' => $skipped,
        ];
    }
}
