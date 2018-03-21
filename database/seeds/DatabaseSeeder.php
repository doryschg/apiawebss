<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

	//$this->call('PersonaSeeder');
        //$this->call('SeguroSeeder');

//$this->call('DomicilioSeeder');
	//$this->call('PacienteSeeder');
	

$this->call('UserSeeder');
    }

}
