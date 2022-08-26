<?php

namespace App\Commands\Sites;

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
    protected $signature = 'sites:info {server : The id of the server} {site : The id of the site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get a single site in a server';

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
                ->get(sprintf('/api/servers/%s/sites/%s', $server, $site));

            $properties = $response->json('data');

            $statusColor = $properties['status'] === 'active' ? 'green' : 'yellow';

            $this->newLine();

            $this->line(sprintf('  <options=bold;bg=cyan> %s </> %s', $site, $properties['domain']));

            $this->line(sprintf('  <options=bold>Status:</>           <fg=%s>%s</>', $statusColor, $properties['status']));
            $this->line(sprintf('  <options=bold>Type:</>             %s', $properties['project_type']));
            $this->line(sprintf('  <options=bold>Last deployed at:</> %s', $properties['last_deploy_at']));
            $this->line(sprintf('  <options=bold>PHP version:</>      %s', $properties['php_version']));
            $this->line(sprintf('  <options=bold>System user:</>      %s', $properties['system_user']));
            $this->line(sprintf('  <options=bold>Web directory:</>    %s', $properties['web_directory']));
            $this->line(sprintf('  <options=bold>Project root:</>     %s', $properties['project_root']));
            $this->line(sprintf('  <options=bold>Health url:</>       %s', $properties['health_url']));
            $this->line(sprintf('  <options=bold>Has Repository:</>   %s', $properties['has_repository']  ? 'Yes' : 'No'));
            $this->line(sprintf('  <options=bold>Created at:</>       %s', $properties['created_at']));

            $this->newLine();

            return 0;
        } catch (Exception $e) {
            $this->components->error('Unexpected error occurred !');

            return 1;
        }
    }
}
