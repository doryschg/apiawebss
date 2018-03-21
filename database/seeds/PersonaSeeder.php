


<?php

use Illuminate\Database\Seeder;
// Hace uso del modelo de Fabricante.
use awebss\Persona;
// Le indicamos que utilice también Faker.
// Información sobre Faker: https://github.com/fzaninotto/Faker
use Faker\Factory as Faker;

class PersonaSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Creamos una instancia de Faker
		$faker = Faker::create();

		// Creamos un bucle para cubrir 5 fabricantes:
		for ($i=0; $i<10; $i++)
		{
			// Cuando llamamos al método create del Modelo Fabricante 
			// se está creando una nueva fila en la tabla.
			Persona::create(
				[
					//'ci'=>$faker->randomNumber(8),
				    'ci'=>$faker->word(),
					'ci_expedido'=>$faker->word(),
					'firma_digital'=>$faker->word(),
					'huella_dactilar'=>$faker->word(),
					'nombres'=>$faker->word(),
					'apellido_primero'=>$faker->word(),
					'apellido_segundo'=>$faker->word(),
					'fecha_nacimiento'=>$faker->word(),
					'estado_civil'=>$faker->word(),
					'genero'=>$faker->word(),
					'fotografia_personal'=>$faker->word(),
					'email'=>$faker->word(),
                    'grupo_sanguineo'=>$faker->word(),
					'tipo_permanencia'=>$faker->word(),
					'estado_act'=>$faker->word(),
					'nro_celular'=>$faker->word(),
					'nro_telefono'=>$faker->word()
					
					// de 9 dígitos como máximo.

				]
			);
		}

	}

}