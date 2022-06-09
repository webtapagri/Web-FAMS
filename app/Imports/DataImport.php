<?php

namespace App\Imports;

use App\TR_QRDATA;
use Maatwebsite\Excel\Concerns\ToModel;

class DataImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return TR_QRDATA|null
     */
    public function model(array $row)
    {
        return new TR_QRDATA([
           'qr_code'     => $row[0],
        ]);
    }
}