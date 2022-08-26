<?php

namespace App\Commands\Sites;

use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class CreateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:create
                            {server : The id of the server}
                            {domain : Root domain}
                            {--web-directory=/public : Customize web directory}
                            {--project-root= : Specify project root}
                            {--project-type= : Availabe types: laravel, nodejs, statamic, craft-cms, symfony, wordpress, octobercms, cakephp}
                            {--system-user= : Specify system user}
                            {--webserver-template= : The ID of your webserver template saved in your account to create site with this template}
                            {--webhook-url= : A URL to your system to get notified on when the site has been installed}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = $this->argument('server');

        $params = [
            'root_domain' => $this->argument('domain'),
            'web_directory' => $this->option('web-directory'),
            'project_root' => $this->option('project-root'),
            'project_type' => $this->option('project-type'),
            'system_user' => $this->option('system-user'),
            'webserver_template' => $this->option('webserver-template'),
            'webhook_url' => $this->option('webhook-url'),
        ];

        $params = array_filter($params);

        $response = Http::withToken(config('ploi.token'))
            ->acceptJson()
            ->baseUrl(config('ploi.base_url'))
            ->post(sprintf('/api/servers/%s/sites', $server), $params);

        if ($response->failed()) {
            $this->components->error($response->json('message'));

            return 1;
        }

        $properties = $response->json('data');

        $statusColor = $properties['status'] === 'active' ? 'green' : 'yellow';

        $this->newLine();

        $this->line(sprintf('  <options=bold;bg=cyan> %s </> %s', $properties['id'], $properties['domain']));

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
    }
}
