<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResultadosAprendizajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ruta del csv
        $path = database_path('seeders/csv/resultados_aprendizaje.csv');

        // Control de errores
        if (!file_exists($path)) {
            $this->command->error("CSV no encontrado: $path");
            return;
        }

        // Leer todas las líneas y parsear con str_getcsv
        $rows = array_map('str_getcsv', file($path));

        // El primer registro es la cabecera
        $header = array_map('trim', array_shift($rows));

        $data = [];
        foreach ($rows as $row) {
            // Ignorar filas vacías o mal formadas
            if (count($row) < count($header)) {
                continue;
            }

            $rec = array_combine($header, $row);

            $moduloId = DB::table('modulos')
                ->where('codigo', trim($rec['cod_modulo']))
                ->value('id');

            $data[] = [
                'modulo_id' => $moduloId,
                'codigo' => trim($rec['cod_modulo']), // cogemos cod_modulo porque asi esta definido en el .csv
                'descripcion' => trim($rec['definicion' ?? '']), // Definicion es lo unico definido en el .csv
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar/actualizar usando upsert para evitar duplicados por 'codigo'
        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('resultados_aprendizaje')->upsert(
                    $chunk,
                    ['codigo'], // llave única para evitar duplicados
                    ['descripcion', 'updated_at'] // hemos quitado nombre porque no aparece en el csv
                );
            }
        });
    }
}
