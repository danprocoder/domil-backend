<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('job_categories')->insert([
            [
                'parent_id' => null,
                'name' => 'Clothing',
                'description' => 'Tailored clothes, custom t-shirts, etc',
                'created_at' => \Carbon\Carbon::now(),
            ],
            [
                'parent_id' => null,
                'name' => 'Furniture',
                'description' => 'Chairs, tables, bed frames, etc',
                'created_at' => \Carbon\Carbon::now(),
            ],
            [
                'parent_id' => 1,
                'name' => 'Native Wears',
                'description' => '',
                'created_at' => \Carbon\Carbon::now(),
            ],
            [
                'parent_id' => 1,
                'name' => 'Custom Shirts',
                'description' => '',
                'created_at' => \Carbon\Carbon::now(),
            ],
            [
                'parent_id' => 2,
                'name' => 'Chair / Table',
                'description' => '',
                'created_at' => \Carbon\Carbon::now(),
            ],
            [
                'parent_id' => 2,
                'name' => 'Bed Frames',
                'description' => '',
                'created_at' => \Carbon\Carbon::now(),
            ]
        ]);
    }
}
