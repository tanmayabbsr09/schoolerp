<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AdmissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
	      foreach (range(1,5000) as $index) {
	        DB::table('admissions')->insert([
	            'name' => $faker->name,
	            'gender' => ($faker->title == 'Ms.')? 'Girl' : 'Boy',
	            'dob' => $faker->year.'-09-05',
	            'blood_group' => 'O+',
	            'mother_language' => 'Oriya',
	            'secondary_language' => 'Oriya',
	            'academic_year' => $faker->year,
	            'admission_class' => $faker->paragraph,
	            'father_name' => $faker->name($gender = 'male'),
	            'father_mobile_no' => '8895483821',
	            'mother_name' => $faker->name($gender = 'female'),
	            'blood_group_proof' => $faker->imageUrl($width = 200, $height = 180) ,
	            'aadhar_card_proof' => $faker->imageUrl($width = 200, $height = 180) ,
	            'birth_certificate' => $faker->imageUrl($width = 200, $height = 180) ,
	            'aadhar_card' => $faker->ean13,
	            'created_at' => $faker->dateTime($max = 'now'),
	            'updated_at' => $faker->dateTime($max = 'now')
	        ]);
	      }
    }
}
