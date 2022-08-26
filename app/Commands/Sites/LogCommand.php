<?php

namespace App\Commands\Sites;

use Exception;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class LogCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:log {server : The id of the server} {site : The id of the site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get latest logs from a site';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $server = $this->argument('server');
            $site = $this->argument('site');

            $response = Http::withToken(config('ploi.token'))
                ->baseUrl(config('ploi.base_url'))
                ->get(sprintf('/api/servers/%s/sites/%s/log', $server, $site));

            $logs = collect($response->json('data'));

            $logs->each(function (array $log) {
                $type = $log['type'] ? sprintf(' <options=bold;bg=white>%s</>', $log['type']) : '';

                $this->newLine();

                $this->line(sprintf('  <options=bold;bg=cyan> %s </>%s %s', $log['created_at'], $type, $log['description']));

                $content = array_filter(explode("\n", $log['content']));

                foreach ($content as $row) {
                    $this->line('  ' . $row);
                }
            });

            $this->newLine();

            return 0;
        } catch (Exception $e) {
            $this->components->error('Unexpected error occurred !');

            return 1;
        }
    }
}
