<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . "/library/common.php");

if (!Auth::isLoggedIn()) {
    // 未ログインならログイン画面にリダイレクト
    redirect("login.php");
}

$id = '';
$name = '';
$nameKana = '';
$birthday = '';
$gender = '';
$organization = '';
$post = '';
$startDate = '';
$tel = '';
$mailAddress = '';
$errorMessage = '';
$successMessage = '';
$isEdit = false;
$isSave = false;

//POST通信
if (isPost()) {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $nameKana = $_POST['name_kana'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $organization = $_POST['organization'] ?? '';
    $post = $_POST['post'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $mailAddress = $_POST['mail_address'] ?? '';

    //trueならば登録ボタンが押されたということ
    $isSave = (isset($_POST['save']) && $_POST['save'] === '1') ? true : false; 

    //trueならば既存データの更新ということ
    $isEdit = (isset($_POST['edit']) && $_POST['edit'] === '1') ? true : false;

    //社員検索画面の編集ボタン押下
    if ($isEdit === true && $isSave === false) {
        //POSTされた社員番号の入力チェック
        if (!validateRequired($id)) { //空白でないか
            $errorMessage .= 'エラーが発生しました。もう一度やり直してください。<br>';
        } else if (!validateId($id)) { //6桁の数値か
            $errorMessage .= 'エラーが発生しました。もう一度やり直してください。<br>';
        } else {
            //存在する社員番号か
            if (!Users::isExists($id)) {
                $errorMessage .= 'エラーが発生しました。もう一度やり直してください。<br>';
            }
        }

        //入力チェックOK?
        if ($errorMessage === '') {
            //社員情報取得SQLの実行
            $user = Users::getById($id);

            $id = $user['id'];
            $name = $user['name'];
            $nameKana = $user['name_kana'];
            $birthday = $user['birthday'];
            $gender = $user['gender'];
            $organization = $user['organization'];
            $post = $user['post'];
            $startDate = $user['start_date'];
            $tel = $user['tel'];
            $mailAddress = $user['mail_address'];
        } else {
            //エラー画面表示
            $title = "エラー";
            require_once(TEMPLATE_DIR . "error.php");
            exit; //処理終了
        }
    }

    //登録ボタン押下
    if ($isSave === true) {
        //POSTされた社員番号の入力チェック
        if (!validateRequired($id)) { //空白でないか
            $errorMessage .= '社員番号を入力してください。<br>';
        } else if (!validateId($id)) { //6桁の数値か
            $errorMessage .= '社員番号は6桁の数値で入力してください。<br>';
        } else {
            //(新規登録時)存在しない社員番号か
            //(更新時)存在する社員番号か
            $exists = Users::isExists($id);
            if ($isEdit === false && $exists) {
                //新規登録時に同一社員番号が存在したらエラー
                $errorMessage .= '登録済みの社員番号です。<br>';
            } else if ($isEdit === true && !$exists) {
                //更新時に同一社員番号が存在しなかったらエラー
                $errorMessage .= '存在しない社員番号です。<br>';
            }
        }

        //POSTされた社員名の入力チェック
        if (!validateRequired($name)) { //空白でないか
            $errorMessage .= '社員名を入力してください。<br>';
        } else if (!validateMaxLength($name, 50)) { //50文字以内か
            $errorMessage .= '社員名は50文字以内で入力してください。<br>';
        }

        //POSTされた社員名カナの入力チェック
        if (!validateRequired($nameKana)) { //空白でないか
            $errorMessage .= '社員名カナを入力してください。<br>';
        } else if (!validateMaxLength($nameKana, 50)) { //50文字以内か
            $errorMessage .= '社員名カナは50文字以内で入力してください。<br>';
        }

        //POSTされた生年月日の入力チェック
        if (!validateRequired($birthday)) { //空白でないか
            $errorMessage .= '生年月日を入力してください。<br>';
        } else if (!validateDate($birthday)) {
            $errorMessage .= '生年月日を正しく入力してください。<br>';
        }

        //POSTされた性別の入力チェック
        //以下のいずれかか
        //男性、女性
        if (!validateGender($gender)) {
            $errorMessage .= '性別を選択してください。<br>';
        }

        //POSTされた部署の入力チェック
        //以下のいずれかか
        //営業部、人事部、総務部、システム開発1部、システム開発2部、システム開発3部、
        //システム開発4部、システム開発5部
        if (!validateOrganization($organization)) {
            $errorMessage .= '部署を選択してください。<br>';
        }

        //POSTされた役職の入力チェック
        //以下のいずれかか
        //部長、次長、課長、一般
        if (!validatePost($post)) {
            $errorMessage .= '役職を選択してください。<br>';
        }

        //POSTされた入社年月日の入力チェック
        if (!validateRequired($startDate)) { //空白でないか
            $errorMessage .= '入社年月日を入力してください。<br>';
        } else if (!validateDate($startDate)) {
            $errorMessage .= '入社年月日を正しく入力してください。<br>';
        }

        //POSTされた電話番号の入力チェック
        if (!validateRequired($tel)) { //空白でないか
            $errorMessage .= '電話番号を入力してください。<br>';
        } else if (!validateTel($tel)) { //15桁以内の数値か
            $errorMessage .= '電話番号は15桁以内の数値で入力してください。<br>';
        }

        //POSTされたメールアドレスの入力チェック
        if (!validateRequired($mailAddress)) { //空白でないか
            $errorMessage .= 'メールアドレスを入力してください。<br>';
        } else if (!validateMailAddress($mailAddress)) { //メールアドレス形式か
            $errorMessage .= 'メールアドレスを正しく入力してください。<br>';
        }

        //入力チェックOK?
        if ($errorMessage === '') {
            // echo "エラーなし";
            //トランザクション開始
            DataBase::beginTransaction();

            //新規登録?
            if ($isEdit === false) {
                //新規登録
                //社員情報登録SQLの実行
                Users::insert(
                    $id,
                    $name,
                    $nameKana,
                    $birthday,
                    $gender,
                    $organization,
                    $post,
                    $startDate,
                    $tel,
                    $mailAddress,
                );
            } else {
                //更新
                //社員情報更新SQLの実行
                Users::update(
                    $id,
                    $name,
                    $nameKana,
                    $birthday,
                    $gender,
                    $organization,
                    $post,
                    $startDate,
                    $tel,
                    $mailAddress,
                );
            }

            //コミット
            DataBase::commit();

            $successMessage = "登録完了しました。";
            $isEdit = true;
        }
    }
}
$title = "社員登録";
require_once(TEMPLATE_DIR . "input.php");
?>