<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
    	foreach (range(1,500) as $index) {
	        DB::table('users')->insert([
	            'name' => $faker->name,
	            'email' => $faker->email,
	            'password' => bcrypt('secret'),
	            'year_of_joining_school' => $faker->year,
	            'dob' => $faker->year.'-09-05',
	            'blood_group' => 'O+',
	            'department_name' => 'Teacher',
	            'employee_gender' => 'Male'
	        ]);
        }
    }
}
