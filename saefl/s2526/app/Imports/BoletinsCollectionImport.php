<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class BoletinsCollectionImport implements ToCollection
{
    use Importable;

    private $rows = 0;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {

        return new Collection([
            'ci_estudiant' => $rows[0],
            'nota' => $rows[1],
    	]);
    }

    public function toCollectionFix($filePath = null, string $disk = null, string $readerType = null): Collection
    {

        $notasImport = $this->toArray($filePath);
        $data = collect();
        foreach ($notasImport as $nota) {

            foreach ($nota as $k => $v) {
                $data->put($v[0], $v[1]);
            }
        }

        return $data;
    }

}
