<?php

// 닉네임 설정을 위해 랜덤으로 겹치지 않는 닉네임을 만들어주는 함수
function getRandomNickname()
{
    while (true) {
        $nicknameArr1 = array('족제비', '너구리', '미어캣', '하마', '코끼리', '사자', '호랑이', '토끼', '사슴', '고양이');
        $nicknameArr2 = array('행복한', '배고픈', '신나는', '흥부자', '슬픔이', '하늘', '바다', '바람', '구름', '햇님');
        $randNum1 = rand(0, 9);
        $randNum2 = rand(0, 9);

        $nickname = (string)$nicknameArr2[$randNum1] . $nicknameArr1[$randNum2];

// 닉네임이 존재 안 하면 리턴.
        if (!isValidUserName($nickname))
            return $nickname;
    }
}

// 유저의 유효성 검사
function isValidUser($id, $pw)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE UserId= ? AND UserPwd = ?) AS exist;";

    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);

}

// 유저 id의 유효성 검사
function isValidUserId($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE UserId= ?) AS exist;";

    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

// 닉네임 설정시 중복 닉네임을 체크하는 함수
function isValidUserName($nickname)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE UserName = ?) AS exist;";

    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$nickname]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

// 유저 생성 함수
function createUser($UserId, $UserPwd, $UserContact)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User (UserId, UserPwd, UserName, UserBirth, UserContact, UserGender,
UserPoint, CreatedAt, UpdatedAt, isDeleted)
VALUES (?, ?, ?, ?, ?, ?, default, default, default, default)";

    $st = $pdo->prepare($query);
    $st->execute([$UserId, $UserPwd, getRandomNickname(), '1990-01-01', $UserContact, 'M']);

    $st = null;
    $pdo = null;

}