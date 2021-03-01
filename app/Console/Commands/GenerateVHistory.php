<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class GenerateVHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:v_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        DB::unprepared("truncate v_history");
        DB::unprepared("insert into v_history select * from view_history");
    }
}
