<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class InitWeChatUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wechat-user:init';

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

    public function handle()
    {
        /** @var \EasyWeChat\OfficialAccount\Application $app */
        $app = app('wechat.official_account');
        $userOpenIds = $app->user->list();
        $bar = $this->output->createProgressBar($userOpenIds['count']);
        $bar->start();
        foreach ($userOpenIds['data']['openid'] as $userOpenId) {
            $user = $app->user->get($userOpenId);
            User::updateOrCreate([
                'nickname' => $user['nickname'],
                'avatar' => $user['headimgurl'],
            ], [
                'open_id' => $user['openid']
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->info('done!' . PHP_EOL);
    }
}
