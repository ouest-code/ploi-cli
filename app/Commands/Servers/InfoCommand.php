<?php

namespace App\Commands\Servers;

use Exception;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class InfoCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'servers:info {server : The id of the server}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show a server in your account';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $server = $this->argument('server');

            $response = Http::withToken(config('ploi.token'))
                ->baseUrl(config('ploi.base_url'))
                ->get(sprintf('/api/servers/%s', $server));

            $properties = $response->json('data');

            $statusColor = $properties['status_id'] === 2 ? 'green' : 'yellow';

            $this->newLine();

            $this->line(sprintf('  <options=bold;bg=cyan> %s </> %s', $server, $properties['name']));

            $this->line(sprintf('  <options=bold>IP:</>            %s', $properties['ip_address']));
            $this->line(sprintf('  <options=bold>Status:</>        <fg=%s>%s</>', $statusColor, $properties['status']));
            $this->line(sprintf('  <options=bold>Type:</>          %s', $properties['type']));
            $this->line(sprintf('  <options=bold>PHP version:</>   %s', $properties['php_version']));
            $this->line(sprintf('  <options=bold>Mysql version:</> %s', $properties['mysql_version']));
            $this->line(sprintf('  <options=bold>Created at:</>    %s', $properties['created_at']));
            $this->line(sprintf('  <options=bold>Monitoring:</>    %s', $properties['monitoring'] ? 'Yes' : 'No'));
            $this->line(sprintf('  <options=bold>Sites count:</>   %s', $properties['sites_count']));

            $this->newLine();

            return 0;
        } catch (Exception $e) {
            $this->components->error('Unexpected error occurred !');

            return 1;
        }
    }
}
