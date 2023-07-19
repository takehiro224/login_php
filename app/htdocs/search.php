<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . "/library/common.php");

if (!Auth::isLoggedIn()) {
    // 未ログインならログイン画面にリダイレクト
    redirect("login.php");
}

$errorMessage = '';
$successMessage = "";

//POST送信かつ削除ボタン押下
if (isPost()) {
    //trueならば削除ボタンが押されたということ
    $isDelete = (isset($_POST['delete']) && $_POST['delete'] === '1') ? true : false;

    if ($isDelete === true) {
        //POSTされた社員番号の入力チェック
        $deleteId = isset($_POST['id']) ? $_POST['id'] : '';
        if (!validateRequired($deleteId)) { //空白でないか
            $errorMessage .= '社員番号が不正です。<br>';
        } else if (!validateId($deleteId)) { //6桁の数値か
            $errorMessage .= '社員番号が不正です。<br>';
        } else {
            //存在する社員番号か
            if (!Users::isExists($deleteId)) {
                $errorMessage .= '社員番号が不正です。<br>';
            }
        }

        //入力チェックOK?
        if ($errorMessage === '') {
            //トランザクション開始
            DataBase::beginTransaction();

            //社員情報の削除
            Users::deleteById($deleteId);

            //コミット
            DataBase::commit();

            $successMessage = "削除完了しました。";
        } else {
           // エラー有り
           echo $errorMessage;
        }
    }
}

$id = $_GET['id'] ?? '';
$nameKana = $_GET['name_kana'] ?? '';
$gender = $_GET['gender'] ?? '';

//件数取得SQLの実行
$count = Users::searchCount($id, $nameKana, $gender);

//社員情報取得SQLの実行
$data = Users::searchData($id, $nameKana, $gender);

$title = "社員検索";
require_once(TEMPLATE_DIR . "search.php");
?>
