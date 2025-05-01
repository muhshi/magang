<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;

    public function __construct()
    {
        $this->data = Attendance::with('user')->get()->map(function ($item) {
            $start = Carbon::parse($item->start_time);
            $end = Carbon::parse($item->end_time);
            $duration = $start->diff($end);
            $durasiJam = $duration->h + ($duration->i / 60);

            return [
                'tanggal' => Carbon::parse($item->date)->translatedFormat('M j, Y'),
                'name' => $item->user->name ?? '-',
                'status' => $item->isLate() ? 'Terlambat' : 'Tepat Waktu',
                'jam_datang' => $item->start_time,
                'jam_pulang' => $item->end_time,
                'durasi' => $item->workDuration(),
                'durasi_jam' => $durasiJam,
                'terlambat' => $item->isLate()
            ];
        })->toArray();
    }

    public function array(): array
    {
        // Drop kolom bantuan (durasi_jam, terlambat) dari tampilan
        return array_map(function ($row) {
            return [
                $row['tanggal'],
                $row['name'],
                $row['status'],
                $row['jam_datang'],
                $row['jam_pulang'],
                $row['durasi'],
            ];
        }, $this->data);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Name', 'Status', 'Jam Datang', 'Jam Pulang', 'Durasi Kerja'];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2']
            ]
        ]);

        // Data styling (mulai baris ke-2)
        foreach ($this->data as $index => $row) {
            $rowNum = $index + 2;

            // Kolom status (C), kolom durasi (F)
            if ($row['terlambat']) {
                $sheet->getStyle("C$rowNum")->getFont()->getColor()->setRGB('FF0000'); // merah
            } else {
                $sheet->getStyle("C$rowNum")->getFont()->getColor()->setRGB('228B22'); // hijau
            }

            if ($row['durasi_jam'] < 8) {
                $sheet->getStyle("F$rowNum")->getFont()->getColor()->setRGB('FF0000'); // merah
            } else {
                $sheet->getStyle("F$rowNum")->getFont()->getColor()->setRGB('228B22'); // hijau
            }
        }

        return [];
    }
}

