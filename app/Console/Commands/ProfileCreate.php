<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Tasks\Profiles\AddProfileFromYoutube;
use App\Tasks\Utils\GeneralEvents;

class ProfileCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:create
        { --force : Create when there\'s duplicated profile }
        { ids?* : Youtube ChannelID (UCxxx) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create profile from youtube';

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
        $ids = $this->argument('ids') ?? []; // channel ids
        $force = $this->option('force'); // true で profile が重複してても作成する

        // TODO: ログ適当＆整合性未チェック
        AddProfileFromYoutube::builder()
            ->renameWhenDuplicated($force)
            ->addEvents(GeneralEvents::apiEvents('Add profile from youtube'))
            ->exec($ids);

        return 0;
    }
}
