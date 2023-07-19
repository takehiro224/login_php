<?php
declare(strict_types=1);

class LoginAccounts
{

    /**
     * ログインIDをキーにデータを取得する
     *
     * @param string $loginId ログインID
     * @return array SQL実行結果配列
     */
    public static function getByLoginId(string $loginId): array | bool
    {
        $sql = "SELECT * FROM login_accounts WHERE login_id = :login_id";
        $param = ["login_id" => $loginId];
        return DataBase::fetch($sql, $param);
    }
}
