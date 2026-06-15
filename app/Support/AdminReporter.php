<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdminReporter
{
    public static function report(Throwable $exception, string $context, array $meta = []): void
    {
        report($exception);

        Log::error($context, [
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'meta' => $meta,
        ]);

        $adminEmail = config('services.admin_report_email');

        if (! $adminEmail) {
            return;
        }

        try {
            Mail::raw(
                self::messageBody($exception, $context, $meta),
                function ($message) use ($adminEmail, $context) {
                    $message
                        ->to($adminEmail)
                        ->subject('[CS2 PickLab] ' . $context);
                }
            );
        } catch (Throwable $mailException) {
            report($mailException);

            Log::error('Failed to send admin error report email.', [
                'message' => $mailException->getMessage(),
                'original_context' => $context,
            ]);
        }
    }

    private static function messageBody(Throwable $exception, string $context, array $meta): string
    {
        return implode(PHP_EOL, [
            'CS2 PickLab admin report',
            '',
            'Context:',
            $context,
            '',
            'Exception:',
            get_class($exception),
            '',
            'Message:',
            $exception->getMessage(),
            '',
            'Location:',
            $exception->getFile() . ':' . $exception->getLine(),
            '',
            'Meta:',
            json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
    }
}