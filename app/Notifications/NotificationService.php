<?php

namespace App\Services;

use App\Mail\GeneralMail;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
namespace App\Notifications;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Notification;
use Mail;
class NotificationService {

    private $message = [];
    private $subject;
    private $data = [];

    function toArray(string $title, string $link = '', string $action = ''){
        // dd($title);
        return  [
            'title' => $title,
            'link' => $link,
            'action' => $action
        ];
    }

    function data(string $title, string $link = '', string $action = ''){
        $this->data = $this->toArray($title, $link, $action);
        return $this;
    }

    private function parse($type, $data){
        $this->message[] = [
            'type' => $type,
            'value' => $data
        ];
    }

    function send($receivers, $channels){
        $data = array_merge($this->toArray($this->subject), $this->data);
        Notification::send($receivers, new GeneralNotification($this->subject, $channels, $this->message, $data));
    }

    function mail($receivers){
        Mail::to($receivers)->send(new GeneralMail($this->subject, $this->message));
    }

    function text($text, $condition = true){
        if($condition){
            $this->parse('text', $text);
        }
        return $this;
    }

    function code($text, $condition = true){
        if($condition) $this->parse('code', $text);
        return $this;
    }

    function goodbye($text, $condition = true){
        if($condition) $this->parse('goodbye', $text);
        return $this;
    }

    static function subject($subject){
        $self = new self;
        $self->subject = $subject;
        return $self;
    }

    function action($action, $link, $condition = true){
        if($condition) $this->parse('action', [
            'action' => $action,
            'link' => $link
        ]);
        return $this;
    }

    function image($image){
        $this->parse('image', $image);
        return $this;
    }

    function greeting($greeting){
        $this->parse('greeting', $greeting);
        return $this;
    }
}

