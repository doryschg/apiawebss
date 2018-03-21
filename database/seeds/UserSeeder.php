<?php

use Illuminate\Database\Seeder;



class UserSeeder extends Seeder
{
    
    public function run()
    {
        
 $users=[
        [//'name'=>'dorys chambi',

        'rol_id'=>1,
        'per_id'=>2,
        'usu_nick'=>'dorys',
          
          'usu_clave_publica'=>'no definido',
         // 'usu_inicio_vigencia'=>'',
          //'usu_fin_vigencia'=>'',
          'delete_at'=>'A',
          'userid_at'=>1,
          'password'=>Hash::make('12345678')

          ]
        ];
        foreach ($users as $user)
        {
        	\awebss\User::create($user);
        }

    }
}
