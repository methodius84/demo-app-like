<?php

namespace App\Consumers;

use App\Nova\User;
use App\Services\LMS\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpAmqpLib\Message\AMQPMessage;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;
use function PHPUnit\Framework\matches;

class LmsConsumer extends RabbitMQJob implements ShouldQueue
{
    public function fire(): void
    {
        // Handle the raw message here
//        try {
            $payload = json_decode($this->getRawBody(), true);

            if(isset($payload['data']['errorStatus']) && $payload['data']['errorStatus'] === true){
                \Log::channel('telegram')->debug('im here');
//                throw new \Exception('Error during LMS integration '.$payload['type']);
            }

            switch($payload['type']){
                // TODO DTO
                case 'user_created':
                    (new UserService())->create($payload['data']);
                    break;
                case 'user_updated':
                    (new UserService())->update($payload['data']);
                    break;
                case 'user_info':
                    (new UserService())->info($payload['data']);
                    break;
            }
//        }
//        catch (\Exception $exception){
//            \Log::channel('telegram')->error($exception->getMessage(), json_decode($this->getRawBody(), true));
//        }
        $this->delete();
    }

    public function getName(): string
    {
        return '';
    }
}
