<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Services\Integrations\Fitbit;
use App\Models\Integration\IntegrationCredential;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class ParseFitbitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:fitbit {--debug= : Debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get members fitbit data';

    /**
     * The console command debug message key.
     *
     * @var string
     */
    protected $infoMessageKey = '[Parse: Fitbti]';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->showMsg('Started');

        $fitbit = new Fitbit();
        $tokens = IntegrationCredential::where('provider', 'fitbit')->get();

        foreach ($tokens as $token) {
            if ($token->is_expired) {
                $this->showMsg('Token expired for member #'.$token->user_id.'. Refreshing...');
                $response = $fitbit->refresh($token->id, $token->refresh_token);

                $token = null;
                if ($response['success']) {
                    $token = $response['token'];
                } else {
                    $this->showMsg('Token refresh failed with message: '. $response['message']);
                }
            }

            if ($token) {
                $fitbit->parseData($token->access_token, $token->user_id);
                $this->showMsg('Data successfully parsed for member #'.$token->user_id);
            }
        }

        $this->showMsg('Finished.');
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
