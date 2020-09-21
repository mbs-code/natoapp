<?php

namespace App\Console\Commands;

use App\Models\Channel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Profile;
use App\Models\Twitter;
use Illuminate\Support\Facades\DB;

class DevTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devtest {--y|yes} {--reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test command
        {--y|yes : seeding}
        {--reset : database rollback and seeding (require --yes option)}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('yes')) {
            if ($this->option('reset')) {
                $this->info('> Reset database.'.PHP_EOL);
                Artisan::call('migrate:refresh');
            }

            $this->info('> seeding.'.PHP_EOL);
            Artisan::call('twitter:add YaezawaNatori minatoaqua');
            Artisan::call('youtube:add UC1519-d1jzGiL1MPTxEdtSA UC1opHUrw8rvnsadT-iGp7Cg');

            DB::transaction(function () {
                $twn = Twitter::where(['screen_name' => 'YaezawaNatori'])->first();
                $ytn = Channel::where(['code' => 'UC1519-d1jzGiL1MPTxEdtSA'])->first();

                $profn = Profile::firstOrNew(['name' => '八重沢なとり']);
                $profn->name = '八重沢なとり';
                $profn->description = 'アイドル部';
                $profn->save();

                $profn->twitters()->syncWithoutDetaching($twn); // 存在してないなら追加
                $profn->channels()->syncWithoutDetaching($ytn);

                ///

                $twa = Twitter::where(['screen_name' => 'minatoaqua'])->first();
                $yta = Channel::where(['code' => 'UC1opHUrw8rvnsadT-iGp7Cg'])->first();

                $profa = Profile::firstOrNew(['name' => '湊あくあ']);
                $profa->name = '湊あくあ';
                $profa->description = 'アイドル部';
                $profa->save();

                $profa->twitters()->syncWithoutDetaching($twa); // 存在してないなら追加
                $profa->channels()->syncWithoutDetaching($yta);
            });
        }

        return 0;
    }
}
