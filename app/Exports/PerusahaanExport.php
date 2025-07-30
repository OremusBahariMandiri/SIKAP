<?php

namespace App\Exports;

use App\Models\Perusahaan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PerusahaanExport implements FromView, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $perusahaans;
    protected $filter;

    public function __construct($perusahaans, $filter = null)
    {
        $this->perusahaans = $perusahaans;
        $this->filter = $filter;
    }

    public function view(): View
    {
        return view('perusahaan.export-perusahaan', [
            'perusahaans' => $this->perusahaans,
            'filter' => $this->filter
        ]);
    }

    public function title(): string
    {
        return 'Daftar Perusahaan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as headers
            1 => ['font' => ['bold' => true, 'size' => 12]],

            // Add borders to all cells
            'A1:Q' . (count($this->perusahaans) + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set background color for header row
                $event->sheet->getStyle('A1:Q1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('4a6fdc');

                // Set text color for header row
                $event->sheet->getStyle('A1:Q1')->getFont()->getColor()
                    ->setRGB('FFFFFF');

                // Add filter buttons to headers
                $event->sheet->setAutoFilter('A1:Q1');

                // Freeze the first row
                $event->sheet->freezePane('A2');

                // Add filter information section if available
                if ($this->filter) {
                    $filterRowStart = count($this->perusahaans) + 3;

                    // Set title for filter info
                    $event->sheet->setCellValue('A' . $filterRowStart, 'Informasi Filter yang Diterapkan:');
                    $event->sheet->mergeCells('A' . $filterRowStart . ':B' . $filterRowStart);
                    $event->sheet->getStyle('A' . $filterRowStart . ':B' . $filterRowStart)->getFont()->setBold(true);

                    $currentRow = $filterRowStart + 1;

                    // Add filter details
                    $filterItems = [
                        'nama' => 'Nama Perusahaan',
                        'bidang' => 'Bidang Usaha',
                        'izin' => 'Izin Usaha',
                        'golongan' => 'Golongan Usaha',
                        'direktur_utama' => 'Direktur Utama',
                        'direktur' => 'Direktur',
                        'komisaris_utama' => 'Komisaris Utama',
                        'komisaris' => 'Komisaris',
                        'alamat' => 'Alamat',
                        'telepon' => 'Telepon',
                        'telepon2' => 'Telepon 2',
                        'email' => 'Email',
                        'email2' => 'Email 2',
                        'website' => 'Website',
                        'tgl_berdiri_from' => 'Tanggal Berdiri (Dari)',
                        'tgl_berdiri_to' => 'Tanggal Berdiri (Sampai)'
                    ];

                    foreach ($filterItems as $key => $label) {
                        if (isset($this->filter[$key]) && $this->filter[$key]) {
                            $event->sheet->setCellValue('A' . $currentRow, $label);
                            $event->sheet->setCellValue('B' . $currentRow, $this->filter[$key]);
                            $currentRow++;
                        }
                    }

                    // Add export date
                    $event->sheet->setCellValue('A' . $currentRow, 'Tanggal Export');
                    $event->sheet->setCellValue('B' . $currentRow, now()->format('d/m/Y H:i:s'));

                    // Add borders to filter info section
                    $event->sheet->getStyle('A' . $filterRowStart . ':B' . $currentRow)->getBorders()
                        ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
            },
        ];
    }
}