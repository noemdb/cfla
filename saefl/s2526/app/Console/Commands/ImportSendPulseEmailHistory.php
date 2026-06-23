<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ResendEmail;
use Carbon\Carbon;

class ImportSendPulseEmailHistory extends Command
{
    protected $signature = 'sendpulse:import-history {file : Path to the CSV file}';
    protected $description = 'Import SendPulse email history from CSV file to ResendEmail model';
    protected $separate = ';';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting import from: {$filePath}");

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Could not open file: {$filePath}");
            return 1;
        }

        // Read and display header row for debugging
        $header = fgetcsv($handle, 0, $this->separate);
        $this->info("CSV Header: " . implode($this->separate, $header));
        $this->info("Number of columns in header: " . count($header));

        $imported = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 0, $this->separate)) !== false) {
            try {
                // Ensure we have exactly 13 columns by padding with empty strings if needed
                $data = array_pad($data, 13, '');

                // Debug information
                $this->info("Processing row: " . implode($this->separate, $data));
                $this->info("Number of columns in row: " . count($data));

                [
                    $emailId,
                    $date,
                    $sender,
                    $recipient,
                    $subject,
                    $size,
                    $status,
                    $reason,
                    $technicalInfo,
                    $read,
                    $clientInfo,
                    $clicked,
                    $clickedClientInfo
                ] = $data;

                // Skip if essential fields are empty
                if (empty($emailId) || empty($date) || empty($sender) || empty($recipient)) {
                    $this->warn("Skipping record - missing essential fields: " . implode($this->separate, $data));
                    $skipped++;
                    continue;
                }

                // Check if email already exists
                if (ResendEmail::where('resend_id', $emailId)->exists()) {
                    $this->info("Skipping existing email: {$emailId}");
                    $skipped++;
                    continue;
                }

                // Map SendPulse status to our status
                $mappedStatus = $this->mapStatus($status);

                // Create new ResendEmail record
                $email = ResendEmail::create([
                    'resend_id' => $emailId,
                    'from' => $sender,
                    'to' => $recipient,
                    'subject' => $subject,
                    'status' => $mappedStatus,
                    'sent_at' => Carbon::parse($date),
                ]);

                // Add events based on status and additional information
                $events = [];

                if ($read === 'Read') {
                    $email->updateStatus('opened', Carbon::parse($date));
                    $events[] = [
                        'type' => 'opened',
                        'timestamp' => Carbon::parse($date)->toIso8601String(),
                        'client_info' => $clientInfo
                    ];
                }

                if ($clicked === 'Clicked') {
                    $email->updateStatus('clicked', Carbon::parse($date));
                    $events[] = [
                        'type' => 'clicked',
                        'timestamp' => Carbon::parse($date)->toIso8601String(),
                        'client_info' => $clickedClientInfo
                    ];
                }

                if ($mappedStatus === 'delivered') {
                    $email->updateStatus('delivered', Carbon::parse($date));
                    $events[] = [
                        'type' => 'delivered',
                        'timestamp' => Carbon::parse($date)->toIso8601String(),
                        'technical_info' => $technicalInfo
                    ];
                }

                if (!empty($events)) {
                    $email->events = $events;
                    $email->save();
                }

                $imported++;

                if ($imported % 100 === 0) {
                    $this->info("Imported {$imported} records...");
                }
            } catch (\Exception $e) {
                $this->error("Error processing record: " . $e->getMessage());
                $skipped++;
            }
        }

        fclose($handle);

        $this->info("Import completed!");
        $this->info("Successfully imported: {$imported} records");
        $this->info("Skipped: {$skipped} records");

        return 0;
    }

    protected function mapStatus($sendPulseStatus)
    {
        return match (strtolower($sendPulseStatus)) {
            'delivered' => 'delivered',
            'not delivered: mailbox unavailable' => 'bounced',
            'not delivered: spam' => 'rejected',
            'not delivered: rejected' => 'rejected',
            default => 'sent'
        };
    }
}