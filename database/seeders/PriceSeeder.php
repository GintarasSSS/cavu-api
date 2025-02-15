<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prices')->insert([
            [
                'start_at' => Carbon::parse('2025-06-01')->format('Y-m-d'),
                'end_at' => Carbon::parse('2025-09-30')->format('Y-m-d'),
                'is_weekend' => null,
                'is_default' => null,
                'price' => 200,
                'description' => 'Summer time markup',
                'created_at' => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'start_at' => Carbon::parse('2025-11-01')->format('Y-m-d'),
                'end_at' => Carbon::parse('2026-01-31')->format('Y-m-d'),
                'is_weekend' => null,
                'is_default' => null,
                'price' => 50,
                'description' => 'Winter time markup',
                'created_at' => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'start_at' => null,
                'end_at' => null,
                'is_weekend' => 1,
                'is_default' => null,
                'price' => 300,
                'description' => 'Weekend time markup',
                'created_at' => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'start_at' => null,
                'end_at' => null,
                'is_weekend' => null,
                'is_default' => 1,
                'price' => 100,
                'description' => 'Default price',
                'created_at' => Carbon::now(),
                'updated_at'  => Carbon::now()
            ]
        ]);
    }
}
