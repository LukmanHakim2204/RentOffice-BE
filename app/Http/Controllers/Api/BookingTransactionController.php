<?php

namespace App\Http\Controllers\Api;

use Twilio\Rest\Client;
use App\Models\OfficeSpace;
use Illuminate\Http\Request;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ViewBookingResource;
use App\Http\Requests\StoreBookingTransactionRequest;
use App\Http\Resources\Api\BookingTransactionsResource;

class BookingTransactionController extends Controller
{
    public function booking_details(Request $request)
    {
        $request->validate([
            'booking_trx_id' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $booking = BookingTransaction::where('phone_number', $request->phone_number)
            ->where('booking_trx_id', $request->booking_trx_id)
            ->with('officeSpace.city')
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        return new ViewBookingResource($booking);
    }

    public function store(StoreBookingTransactionRequest $request)
    {
        $validatedData = $request->validated();

        // Debug: Log data yang masuk
        Log::info('Booking data received:', $validatedData);

        $officeSpace = OfficeSpace::find($validatedData['office_space_id']);

        $validatedData['is_paid'] = false;
        $validatedData['booking_trx_id'] = BookingTransaction::generateUniqueTrxId();
        $validatedData['duration'] = $officeSpace->duration;
        $validatedData['ended_at'] = (new \DateTime($validatedData['started_at']))
            ->modify("+{$officeSpace->duration} days")->format('Y-m-d');

        $bookingTransaction = BookingTransaction::create($validatedData);
        $bookingTransaction->load('officeSpace');

        // Debug: Log nomor telepon - PERBAIKAN SYNTAX
        Log::info('Phone number before Twilio:', ['phone' => $bookingTransaction->phone_number]);

        try {
            // NOTIFICATIONS
            $ssid = getenv('TWILIO_ACCOUNT_SID');
            $token = getenv('TWILIO_AUTH_TOKEN');
            $twilioPhone = getenv('TWILIO_PHONE_NUMBER');

            // Debug: Log Twilio credentials
            Log::info('Twilio config:', [
                'ssid' => $ssid ? 'Set' : 'Not set',
                'token' => $token ? 'Set' : 'Not set',
                'phone' => $twilioPhone
            ]);

            if (!$ssid || !$token || !$twilioPhone) {
                throw new \Exception('Twilio credentials incomplete');
            }

            $twilio = new Client($ssid, $token);

            // Format nomor telepon untuk Indonesia
            $phoneNumber = $bookingTransaction->phone_number;

            // Jika nomor dimulai dengan 08, ganti dengan +628
            if (substr($phoneNumber, 0, 2) == '08') {
                $phoneNumber = '+628' . substr($phoneNumber, 2);
            }
            // Jika sudah ada +, gunakan langsung
            elseif (substr($phoneNumber, 0, 1) != '+') {
                $phoneNumber = '+62' . ltrim($phoneNumber, '0');
            }

            Log::info('Formatted phone number:', ['formatted_phone' => $phoneNumber]);

            $messageBody = "Hi {$bookingTransaction->name}, Terima kasih telah booking kantor di First Office. \n \n";
            $messageBody .= "Pesanan Kantor {$bookingTransaction->officeSpace->name} sedang kami proses dengan Booking Transaction ID : {$bookingTransaction->booking_trx_id}.\n \n";
            $messageBody .= "Kami Akan Menginformasikan kembali status pesanan anda secepat mungkin.";

            // $message = $twilio->messages->create(
            //     $phoneNumber, // Gunakan nomor yang sudah diformat
            //     [
            //         'from' => $twilioPhone,
            //         'body' => $messageBody
            //     ]
            // );
            $message = $twilio->messages
                ->create(
                    "whatsapp:{$phoneNumber}", // to
                    array(
                        "from" => "whatsapp:+14155238886",
                        "body" => $messageBody
                    )
                );

            Log::info('SMS sent successfully:', ['sid' => $message->sid]);
        } catch (\Exception $e) {
            Log::error('SMS sending failed:', [
                'error' => $e->getMessage(),
                'phone' => $bookingTransaction->phone_number,
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }

        return new BookingTransactionsResource($bookingTransaction);
    }
}
