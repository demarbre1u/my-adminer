<?php

/** 
 * Display a list of predefined database servers to login with just one click.
 * Don't use this in production enviroment unless the access is restricted
 *
 * @link https://www.adminer.org/plugins/#use
 * @author Gio Freitas, https://www.github.com/giofreitas
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class OneClickLogin
{
    /** @access protected */
    var $servers, $driver;

    /** 
     *
     * Set supported servers
     * @param array $servers
     * @param string $driver
     */
    function __construct($servers, $driver = "server")
    {

        $this->servers = $servers;
        $this->driver = $driver;
    }

    function login($login, $password)
    {
        foreach ($this->servers as $env_name => $servers) {
            $exists = isset($servers[SERVER]);

            if ($exists) {
                return true;
            }
        }

        return false;
    }

    function databaseValues($server)
    {
        $databases = $server['databases'];
        if (is_array($databases))
            foreach ($databases as $database => $name) {
                if (is_string($database))
                    continue;
                unset($databases[$database]);
                if (!isset($databases[$name]))
                    $databases[$name] = $name;
            }
        return $databases;
    }

    function loginForm()
    {
        foreach ($this->servers as $env_name => $servers) {
?>
            <table class="oneclick-login__table">
                <tr>
                    <th colspan="4"><?= $env_name ?></th>
                </tr>

                <?php
                foreach ($servers as $host => $server) {
                    $databases = $server['databases'];

                ?>

                    <?php
                    foreach ($databases as $db_label => $database) {
                    ?>

                        <tr>
                            <td><?= $host ?></th>
                            <td><?= $server["username"] ?></th>
                            <td><?= $db_label ?></th>
                            <td style="width: 0%">
                                <form action="" method="post">
                                    <input type="hidden" name="auth[driver]" value="<?= $this->driver; ?>">
                                    <input type="hidden" name="auth[server]" value="<?= $host; ?>">
                                    <input type="hidden" name="auth[username]" value="<?= h($server["username"]); ?>">
                                    <input type="hidden" name="auth[password]" value="<?= h($server["pass"]); ?>">
                                    <input type='hidden' name="auth[db]" value="<?= h($database); ?>" />
                                    <input type='hidden' name="auth[permanent]" value="1" />
                                    <input type="submit" value="<?= lang('Connect'); ?>">
                                </form>
                            </td>
                        </tr>

                <?php
                    }
                }
                ?>
            </table>

        <?php
        }
        ?>




<?php
        return true;
    }
}
