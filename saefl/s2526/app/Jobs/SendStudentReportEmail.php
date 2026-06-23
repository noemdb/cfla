<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentReportMail;
use App\Models\app\Estudiant;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class SendStudentReportEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $estudiant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Estudiant $estudiant)
    {
        $this->estudiant = $estudiant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pdf = Pdf::loadView('reports.student', ['student' => $this->estudiant]);
        $pdfContent = $pdf->output();
        $pdfName = 'report_' . $this->estudiant->id . '.pdf';

        Mail::to($this->estudiant->email)->send(new StudentReportMail($this->estudiant, $pdfContent, $pdfName));
    }
}
