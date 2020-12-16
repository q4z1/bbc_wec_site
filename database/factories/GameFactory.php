<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->numberBetween(1000,10000),
            'type' => $this->faker->numberBetween(1,6),
            'pos1' => $this->faker->numberBetween(1,100),
            'pos2' => $this->faker->numberBetween(1,100),
            'pos3' => $this->faker->numberBetween(1,100),
            'pos4' => $this->faker->numberBetween(1,100),
            'pos5' => $this->faker->numberBetween(1,100),
            'pos6' => $this->faker->numberBetween(1,100),
            'pos7' => $this->faker->numberBetween(1,100),
            'pos8' => $this->faker->numberBetween(1,100),
            'pos9' => $this->faker->numberBetween(1,100),
            'pos10' => $this->faker->numberBetween(1,100),
            'started' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'unique_game_id' => 1,
            'pdb' => '54f310b95ffc47f5a71c6286d78abb291149b9c3.pdb'
        ];
    }
}
