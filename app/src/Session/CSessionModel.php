<?php
    
namespace Idun\Session;
    
/**
 * Class that extends the Anax-MV Session class
 *
 */
class CSessionModel extends \Anax\Session\CSession implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    
    /**
     * Unset values in session.
     *
     * @param string $key   in session variable.
     *
     * @return void
     */
    public function delete($key)
    {
        unset($_SESSION[$key]);
    }  
    
}
    
