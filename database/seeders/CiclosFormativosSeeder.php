<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiclosFormativosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ruta
        $path = database_path('seeders/csv/ciclos.csv');

        // Leer y parsear
        $rows = array_map('str_getcsv', file($path));

        // 1º Registro
        $header = array_map('trim', array_shift($rows));

        $data = [];
        foreach ($rows as $row) {
            // Ignorar filas vacías o mal formadas
            if (count($row) < count($header)) {
                continue;
            }

            $rec = array_combine($header, $row);

            
            $familiaId = DB::table('familias_profesionales')
                ->where('codigo', trim($rec['familia']))
                ->value('id');

            $data[] = [
                'nombre' => trim($rec['nombre'] ?? ''),
                'codigo' => trim($rec['cod_ciclo'] ?? ''),
                'familia_profesional_id' => $familiaId,
                'grado' => trim($rec['nivel'] ?? ''),
                'descripcion' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar/actualizar usando upsert para evitar duplicados por 'codigo'
        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('ciclos_formativos')->upsert(
                    $chunk,
                    ['codigo'], // llave única para evitar duplicados
                    ['nombre', 'descripcion', 'updated_at']
                );
            }
        });

    }
}
