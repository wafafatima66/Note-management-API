<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        function createApplication($app_name, $description, $url)
        {
            $application = new Application();
            $application->app_name = $app_name;
            $application->description = $description;
            $application->url = $url;
            $application->save();

        }

        createApplication('Docua', 'This is a docua application', '');
        createApplication('Postman', 'This is a postman application', '');
    }
}
