<?php
class Auth
{

    /**
     * ログインを行う
     *
     * @param string $loginId ログインID
     * @param string $password パスワード
     * @return bool true:ログイン成功／false:ログイン失敗
     */
    public static function login(string $loginId, string $password): bool
    {
        $loginAccount = LoginAccounts::getByLoginId($loginId);
        if (empty($loginAccount["id"])) {
            return false;
        } elseif (password_verify($password, $loginAccount["password"]) === false) {
            return false;
        }
        Session::regenerate();
        Session::set("id", $loginAccount["id"]);
        Session::set("login_id", $loginAccount["login_id"]);
        Session::set("name", $loginAccount["name"]);
        return true;
    }

    /**
     * ログアウトを行う
     *
     * @param なし
     * @return なし
     */
    public static function logout(): void
    {
        Session::destroy();
        setcookie("PHPSESSID", "", time() - 1800, "/");
        Session::start();
    }

    /**
     * ログイン済かをチェックする
     *
     * @param なし
     * @return bool true:ログイン済／false:未ログイン
     */
    public static function isLoggedIn(): bool
    {
        return !is_null(Session::get("id"));
    }
}
?>