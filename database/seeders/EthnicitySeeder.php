<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ethnicity;

class EthnicitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ethnicities = [
            'Kikuyu',
            'Luhya',
            'Kalenjin',
            'Luo',
            'Kamba',
            'Kenyan Somali',
            'Kisii',
            'Mijikenda',
            'Meru',
            'Maasai',
            'Turkana',
            'Embu',
            'Samburu',
            'Taita',
            'Borana',
            'Tharaka',
            'Pokot',
            'Rendille',
            'Orma',
            'Giriama',
            'Dawida',
            'Kuria',
            'Gabra',
            'Ilchamus',
            'Digo',
            'Taveta',
            'Elmolo',
            'Ndorobo',
            'Ogiek',
            'Kony',
            'Konso',
            'Waata',
            'Sagalla',
            'Malakote',
            'Nyika',
            'Burji',
            'Bajun',
            'Shona',
            'Makonde',
            'Nubian',
            'Swahili',
            'Other',
            'Caucasian',
        ];

        foreach ($ethnicities as $ethnicity) {
            Ethnicity::create(['name' => $ethnicity]);
        }
    }
}
