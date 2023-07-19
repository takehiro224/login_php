<?php
declare(strict_types=1);

// 共通部分の読込
require_once(dirname(__DIR__) . "/library/common.php");

if (Auth::isLoggedIn()) {
    // ログイン済みなら検索画面にリダイレクト
    redirect("search.php");
}

$loginId = "";
$password = "";
$errorMessage = "";

//POST通信？
if (isPost()) {
    //ログイン認証SQLの実行
    $loginId = $_POST['login_id'] ?? '';
    $password = $_POST['password'] ?? '';

    if (Auth::login($loginId, $password)) {
        //ログイン成功
        redirect("search.php");
    } else {
        //ログイン失敗
        $errorMessage .= "ログインID、又はパスワードに誤りがあります。";
    }
}

//各入力項目表示
$title = "ログイン";
require_once(TEMPLATE_DIR . "login.php");
?>