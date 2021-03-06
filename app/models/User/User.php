<?php

namespace App\User;

class User
{
    protected $_id;
    protected $_username;
    protected $_password;
    protected $_api_key;
    protected $_options = array();
    protected $_optionsLoaded = false;

    public function __construct(array $options = array())
    {
        if (isset($options["id"])) {
            $this->setId($options["id"]);
        }
        if (isset($options["username"])) {
            $this->setUsername($options["username"]);
        }
        if (isset($options["password"])) {
            $this->setPassword($options["password"]);
        }
        if (isset($options["api_key"])) {
            $this->setApiKey($options["api_key"]);
        }
    }

    /**
    * @param int $id
    * @return User
    */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
    * @return int
    */
    public function getId()
    {
        if (!$this->_id) {
            return md5($this->_username);
        }
        return $this->_id;
    }

    /**
    * @param string $username
    * @return User
    */
    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }

    /**
    * @return string
    */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
    * @param string $key
    * @return User
    */
    public function setApiKey($key)
    {
        $this->_api_key = $key;
        return $this;
    }

    /**
    * @return string
    */
    public function getApiKey()
    {
        return $this->_api_key;
    }

    /**
    * @param string $password
    * @return User
    */
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    /**
    * @return string
    */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Indique si au moins un service de notification est activé.
     *
     * @return boolean
     */
    public function hasNotification()
    {
        $notifications = $this->getOption("notification");
        if (!$notifications || !is_array($notifications)) {
            return false;
        }

        foreach ($notifications AS $name => $params) {
            if (is_array($params) && !empty($params["active"])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne les systèmes d'alerte activés.
     *
     * @return array
     */
    public function getNotificationsEnabled()
    {
        $notifications = $this->getOption("notification");
        if (!$notifications || !is_array($notifications)) {
            return array();
        }

        $notifications_enabled = array();
        foreach ($notifications AS $name => $params) {
            if (is_array($params) && !empty($params["active"])) {
                $notifications_enabled[$name] = $params;
            }
        }

        return $notifications_enabled;
    }

    /**
     * Indique si un système d'alerte est actif ou non.
     *
     * @param string $name
     * @return boolean
     */
    public function notificationEnabled($name)
    {
        $params = $this->getOption("notification.".$name);
        return is_array($params) && !empty($params["active"]);
    }

    /**
     * Indique si l'utilisateur est administrateur.
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->getUsername() == "admin";
    }

    public function getOption($name, $default = null)
    {
        if (strpos($name, ".")) {
            $options = explode(".", $name);
            $nbOptions = count($options);
            $current = $this->_options;
            for ($i = 0; $i < $nbOptions; $i++) {
                if (is_array($current) && isset($current[$options[$i]])) {
                    $current = $current[$options[$i]];
                } else {
                    break;
                }
            }
            if ($i == $nbOptions) {
                return $current;
            }
            return $default;
        }
        return isset($this->_options[$name])?$this->_options[$name]:$default;
    }

    public function setOption($name, $value)
    {
        if (strpos($name, ".")) {
            $options = explode(".", $name);
            $nbOptions = count($options);
            $current = $value;
            for ($i = $nbOptions - 1; $i >= 0; $i--) {
                $current = array($options[$i] => $current);
            }
            $this->_options = array_replace_recursive($this->_options, $current);
        } else {
            $this->_options[$name] = $value;
        }
        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setOptions(array $options)
    {
        $this->_options = $options;
        return $this;
    }

    public function mergeOptions(array $options)
    {
        $this->_options = array_replace_recursive($this->_options, $options);
        return $this;
    }
}