<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once __DIR__ . '/../config/mail_config.php';

class Mailer {
    private $mail;
    private static $instance = null;

    private function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = SMTP_HOST;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = SMTP_USERNAME;
        $this->mail->Password = SMTP_PASSWORD;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = SMTP_PORT;
        
        // Default sender
        $this->mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sendBookingConfirmation($booking, $customer, $car) {
        try {
            // Reset all recipients
            $this->mail->clearAddresses();
            
            // Add customer as recipient
            $this->mail->addAddress($customer['email'], $customer['name']);
            
            // Email content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Booking Confirmation - MyCaRent';
            
            // Create HTML email body
            $body = "
                <h2>Booking Confirmation</h2>
                <p>Dear {$customer['name']},</p>
                <p>Thank you for booking with MyCaRent. Here are your booking details:</p>
                <div style='background: #f9f9f9; padding: 15px; border-radius: 5px;'>
                    <h3>Booking Details</h3>
                    <p><strong>Booking ID:</strong> {$booking['booking_id']}</p>
                    <p><strong>Car:</strong> {$car['make']} {$car['model']}</p>
                    <p><strong>Pick-up Date:</strong> {$booking['pickup_date']}</p>
                    <p><strong>Return Date:</strong> {$booking['return_date']}</p>
                    <p><strong>Total Amount:</strong> ₱{$booking['total_amount']}</p>
                </div>
                <p>If you have any questions, please don't hesitate to contact us.</p>
                <p>Best regards,<br>MyCaRent Team</p>
            ";
            
            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendBookingNotificationToAdmin($booking, $customer, $car) {
        try {
            // Reset all recipients
            $this->mail->clearAddresses();
            
            // Add admin as recipient
            $this->mail->addAddress('admin@mycarent.com', 'Admin');
            
            // Email content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'New Booking Notification - MyCaRent';
            
            // Create HTML email body
            $body = "
                <h2>New Booking Notification</h2>
                <p>A new booking has been made:</p>
                <div style='background: #f9f9f9; padding: 15px; border-radius: 5px;'>
                    <h3>Booking Details</h3>
                    <p><strong>Booking ID:</strong> {$booking['booking_id']}</p>
                    <p><strong>Customer Name:</strong> {$customer['name']}</p>
                    <p><strong>Customer Email:</strong> {$customer['email']}</p>
                    <p><strong>Car:</strong> {$car['make']} {$car['model']}</p>
                    <p><strong>Pick-up Date:</strong> {$booking['pickup_date']}</p>
                    <p><strong>Return Date:</strong> {$booking['return_date']}</p>
                    <p><strong>Total Amount:</strong> ₱{$booking['total_amount']}</p>
                </div>
                <p>Please review this booking in the admin dashboard.</p>
            ";
            
            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail Error: " . $e->getMessage());
            return false;
        }
    }
}
