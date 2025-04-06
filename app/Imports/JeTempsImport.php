<?php

namespace App\Imports;

use App\Models\JeTemp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class JeTempsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new JeTemp([
            'create_date' => $this->parseDate($row['create_date']),
            'posting_date' => $this->parseDate($row['posting_date']),
            'tx_num' => $row['tx_num'] ?? null,
            'doc_num' => $row['doc_num'] ?? null,
            'doc_type' => $row['doc_type'] ?? null,
            'project_code' => $row['project_code'] ?? null,
            'department' => $row['department'] ?? null,
            'account' => $row['account'] ?? null,
            'debit' => $row['debit'] ?? 0.00,
            'credit' => $row['credit'] ?? 0.00,
            'fc_debit' => $row['fc_debit'] ?? 0.00,
            'fc_credit' => $row['fc_credit'] ?? 0.00,
            'remarks' => $row['remarks'] ?? null,
            'user_code' => $row['user_code'] ?? null,
            'user_name' => $row['user_name'] ?? null,
        ]);
    }

    private function parseDate($value)
    {
        if (!$value) return null;
        try {
            return Carbon::createFromFormat('d.m.Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
} 