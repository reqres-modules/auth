<?php
namespace Reqres\Module\Auth;

use Reqres\User;
use Reqres\Request;
use Reqres\Form;

/**
 *
 * Этот модуль предназначен для работы с формой авторизации.
 * Внимание! ТОЛЬКО с представлением View авторизации
 * Controller и Model используется в самом проекте, через класс Reqres\User в котором прописывается весь механизм авторизации
 *
 * Подразумевается что любая страница может стать формой для авторизации
 *
 * TODO Подразумевается, что возможна многоступенчатая авторизация
 *
 * Можно унаследовать как саму форму авторизации, с её полями, так и представление этой формы
 * Если в конструкторе прописать if(!User::info()) $this-> mod_auth_login(); 					- откроется форма авторизации
 *
 * Этот модуль использует JS протокол "auth", который перехватывает запросы авторизации
 *
 * Форма должна содержать 2 обязательтных поял login и password !!!
 *
 */
trait Controller
{

    /**
     * 
     * Метод проверки авторизации
     * 
     */
    abstract function mod_auth_check();
    
    /**
     * 
     * Метод для возврата формы авторизации
     * 
     */
    //abstract function mod_auth_form();
    
    /**
     *
     * Создаем форму авторизации
     *
     * Её можно унаследовать, добавив в нее например поле SMS авторизацию
     *
     */
    protected function mod_auth_form_template()
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
	function mod_auth_login()
	{
        
        // заносим форму в переменную
        $this-> mod_auth_form = method_exists($this, 'mod_auth_form') ? $this-> mod_auth_form() : $this-> mod_auth_form_template();
        
        // проверяем форму (!) не авторизацию, а форму
        if($this-> mod_auth_form-> check('login')){

            // получаем данные на проверку
            $this-> values = $this-> mod_auth_form-> valuesUser();
            
            // если введены верные данные
            if(User::login($this-> values['login'], $this-> values['password'])){

                // сохраняем 
                setcookie('mod_auth_last_login', $this-> values['login'], 0, '/');
                
                // возвращаем текущий статус авторизации
                $this-> mod_auth_status();

            } else {
                
				// добавляем ошибку в форму
                $this-> mod_auth_form-> errorAdd('Неудачная авторизация', 'password');

            }
            
        }
        
		// получаем ошибки
        $this-> errors = $this-> mod_auth_form-> errors();
        
        if(!empty($this-> errors)){
            
            // авторизация не пройдена
            $this-> mod_auth_error();          
            
        }

        // если AJAX то возвращаем форму авторизации
        $this-> mod_auth_status();
        
	}


	/**
     *
     * Сюда мы поподаем во всех случаях, кроме случая ошибки в форме
     *
     */
	protected function mod_auth_status()
	{
	
        // смотрим данные о пользователе
        if(!$this-> info = User::info()) $this-> info = null;
        
        // если AJAX запрос
        if(Request::get()-> ajax()){

            // фэил
            if(!$this-> info) $this-> view()-> mod_auth_ajax();
            // успех
            else $this-> view()-> mod_auth_ajax_response_success();

        }

        // открываем стандартную форму
		$this-> view()-> mod_auth_page();

	}
    
    
	/**
     *
     * Ошибка авторизации
     *
     */
	function mod_auth_error()
	{

		// если ajax запрос       
        if(Request::get()-> ajax()){
            
            // фофч афшд
            $this-> view()-> mod_auth_ajax_response_error();
            
        }
        
        // открываем стандартную форму
		$this-> view()-> mod_auth_page();
        
	}
    
    
	/**
     *
     * Страница формы авторизации
     *
     */
	function mod_auth_logout()
	{

		User::logout();

        if(!Request::get()-> ajax())
            // переходим на главную
            header('Location: /');
        
		exit;

	}

}