<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();
        // $number = $this->faker->randomDigit();

        $note = [
            [
                'description' => $this->faker->paragraph(6),
                'category_id'=>'1',
                'user_id'=>'1',
                'title'=>$this->faker->realText(10)
            ],
            [
                'description' => $this->faker->paragraph(6),
                'category_id'=>'1',
                'user_id'=>'1',
                'title'=>$this->faker->realText(10)
            ],
            [
                'description' => $this->faker->paragraph(6) , 
                'category_id'=>'1',
                'user_id'=>'1',
                'title'=>$this->faker->realText(10)
            ],
            [
                'description' => $this->faker->paragraph(6) , 
               'category_id'=>'1',
                'user_id'=>'1',
                'title'=>$this->faker->realText(10)
            ],
            
        ];
  
        foreach ($note as $key => $value) {
            Note::create($value);
        }
    }
}
