<?php
namespace Reqres\Module\Auth;

use Reqres\Superglobals\POST;
use Reqres\User;
use Reqres\Request;
use Reqres\Form;

/**
 *
 * Этот модуль предназначен для работы с формой авторизации.
 * Внимание! ТОЛЬКО с представлением View процесса авторизации
 * Controller и Model используется в самом проекте, через класс Reqres\User в котором прописывается весь механизм авторизации
 *
 * Подразумевается что любая страница может стать формой для авторизации
 *
 * TODO Подразумевается, что возможна многоступенчатая авторизация
 *
 * Можно унаследовать как саму форму авторизации, с её полями, так и представление этой формы
 * Если в конструкторе прописать if(!User::info()) $this-> auth_login(); 					- откроется форма авторизации
 *
 * Этот модуль использует JS протокол "auth", который перехватывает запросы авторизации
 *
 */
class Controller extends \Reqres\MVC\Controller 
{

    /**
     *
     * Создаем форму авторизации
     *
     * Её можно унаследовать, добавив в нее например поле SMS авторизацию
     *
     */
    protected function auth_form_template()
    {
        
		return (new Form)
            -> method('POST')
            -> field('login', 'String')
            	-> fkey('login')
            	-> title('Логин')
            	-> notempty(true)
            	-> back()
            -> field('password', 'String')
            	-> tagAttr('type', 'password')
            	-> fkey('password')
            	-> title('Пароль')
            	-> notempty(true)
            	-> back()
        ;
        
    }

    /**
     *
     * Скрипт авторизации
     *
     */
	function auth_login()
	{

        // получаем форму
        $this-> auth_form = $this-> auth_form();
        
        // если форма выполнена
        if($this-> auth_form-> check('login')){

            // разлогиниваемся
            //User::logout();
            
            // получаем данные
            $this-> values = $this-> auth_form-> valuesUser();
            
            // если введены верные данные
            if(User::login($this-> values['login'], $this-> values['password'])){

                // если AJAX то возвращаем текущий статус авторизации
                $this-> auth_status();

            } else {
                
				// добавляем ошибку в форму
                $this-> auth_form-> errorAdd('Неудачная авторизация', 'password');

            }
            
        }
        
		// получаем ошибки    
        $this-> errors = $this-> auth_form-> errors();
        
        if(!is_null($this-> errors)){
            
            // авторизация не пройдена
            $this-> auth_error();          
            
        }
        
        // если AJAX то возвращаем форму авторизации
        $this-> auth_status();
        
	}


	/**
     *
     * Форма авторизации, либо данные авторизации
     *
     */
	protected function auth_status()
	{

        // авторизация не пройдена
        $this-> info = User::info();
		//$this-> info = false;
        
        // если AJAX запрос
        if(Request::get()-> ajax()){

            $this-> authed = (bool) $this-> info;
            
            $this-> view()-> auth_ajax();

        }

        // открываем стандартную форму
		$this-> view()-> auth_page();

	}
    
    
	/**
     *
     * Ошибка авторизации
     *
     */
	function auth_error()
	{

        // получаем данные авторизации
        $this-> info = User::info();
        
		// если ajax запрос       
        if(Request::get()-> ajax()){
            
            $this-> view()-> auth_ajax_error();
            
        }
        
        // открываем стандартную форму
		$this-> view()-> auth_page();        
        
	}
    
    
	/**
     *
     * Страница формы авторизации
     *
     */
	function auth_logout()
	{

		User::logout();

		// переходим на главную
		header('Location: /');
		exit;

	}

}