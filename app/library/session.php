<?php
declare(strict_types=1);

class Session
{

    /**
     * sessionに値を設定する
     *
     * @param string $key キー
     * @param mixed $value 値
     * @return なし
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * sessionから指定KEYの値を取得する
     *
     * @param string $key キー
     * @return mixed 指定KEYの値
     */
    public static function get($key): mixed
    {
        $value = null;
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
        }
        return $value;
    }

    /**
     * sessionを開始する
     *
     * @param なし
     * @return なし
     */
    public static function start(): void
    {
        session_start();
    }

    /**
     * sessionに登録されたデータを全て破棄する
     *
     * @param なし
     * @return なし
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * 現在のsession_idを新しく生成したものと置き換える
     *
     * @param なし
     * @return なし
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}
?>