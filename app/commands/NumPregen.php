<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class NumPregen extends Command {

        /**
         * The console command name.
         *
         * @var string
         */
        protected $name = 'rsabackend:numpregen';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Command description.';

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
        public function fire()
        {
					$num = Pregen::All()->count();
					echo $num;
        }

        /**
         * Get the console command arguments.
         *
         * @return array
         */
        protected function getArguments()
        {
                return array(
                );
        }

        /**
         * Get the console command options.
         *
         * @return array
         */
        protected function getOptions()
        {
                return array(
                );
        }

}
