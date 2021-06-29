<?php

namespace Database\Seeders;

use App\Models\Mission;
use Illuminate\Database\Seeder;

class MissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $missions = [
            [
                "title" => 'Leia Livro',
                "description" => 'Leia um capítulo de algum livro',
                "confidence" => 25,
                "gold" => 50,
                "rank" => 'E',
                "status" => 'intelligence',
                "exp_status" => 1
            ],
            [
                "title" => 'Leia Livro #2',
                "description" => 'Leia dois capítulos de algum livro',
                "confidence" => 50,
                "gold" => 100,
                "rank" => 'D',
                "status" => 'intelligence',
                "exp_status" => 2
            ],
            [
                "title" => 'Leia Livro #3',
                "description" => 'Leia três capítulos de algum livro',
                "confidence" => 75,
                "gold" => 150,
                "rank" => 'C',
                "status" => 'intelligence',
                "exp_status" => 3
            ],
            [
                "title" => 'Leia Livro #4',
                "description" => 'Leia quatro capítulos de algum livro',
                "confidence" => 100,
                "gold" => 200,
                "rank" => 'B',
                "status" => 'intelligence',
                "exp_status" => 4
            ],
            [
                "title" => 'Leia Livro #5',
                "description" => 'Leia cinco capítulos de algum livro',
                "confidence" => 150,
                "gold" => 250,
                "rank" => 'A',
                "status" => 'intelligence',
                "exp_status" => 5
            ],
            [
                "title" => 'Leia Livro #6',
                "description" => 'Leia seis capítulos de algum livro',
                "confidence" => 200,
                "gold" => 300,
                "rank" => 'S',
                "status" => 'intelligence',
                "exp_status" => 6
            ],
            [
                "title" => 'Coma sem Gostar',
                "description" => 'Coma algum alimento que você não goste',
                "confidence" => 25,
                "gold" => 50,
                "rank" => 'E',
                "status" => 'courage',
                "exp_status" => 1
            ],
            [
                "title" => 'Faça um Amigo',
                "description" => 'Faça um novo amigo utilizando a plataforma shyland',
                "confidence" => 25,
                "gold" => 50,
                "rank" => 'E',
                "status" => 'friendship',
                "exp_status" => 1
            ],
            [
                "title" => 'Leia Notícias',
                "description" => 'Utilize algum portal de notícias ou algum jornal impresso de sua preferência e leia alguma notícia',
                "confidence" => 25,
                "gold" => 50,
                "rank" => 'E',
                "status" => 'sociability',
                "exp_status" => 1
            ],
            [
                "title" => 'Faça Boa Ação',
                "description" => 'Faça uma boa ação para alguém que esteja precisando',
                "confidence" => 25,
                "gold" => 50,
                "rank" => 'E',
                "status" => 'kindness',
                "exp_status" => 1
            ],
            [
                "title" => 'Faça um Desenho',
                "description" => 'Pense em algum interessante e faça alguma desenho. Deixe a sua imaginação fluir!',
                "confidence" => 25,
                "gold" => 50,
                "rank" => 'E',
                "status" => 'criativity',
                "exp_status" => 1
            ]
        ];

        foreach($missions as $mission) {
            $m = new Mission();
            $m->store($mission);
        }

    }
}
