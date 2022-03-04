<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Google_client;

/**
 * PushNotification command.
 */
class PushNotificationCommand extends Command
{
    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $authJson = file_get_contents('{取得した認証用JSONのファイルパス}');
        $authData = json_decode($authJson, true);

        $googleClient = new Google_client;
        $googleClient->useApplicationDefaultCredentials();
        $googleClient->setAuthConfig($authData);
        $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $httpClient = $googleClient->authorize();

        $data = [
            'message' => 
                [
                    'token' => '{Push通知するデバイスで取得したID}',
                    'notification' => 
                        [
                            'body' => '通知がありまする。',
                            'title' => 'タイトルでござる'
                        ]
                ]
            ];
        $fcmApi = 'https://fcm.googleapis.com/v1/projects/{送信者ID}/messages:send';
        $result = $httpClient->post($fcmApi, ['json' => $data]);
        $this->log(print_r($result, true), LOG_DEBUG);
    }
}
