<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use DateTime;

class expireStory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'story:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Your story is removed';

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
     * @return mixed
     */
    public function handle()
    {
    
      $date = new DateTime();
      $date->modify('-24 hours');
      $formatted_date = $date->format('Y-m-d H:i:s');
      DB::table('statuses')->where('created_at', '<',$formatted_date)->delete();
      $this->info('Story is Deleted Previous 24 Hours!');
    }
}
