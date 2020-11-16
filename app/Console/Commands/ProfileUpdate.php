<?php

namespace App\Console\Commands;

use App\Models\Profile;
use Illuminate\Console\Command;

class ProfileUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all profiles';

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
        // 全てのキャッシュを更新する
        $profiles = Profile::all();
        foreach ($profiles as $profile) {
            // cache update
            $profile->cacheSync()->save();
        }

        $this->line('success!');
        return 0;
    }
}
