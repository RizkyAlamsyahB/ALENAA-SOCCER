<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class EmailHelper
{
    /**
     * Kirim email dengan mencoba beberapa kali jika gagal
     *
     * @param string $to Email penerima
     * @param Mailable $mailable Email yang akan dikirim
     * @param int $maxAttempts Jumlah maksimal percobaan
     * @param string $context Konteks untuk logging
     * @return bool
     */
    public static function sendWithRetry($to, Mailable $mailable, $maxAttempts = 3, $context = [])
    {
        $attempt = 1;
        $success = false;
        $lastError = null;

        while ($attempt <= $maxAttempts && !$success) {
            try {
                Log::info("Mencoba mengirim email attempt #{$attempt}", array_merge([
                    'to' => $to,
                    'mailable_class' => get_class($mailable),
                ], $context));

                Mail::to($to)->send($mailable);

                $success = true;
                Log::info("Berhasil mengirim email pada attempt #{$attempt}", array_merge([
                    'to' => $to,
                ], $context));

                return true;
            } catch (\Exception $e) {
                $lastError = $e;
                Log::warning("Gagal mengirim email pada attempt #{$attempt}: " . $e->getMessage(), array_merge([
                    'to' => $to,
                    'error' => $e->getMessage(),
                ], $context));

                // Tunggu sebentar sebelum mencoba lagi (jika bukan percobaan terakhir)
                if ($attempt < $maxAttempts) {
                    $sleepTime = pow(2, $attempt - 1); // Exponential backoff: 1, 2, 4 detik
                    sleep($sleepTime);
                }
            }

            $attempt++;
        }

        if (!$success) {
            Log::error("Gagal mengirim email setelah {$maxAttempts} percobaan", array_merge([
                'to' => $to,
                'last_error' => $lastError ? $lastError->getMessage() : null,
            ], $context));
        }

        return $success;
    }

    /**
     * Kirim email dengan menyertakan teks backup (alternatif) jika render template gagal
     *
     * @param string $to Email penerima
     * @param string $subject Subjek email
     * @param string $textContent Konten teks alternatif
     * @param string $htmlView Nama view HTML
     * @param array $viewData Data untuk view
     * @return bool
     */
    public static function sendWithFallback($to, $subject, $textContent, $htmlView, $viewData = [])
    {
        try {
            Mail::send([$htmlView, 'emails.text_backup'], $viewData, function($message) use ($to, $subject, $textContent) {
                $message->to($to)
                    ->subject($subject)
                    ->text($textContent);
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending email with fallback: " . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
