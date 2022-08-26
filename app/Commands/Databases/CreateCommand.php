<?php

namespace App\Commands\Databases;

use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class CreateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'databases:create
                            {server : The id of the server}
                            {name : Database domain}
                            {--user= : Create a new user attach to the database}
                            {--password= : Password to connect to your database}
                            {--description= : Ability to give your database a description for clarity}
                            {--site-id= : Ability to attach a site to your database}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a new database in your server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = $this->argument('server');

        $params = [
            'name' => $this->argument('name'),
            'user' => $this->option('user'),
            'password' => $this->option('password'),
            'description' => $this->option('description'),
            'site_id' => $this->option('site-id'),
        ];

        $params = array_filter($params);

        $response = Http::withToken(config('ploi.token'))
            ->acceptJson()
            ->baseUrl(config('ploi.base_url'))
            ->post(sprintf('/api/servers/%s/databases', $server), $params);

        if ($response->failed()) {
            $this->components->error($response->json('message'));

            return 1;
        }

        $properties = $response->json('data');

        $statusColor = $properties['status'] === 'active' ? 'green' : 'yellow';

        $this->newLine();

        $this->line(sprintf('  <options=bold;bg=cyan> %s </> %s', $properties['id'], $properties['name']));

        $this->line(sprintf('  <options=bold>Status:</>           <fg=%s>%s</>', $statusColor, $properties['status']));
        $this->line(sprintf('  <options=bold>Type:</>             %s', $properties['type']));
        $this->line(sprintf('  <options=bold>Created at:</>       %s', $properties['created_at']));

        $this->newLine();

        return 0;
    }
}
