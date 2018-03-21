<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use awebss\Paciente;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();

		// Para cubrir los aviones tenemos que tener en cuanta qué fabricantes tenemos.
		// Para que la clave foránea no nos de problemas.
		// Averiguamos cuantos fabricantes hay en la tabla.
		//$cuantos= Persona::all()->count();

		// Creamos un bucle para cubrir 20 aviones:
		for ($i=0; $i<9; $i++)
		{
			// Cuando llamamos al método create del Modelo Avion 
			// se está creando una nueva fila en la tabla.
			Paciente::create(
				[
				 'num_hist_clinico'=>$faker->word(),
				 'id_persona'=>$faker->numberBetween(1,10),
				 'id_seguro'=>$faker->numberBetween(1,10)

				]
			);
		}

    }
}
