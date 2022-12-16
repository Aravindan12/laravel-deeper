<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use function Termwind\{render};
use function Termwind\{ask};
use Illuminate\Support\Str;
use File;

class AppStats extends Command
{
    protected $signature = 'app:stats';

    protected $description = 'Display the application stats';

    public function handle()
    {
        $result = [];
        foreach ($this->getData() as $key => $data){
            $result[$key] = ask(<<<HTML
                <span class="mt-1 ml-2 mr-1 bg-green px-1 text-black">
                    $data
                </span>
            HTML);
        }
        Log::info($result);

        $this->blade($result);
        return 'blade created successfully';
    }

    public function getData()
    {
        return [
            'name' => 'What is your name?',
            'age' => 'What is your age?',
            'profession' => 'What is your profession?',
            'dob' => 'What is your dob?',
        ];
    }

    protected function blade($result)
    {
        $bladeName = strtolower($result['name']);
        $route = Str::plural(strtolower($result['name']));
        $modelTemplate = str_replace(
            ['{{modelNamePluralLowerCase}}'],
            [$bladeName],
            $this->getStub('Blade')
        );
        file_put_contents(resource_path("views/{$bladeName}.blade.php"), $modelTemplate);
        File::append(
            base_path('routes/web.php'),
                'Route::get("' .$route. '", function(){ return view("'.$bladeName.'");});'
        );
    }

    protected function getStub($type): bool|string
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }
}
