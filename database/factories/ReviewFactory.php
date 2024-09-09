<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'score' => 1,  // スコアを1に設定
            'content' => 'テスト', 
            'restaurant_id' => \App\Models\Restaurant::factory(),  // 関連するレストランを生成
            'user_id' => \App\Models\User::factory(),  // 関連するユーザーを生成
        ];
    }
    
}
