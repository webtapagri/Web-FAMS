<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FamsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }   

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        //echo "1<pre>"; print_r($this->data); die();
        /*
        stdClass Object
        (
            [noreg] => Array
                (
                    [0] => ini noreg
                    [1] => 1
                    [2] => 2
                )

            [sender] => TAP Agri
        )
        */

        if( $this->data->jenis_pemberitahuan == 'PENDAFTARAN' )
        {
            return $this->from('no-reply@tap-agri.com')
                   ->subject("Permohonan Persetujuan Pengajuan Aset ( {$this->data->noreg[0]} )")
                   ->view('email.email_pendaftaran')
                   ->with(
                    [
                        'nama' => 'PEMBERITAHUAN CREATE DOCUMENT SAP',
                        'website' => 'http://ams.tap-agri.com/',
                    ]);
        }
        else if( $this->data->jenis_pemberitahuan == 'DISPOSAL' )
        {
            return $this->from('no-reply@tap-agri.com')
                   ->subject("Permohonan Persetujuan Disposal Aset ( {$this->data->noreg[0]} )")
                   ->view('email.email_disposal')
                   ->with(
                    [
                        'nama' => 'PEMBERITAHUAN DISPOSAL DOCUMENT',
                        'website' => 'http://ams.tap-agri.com/',
                    ]);
        }
        else if( $this->data->jenis_pemberitahuan == 'MUTASI' )
        {
            return $this->from('no-reply@tap-agri.com')
                   ->subject("Permohonan Persetujuan Mutasi Aset ( {$this->data->noreg[0]} )")
                   ->view('email.email_mutasi')
                   ->with(
                    [
                        'nama' => 'PEMBERITAHUAN MUTASI DOCUMENT',
                        'website' => 'http://ams.tap-agri.com/',
                    ]);
        }
        else
        {
            return $this->from('no-reply@tap-agri.com')
                   ->view('email.email_template')
                   ->with(
                    [
                        'nama' => 'FAMS Website',
                        'website' => 'http://ams.tap-agri.com/',
                    ]);
        }

        
    }
}