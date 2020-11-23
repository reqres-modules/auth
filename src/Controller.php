<?php
namespace Reqres\Module\Auth;

use Reqres\{Request, Form};

/**
 *
 * Модуль предоставляет интерфейс для работы с простейшей формой авторизации
 * Форма может быть кастомная, но должна содержать 2 обязательтных поля login и password
 * На клиентской стороне используется протокол "Auth" и "Form" (нужно установить через yarn)
 *
 *
 * Модуль НЕ занимается вопросами хранения хэшей пользователя
 * Хэш запрашивается через абстрактный метод mod_auth_userhash($userid)
 *
 * Модуль НЕ занимается хранением статуса авторизации ни в сесси ни в куках
 * Это функционал легко реализуется в приложении
 *
 * Подразумевается что любая страница приложения может стать формой для авторизации
 * Достаточно в нужном месте вызвать mod_auth_required
 *
 * TODO Подразумевается, что возможна многоступенчатая авторизация через Reqres\Process
 * ТОDO Можно добавить по желанию хранение в сессии статуса авторизациичерез $mod_auth_session = 'session_key'
 * ТОDO Добавить проверку формы
 *
 */
trait Controller
{


    /**
     * 
     * Метод получения данных пользователя 
     * приложение может заглянуть в файл или БД
     * Вернуть должен в формате
     *
	 * [ 'password' => '...', 'algo' => 'bcrypt' ]
     * 
     */
	protected abstract function mod_auth_userhash($userid);
    
    /**
     *
     * Успешная авторизация
     *
     */    
    protected abstract function mod_auth_on_success($userid, $userdata);
    
    
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
     * Логика авторизации
     *
     */
	protected function mod_auth_required()
	{
        
        // заносим форму в переменную
        $this-> mod_auth_form = method_exists($this, 'mod_auth_form') ? $this-> mod_auth_form() : $this-> mod_auth_form_template();
        
        // TODO
        //if(!$this-> mod_auth_form['login']-> notempty() ||  $this-> mod_auth_form['password']-> notempty())
            
        // проверяем форму
        if($this-> mod_auth_form-> check('login')){

            // получаем данные на проверку
            $this-> mod_auth_values = $this-> mod_auth_form-> values();

            // запрашиваем данные пользователя через абстрактный метод
            if($userdata = $this-> mod_auth_userhash($this-> mod_auth_values['login']))
                // проверяем его пароль
	            if($this-> model()-> mod_auth_check_password($this-> mod_auth_values['password'], $userdata)){
                    
                    // сообщаем приложению что авторизация пройдена
                    $this-> mod_auth_on_success($this-> mod_auth_values['login'], $userdata);
                    // выводим пезультат
		            $this-> view()-> mod_auth_success();
                    
                }

            
            $this-> mod_auth_form-> errorAdd('Неудачная авторизация', 'password');

        }
        
		// получаем ошибки
        if($this-> errors = $this-> mod_auth_form-> errors())
            $this-> view()-> mod_auth_error();

        
        // открываем стандартную форму
        $this-> view()-> mod_auth_required();            
 
    }

	/**
     *
     * Логика logout TODO
     *
     * /
	function mod_auth_logout()
	{


	}
    
    /**/

}