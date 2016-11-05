<?php
namespace Reqres\Module\Auth;

use Reqres\Response;

class View extends \Reqres\MVC\View 
{

	function auth()
	{
		
		Response\HTML::response()
			-> uses($this)
			-> title('Авторизация')

			-> head_tag('css', "/bower/bootstrap/dist/css/bootstrap.css")
			-> head_tag('css', "/bower/bootstrap/dist/css/bootstrap-theme.css")
			-> head_tag('js', "/bower/bootstrap/dist/js/bootstrap.min.js")

            -> tagAttr('style', 'background:#555', 'body')
            -> put('body.modal', '/modal/')
                // модалка должна быть открыта по умолчанию
                -> tagAttr('style', 'display:block', 'modal')

                -> put('.', 'common/auth/user')
                    -> back()
                -> put('body', $this-> auth_form)
            
            -> back(0)

			-> respond();
	}

    /*
    function auth_ajax()
    {
        
		Response::json()
            -> uses($this)
            -> header('Content-Type: application/json')
            -> header('MyApp-Protocol-Id: auth')
        	-> data([
                'status' => false
            ])
            -> respond();
        
        
    }
    */

    function auth_ajax()
    {
        
        //exit((string) $this-> auth_page_template());
        
        Response::JSON()
            //-> header('Content-Type: application/json')
            -> header('MyApp-Protocol-Id: auth')
            -> data([
                
                'authed' => $this-> authed,
                'modal' => (string) $this-> auth_page_template()-> put('body.modal')
                
            ])
            -> respond();
        
    }

    
    function auth_ajax_error()
    {
        
        Response::JSON()
            //-> header('Content-Type: application/json')
            -> header('MyApp-Protocol-Id: auth')
            -> data([
                
                'status' => 'error',
                'errors' => $this-> errors
                
            ])
            -> respond();
        
        
    }    
}