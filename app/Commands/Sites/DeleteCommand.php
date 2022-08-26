<?php

namespace App\Commands\Sites;

use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class DeleteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:delete {server : The id of the server} {site : The id of the site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete a site';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = $this->argument('server');
        $site = $this->argument('site');

        $response = Http::withToken(config('ploi.token'))
            ->acceptJson()
            ->baseUrl(config('ploi.base_url'))
            ->delete(sprintf('/api/servers/%s/sites/%s', $server, $site));

        if ($response->failed()) {
            $this->components->error($response->json('message'));

            return 1;
        }

        $this->components->info(sprintf('Site %s deleted', $site));

        return 0;
    }
}
