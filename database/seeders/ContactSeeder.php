<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create('ja_JP');
        $categoryIds = Category::pluck('id')->all();
        $tagIds = Tag::pluck('id')->all();

        for ($i = 0; $i < 20; $i++) {
            $contact = Contact::create([
                'category_id' => $faker->randomElement($categoryIds),
                'last_name' => $faker->lastName,
                'first_name' => $faker->firstName,
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->safeEmail,
                'tel' => $faker->numerify('090########'),
                'address' => $faker->prefecture.$faker->city.$faker->streetAddress,
                'building' => $faker->optional()->secondaryAddress,
                'detail' => $faker->realText(80),
            ]);

            if (! empty($tagIds)) {
                $attachCount = min(count($tagIds), $faker->numberBetween(1, 3));
                $selectedTags = $faker->randomElements($tagIds, $attachCount);
                $contact->tags()->attach($selectedTags);
            }//
        }
    }
}
