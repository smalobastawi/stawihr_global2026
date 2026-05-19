<?php
namespace App\Lib\Enumerations;

class Ethnicity
{
    public const KIKUYU = 1;
    public const LUHYA = 2;
    public const KALENJIN = 3;
    public const LUO = 4;
    public const KAMBA = 5;
    public const KENYAN_SOMALI = 6;
    public const KISII = 7;
    public const MIJIKENDA = 8;
    public const MERU = 9;
    public const MAASAI = 10;
    public const TURKANA = 11;
    public const EMBU = 12;
    public const SAMBURU = 13;
    public const TAITA = 14;
    public const BORANA = 15;
    public const THARAKA = 16;
    public const POKOT = 17;
    public const RENDILLE = 18;
    public const ORMA = 19;
    public const GIRIAMA = 20;
    public const DAWIDA = 21;
    public const KURIA = 22;
    public const GABRA = 23;
    public const ILCHAMUS = 24;
    public const DIGO = 25;
    public const TAVETA = 26;
    public const ELMOLO = 27;
    public const NDOROBO = 28;
    public const OGIEK = 29;
    public const KONY = 30;
    public const KONSO = 31;
    public const WAATA = 32;
    public const SAGALLA = 33;
    public const MALAKOTE = 34;
    public const NYIKA = 35;
    public const BURJI = 36;
    public const BAJUN = 37;
    public const SHONA = 38;
    public const MAKONDE = 39;
    public const NUBIAN = 40;
    public const SWAHILI = 41;
    public const OTHER = 42;
    public const CAUCASIAN = 43;

    public static function toArray(): array
    {
        return [
            self::KIKUYU => 'Kikuyu',
            self::LUHYA => 'Luhya',
            self::KALENJIN => 'Kalenjin',
            self::LUO => 'Luo',
            self::KAMBA => 'Kamba',
            self::KENYAN_SOMALI => 'Kenyan Somali',
            self::KISII => 'Kisii',
            self::MIJIKENDA => 'Mijikenda',
            self::MERU => 'Meru',
            self::MAASAI => 'Maasai',
            self::TURKANA => 'Turkana',
            self::EMBU => 'Embu',
            self::SAMBURU => 'Samburu',
            self::TAITA => 'Taita',
            self::BORANA => 'Borana',
            self::THARAKA => 'Tharaka',
            self::POKOT => 'Pokot',
            self::RENDILLE => 'Rendille',
            self::ORMA => 'Orma',
            self::GIRIAMA => 'Giriama',
            self::DAWIDA => 'Dawida',
            self::KURIA => 'Kuria',
            self::GABRA => 'Gabra',
            self::ILCHAMUS => 'Ilchamus',
            self::DIGO => 'Digo',
            self::TAVETA => 'Taveta',
            self::ELMOLO => 'Elmolo',
            self::NDOROBO => 'Ndorobo',
            self::OGIEK => 'Ogiek',
            self::KONY => 'Kony',
            self::KONSO => 'Konso',
            self::WAATA => 'Waata',
            self::SAGALLA => 'Sagalla',
            self::MALAKOTE => 'Malakote',
            self::NYIKA => 'Nyika',
            self::BURJI => 'Burji',
            self::BAJUN => 'Bajun',
            self::SHONA => 'Shona',
            self::MAKONDE => 'Makonde',
            self::NUBIAN => 'Nubian',
            self::SWAHILI => 'Swahili',
            self::OTHER => 'Other',
            self::CAUCASIAN => 'Caucasian',
        ];
    }

    public static function getName($value): string
    {
        $ethnicities = self::toArray();
        return $ethnicities[$value] ?? 'Other';
    }

    public static function getValue($name)
    {
        $ethnicities = array_flip(self::toArray());
        return $ethnicities[ucwords(strtolower($name))] ?? null;
    }
}
