<?php
/*
This file is part of Mayfly.

Mayfly is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Foobar is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Foobar.  If not, see <http://www.gnu.org/licenses/>.

*/
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateKeys extends Command {
        var $config = array(
                    "digest_alg" => "sha512",
                    "private_key_bits" => 4096,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );


	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'rsabackend:generate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pre-Generates keys.';

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

		# Preflight Check
		if (file_exists("/tmp/generate.lock"))
		{
			$stat = stat("/tmp/generate.lock");
			if ($stat['mtime'] > time() - 360)
			{
				exit;
			} else {
				unlink("/tmp/generate.lock");
			}
		}
		touch("/tmp/generate.lock");
		#Pregen::Where('valid_untill','<',date('Y-m-d H:i:s'))->delete();

		$count = Pregen::All()->count();
		if ($count < 500)
		{
			$x = 1;
			if ($count < 480)
				$x = 2;
			if ($count < 400)
				$x = 10;
			if ($count < 200)
				$x = 50;

			for($i = 0; $i < $x; $i++)
			{
				$randPass = $this->getRandom();
				$res = openssl_pkey_new($this->config);
				openssl_pkey_export($res, $privKey,$randPass);
				$pubKey = openssl_pkey_get_details($res);
				$pubKey = $pubKey["key"];

				$m = new Pregen();
				$m->public = $pubKey;
				$m->private = $privKey;
				$m->password = $randPass;
				$m->valid_untill = date('Y-m-d H:i:s',time() + 18000);
				$m->save();
				if ($count  > 20) {
					for($n =0; $n < 1; $n++)
					{
						#echo "$n/5\r";
						sleep(1);
					}
				}
				$count++;
				#echo "  -  $i / $x -  \n";
			}
		}
		unlink("/tmp/generate.lock");
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


	public function getRandom($length = 15)
        {
                return substr(md5(rand()), 0, $length);
        }

}
