<?php

namespace App\Commands\Databases;

use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'databases:list {server : The id of the server}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all the databases available in the server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = $this->argument('server');

        $response = Http::withToken(config('ploi.token'))
            ->acceptJson()
            ->baseUrl(config('ploi.base_url'))
            ->get(sprintf('/api/servers/%s/databases', $server));

        if ($response->failed()) {
            $this->components->error($response->json('message'));

            return 1;
        }

        $databases = $response->collect('data');

        $this->newLine();

        $this->components->twoColumnDetail('<fg=gray>[id] Name</>', '<fg=gray>Status</>');

        $databases->each(function (array $database) {
            $statusColor = $database['status'] === 'active' ? 'green' : 'yellow';

            $this->components->twoColumnDetail(
                sprintf('[%s] %s', $database['id'], $database['name']),
                sprintf('<fg=%s;options=bold>%s</>', $statusColor, $database['status'])
            );
        });

        $this->newLine();

        return 0;
    }
}
