<?php

namespace App\Services;

use App\Models\Campain;
use App\Models\CampainDetail;
use App\Utility\RedisUtility;
use Illuminate\Support\Facades\Mail;

class SendEmailService
{

    public function send($campain)
    {
        $time_start = microtime(true);

        do {
            $get_detail = null;
            try {
                $details = RedisUtility::queuePop(['Redis_Send_Email_Campain_' . $campain->id]);
                if ($details == null) {
                    break;
                }
                $_details = json_decode($details);
                $get_detail = $_details;
                Mail::send($_details->view, ['order' => $_details->order], function ($message) use ($_details) {
                    $message->to($_details->email)
                        ->subject($_details->subject)
                        ->from($_details->from, 'no-reply');
                });

                if ($_details != null) {
                    $campain->email_sended = $campain->email_sended + 1;
                    $campain->save();
                }

                $time_end = microtime(true);
                $time = $time_end - $time_start;
                CampainDetail::create([
                    'campain_id' => $campain->id,
                    'type' => $campain->type,
                    'type_campain' => 0,
                    'view' => 'view',
                    'status' => 0,//thành công
                    'contact' => $_details->email,
                    'reason' =>$_details->subject,
                    'content'=> json_encode($get_detail)
                ]);
            } catch (\Exception $e) {
                CampainDetail::create([
                    'campain_id' => $campain->id,
                    'type' => $campain->type,
                    'type_campain' => 0,
                    'view' => 'view',
                    'status' => 2, //thất bại
                    'contact' => $_details->email,
                    'reason' =>$_details->subject,
                    'content'=> json_encode($get_detail)
                ]);
            }
        } while ($_details != null || $time < 5);

        $total = json_decode($campain->total);
        if ($campain->email_sended >= $total->email) {
            $campain->update(['run' => 1]);
        }
    }

}
