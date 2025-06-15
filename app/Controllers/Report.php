<?php
namespace App\Controllers;

use App\Models\ReportModel;
use App\Models\VehicleModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Report extends BaseController
{
    protected $reportModel;
    protected $vehicleModel;

    public function __construct()
    {
        $this->reportModel = new ReportModel();
        $this->vehicleModel = new VehicleModel();
    }

    public function index()
    {
        $user = $this->getCurrentUser();
        if (!$this->isLoggedIn()) {
            return redirect()->to('auth/login');
        }

        $reports = $this->reportModel->getVehicleReport();
        // Tambahkan status approval untuk setiap report
        foreach ($reports as &$report) {
            $approvalStatus = $this->reportModel->getVehicleStatus($report['vehicle_id']);
            $report['approval_status'] = $approvalStatus;
            
            // Update status vehicle jika diperlukan
            if ($approvalStatus === 'approved' && $report['vehicle_status'] !== 'approved') {
                $this->vehicleModel->update($report['vehicle_id'], ['status' => 'approved']);
                $report['vehicle_status'] = 'approved';
            } elseif ($approvalStatus === 'rejected' && $report['vehicle_status'] !== 'rejected') {
                $this->vehicleModel->update($report['vehicle_id'], ['status' => 'rejected']);
                $report['vehicle_status'] = 'rejected';
            }
        }

        return view('report/index', [
            'reports' => $reports
        ]);
    }

    public function export($type = 'excel')
    {
        $reportModel = new ReportModel();
        $reports = $reportModel->getVehicleReport();

        if ($type === 'excel') {
            return $this->exportToExcel($reports);
        } else if ($type === 'pdf') {
            return $this->exportToPDF($reports);
        }
    }

    private function exportToExcel($reports)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tipe Kendaraan');
        $sheet->setCellValue('C1', 'Nomor Kendaraan');
        $sheet->setCellValue('D1', 'Driver');
        $sheet->setCellValue('E1', 'Tanggal Mulai');
        $sheet->setCellValue('F1', 'Tanggal Selesai');
        $sheet->setCellValue('G1', 'Tujuan');
        $sheet->setCellValue('H1', 'Status Approval');
        $sheet->setCellValue('I1', 'Catatan');
        $sheet->setCellValue('J1', 'Dibuat Oleh');
        $sheet->setCellValue('K1', 'Tanggal Dibuat');

        // Isi data
        $row = 2;
        foreach ($reports as $index => $report) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $report['vehicle_type']);
            $sheet->setCellValue('C' . $row, $report['vehicle_number']);
            $sheet->setCellValue('D' . $row, $report['driver_name'] ?? '-');
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($report['start_date'])));
            $sheet->setCellValue('F' . $row, date('d/m/Y', strtotime($report['end_date'])));
            $sheet->setCellValue('G' . $row, $report['purpose']);
            $sheet->setCellValue('H' . $row, $this->getStatusText($report['status']));
            $sheet->setCellValue('I' . $row, $report['notes']);
            $sheet->setCellValue('J' . $row, $report['creator_name']);
            $sheet->setCellValue('K' . $row, date('d/m/Y H:i', strtotime($report['created_at'])));
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Kendaraan_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportToPDF($reports)
    {
        $dompdf = new \Dompdf\Dompdf();
        
        // Buat HTML untuk PDF
        $html = '
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 5px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                h2 {
                    text-align: center;
                    margin-bottom: 5px;
                }
                .date {
                    text-align: center;
                    margin-bottom: 20px;
                    font-size: 11px;
                }
            </style>
        </head>
        <body>
            <h2>Laporan Kendaraan</h2>
            <p class="date">Tanggal Export: ' . date('d/m/Y H:i') . '</p>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tipe Kendaraan</th>
                        <th>Nomor Kendaraan</th>
                        <th>Driver</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Tujuan</th>
                        <th>Status Approval</th>
                        <th>Catatan</th>
                        <th>Dibuat Oleh</th>
                        <th>Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($reports as $index => $report) {
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . $report['vehicle_type'] . '</td>
                <td>' . $report['vehicle_number'] . '</td>
                <td>' . ($report['driver_name'] ?? '-') . '</td>
                <td>' . date('d/m/Y', strtotime($report['start_date'])) . '</td>
                <td>' . date('d/m/Y', strtotime($report['end_date'])) . '</td>
                <td>' . $report['purpose'] . '</td>
                <td>' . $this->getStatusText($report['status']) . '</td>
                <td>' . $report['notes'] . '</td>
                <td>' . $report['creator_name'] . '</td>
                <td>' . date('d/m/Y H:i', strtotime($report['created_at'])) . '</td>
            </tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Set header untuk download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Laporan_Kendaraan_' . date('Y-m-d') . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        // Output PDF
        echo $dompdf->output();
        exit;
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 'approved':
                return 'Disetujui';
            case 'rejected':
                return 'Ditolak';
            case 'pending_level_1':
                return 'Menunggu Level 1';
            case 'pending_level_2':
                return 'Menunggu Level 2';
            default:
                return 'Tidak Diketahui';
        }
    }
}
?>