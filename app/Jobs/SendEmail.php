<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;

use App\Mail\FamsEmail;
use Illuminate\Support\Facades\Mail;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
	private $email;
	private $data;
	
	public $tries = 20; //max retry
	public $timeout  = 600; //max ekse time
	
    public function __construct($email, $data)
    {
        $this->email = $email;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		\Log::info('Send email to:');
		\Log::info($this->email);
		
		// try {
			Mail::to($this->email)
				->bcc('system.administrator@tap-agri.com')
				->send(new FamsEmail($this->data));
		// }catch (\Throwable $e) {
			// \Log::error('Error: '.$e->getMessage());
		// }catch (\Exception $e) {
			// \Log::error('Err: '.$e->getMessage());
		// }
    }
}
