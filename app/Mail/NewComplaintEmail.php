<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
class NewComplaintEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public $file_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function build()
    {
        $filename=$this->file_data;
        $complaint_no= $this->mailData['complaint']['complaint_no'];
        $complaint_date= $this->mailData['complaint']['complaint_date'];
        $attachments= $this->mailData['attachment'];
        $subject="New Complaint! Complaint #: ".$complaint_no." (Date: ".$complaint_date.")";
        if (count($attachments)>0)
        {
            foreach($attachments as $attach_file)
            {
                $filename=$attach_file->image_file_name;
                // $file = Storage::disk('s3')->get($attach_file->file); 
                $file_url=$attach_file->url;
                // $this->attachData($file, $image_file_name);
            }  
            return $this->subject($subject)
                            ->view('emails.new_complaint');
        
                        
           
        } else {
            return $this->subject($subject)
                    ->view('emails.new_complaint');
        }
    }


}
