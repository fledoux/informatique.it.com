<?php

namespace App\Console\Commands;

use App\Mail\AppointmentBookingConfirmationMail;
use App\Mail\AppointmentBookingNotificationMail;
use App\Models\AppointmentBooking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail-send {--email=test@example.com : Email to send to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending appointment booking emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing email configuration...');
        $this->info('MAIL_MAILER: ' . config('mail.mailer'));
        $this->info('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
        $this->info('AWS_ACCESS_KEY: ' . substr(config('services.ses.key'), 0, 10) . '...');
        $this->info('AWS_REGION: ' . config('services.ses.region'));
        $this->newLine();

        $booking = AppointmentBooking::latest()->first();
        if (!$booking) {
            $this->error('❌ No bookings found in database');
            return 1;
        }

        $this->info("📧 Booking found: ID {$booking->id}");
        $this->info("   Name: {$booking->first_name} {$booking->last_name}");
        $this->info("   Email: {$booking->email}");
        $this->newLine();

        // Test 1: Confirmation email to client
        $this->info('📤 Sending confirmation email to client...');
        try {
            Mail::to($booking->email)->send(new AppointmentBookingConfirmationMail($booking));
            $this->info('✅ Confirmation email sent successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Error sending confirmation email:');
            $this->error('   ' . $e->getMessage());
            if ($e->getPrevious()) {
                $this->error('   Previous: ' . $e->getPrevious()->getMessage());
            }
            return 1;
        }

        $this->newLine();

        // Test 2: Notification email to admin
        $this->info('📤 Sending notification email to admin...');
        try {
            $adminEmail = $booking->appointmentProject->notification_email ?? config('app.company.emails.help');
            $this->info('   Admin email: ' . $adminEmail);
            Mail::to($adminEmail)->send(new AppointmentBookingNotificationMail($booking));
            $this->info('✅ Notification email sent successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Error sending notification email:');
            $this->error('   ' . $e->getMessage());
            if ($e->getPrevious()) {
                $this->error('   Previous: ' . $e->getPrevious()->getMessage());
            }
            return 1;
        }

        $this->newLine();
        $this->info('✅ All emails sent successfully!');
        return 0;
    }
}
