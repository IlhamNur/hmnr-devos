<?php

namespace Database\Seeders;

use App\Models\KnowledgeItem;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Snippet;
use App\Models\Solution;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User A
        $userA = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);

        // User B
        $userB = User::factory()->create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        $projectNames = [
            'Astra Chatbot',
            'Sabrina Integration',
            'HMNR Developer Toolbox',
            'PetPro',
            'Green House App'
        ];

        $projects = collect();
        foreach ($projectNames as $name) {
            $projects->push(Project::factory()->create([
                'user_id' => $userA->id,
                'name' => $name,
                'slug' => Str::slug($name),
            ]));
        }

        $problemTitles = [
            'MVEL LinkedTreeMap casting',
            'Azure HTTP 408 timeout',
            'PostgreSQL connection refused',
            'Laravel queue timeout',
            'CORS issue',
            'JSON escaping problem'
        ];

        $problems = collect();
        foreach ($problemTitles as $index => $title) {
            $problems->push(Problem::factory()->create([
                'user_id' => $userA->id,
                'project_id' => $projects->random()->id,
                'title' => $title,
                'slug' => Str::slug($title),
            ]));
        }

        $snippetTitles = [
            'Laravel API Response',
            'MVEL Nested Map Access',
            'Java Random Number',
            'PostgreSQL JSON Query',
            'Laravel Role Check'
        ];

        foreach ($snippetTitles as $title) {
            Snippet::factory()->create([
                'user_id' => $userA->id,
                'title' => $title,
            ]);
        }
        
        // Add some solutions for user A
        foreach ($problems as $problem) {
            Solution::factory(rand(1, 2))->create([
                'user_id' => $userA->id,
                'problem_id' => $problem->id,
            ]);
        }

        // Add some knowledge items
        KnowledgeItem::factory(5)->create([
            'user_id' => $userA->id,
        ]);

        // Tags
        $tags = Tag::factory(5)->create([
            'user_id' => $userA->id,
        ]);

        // Attach tags to some entities for User A
        $projects->first()->tags()->attach($tags->random());
        $problems->first()->tags()->attach($tags->random());

        // Create some random data for User B to ensure separation is testable visually
        Project::factory(2)->create(['user_id' => $userB->id]);
        Problem::factory(3)->create(['user_id' => $userB->id]);
        Snippet::factory(2)->create(['user_id' => $userB->id]);
    }
}
