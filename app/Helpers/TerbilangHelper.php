<?php

namespace App\Helpers;

class TerbilangHelper
{
    private static array $angka = [
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
    ];

    private static function convert(float|int $nilai): string
    {
        $nilai = abs($nilai);
        $huruf = '';

        if ($nilai < 12) {
            $huruf = ' ' . self::$angka[(int)$nilai];
        } elseif ($nilai < 20) {
            $huruf = self::convert($nilai - 10) . ' Belas';
        } elseif ($nilai < 100) {
            $huruf = self::convert((int)($nilai / 10)) . ' Puluh' . self::convert(fmod($nilai, 10));
        } elseif ($nilai < 200) {
            $huruf = ' Seratus' . self::convert($nilai - 100);
        } elseif ($nilai < 1000) {
            $huruf = self::convert((int)($nilai / 100)) . ' Ratus' . self::convert(fmod($nilai, 100));
        } elseif ($nilai < 2000) {
            $huruf = ' Seribu' . self::convert($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $huruf = self::convert((int)($nilai / 1000)) . ' Ribu' . self::convert(fmod($nilai, 1000));
        } elseif ($nilai < 1000000000) {
            $huruf = self::convert((int)($nilai / 1000000)) . ' Juta' . self::convert(fmod($nilai, 1000000));
        } elseif ($nilai < 1000000000000) {
            $huruf = self::convert((int)($nilai / 1000000000)) . ' Miliar' . self::convert(fmod($nilai, 1000000000));
        } elseif ($nilai < 1000000000000000) {
            $huruf = self::convert((int)($nilai / 1000000000000)) . ' Triliun' . self::convert(fmod($nilai, 1000000000000));
        }

        return $huruf;
    }

    public static function make(float|int $nilai): string
    {
        if ($nilai == 0) {
            return 'Nol Rupiah';
        }

        $result = trim(preg_replace('/\s+/', ' ', self::convert($nilai)));
        return $result . ' Rupiah';
    }
}
