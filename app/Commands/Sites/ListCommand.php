<?php

namespace App\Commands\Sites;

use Exception;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:list {id : The id of the server}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all the sites for a specific server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $id = $this->argument('id');

            $response = Http::withToken(config('ploi.token'))
                ->baseUrl(config('ploi.base_url'))
                ->get(sprintf('/api/servers/%s/sites', $id));

            $sites = collect($response->json('data'));

            $this->newLine();

            $this->components->twoColumnDetail('<fg=gray>[id] Domain</>', '<fg=gray>Status</>');

            $sites->each(function (array $site) {
                $statusColor = $site['status'] === 'active' ? 'green' : 'yellow';

                $this->components->twoColumnDetail(
                    sprintf('[%s] %s', $site['id'], $site['domain']),
                    sprintf('<fg=%s;options=bold>%s</>', $statusColor, $site['status'])
                );
            });

            $this->newLine();

            return 0;
        } catch (Exception $e) {
            $this->components->error('Unexpected error occurred !');

            return 1;
        }
    }
}
