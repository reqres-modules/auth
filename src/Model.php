<?php
namespace Reqres\Module\Auth;

trait Model
{

    /**
     *
     * Проверяем пароль
     *
     * Поддерживаемые алгоритмы bcrypt, sha1, md5
     *
     */
	function mod_auth_check_password($password, $userdata, $algo = 'bcrypt')
    {
        
        $hash = $userdata['password'];
        
        if(isset($userdata['algo'])) $algo = $userdata['algo'];

        switch($algo){
                
			case 'bcrypt': return password_verify($password, $hash); break;
                
			case 'sha1': return sha1($password) === $hash; break;
                
			case 'md5': return md5($password) === $hash; break;

        }
        
        return false;
        
    }
    
}