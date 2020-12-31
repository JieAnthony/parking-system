<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SaveAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anthony:admin-save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快速保存后台数据数据到填充类';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tables = Arr::except(config('admin.database'), ['connection', 'users_model', 'roles_model', 'permissions_model', 'menu_model']);
        $this->call('iseed', [
            'tables' => implode(',', $tables),
            '--force' => true,
        ]);
        $this->info('done!');
    }
}
