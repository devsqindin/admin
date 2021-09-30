<?php

use Illuminate\Database\Seeder;
use App\Usuario;
use App\Operador;
use App\OperadorPermissao;
use App\Taxa;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // Usuario::create([
        // 	'nome_completo'=>'Suporte F5',
        // 	'email'=>'suporte@f5webnet.com.br',
        // 	'password'=>'f52020',
        // ]);

        /*Operador::create([
            'nome'=>'Suporte F5',
            'email'=>'suporte@f5webnet.com.br',
            'password'=>'f52020',
        ]);

        Taxa::create([
            'slug'=>'taxa_core',
            'type'=>'perc',
            'valor'=>$this->percent(1.2),
            'nparcela'=>null,
        ]);

        Taxa::create([
            'slug'=>'taxa_intermedia',
            'type'=>'perc',
            'valor'=>$this->percent(1.99),
            'nparcela'=>null,
        ]);

        Taxa::create([
            'slug'=>'taxa_cardholder',
            'type'=>'perc',
            'valor'=>$this->percent(0.5),
            'nparcela'=>null,
        ]);

        Taxa::create([
            'slug'=>'tarifa_core',
            'valor'=>5,
            'nparcela'=>null,
        ]);

        Taxa::create([
            'slug'=>'tarifa_serasa',
            'valor'=>12.75,
            'nparcela'=>null,
        ]);

        Taxa::create([
            'slug'=>'taxam_iof',
            'type'=>'perc',
            'valor'=>$this->percent(0.38),
            'nparcela'=>null,
        ]);

        Taxa::create([
            'slug'=>'taxad_iof',
            'type'=>'perc',
            'valor'=>$this->percent(0.0082),
            'nparcela'=>null,
        ]);

        // taxa intermediador

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(10.18),
            'nparcela'=>3,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(11.77),
            'nparcela'=>4,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(13.36),
            'nparcela'=>5,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(14.96),
            'nparcela'=>6,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(16.15),
            'nparcela'=>7,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(17.74),
            'nparcela'=>8,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(19.32),
            'nparcela'=>9,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(20.89),
            'nparcela'=>10,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(22.52),
            'nparcela'=>11,
        ]);

        Taxa::create([
            'slug'=>'taxaint',
            'type'=>'perc',
            'valor'=>$this->percent(24.21),
            'nparcela'=>12,
        ]);*/

        OperadorPermissao::create([
            'tela'=>'clientes',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'cadastro',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'documentos',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'creditos',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'faturas',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'acoes',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'taxas',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'faq',
            'acesso'=>1,
            'id_operador'=>1
        ]);
        OperadorPermissao::create([
            'tela'=>'operadores',
            'acesso'=>1,
            'id_operador'=>1
        ]);

    }

    public function percent($val) {
        return $val/100;
    }

}
