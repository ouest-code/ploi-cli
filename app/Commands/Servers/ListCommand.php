<?php

namespace App\Commands\Servers;

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
    protected $signature = 'servers:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all the servers in your account';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $response = Http::withToken(config('ploi.token'))
                ->baseUrl(config('ploi.base_url'))
                ->get('/api/servers');

            $servers = collect($response->json('data'));

            $this->newLine();

            $this->components->twoColumnDetail('<fg=gray>[id] Server name</>', '<fg=gray>IP / Status</>');

            $servers->each(function (array $server) {
                $statusColor = $server['status_id'] === 2 ? 'green' : 'yellow';

                $this->components->twoColumnDetail(
                    sprintf('[%s] %s', $server['id'], $server['name']),
                    sprintf('%s / <fg=%s;options=bold>%s</>', $server['ip_address'], $statusColor, $server['status'])
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
