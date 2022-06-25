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
        function createApplication($app_key, $app_name, $description, $url)
        
        {
            $application = new Application();
            $application->app_key = $app_key;
            $application->app_name = $app_name;
            $application->description = $description;
            $application->url = $url;
            $application->save();

        }

        createApplication('ROOM','Room', 'Room application', '');
        createApplication('DOCUA','Docua', 'docua application', '');
        createApplication('EMPLOYEE','Employee', 'Employee application', '');
        createApplication('RECRUITMENT','Recruitment', 'Recruitment application', '');
        createApplication('HANDBOOK','Handbook (docua)', 'Handbook (docua) application', '');
        createApplication('CRM','CRM', 'CRM application', '');
        createApplication('SFA','SFA', 'SFA application', '');
        createApplication('DAILY_REPORT','Daily Report', 'Daily Report application', '');
        createApplication('CHECKLIST','Checklist', 'Checklist application', '');
        createApplication('REPORTS','Reports', 'Reports application', '');
        createApplication('APPROVAL','Approval', 'Approval application', '');
        createApplication('COMPANY_SNS','Company sns', 'Company sns application', '');
    }
}
