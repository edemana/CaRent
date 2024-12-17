<?php
session_start();
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

$format = $_GET['format'] ?? 'csv';
$type = $_GET['type'] ?? 'bookings';

// Set headers based on format
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . $type . '_' . date('Y-m-d') . '.csv"');
} else {
    require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php';
}

// Get report data based on type
switch ($type) {
    case 'bookings':
        $sql = "SELECT 
                b.id,
                u.Name as customer_name,
                c.Make,
                c.Model,
                b.Pickup_date,
                b.Return_date,
                c.RentalPrice * DATEDIFF(b.Return_date, b.Pickup_date) as total_amount,
                CASE 
                    WHEN CURRENT_DATE BETWEEN b.Pickup_date AND b.Return_date THEN 'Active'
                    WHEN CURRENT_DATE < b.Pickup_date THEN 'Upcoming'
                    ELSE 'Completed'
                END as status
            FROM bookings b
            JOIN users u ON u.User_id = b.User_id
            JOIN car c ON c.Vehicle_id = b.car_id
            ORDER BY b.Pickup_date DESC";
        break;

    case 'revenue':
        $sql = "SELECT 
                DATE_FORMAT(b.Pickup_date, '%Y-%m') as month,
                COUNT(*) as total_bookings,
                SUM(c.RentalPrice * DATEDIFF(b.Return_date, b.Pickup_date)) as revenue
            FROM bookings b
            JOIN car c ON c.Vehicle_id = b.car_id
            GROUP BY DATE_FORMAT(b.Pickup_date, '%Y-%m')
            ORDER BY month DESC";
        break;

    case 'cars':
        $sql = "SELECT 
                c.Make,
                c.Model,
                c.Year,
                c.RentalPrice,
                COUNT(b.id) as total_bookings,
                SUM(c.RentalPrice * DATEDIFF(b.Return_date, b.Pickup_date)) as total_revenue
            FROM car c
            LEFT JOIN bookings b ON c.Vehicle_id = b.car_id
            GROUP BY c.Vehicle_id
            ORDER BY total_bookings DESC";
        break;
}

$result = $conn->query($sql);
$data = $result->fetch_all(MYSQLI_ASSOC);

if ($format === 'csv') {
    $output = fopen('php://output', 'w');
    
    // Add headers
    fputcsv($output, array_keys($data[0]));
    
    // Add data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
} else {
    // Create PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('CarRent Admin');
    $pdf->SetAuthor('CarRent System');
    $pdf->SetTitle('Report - ' . ucfirst($type));
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->AddPage();
    
    // Add title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, ucfirst($type) . ' Report - ' . date('Y-m-d'), 0, 1, 'C');
    $pdf->Ln(10);
    
    // Add table
    $pdf->SetFont('helvetica', '', 10);
    
    // Add headers
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 10);
    foreach (array_keys($data[0]) as $header) {
        $pdf->Cell(40, 7, ucwords(str_replace('_', ' ', $header)), 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Add data
    $pdf->SetFont('helvetica', '', 10);
    foreach ($data as $row) {
        foreach ($row as $cell) {
            $pdf->Cell(40, 6, $cell, 1);
        }
        $pdf->Ln();
    }
    
    // Output PDF
    $pdf->Output('report_' . $type . '_' . date('Y-m-d') . '.pdf', 'D');
}
