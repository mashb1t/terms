<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quiz_id' => Quiz::all()->random()->id,
            'question' => $this->faker->text,
            'answer' => $this->faker->text,
            'slot_id' => Slot::all()->random()->id,
            'correct_answered_at' => $this->faker->dateTimeBetween('-30 days')
        ];
    }
}
