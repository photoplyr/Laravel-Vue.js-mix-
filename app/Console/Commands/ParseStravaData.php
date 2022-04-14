<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Services\Integrations\Strava;
use App\Models\Integration\IntegrationCredential;
use Illuminate\Console\Command;

class ParseStravaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:strava {--debug= : Debug}
                                         {--date= : Specify Parsing Date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get members strava data';

    /**
     * The console command debug message key.
     *
     * @var string
     */
    protected $infoMessageKey = '[Parse: Strava Data]';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->showMsg('Started');

        $date   = $this->option('date') ?: null;
        $strava = new Strava();
        $tokens = IntegrationCredential::where('provider', 'strava')->get();

        foreach ($tokens as $token) {
            $this->showMsg('Parsing data for User #'.$token->user_id);
            $result = $strava->parseData($token, $date);

            if ($result['success'] == false) {
                $this->showMsg('Error ocured. Seems like rate limit reached.');
                return;
            }

            if ($tokens->count() > 1) {
                // Rate limit is 1000 requests per day.
                // In case if we reach this limit we can create system that going to parse users in queue
                // Since we are parsing data for past week for each user, we can parse each user with 1 week interval (if it's suitable)
                // In this case we'll need to implement queue logic to parse only users that werent parsed earlier
                $this->showMsg('Since rate limit is 1000 per day we can make request each 87 seconds. Sleep for 87 seconds...');
                sleep(87);
            }
        }

        $this->showMsg('Done');
    }

    /**
     * Show logs
     * @param [string] $msg  [Content of message]
     * @param [string] $type [Type of message]
     * @return null
    */
    private function showMsg($msg, $type = null)
    {
        if ($this->option('debug')) {
            switch ($type) {
                case 'warning':
                    $this->warn(Carbon::now()->format('Y-m-d H:i:s').' - '. $this->infoMessageKey .' '.$msg);
                break;

                case 'error':
                    $this->error(Carbon::now()->format('Y-m-d H:i:s').' - '. $this->infoMessageKey .' '.$msg);
                break;

                default:
                    $this->info(Carbon::now()->format('Y-m-d H:i:s').' - '. $this->infoMessageKey .' '.$msg);
            }
        }
    }
}
