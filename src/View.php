<?php
namespace Reqres\Module\Auth;

use Reqres\Response;
use Reqres\Exception\ExResponse;

trait View
{

    /**
     *
     * Отображение формы
     *
     */
    abstract function mod_auth_page();
    

    /**
     *
     * AJAX ответ в случае остутствия авторизации
     *
     */
    function mod_auth_ajax()
    {
    
        try {
            
            // отлавливаем стандартный ответ
            $this-> mod_auth_page();
            
            
        } catch(ExResponse $e){

            // пересылаем ответ с указанием протокола
            $e-> response()
                // указываем протокол авторизации
                -> protocol('Auth')
                -> respond();
            
        }
        
        
    }
    
    /**
     *
     * AJAX ответ в случае ошибки
     *
     */
    function mod_auth_ajax_response_error()
    {
     
		Response::JSON()
            -> protocol('Form','Error')
            -> data([
                'errors' => $this-> errors,
            ])
            -> respond();
        
    }

    
    /**
     *
     * AJAX ответ в случае успешной авторизации
     *
     */
    function mod_auth_ajax_response_success()
    {
     
		Response::JSON()
            -> protocol('Auth')
            -> data([
                'info' => $this-> info,
                'status' => 'success'
            ])
            -> respond();
        
    }
    
}