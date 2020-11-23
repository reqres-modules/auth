<?php
namespace Reqres\Module\Auth;

use Reqres\Response;
use Reqres\Exception;

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
     * Требуется авторизация
     *
     */
	function mod_auth_required()
    {
        
        // выводим страницу формы авторизации
		return $this-> mod_auth_page();        
        
    }
    
    /**
     *
     * Требуется авторизация
     *
     */
    function ajax_mod_auth_required()
    {

        // пересылаем ответ с указанием протокола
        return Response::JSON()
            // указываем протокол авторизации
            -> protocol('Auth', 'Required')
            -> data([
                'authHTML' => $this-> view()-> mod_auth_page()-> block('body').'',
                'message' => 'Требуется авторизация'
            ]);
        ;

    }
    

    /**
     *
     * Ошибка при выполнении формы авторизации
     *
     */    
    function mod_auth_error()
    {
        
        return $this-> mod_auth_page();
        
    }

    
    /**
     *
     * Ошибка при выполнении формы авторизации
     *
     */
    function ajax_mod_auth_error()
    {
        
		return Response::JSON()
            -> protocol('Form', 'Error')
            -> data([
                'errors' => $this-> errors,
            ])
        ;
        
    }    
    
    
    /**
     *
     * Успешная авторизация
     *
     */    
    function mod_auth_success($info = [])
    {

        // перезагружаем страницу
		Request::reload();

    }
    

    /**
     *
     * Удачная авторизация
     *
     */    
    function ajax_mod_auth_success($info = [])
    {
        
		return Response::JSON()
            -> protocol('Auth', 'Success')
            -> data([
                'info' => $info,
                'message' => 'Вы были успешно авторизированы'
            ])
        ;
    }

    /**/
    
}