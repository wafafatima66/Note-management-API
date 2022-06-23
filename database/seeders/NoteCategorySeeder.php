<?php

namespace Database\Seeders;

use App\Models\NoteCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = [
            [
                'user_id'=>'1',
                'title' => 'Category 1',
                'parent_id'=>'1'
                
            ],
            [
                'user_id'=>'1',
                'title' => 'Category 2',
                'parent_id'=>'1'
                
            ],
            [
                'user_id'=>'1',
                'title' => 'Category 3',
                'parent_id'=>'1'
                
            ],
            [
                'user_id'=>'1',
                'title' => 'Sub Category 1',
                'parent_id'=>'2',
            ],
           
            
        ];
  
        foreach ($category as $key => $value) {
            NoteCategory::create($value);
        }
    }
}
