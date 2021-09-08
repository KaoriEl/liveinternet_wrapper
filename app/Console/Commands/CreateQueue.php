<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class CreateQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'создает очередь в rabbit_mq';

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
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection(env("RABBITMQ_HOST"), env("RABBITMQ_PORT"), env("RABBITMQ_LOGIN"), env("RABBITMQ_PASSWORD"));
        $channel = $connection->channel();

        $channel->queue_declare('Queue_wrapper', false, false, false, false);
        $channel->close();
        $connection->close();
        return "ok";
    }
}
