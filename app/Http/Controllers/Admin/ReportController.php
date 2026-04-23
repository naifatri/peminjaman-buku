<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $borrowings = Borrowing::with(['user', 'book'])
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('borrow_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('borrow_date', '<=', $endDate);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact('borrowings'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $borrowings = Borrowing::with(['user', 'book'])
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('borrow_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('borrow_date', '<=', $endDate);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('borrowings', 'startDate', 'endDate'));
        return $pdf->download('laporan-peminjaman.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $borrowings = Borrowing::with(['user', 'book'])
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('borrow_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('borrow_date', '<=', $endDate);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=laporan-peminjaman.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($borrowings) {
            $file = fopen('php://output', 'w');
            // Header baris CSV
            fputcsv($file, ['ID', 'Peminjam', 'Buku', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status', 'Denda']);

            foreach ($borrowings as $borrowing) {
                fputcsv($file, [
                    $borrowing->id,
                    $borrowing->user->name,
                    $borrowing->book->title,
                    $borrowing->borrow_date,
                    $borrowing->return_date ?? '-',
                    $borrowing->status,
                    $borrowing->fine_amount
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
