<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacionCita extends Mailable
{
    use Queueable, SerializesModels;

    public $documento;
    public $nombrePersona;
    public $telefono;
    public $correoElectronico;
    public $fecha;
    public $hora;
    
    public $msg = "Notificación del Sistema";
    public $msje = 'Enviado';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($documento, $nombrePersona, $telefono, $correoElectronico, $fecha, $hora)
    {
        //
        $this->documento = $documento;
        $this->nombrePersona = $nombrePersona;
        $this->telefono = $telefono;
        $this->correoElectronico = $correoElectronico;
        
        $this->fecha = $fecha;
        $this->hora  = $hora;
        
        $this->subject($this->msg);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('exito');
    }
}   
