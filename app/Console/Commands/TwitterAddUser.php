<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertTwitterUser;

class TwitterAddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:add {names*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add twitter item$item
        {names* : twitter screen name (@xxx)}';

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
        $names = $this->argument('names');
        UpsertTwitterUser::getInstance(true)
            ->addEvent('inserted', function ($item, $index, $length) {
                $pref = '['.($index+1).'/'.$length.']insert: ';
                echo($pref.$item->screen_name.' => ['.$item->id.']'.$item->name.' '.PHP_EOL);
            })
            ->execArray($names);

        return 0;
    }
}
